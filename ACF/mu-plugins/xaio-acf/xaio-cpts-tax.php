<?php
/**
 * Plugin Name: XAIO – CPTs & Taxonomies
 * Description: Registers XAIO custom post types and taxonomies (ACF-free friendly).
 * Author: XAIO
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action('init', function() {
    // Common supports
    $supports = ['title','editor','revisions'];

    // Helper to register CPT
    $reg = function($key, $singular, $plural, $args = []) use ($supports) {
        $labels = [
            'name' => $plural,
            'singular_name' => $singular,
            'add_new_item' => "Add New $singular",
            'edit_item' => "Edit $singular",
            'new_item' => "New $singular",
            'view_item' => "View $singular",
            'search_items' => "Search $plural",
            'not_found' => "No $plural found",
            'not_found_in_trash' => "No $plural found in Trash",
        ];
        $defaults = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'supports' => $supports,
            'show_in_rest' => true,
            'rewrite' => ['slug' => $key, 'with_front' => false],
            'exclude_from_search' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
        ];
        register_post_type($key, array_merge($defaults, $args));
    };

    // CPTs
    $reg('fact', 'Fact', 'Facts');
    $reg('event', 'Event', 'Events');
    $reg('report', 'Report', 'Reports');
    $reg('snippet', 'Snippet', 'Snippets');
    $reg('evidence', 'Evidence', 'Evidence');
    $reg('source', 'Source', 'Sources');
    $reg('organization', 'Organization', 'Organizations');
    $reg('contributor', 'Contributor', 'Contributors');
    // Info (no archive)
    $reg('info', 'Info', 'Info', ['has_archive' => false]);

    // Taxonomies
    $tax = function($key, $singular, $plural, $object_types, $args = []) {
        $labels = [
            'name' => $plural,
            'singular_name' => $singular,
            'search_items' => "Search $plural",
            'all_items' => "All $plural",
            'edit_item' => "Edit $singular",
            'update_item' => "Update $singular",
            'add_new_item' => "Add New $singular",
            'new_item_name' => "New $singular Name",
            'menu_name' => $plural,
        ];
        $defaults = [
            'labels' => $labels,
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => $key, 'with_front' => false],
        ];
        register_taxonomy($key, $object_types, array_merge($defaults, $args));
    };

    // domain (non-hierarchical): fact, event, report
    $tax('domain', 'Domain', 'Domains', ['fact','event','report'], ['hierarchical' => false]);

    // geo (hierarchical): fact, event
    $tax('geo', 'Geography', 'Geography', ['fact','event'], ['hierarchical' => true]);

    // source_type (non-hierarchical): source, evidence
    $tax('source_type', 'Source Type', 'Source Types', ['source','evidence'], ['hierarchical' => false]);

    // lang (non-hierarchical, optional): evidence, source
    $tax('lang', 'Language', 'Languages', ['evidence','source'], ['hierarchical' => false]);

    // actor (non-hierarchical, optional): event, fact, report
    $tax('actor', 'Actor', 'Actors', ['event','fact','report'], ['hierarchical' => false]);

}, 0);

// 4) Permalink Strategy – date-prefixed slugs for selected CPTs
function xaio_date_prefix_slug($post_id, $post, $update){
  if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

  $types = ['fact','event','report','snippet'];
  if (!in_array($post->post_type, $types, true)) return;

  $date = get_the_date('Y-m-d', $post_id);
  if (!$date || $post->post_status === 'draft') $date = current_time('Y-m-d');

  $slug = $post->post_name ?: sanitize_title($post->post_title);
  if (strpos($slug, $date . '-') === 0) return;

  $new_slug = sanitize_title($date . '-' . substr($slug, 0, 120));
  $unique   = wp_unique_post_slug($new_slug, $post_id, $post->post_status, $post->post_type, $post->post_parent);

  remove_action('wp_insert_post', 'xaio_date_prefix_slug', 10);
  wp_update_post(['ID' => $post_id, 'post_name' => $unique]);
  add_action('wp_insert_post', 'xaio_date_prefix_slug', 10, 3);
}
add_action('wp_insert_post', 'xaio_date_prefix_slug', 10, 3);

}
