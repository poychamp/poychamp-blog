<?php
/**
 * Poychamp theme setup. Tokens live in theme.json, the rest in style.css.
 */

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('poychamp', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
});

// Same rules in the editor, so what you see there is what ships.
add_action('after_setup_theme', function () {
    add_editor_style('style.css');
});

// viewport-fit=cover lets the page reach under the notch, so the safe-area padding
// in style.css has something to do. Replaces core's tag, which WordPress adds at
// template load, so it can only be removed once wp_head is running.
add_action('wp_head', function () {
    remove_action('wp_head', '_block_template_viewport_meta_tag', 0);
    echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />' . "\n";
}, -1);

// Dark only: native controls and the mobile browser bar match the page (spec §2).
add_action('wp_head', function () {
    echo '<meta name="color-scheme" content="dark">' . "\n";
    echo '<meta name="theme-color" content="#0A0A0A">' . "\n";
}, 1);

add_action('init', function () {
    register_block_style('core/group', ['name' => 'card', 'label' => __('Card', 'poychamp')]);
    register_block_style('core/button', ['name' => 'secondary', 'label' => __('Secondary', 'poychamp')]);
});
