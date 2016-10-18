<?php

/**
 * Name: 获取文章缩略图
 * Description: 可以在主题中使用get_post_thumb_src, the_post_thumb_src, get_post_thumb, the_post_thumb几个函数来获取文章的缩略图
 */

function get_post_thumb_src($size = 'post-thumbnail',$default = false){
	global $post;
	if(function_exists('has_post_thumbnail') && has_post_thumbnail()){
		$image_id = get_post_thumbnail_id($post->ID);
		$image_src = wp_get_attachment_image_src($image_id,$size);
		$image_src = apply_filters('wp_get_attachment_image_src',$image_src[0]);
		return $image_src;
	}
	$thumb_meta = get_post_meta($post->ID,'thumbnail_src',true);
	if($thumb_meta){
		return $thumb_meta;
	}
	preg_match_all('/<img.+src=[\'\"]([^\'\"]+)[\'\"].* \/>/i',$post->post_content,$images);
	if(!empty($images)) foreach($images[1] as $image){
		if(strpos($image,'http') === 0){
			$image_src = $image;
			break;
		}else{
			$image_src = false;
		}
	}
	if(!isset($image_src)){
		$image_src = $default;
	}
	return $image_src;
}
function the_post_thumb_src($size = 'post-thumbnail',$default = false){
	echo get_post_thumb_src($size,$default);
}
function get_post_thumb($size = 'post-thumbnail',$default = false,$alt = false,$width = false,$height = false,$attrs = false){
	global $post;
	$src = get_post_thumb_src($size,$default);
	if(!$src){
		return false;
	}
	$alt = ($alt ? ' alt="'.$alt.'"' : ' alt="'.$post->post_title.'"');
	if($width) $width = ' width="'.$width.'"';
	if($height) $height = ' height="'.$height.'"';
	if($attrs) $attrs = ' '.$attrs;
	return '<img src="'.$src.'"'.$alt.$width.$height.$attrs.' class="post-thumbnail post-thumbnail-size-'.$size.'" />';
}
function the_post_thumb($size = 'post-thumbnail',$default = false,$alt = false,$width = false,$height = false,$attrs = false){
	echo get_post_thumb($size,$default,$alt,$width,$height,$attrs);
}