<?php

/**
 * Name: 手机访问使用手机主题
 * Description: 如果你创建了一个专门为手机准备的<strong><u>子主题</u></strong>，比如说theme-mobile， 这个模块让你的网站在PC访问时使用theme主题，手机访问时使用theme-mobile主题
 */

require_once('MobileDetect.class.php');

//根据访问设备切换 WordPress 主题
function DeviceThemeExtends($theme){
    $detect = new MobileDetect();

    if($detect->isMobile())
        $theme .= '-mobile';

    return $theme;
}
//add_filter('template','DeviceThemeExtends' );
add_filter('stylesheet','DeviceThemeExtends' );