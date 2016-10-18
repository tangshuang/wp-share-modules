<?php

/**
 * Name: 文章摘要修复
 * Description: 修复文章摘要的字数、结尾符号，增加get_excerpt()函数
 */

// 摘要的长度
add_filter('excerpt_length','custom_excerpt_length',99);
function custom_excerpt_length($length){
  return 120;
}
// 摘要的末尾链接
add_filter('excerpt_more','new_excerpt_more',109);
function new_excerpt_more($more){
  return '...';
}

function get_excerpt($length = false,$content = false,$more = ' ...'){
  global $post;
  if(!$length) $length = custom_excerpt_length($length);
  if(!$content) {
    if($post->post_excerpt) $content = $post->post_excerpt;
    else $content = $post->post_content;
  }
  $content = strip_tags($content);
  $content = mb_substr($content,0,$length).$more;
  //$content = apply_filters('the_excerpt',$content);
  return $content;
}
