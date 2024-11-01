<?php
/*
Plugin Name: WpMultimediaBridge
Plugin URI: http://en.imatrice.com/?imaname=wpmb&imaver=1.1.4&imaverdb=1.1.4&imatype=info
Description: WpMultimediaBridge converts video URL into a visual interactive element. Soon completely redesigned with new themes, new features and renamed to WpVib. Stay connected!
Version: 1.1.5
Author: imatrice
Author URI: http://en.imatrice.com/

    # Copyright (C) 2012 imatrice
    #
    # "WpMultimediaBridge" is intellectual property of Imatrice,
    # and may not be used in conjuction with the redistribution, advertisment,
    # sale or other public use of this software without permission.
    #
    # This program is free software: you can redistribute it and/or modify
    # it under the terms of the GNU General Public License as published by
    # the Free Software Foundation, either version 3 of the License, or
    # (at your option) any later version.
    #
    # This program is distributed in the hope that it will be useful,
    # but WITHOUT ANY WARRANTY; without even the implied warranty of
    # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    # GNU General Public License for more details.
    #
    # You should have received a copy of the GNU General Public License
    # along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Define WpMultimediaBridge core access (path and url).
if (!defined('WPMB_CORE_URL')) define('WPMB_CORE_URL', plugin_dir_url(__FILE__) . 'core');
if (!defined('WPMB_CORE_DIR')) define('WPMB_CORE_DIR', plugin_dir_path(__FILE__) . 'core');

// Define WpMultimediaBridge admin access (path and url).
if (!defined('WPMB_ADMIN_URL')) define('WPMB_ADMIN_URL', plugin_dir_url(__FILE__) . 'admin');
if (!defined('WPMB_ADMIN_DIR')) define('WPMB_ADMIN_DIR', plugin_dir_path(__FILE__) . 'admin');

// Define WpMultimediaBridge resources access (path and url).
if (!defined('WPMB_RESOURCES_URL')) define('WPMB_RESOURCES_URL', plugin_dir_url(__FILE__) . 'resources');
if (!defined('WPMB_RESOURCES_DIR')) define('WPMB_RESOURCES_DIR', plugin_dir_path(__FILE__) . 'resources');

// Main classes required before what's following.
require_once(WPMB_CORE_DIR . '/wpmb-classes.php');
// Also load some procedural functions for WP and/or PHP.
require_once(WPMB_CORE_DIR . '/wpmb-proc-functions.php');

// Uninstall routine.
if ( function_exists('register_uninstall_hook') )
    register_uninstall_hook(__FILE__, array('Wpmb', 'uninstall_plugin'));

// Global container for plugin options.
global $wpmb_options;
$wpmb_options = Wpmb::get_options();

// Global container for media providers.
global $wpmb_media_providers;
$wpmb_media_providers = array(
    '#http://(www\.)?youtube\.com/watch.*#i' => new Wpmb_Crawling_Pattern_Youtube(),
    '#http://(www\.)?youtu\.be/.*#i'         => new Wpmb_Crawling_Pattern_Youtube(),
    '#http://(www\.)?vimeo\.com/.*#i'        => new Wpmb_Crawling_Pattern_Vimeo()
);

// IMPORTANT global flag to disable Wpmb filtering with post excerpt.
global $wpmb_excerpt_flag;
$wpmb_excerpt_flag = false;

// Define WpMultimediaBridge theme access (path and url).
if (!defined( 'WPMB_THEMES_URL' )) define('WPMB_THEMES_URL', plugin_dir_url(__FILE__) . 'themes/' . $wpmb_options['wpmb_theme_folder']);
if (!defined( 'WPMB_THEMES_DIR' )) define('WPMB_THEMES_DIR', plugin_dir_path(__FILE__) . 'themes/' . $wpmb_options['wpmb_theme_folder']);

if ($wpmb_options['wpmb_disable_post_revision'])
    define('WP_POST_REVISIONS', 0);

// Loading the right view according to theme administrator decide to use.
require_once(WPMB_THEMES_DIR . '/wpmb-view.php');

// Loading administration class for wp-admin control panel.
require_once(WPMB_ADMIN_DIR . '/wpmb-classes.php');

/** BEGIN - Actions */
add_action('init', array('Wpmb', 'init'));

add_action('admin_init', array('Wpmb_Admin', 'admin_css_js')); // Admin styles and scripts
add_action('wp_ajax_save-media-data', array('Wpmb_Admin_Ajax', 'save_media_data'));
add_action('wp_ajax_reset-media-data', array('Wpmb_Admin_Ajax', 'reset_media_data'));
add_action('wp_ajax_activate-media', array('Wpmb_Admin_Ajax', 'activate_media'));

add_action('admin_menu', array('Wpmb_Admin', 'admin_menu')); // Admin plugin menu
add_action('admin_menu', array('Wpmb_Admin', 'admin_add_meta_box')); // Post editor meta box
add_action('save_post', array('Wpmb', 'process_post_meta_box')); // Process custom post meta box
add_action('deleted_post', array('Wpmb', 'process_post_deletion')); // Deleting related medias.
if ($wpmb_options['wpmb_disable_post_autosave'])
    add_action('wp_print_scripts', array('Wpmb', 'disable_post_autosave'));

add_action('wp', array('Wpmb', 'public_css_js')); // Public styles and scripts
add_action('wp_ajax_get-embedded-object', array('Wpmb', 'get_embedded_object_ajax'));
add_action('wp_ajax_nopriv_get-embedded-object', array('Wpmb', 'get_embedded_object_ajax'));
/** END - Actions */

// Footer stats for video load (POC).
global $wpmb_stats_footer;
$wpmb_stats_footer = '';
add_action('wp_footer', array('Wpmb', 'display_footer_iframe_stats'));

/** BEGIN - Filters */
// Disable the Wordpress AutoEmbed for urls in content.
if (get_option('embed_autourls', false))
    update_option('embed_autourls', '0');

// Disable the Wordpress Make Clickable for urls in content. NOT ACTIVE IN VERSION 1
//if (has_filter('comment_text', 'make_clickable'))
//	remove_filter('comment_text', 'make_clickable', 9);

// Just in case someone did it!
if (has_filter('the_content', 'make_clickable'))
    remove_filter('the_content', 'make_clickable');

// Filter post/page content?
// Plus a workaround because my multimedia canvas was getting screwed by wpautop().
if (has_filter('the_content', 'wpautop')) {
    remove_filter('the_content', 'wpautop');
    add_filter('the_content', array('Wpmb', 'filter_post_content_wpautop'), 5);
} else {
    add_filter('the_content', array('Wpmb', 'filter_post_content'), 5);
}

// Filter post/page comment? NOT ACTIVE IN VERSION 1
//add_filter('get_comment_text', array('Wpmb', 'filter_comment_content'), 5);

// Wpmb content filtering must be disable while the post excerpt is being built by Wordpress.
// So I've added a custom function before wp_trim_excerpt() so Wpmb media filtering will not be processed.
if (function_exists('wp_trim_excerpt') && has_filter('get_the_excerpt', 'wp_trim_excerpt')) {
    remove_filter('get_the_excerpt', 'wp_trim_excerpt');
    add_filter('get_the_excerpt', array('Wpmb', 'rewrite_wp_trim_excerpt'));
}
/** END - Filters */
?>