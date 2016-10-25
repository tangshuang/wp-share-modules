<?php

add_action('admin_init','share_modules_add_admin_update_fetch');
function share_modules_add_admin_update_fetch() {
	$file_name = substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);
	if(current_user_can('edit_theme_options') && $file_name === 'plugins.php' && isset($_GET['page']) && @$_GET['page'] === 'share_modules' && isset($_GET['action']) && @$_GET['action'] == 'check_update') {

		$url = 'https://github.com/tangshuang/wp-share-modules/blob/master/wp-share-modules.php';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch,CURLOPT_REFERER,'https://github.com/tangshuang/wp-share-modules');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$content = curl_exec($ch);
		curl_close($ch);

		$result = array(
			'latest' => 0,
		);
		if($content) {
			preg_match('/\/\*(.*?)\*\//s',$content,$matches);
			if(isset($matches[1])) {
				$content = $matches[1];
				if($content) {
					preg_match('/Version:(.*?)\n/s',$content,$matches);
					if(isset($matches[1])) {
						require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
						$plugin_data = get_plugin_data(SHARE_MODULES_PLUGIN_NAME);
						$result = array(
							'latest' => strip_tags(trim($matches[1])),
							'version' => $plugin_data['Version']
						);
					}
				}
			}
		}

		header('Content-Type: application/json');
		echo json_encode($result);

		exit();
	}
}

add_action('admin_print_footer_scripts','share_modules_add_update_script');
function share_modules_add_update_script() {
	$file_name = substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);
	if(!(current_user_can('edit_theme_options') && $file_name === 'plugins.php' && isset($_GET['page']) && @$_GET['page'] === 'share_modules')) return;
?>
<style>
#wp-share-modules-dialog {
	position: fixed;
	top: 0;
	left: 0;
	width:100%;
	height:100%;
	z-index: 999;
}
#wp-share-modules-dialog-bg {
	position: absolute;
	top: 0;
	left: 0;
	width:100%;
	height:100%;
	background:#000;
	opacity:.8
}
#wp-share-modules-dialog-container {
	position: absolute;
	top: 50%;
	left: 50%;

	width: 400px;
	height: 200px;

	margin-top: -100px;
	margin-left: -200px;

	background: #fff;
}
#wp-share-modules-dialog-close {
	float: right;
	margin-top: 5px;
	margin-right: 10px;
	text-decoration: none;
	color: #333;
	font-size: 22px;
}
#wp-share-modules-dialog-inner {
	margin: 20px;
	font-size: 16px;
	line-height: 1.8;
}
</style>
<script>
jQuery(function($){
	$.get('<?php echo admin_url('plugins.php?page=share_modules&action=check_update'); ?>').done(function(data){
		console.log(data)
		if(data && data.latest) {
			var latest = data.latest;
			var version = data.version;
			if(shareModulesCompareVersion(version,latest) == -1) {
				shareModulesShowNotice('Share Modules有了新的更新，当前你安装的版本是' + version + '，而最新版本是' + latest + '。点击<a href="https://github.com/tangshuang/wp-share-modules" target="_blank">这里</a>去查看变化和下载安装最新版本。');
			}
		}
	});
	
	function shareModulesCompareVersion(a,b) { // a > b返回1，a < b返回-1，相等返回0
		function toNum(version){
			version = version.split(/\D/);
			for(var i = 0,len = version.length;i < len;i ++) {
				var item = version[i];
				version[i] = parseInt(item);
			}
			return version;
		}
		a = toNum(a);
		b = toNum(b);
		var len = Math.max(a.length,b.length);
		for(var i = 0;i < len;i ++) {
			if(!a[i]) a[i] = 0;
			if(!b[i]) b[i] = 0;
			if(a[i] < b[i]) {
				return -1;
			}
			else if(a[i] > b[i]) {
				return 1;
			}
		}
		return 0;
	}

	function shareModulesShowNotice(msg) {
		var $container = $('#wpcontent');
		var $dialog = $('<div id="wp-share-modules-dialog">\
			<div id="wp-share-modules-dialog-bg"></div>\
			<div id="wp-share-modules-dialog-container">\
				<a href="javascript:void(0)" id="wp-share-modules-dialog-close">&times;</a>\
				<div id="wp-share-modules-dialog-inner"></div>\
			</div>\
		</div>');

		$dialog.find('#wp-share-modules-dialog-inner').html(msg);
		$container.append($dialog);

		$dialog.on('click','#wp-share-modules-dialog-bg,#wp-share-modules-dialog-close',function(){
			$dialog.remove();
		});
	}
});
</script>
<?php
}