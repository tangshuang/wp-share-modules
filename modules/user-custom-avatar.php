<?php

/**
 * Name: 用户自定义头像
 * Description: 使用用户填写的avatar字段的url作为头像src
 */

class UserCustomAvatar {
  function __construct(){
    add_filter('get_avatar',array($this,'get_avatar'),99,5);
    add_filter('user_contactmethods',array($this,'user_profile'),10,1);
  }
  function get_avatar($avatar,$id_or_email,$size = '60', $default , $alt = false) {
    global $wpdb;
    $image = null;
    if(is_numeric($id_or_email)) {
      $image = get_user_meta($id_or_email,'avatar',true);
    }
    elseif(is_string($id_or_email)) {
      $user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM wp_users WHERE user_email=%s",$id_or_email));
      $image = get_user_meta($user_id,'avatar',true);
    }
    if($image) {
      $avatar = '<img src="'.$image.'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" class="avatar" />';
    }
    return $avatar;
  }
  function user_profile($contactmethods) {
    $contactmethods['avatar'] = '头像图片URL';
    return $contactmethods;
  }
}
$UserCustomAvatar = new UserCustomAvatar();