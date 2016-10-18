<?php

/**
 * Name: 文章浏览次数
 * Description: 开启后可以统计文章浏览的次数，并且你可以在主题中使用get_post_view_count等函数获取
 */

function get_post_view_count($post_id = 0){
	if(!$post_id) $post_id = get_the_ID();
	$count_key = 'views';
	$count = get_post_meta($post_id,$count_key,true);
	if($count == ''){
		delete_post_meta($post_id,$count_key);
		add_post_meta($post_id,$count_key,'0');
		return 0;
	}
	return $count;
}
function the_post_view_count($post_id = 0){
	if(!$post_id) $post_id = get_the_ID();
	echo get_post_view_count($post_id);
}

function get_post_view_count_total() {
    global $wpdb;
    $total = $wpdb->get_var("SELECT SUM(meta_value+0) FROM $wpdb->postmeta WHERE `meta_key`='views';");
    return $total;
}

function get_post_view_count_max() {
    global $wpdb;
    return $wpdb->get_var("SELECT MAX(meta_value+0) FROM $wpdb->postmeta WHERE `meta_key`='views';");
}

add_action('wp','set_post_view_count',-99);
function set_post_view_count(){
	if(!is_single()) {
		return;
	}

    global $post;
    $post_id = $post->ID;

	// Autosave, do nothing
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	// AJAX? Not used here
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
	// Return if it's a post revision
	if ( false !== wp_is_post_revision( $post_id ) ) return;
	if(!is_single()) return;
    
	$count_key = 'views';
	
	//if(isset($_COOKIE['post_view_count_'.$post_id.'_'.COOKIEHASH]) && $_COOKIE['post_view_count_'.$post_id.'_'.COOKIEHASH] == '1') return;
	
	$count = (int)get_post_meta($post_id,$count_key,true);
	// if($count < 200) {
	// 	$count = 200 + mt_rand(10,50);
	// }
	$count ++ ;
	update_post_meta($post_id,$count_key,$count) || add_post_meta($post_id,$count_key,$count);
	// setcookie('post_view_count_'.$post_id.'_'.COOKIEHASH,'1',time() + 3600,COOKIEPATH,COOKIE_DOMAIN);
}
