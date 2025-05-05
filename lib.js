define(['jquery'], function($) {
    return {
        init: function(blockId, apiType, persistConvo) {
            $('#openai_chat_log').on('submit', '.openai_chat_form', function(e) {
                e.preventDefault();
                const form = $(this);
                const message = form.find('textarea').val().trim();
                if (!message) {
                    return;
                }

                form.find('textarea').val('');
                const messageContainer = $('<div class="openai_message user"></div>');
                messageContainer.text(message);
                $('#openai_chat_log').append(messageContainer);

                const history = JSON.parse(localStorage.getItem('chatHistory')) || [];
                history.push({ role: 'user', content: message });

                $.ajax({
                    url: M.cfg.wwwroot + '/blocks/openai_chat/handle_streaming.php',
                    method: 'POST',
                    data: JSON.stringify({ history: history }),
                    contentType: 'application/json',
                    xhrFields: {
                        onprogress: function(event) {
                            if (event.lengthComputable) {
                                const percentComplete = (event.loaded / event.total) * 100;
                                console.log(`Completed: ${percentComplete}%`);
                            }
                        }
                    },
                    success: function(response) {
                        const botMessageContainer = $('<div class="openai_message bot"></div>');
                        botMessageContainer.text(response.message);
                        $('#openai_chat_log').append(botMessageContainer);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching response:', textStatus, errorThrown);
                    }
                });
            });
        }
    };
});
