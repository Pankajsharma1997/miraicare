<?php
/**
 * Add theme page
 */
function gutenify_starter_menu() {
	add_theme_page( esc_html__( 'Gutenify Starter', 'gutenify-starter' ), esc_html__( 'Gutenify Theme', 'gutenify-starter' ), 'edit_theme_options', 'gutenify-starter-info', 'gutenify_starter_theme_page_display' );
}
add_action( 'admin_menu', 'gutenify_starter_menu' );

/**
 * Display About page
 */
function gutenify_starter_theme_page_display() {
	$theme = wp_get_theme();

	// if ( is_child_theme() ) {
	// 	$theme = wp_get_theme()->parent();
	// }

	include_once 'templates/theme-info.php';
}

function gutenify_starter_admin_plugin_notice() {
	$theme = wp_get_theme();
	$hide_notice_bar = get_user_meta( get_current_user_id(), 'gutenify_starter_hide_theme_info_noticebar', true );
	if ( '1' !== $hide_notice_bar ) {
		wp_enqueue_style( 'gutenify-starter-admin-style' );
		wp_enqueue_script( 'gutenify-starter-admin' );
		include 'templates/admin-plugin-notice.php';
	}
}
add_action( 'admin_notices', 'gutenify_starter_admin_plugin_notice' );


function gutenify_starter_hide_theme_info_noticebar() {
	check_ajax_referer( 'gutenify_starter-nonce', 'gutenify_starter-nonce-name' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( -1 );
	}

	update_user_meta( get_current_user_id(), 'gutenify_starter_hide_theme_info_noticebar', 1 );

	wp_die( 1 );
}
add_action( 'wp_ajax_gutenify_starter_hide_theme_info_noticebar', 'gutenify_starter_hide_theme_info_noticebar' );
