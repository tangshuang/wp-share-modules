<?php

/**
 * Name: 浏览器缓存页面
 * Description: 在主题中使用wp_http_response_cache()函数可以实现将网页缓存在浏览器，下次打开时秒开，默认缓存15分钟
 */

function wp_http_response_cache($expire = '+15 minutes') {
    date_default_timezone_set('Etc/GMT');
    header("Cache-Control: public");
    header("Pragma: cache");
    // 如果存在缓存，则使用缓存
    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $last_modified = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        $expire = strtotime(trim("$last_modified $expire"));
        if($expire > time()) {
            header("Expires: ".gmdate("D, d M Y H:i:s",$expire)." GMT");
            header("Last-Modified: $last_modified",true,304);
            exit;
        }
    }
    // 如果不存在缓存，则增加上次更新时间，从而加入缓存
    header("Expires: ".gmdate("D, d M Y H:i:s",strtotime($expire))." GMT");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    date_default_timezone_set('PRC');
}
