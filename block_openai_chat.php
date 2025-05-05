<?php
class block_openai_chat extends block_base {
    public function init() {
        $this->title = get_string('openai_chat', 'block_openai_chat');
    }

    public function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        global $OUTPUT, $PAGE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $persistconvo = get_config('block_openai_chat', 'persistconvo');
        if (!empty($this->config)) {
            $persistconvo = (property_exists($this->config, 'persistconvo') && get_config('block_openai_chat', 'allowinstancesettings')) ? $this->config->persistconvo : $persistconvo;
        }

        $this->page->requires->js_call_amd('block_openai_chat/lib', 'init', [[
            'blockId' => $this->instance->id,
            'apiType' => get_config('block_openai_chat', 'type') ?: 'chat',
            'persistConvo' => $persistconvo
        ]]);

        $showlabelscss = '';
        if (!empty($this->config) && !$this->config->showlabels) {
            $showlabelscss = '
                .openai_message:before {
                    display: none;
                }
                .openai_message {
                    margin-bottom: 0.5rem;
                }
            ';
        }

        $assistantname = get_config('block_openai_chat', 'assistantname') ?: get_string('defaultassistantname', 'block_openai_chat');
        $username = get_config('block_openai_chat', 'username') ?: get_string('defaultusername', 'block_openai_chat');

        if (!empty($this->config)) {
            $assistantname = (!empty($this->config->assistantname)) ? $this->config->assistantname : $assistantname;
            $username = (!empty($this->config->username)) ? $this->config->username : $username;
            if ($username === "Local") {
                $username = $USER->username;
            }
        }

        $assistantname = format_string($assistantname, true, ['context' => $this->context]);
        $username = format_string($username, true, ['context' => $this->context]);
        $username_css = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        $this->content = new stdClass;

        $this->content->text = '
            <script>
                var assistantName = ' . json_encode($assistantname) . ';
                var userName = ' . json_encode($username) . ';
            </script>

            <style>
                ' . $showlabelscss . '
                .openai_message.user:before {
                    content: "' . addslashes($username_css) . '";
                    display: inline-block;
                    max-width: 100%;
                }
                .openai_message.bot:before {
                    content: "' . addslashes($assistantname) . '";
                }

                .tts-button {
                    background: transparent;
                    border: none;
                    cursor: pointer;
                    font-size: 1rem;
                    margin-left: 0.5rem;
                }
            </style>

            <div id="openai_chat_log" role="log"></div>

            <script>
                function speak(text) {
                    if ("speechSynthesis" in window) {
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = "es-ES";
                        utterance.rate = 0.90;
                        utterance.pitch = 1.0;
                        speechSynthesis.cancel(); // Stop any previous speech
                        speechSynthesis.speak(utterance);
                    }
                }

                const logContainer = document.getElementById("openai_chat_log");

                function waitForFullTextAndSpeak(element) {
                    const checkInterval = 300; // ms
                    const maxTries = 20;
                    let tries = 0;
                    let lastText = "";

                    const interval = setInterval(() => {
                        const currentText = element.textContent.trim();
                        if (currentText === lastText || tries >= maxTries) {
                            clearInterval(interval);
                            setTimeout(() => speak(currentText), 100); 
                        } else {
                            lastText = currentText;
                            tries++;
                        }
                    }, checkInterval);
                }

                if (logContainer && "MutationObserver" in window) {
                    const observer = new MutationObserver(mutations => {
                        mutations.forEach(mutation => {
                            mutation.addedNodes.forEach(node => {
                                if (
                                    node.nodeType === Node.ELEMENT_NODE &&
                                    node.classList.contains("openai_message") &&
                                    node.classList.contains("bot")
                                ) {
                                    waitForFullTextAndSpeak(node);
                                }
                            });
                        });
                    });

                    observer.observe(logContainer, { childList: true });
                } else {
                    console.warn("MutationObserver not supported or chat log not found.");
                }

                if (!("speechSynthesis" in window)) {
                    alert("Your browser does not support text-to-speech. Please try using a different browser.");
                }
            </script>
        ';

        if (
            empty(get_config('block_openai_chat', 'apikey')) &&
            (!get_config('block_openai_chat', 'allowinstancesettings') || empty($this->config->apikey))
        ) {
            $this->content->footer = get_string('apikeymissing', 'block_openai_chat');
        } else {
            $contextdata = [
                'logging_enabled' => get_config('block_openai_chat', 'logging'),
                'is_edit_mode' => $PAGE->user_is_editing(),
                'pix_popout' => '/blocks/openai_chat/pix/arrow-up-right-from-square.svg',
                'pix_arrow_right' => '/blocks/openai_chat/pix/arrow-right.svg',
                'pix_refresh' => '/blocks/openai_chat/pix/refresh.svg',
            ];

            $this->content->footer = $OUTPUT->render_from_template('block_openai_chat/control_bar', $contextdata);
        }

        return $this->content;
    }

    public function handle_streaming_response($history) {
        $response_data = $this->create_completion($history, true);
        header('Content-Type: application/json');
        echo json_encode($response_data);
        exit;
    }
}
