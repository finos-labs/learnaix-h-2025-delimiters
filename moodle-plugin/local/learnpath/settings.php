<?php
/**
 * Settings for LearnPath Navigator Plugin
 * Team Delimiters - NatWest Hack4aCause
 *
 * @package    local_learnpath
 * @copyright  2025 Team Delimiters
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_learnpath', get_string('pluginname', 'local_learnpath'));
    
    // Snowflake Configuration Section
    $settings->add(new admin_setting_heading('learnpath_snowflake_header',
        get_string('snowflake_config', 'local_learnpath'),
        get_string('snowflake_config_desc', 'local_learnpath')));
    
    // Snowflake CLI Path
    $settings->add(new admin_setting_configtext('local_learnpath/snowflake_cli_path',
        get_string('snowflake_cli_path', 'local_learnpath'),
        get_string('snowflake_cli_path_desc', 'local_learnpath'),
        'snow',
        PARAM_TEXT));
    
    // Snowflake Connection Name
    $settings->add(new admin_setting_configtext('local_learnpath/snowflake_connection',
        get_string('snowflake_connection', 'local_learnpath'),
        get_string('snowflake_connection_desc', 'local_learnpath'),
        'default',
        PARAM_TEXT));
    
    // Snowflake Config Path
    $settings->add(new admin_setting_configtext('local_learnpath/snowflake_config_path',
        get_string('snowflake_config_path', 'local_learnpath'),
        get_string('snowflake_config_path_desc', 'local_learnpath'),
        '',
        PARAM_TEXT));
    
    // AI Model Selection
    $models = array(
        'mistral-7b' => 'Mistral 7B',
        'llama3.1-8b' => 'Llama 3.1 8B',
        'llama3.1-70b' => 'Llama 3.1 70B',
        'mixtral-8x7b' => 'Mixtral 8x7B'
    );
    
    $settings->add(new admin_setting_configselect('local_learnpath/ai_model',
        get_string('ai_model', 'local_learnpath'),
        get_string('ai_model_desc', 'local_learnpath'),
        'mistral-7b',
        $models));
    
    // Enable/Disable AI Features
    $settings->add(new admin_setting_configcheckbox('local_learnpath/enable_ai',
        get_string('enable_ai', 'local_learnpath'),
        get_string('enable_ai_desc', 'local_learnpath'),
        1));
    
    $ADMIN->add('localplugins', $settings);
}
