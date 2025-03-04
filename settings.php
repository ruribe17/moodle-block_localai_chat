<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin settings
 *
 * @package    block_localai_chat
 * @copyright  2024 Bryce Yoder <me@bryceyoder.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    if (!defined('BEHAT_SITE_RUNNING')) {
        $ADMIN->add('reports', new admin_externalpage(
            'openai_chat_report', 
            get_string('openai_chat_logs', 'block_localai_chat'), 
            new moodle_url("$CFG->wwwroot/blocks/openai_chat/report.php", ['courseid' => 1]),
            'moodle/site:config'
        ));
    }

    if ($ADMIN->fulltree) {

        require_once($CFG->dirroot .'/blocks/openai_chat/lib.php');

        $type = get_type_to_display();
        $assistant_array = [];
        if ($type === 'assistant') {
            $assistant_array = fetch_assistants_array();
        }

        global $PAGE;
        $PAGE->requires->js_call_amd('block_localai_chat/settings', 'init');

        $settings->add(new admin_setting_configtext(
            'block_localai_chat/apikey',
            get_string('apikey', 'block_localai_chat'),
            get_string('apikeydesc', 'block_localai_chat'),
            '',
            PARAM_TEXT
        ));

        $settings->add(new admin_setting_configselect(
            'block_localai_chat/type',
            get_string('type', 'block_localai_chat'),
            get_string('typedesc', 'block_localai_chat'),
            'chat',
            ['chat' => 'chat', 'assistant' => 'assistant', 'azure' => 'azure']
        ));

        $settings->add(new admin_setting_configcheckbox(
            'block_localai_chat/restrictusage',
            get_string('restrictusage', 'block_localai_chat'),
            get_string('restrictusagedesc', 'block_localai_chat'),
            1
        ));

        $settings->add(new admin_setting_configtext(
            'block_localai_chat/assistantname',
            get_string('assistantname', 'block_localai_chat'),
            get_string('assistantnamedesc', 'block_localai_chat'),
            'Assistant',
            PARAM_TEXT
        ));

        $settings->add(new admin_setting_configtext(
            'block_localai_chat/username',
            get_string('username', 'block_localai_chat'),
            get_string('usernamedesc', 'block_localai_chat'),
            'User',
            PARAM_TEXT
        ));

        $settings->add(new admin_setting_configcheckbox(
            'block_localai_chat/logging',
            get_string('logging', 'block_localai_chat'),
            get_string('loggingdesc', 'block_localai_chat'),
            0
        ));

        // Assistant settings //

        if ($type === 'assistant') {
            $settings->add(new admin_setting_heading(
                'block_localai_chat/assistantheading',
                get_string('assistantheading', 'block_localai_chat'),
                get_string('assistantheadingdesc', 'block_localai_chat')
            ));

            if (count($assistant_array)) {
                $settings->add(new admin_setting_configselect(
                    'block_localai_chat/assistant',
                    get_string('assistant', 'block_localai_chat'),
                    get_string('assistantdesc', 'block_localai_chat'),
                    count($assistant_array) ? reset($assistant_array) : null,
                    $assistant_array
                ));
            } else {
                $settings->add(new admin_setting_description(
                    'block_localai_chat/noassistants',
                    get_string('assistant', 'block_localai_chat'),
                    get_string('noassistants', 'block_localai_chat'),
                ));
            }

            $settings->add(new admin_setting_configcheckbox(
                'block_localai_chat/persistconvo',
                get_string('persistconvo', 'block_localai_chat'),
                get_string('persistconvodesc', 'block_localai_chat'),
                1
            ));

        } else {

            // Chat settings //

            if ($type === 'azure') {
                $settings->add(new admin_setting_heading(
                    'block_localai_chat/azureheading',
                    get_string('azureheading', 'block_localai_chat'),
                    get_string('azureheadingdesc', 'block_localai_chat')
                ));

                $settings->add(new admin_setting_configtext(
                    'block_localai_chat/resourcename',
                    get_string('resourcename', 'block_localai_chat'),
                    get_string('resourcenamedesc', 'block_localai_chat'),
                    "",
                    PARAM_TEXT
                ));

                $settings->add(new admin_setting_configtext(
                    'block_localai_chat/deploymentid',
                    get_string('deploymentid', 'block_localai_chat'),
                    get_string('deploymentiddesc', 'block_localai_chat'),
                    "",
                    PARAM_TEXT
                ));

                $settings->add(new admin_setting_configtext(
                    'block_localai_chat/apiversion',
                    get_string('apiversion', 'block_localai_chat'),
                    get_string('apiversiondesc', 'block_localai_chat'),
                    "2023-09-01-preview",
                    PARAM_TEXT
                ));
            }

            $settings->add(new admin_setting_heading(
                'block_localai_chat/chatheading',
                get_string('chatheading', 'block_localai_chat'),
                get_string('chatheadingdesc', 'block_localai_chat')
            ));

            $settings->add(new admin_setting_configtextarea(
                'block_localai_chat/prompt',
                get_string('prompt', 'block_localai_chat'),
                get_string('promptdesc', 'block_localai_chat'),
                "Below is a conversation between a user and a support assistant for a Moodle site, where users go for online learning.",
                PARAM_TEXT
            ));

            $settings->add(new admin_setting_configtextarea(
                'block_localai_chat/sourceoftruth',
                get_string('sourceoftruth', 'block_localai_chat'),
                get_string('sourceoftruthdesc', 'block_localai_chat'),
                '',
                PARAM_TEXT
            ));
        }


        // Advanced Settings //

        $settings->add(new admin_setting_heading(
            'block_localai_chat/advanced',
            get_string('advanced', 'block_localai_chat'),
            get_string('advanceddesc', 'block_localai_chat')
        ));

        $settings->add(new admin_setting_configcheckbox(
            'block_localai_chat/allowinstancesettings',
            get_string('allowinstancesettings', 'block_localai_chat'),
            get_string('allowinstancesettingsdesc', 'block_localai_chat'),
            0
        ));

        if ($type === 'assistant') {

        } else {
            $settings->add(new admin_setting_configselect(
                'block_localai_chat/model',
                get_string('model', 'block_localai_chat'),
                get_string('modeldesc', 'block_localai_chat'),
                'text-davinci-003',
                get_models()['models']
            ));

            $settings->add(new admin_setting_configtext(
                'block_localai_chat/temperature',
                get_string('temperature', 'block_localai_chat'),
                get_string('temperaturedesc', 'block_localai_chat'),
                0.5,
                PARAM_FLOAT
            ));

            $settings->add(new admin_setting_configtext(
                'block_localai_chat/maxlength',
                get_string('maxlength', 'block_localai_chat'),
                get_string('maxlengthdesc', 'block_localai_chat'),
                500,
                PARAM_INT
            ));

            $settings->add(new admin_setting_configtext(
                'block_localai_chat/topp',
                get_string('topp', 'block_localai_chat'),
                get_string('toppdesc', 'block_localai_chat'),
                1,
                PARAM_FLOAT
            ));

            $settings->add(new admin_setting_configtext(
                'block_localai_chat/frequency',
                get_string('frequency', 'block_localai_chat'),
                get_string('frequencydesc', 'block_localai_chat'),
                1,
                PARAM_FLOAT
            ));

            $settings->add(new admin_setting_configtext(
                'block_localai_chat/presence',
                get_string('presence', 'block_localai_chat'),
                get_string('presencedesc', 'block_localai_chat'),
                1,
                PARAM_FLOAT
            ));
        }
    }
}
