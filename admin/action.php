<?php

add_action('admin_init','share_modules_add_admin_action');
function share_modules_add_admin_action() {
	$file_name = substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);
	if(current_user_can('edit_theme_options') && $file_name === 'plugins.php' && isset($_GET['page']) && @$_GET['page'] === 'share_modules' && isset($_POST['action']) && @$_POST['action'] == 'wp_share_modules') {
		$modules = @$_POST['wp_share_modules'];
		if($modules) {
			$modules = array_values(array_filter($modules));
			update_option('wp_share_modules',$modules);
		}

		wp_redirect(add_query_arg('time',time()));
		exit();
	}
}