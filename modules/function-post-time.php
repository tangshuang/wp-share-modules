<?php

/**
 * Name: 倒计时型的文章时间
 * Description: 你可以使用the_post_time()输出类似“25分钟前”“1天前”这样的时间
 */

function the_post_time() {
    global $post;
    date_default_timezone_set('PRC');
    $current_time = time();
    $post_time = strtotime($post->post_date);
    $time = $current_time - $post_time;
    if($time < 3600) {
        $time = $time/60;
        echo (int)$time.'分钟前';
    }
    elseif($time < 3600*24) {
        $time = $time/3600;
        echo (int)$time.'小时前';
    }
    elseif($time < 3600*24*7) {
        $time = $time/(3600*24);
        echo (int)$time.'天前';
    }
    else {
        $time = date('Y-m-d H:i',$post_time);
        echo $time;
    }
}