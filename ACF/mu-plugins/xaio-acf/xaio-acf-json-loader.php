<?php
/**
 * Plugin Name: XAIO – ACF Local JSON Loader
 * Description: Loads ACF Local JSON field groups shipped with XAIO from this plugin folder.
 * Author: XAIO
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) { exit; }

// Load JSON from this plugin's acf-json directory (field groups).
add_filter('acf/settings/load_json', function($paths) {
    $paths[] = plugin_dir_path(__FILE__) . 'acf-json/field-groups';
    return $paths;
});

// Optional: Save JSON edits back into this folder (enable if you want writable sync here).
// add_filter('acf/settings/save_json', function($path) {
//     return plugin_dir_path(__FILE__) . 'acf-json/field-groups';
// });
