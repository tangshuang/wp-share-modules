<?php

add_action('admin_menu','share_modules_add_admin_menu');
function share_modules_add_admin_menu() {
  add_plugins_page('SHARE MODULES','SHARE MODULES','edit_theme_options','share_modules','share_modules_admin_menu_page');
}
function share_modules_admin_menu_page() {
  include(SHARE_MODULES_PLUGIN_DIR.'/admin/view.php');
}