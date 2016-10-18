<?php

/*
Plugin Name: WP SHARE MODULES
Plugin URI: http://www.tangshuang.net/wp-share-modules
Description: 本插件为你提供丰富的WordPress插件、主题开发工具包，减少你的开发量。
Version: 0.0.1
Author: 否子戈
Author URI: http://www.tangshuang.net
*/

define('SHARE_MODULES_PLUGIN_NAME',__FILE__);
define('SHARE_MODULES_PLUGIN_DIR',dirname(SHARE_MODULES_PLUGIN_NAME));
define('SHARE_MODULES_PLUGIN_URL',plugins_url('',SHARE_MODULES_PLUGIN_NAME));

function shmd_scandir($dir) {
  if(function_exists('scandir')) {
    return scandir($dir);
  }
  else {
    $handle = @opendir($dir);
    $arr = array();
    while(($arr[] = @readdir($handle)) !== false) {}
    @closedir($handle);
    $arr = array_filter($arr);
    return $arr;
  }
}

function _shmd_get_info_from_file($file) {
	if(!file_exists($file)) {
        return false;
    }
    $module = basename($file,'.php');
    $info = array(
    	'id' => $module,
		'name' => $module,
		'description' => '',
		'file' => $file,
		'is_active' => 0
	);
	$content = _shmd_get_file_content($file);
	if($content) {
		preg_match('/\/\*\*(.*?)\*\//s',$content,$matches);
		if(isset($matches[1])) {
			$content = $matches[1];
			if($content) {
				preg_match('/Name:(.*?)\n/s',$content,$names);
				if(isset($names[1])) {
					$info['name'] = $names[1];
				}
				preg_match('/Description:(.*?)\n/s',$content,$descriptions);
				if(isset($descriptions[1])) {
					$info['description'] = $descriptions[1];
				}
			}
		}
	}
	
	return $info;
}

function _shmd_get_file_content($file) {
	if(!file_exists($file)) {
        return false;
    }
    $handle = fopen($file,'rb');
    $content = '';
    while(!feof($handle)){
        $content .= fread($handle, 1024*8);
    }
    fclose($handle);
    return $content;
}

global $wp_share_modules;
$wp_share_modules = array();
$exist_modules = get_option('wp_share_modules');
$exist_modules = is_array($exist_modules) && !empty($exist_modules) ? $exist_modules : array();

$modules_dir = SHARE_MODULES_PLUGIN_DIR.'/modules';
if(is_dir($modules_dir)) {
	$files = shmd_scandir($modules_dir);
	if(is_array($files) && !empty($files)) foreach($files as $file) {
		if(substr($file,-4) == '.php') {
			$module = basename($file,'.php');
			$module_file = $modules_dir.'/'.$file;
			$wp_share_modules[$module] = _shmd_get_info_from_file($module_file);
			if(in_array($module,$exist_modules)) {
				include_once($module_file);
				$wp_share_modules[$module]['is_active'] = 1;
			}
		}
		else if(is_dir($modules_dir.'/'.$file) && file_exists($modules_dir.'/'.$file.'/'.$file.'.php')) {
			$module = $file;
			$module_file = $modules_dir.'/'.$file.'/'.$file.'.php';
			$wp_share_modules[$module] = _shmd_get_info_from_file($module_file);
			if(in_array($module,$exist_modules)) {
				include_once($module_file);
				$wp_share_modules[$module]['is_active'] = 1;
			}
		}
	}
}

require_once(SHARE_MODULES_PLUGIN_DIR.'/admin/action.php');
require_once(SHARE_MODULES_PLUGIN_DIR.'/admin/menu.php');
