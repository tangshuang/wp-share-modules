<?php

/**
 * Name: 移除不用的用户信息
 * Description: 移除的用户信息有first_name, last_name, aim, yim, jabber
 */

// 用户信息
function remove_user_metas($contactmethods) {
  unset($contactmethods['first_name']);
  unset($contactmethods['last_name']);
  unset($contactmethods['aim']);
  unset($contactmethods['yim']);
  unset($contactmethods['jabber']);
  return $contactmethods;
}
add_filter('user_contactmethods','remove_user_metas',10,1);

// 修复系统问题
function remove_user_metas_js() {
  global $wp_version,$hook_suffix;
  if(in_array($hook_suffix,array('profile.php','user-edit.php'))) : ?>
  <script type="text/javascript">
  jQuery(function($){
    $('#rich_editing,#comment_shortcuts').parent().parent().parent().remove();
    $('#last_name').parent().parent().remove();
    $('#first_name').parent().parent().remove();
  });
  </script>
  <?php
  endif;
}
add_action('admin_print_footer_scripts','remove_user_metas_js');
