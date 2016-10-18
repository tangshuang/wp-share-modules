<?php

/**
 * Name: 移除头部多余信息
 * Description: 本模块移除的包括feed, generator, wlwmanifest_link, rsd_link, version, dns-prefetch
 */

remove_action('wp_head','feed_links',2);
remove_action('wp_head','feed_links_extra',3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');

// 去除头部中的版本
add_filter('the_generator', '__remove_wordpress_version');
function __remove_wordpress_version() { 
	return null;
}

// 去除最近评论的样式,2.8以下版本
add_filter('wp_head','__remove_wp_widget_recent_comments_style',1);
function __remove_wp_widget_recent_comments_style() {
	if(has_filter('wp_head','wp_widget_recent_comments_style')){
		remove_filter('wp_head','wp_widget_recent_comments_style');
	}
}

// 去除最近评论的样式,2.9+版本
add_action('widgets_init','__remove_recent_comments_style');
function __remove_recent_comments_style() {
  global $wp_widget_factory;
  remove_action('wp_head',array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'],'recent_comments_style'));
}

//移除WordPress头部加载DNS预获取（dns-prefetch）
function remove_dns_prefetch( $hints, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        return array_diff( wp_dependencies_unique_hosts(), $hints );
    }
 
    return $hints;
}
add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );