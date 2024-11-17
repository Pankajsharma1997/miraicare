<?php
function gutenify_health_clinic_child_enqueue_styles() {
    // Enqueue the parent theme stylesheet
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'gutenify_health_clinic_child_enqueue_styles');
