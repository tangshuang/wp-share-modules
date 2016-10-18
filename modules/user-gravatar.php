<?php

/**
 * Name: 解决gravatar被墙
 * Description: 启用之后gravatar头像都可以更快加载出来
 */

add_filter('get_avatar', 'get_ssl_gravatar',10,5);
function get_ssl_gravatar($avatar , $id_or_email , $size = '60'  , $default , $alt = false) {
  $avatar = preg_replace('/.*\/avatar\/(.*)\?s=([\d]+)&.*/','<img src="https://secure.gravatar.com/avatar/$1?s=$2" class="avatar avatar-$2" height="'.$size.'" width="'.$size.'" alt="'.$alt.'">',$avatar);
  return $avatar;
}
