<?php

/**
 * Name: 清除头部信息（高级版）
 * Description: 本模块可以清除wp-json, moji, embed
 */

// 去除wp-json
add_filter('rest_enabled', '_return_false');
add_filter('rest_jsonp_enabled', '_return_false');
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

function disable_embeds_init() {
    /* @var WP $wp */
    global $wp;

    // Remove the embed query var.
    $wp->public_query_vars = array_diff( $wp->public_query_vars, array(
        'embed',
    ) );

    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );

    // Turn off
    add_filter( 'embed_oembed_discover', '__return_false' );

    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );

    // Remove all embeds rewrite rules.
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );

    // 禁用emoji
    remove_action( 'admin_print_scripts','print_emoji_detection_script');
    remove_action( 'admin_print_styles','print_emoji_styles');
    remove_action( 'wp_head','print_emoji_detection_script',7);
    remove_action( 'wp_print_styles','print_emoji_styles');
    remove_filter( 'the_content_feed','wp_staticize_emoji');
    remove_filter( 'comment_text_rss','wp_staticize_emoji');
    remove_filter( 'wp_mail','wp_staticize_emoji_for_email');
}

add_action( 'init', 'disable_embeds_init', 9999 );

/**
 * Removes the 'wpembed' TinyMCE plugin.
 *
 * @since 1.0.0
 *
 * @param array $plugins List of TinyMCE plugins.
 * @return array The modified list.
 */
function disable_embeds_tiny_mce_plugin( $plugins ) {
    return array_diff( $plugins, array( 'wpembed' ) );
}

/**
 * Remove all rewrite rules related to embeds.
 *
 * @since 1.2.0
 *
 * @param array $rules WordPress rewrite rules.
 * @return array Rewrite rules without embeds rules.
 */
function disable_embeds_rewrites( $rules ) {
    foreach ( $rules as $rule => $rewrite ) {
        if ( false !== strpos( $rewrite, 'embed=true' ) ) {
            unset( $rules[ $rule ] );
        }
    }

    return $rules;
}

/**
 * Remove embeds rewrite rules on plugin activation.
 *
 * @since 1.2.0
 */
function disable_embeds_remove_rewrite_rules() {
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'disable_embeds_remove_rewrite_rules' );

/**
 * Flush rewrite rules on plugin deactivation.
 *
 * @since 1.2.0
 */
function disable_embeds_flush_rewrite_rules() {
    remove_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'disable_embeds_flush_rewrite_rules' );