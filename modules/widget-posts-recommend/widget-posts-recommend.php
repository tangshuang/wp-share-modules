<?php

/**
 * Name: 文章列表扩展Widget
 * Description: 启用后，可以在后台找到一个“文章推荐（选项丰富）”的小工具
 */

class PostsRecommend extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname'=>'posts-recommend','description'=>'不同的选项帮助你推荐文章');
        $control_ops = array('width'=>250,'height'=>300);
        parent::__construct(false,'文章推荐（选项丰富）',$widget_ops,$control_ops);
    }
    function form($instance) {
        $instance = wp_parse_args((array)$instance,array('title'=>'文章推荐','cat'=>'','tag'=>'','count'=>5,'type'=>0,'format'=>0));
        $title = htmlspecialchars($instance['title']);
        echo '<p style="text-align:left;"><label>标题：<input name="'.$this->get_field_name('title').'" type="text" size="5" value="'.$title.'" class="widefat" /></label></p>';
        echo '<p style="text-align:left;"><label>分类：<input name="'.$this->get_field_name('cat').'" type="text" size="10" value="'.$instance['cat'].'"></label></p>';
        echo '<p style="text-align:left;color:#999;">填写分类的ID，如果想要推荐多个分类的文章，使用英文逗号隔开，如“1,2,3”，如果排除某一个分类，使用减号，如“1,-2,3,-4”，不填则是所有分类</p>';
        echo '<p style="text-align:left;"><label>标签：<input name="'.$this->get_field_name('tag').'" type="text" size="10" value="'.$instance['tag'].'"></label></p>';
        echo '<p style="text-align:left;color:#999;">如果填写了标签，就只显示这些标签下的文章，标签之间用英文逗号隔开，例如“标签一,标签二,标签三”，如果你想只显示同时包含这三个标签的话，格式为“标签一+标签二+标签三”，不填则跟标签无关</p>';
        echo '<p><label>类型：<select name="'.$this->get_field_name('type').'">
        <option value="0" '.selected($instance['type'],0,false).'>时间最新</option>
        <option value="1" '.selected($instance['type'],1,false).'>评论最热</option>
        <option value="2" '.selected($instance['type'],2,false).'>阅读最多</option>
        <option value="3" '.selected($instance['type'],3,false).'>置顶推荐</option>
        <option value="4" '.selected($instance['type'],4,false).'>随机排序</option>
        <option value="5" '.selected($instance['type'],5,false).'>最近更新</option>
        <option value="6" '.selected($instance['type'],6,false).'>搜索引擎</option>
        <option value="7" '.selected($instance['type'],7,false).'>浏览历史</option>
        <option value="8" '.selected($instance['type'],8,false).'>最受喜欢</option>
      </select></label></p>';
        echo '<p style="text-align:left;"><label>条数：<input name="'.$this->get_field_name('count').'" type="text" size="5" value="'.$instance['count'].'" /></label></p>';
        echo '<p style="text-align:left;"><label>格式：<select name="'.$this->get_field_name('format').'">
        <option value="0" '.selected($instance['format'],0,false).'>文本列表</option>
        <option value="1" '.selected($instance['format'],1,false).'>图文混排</option>
      </select></label></p>';
        echo '<p style="text-align:left;color:#999;">如果选择图文混排，只有上传了特色图片的文章才会出现在列表中。</p>';
        echo '<p>注意，如果最后结果没有返回数据的话，检查是不是你的条件太苛刻导致。</p>';
    }
    function update($new_instance,$old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['cat'] = strip_tags(stripslashes($new_instance['cat']));
        $instance['tag'] = strip_tags(stripslashes($new_instance['tag']));
        $instance['type'] = $new_instance['type'];
        $instance['format'] = $new_instance['format'];
        $instance['count'] = strip_tags(stripslashes($new_instance['count']));
        return $instance;
    }
    function widget($args,$instance) {
        $query = array(
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $instance['count'],
            'post_status' => 'publish',
            'post_type' => 'post'
        );
        if(trim($instance['cat']) != '')$query['cat'] = $instance['cat'];
        if(trim($instance['tag']) != '')$query['tag'] = $instance['tag'];
        $type = $instance['type'];
        if($type == 1) {
            $query['orderby'] = 'comment_count';
            $query['order'] = 'DESC';
        }
        elseif($type == 2) {
            $query['meta_query'] = array(
                array(
                    'key' => 'views'
                )
            );
            $query['meta_key'] = 'views';
            $query['orderby'] = 'meta_value_num';
            $query['order'] = 'DESC';
        }
        elseif($type == 3) {
            $sticky_posts = get_option('sticky_posts');
            if(!empty($sticky_posts)) {
                $query['post__in'] = $sticky_posts;
            }
            else {
                return;
            }
        }
        elseif($type == 4) {
            $query['orderby'] = 'rand';
        }
        elseif($type == 5) {
            global $wpdb;
            date_default_timezone_set('Asia/Shanghai');
            $lastday = date('Y-m-d H:i:s',strtotime('-2 days'));
            $modified_posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_date!=post_modified AND post_data<'$lastday'");
            if(!empty($modified_posts)) {
                $query['post__in'] = $modified_posts;
                $query['orderby'] = 'modified';
                $query['order'] = 'DESC';
            }
            else {
                return;
            }
        }
        elseif($type == 6) {
            $search = $this->get_search_src_keywords();
            if($search){
                $query['s'] =  $search;
                $query['orderby'] = 'rand';
            }
            else {
                return;
            }
        }
        elseif($type == 7) {
            if(isset($_COOKIE['has_post_read'.COOKIEHASH]) && !empty($_COOKIE['has_post_read'.COOKIEHASH])) {
                $read_posts = $_COOKIE['has_post_read'.COOKIEHASH];
                $read_posts = explode(',',$read_posts);
                $query['post__in'] = array_unique(array_filter($read_posts));
            }
            else {
                return;
            }
        }
        elseif($type == 8) {
            $query['meta_query'] = array(
                array(
                    'key' => '_likes_amount'
                )
            );
            $query['meta_key'] = '_likes_amount';
            $query['orderby'] = 'meta_value';
            $query['order'] = 'DESC';
        }
        if($instance['format'] == 1) {
            if(!isset($query['meta_query'])) $query['meta_query'] = array();
            $query['meta_query'][] = array(
                'key' => '_thumbnail_id',
            );
        }
        query_posts($query);
        // 下面开始打印
        if(have_posts()) :
            extract($args);
            $title = apply_filters('widget_title',$instance['title']);
            echo $before_widget;
            if($title) echo $before_title . $title . $after_title;
            $has_thumbnail = $instance['format'] == 1;
            if($instance['format'] == 1) {
                include(dirname(__FILE__).'/template-thumbnail.php');
            }
            else {
                include(dirname(__FILE__).'/template-list.php');
            }
            echo $after_widget;
        endif;
        wp_reset_query();
    }
    // 下面两个函数获取来自搜索引擎的关键词
    function get_search_src_keywords () {
        if(!isset($_SERVER['HTTP_REFERER'])) return null;
        $url = $_SERVER['HTTP_REFERER'];
        $search_1="google.com"; //q= utf8
        $search_2="baidu.com"; //wd= gbk
        $search_3="yahoo.cn"; //q= utf8
        $search_4="sogou.com"; //query= gbk
        $search_5="soso.com"; //w= gbk
        $search_6="bing.com"; //q= utf8
        $search_7="youdao.com"; //q= utf8
        $search_8="so.com"; //q= utf8

        $google = preg_match("/\b{$search_1}\b/",$url);//记录匹配情况，用于入站判断。
        $baidu = preg_match("/\b{$search_2}\b/",$url);
        $yahoo = preg_match("/\b{$search_3}\b/",$url);
        $sogou = preg_match("/\b{$search_4}\b/",$url);
        $soso = preg_match("/\b{$search_5}\b/",$url);
        $bing = preg_match("/\b{$search_6}\b/",$url);
        $youdao = preg_match("/\b{$search_7}\b/",$url);
        $_360 = preg_match("/\b{$search_8}\b/",$url);
        $s_s_keyword = '';
        if($google || $yahoo || $bing || $youdao || $_360) {
            //来自google
            $s_s_keyword = $this->get_keyword($url,'q=');//关键词前的字符为“q=”。
            $s_s_keyword = urldecode($s_s_keyword);
        }
        elseif($baidu) {
            //来自百度
            $s_s_keyword = $this->get_keyword($url,'wd=');//关键词前的字符为“wd=”。
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK","UTF-8",$s_s_keyword);//引擎为gbk
        }
        elseif($sogou) {
            $s_s_keyword = $this->get_keyword($url,'query=');
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK","UTF-8",$s_s_keyword);
        }
        elseif($soso) {
            $s_s_keyword = $this->get_keyword($url,'w=');
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK","UTF-8",$s_s_keyword);
        }

        return $s_s_keyword;
    }
    // 函数作用：从url中提取关键词。参数说明：url及关键词前的字符。
    function get_keyword($url,$kw_start) {
        $start = stripos($url,$kw_start);
        $url = substr($url,$start+strlen($kw_start));
        $start = stripos($url,'&');
        $s_s_keyword = '';
        if($start>0) {
            $start=stripos($url,'&');
            $s_s_keyword = substr($url,0,$start);
        }
        else {
            $s_s_keyword=substr($url,0);
        }
        return $s_s_keyword;
    }
}

add_action('widgets_init','PostsRecommendInit');
function PostsRecommendInit(){
    register_widget('PostsRecommend');
}

// 记录历史阅读数据
add_action('wp','set_cookie_of_post_read');
function set_cookie_of_post_read() {
    if(is_single()){
        global $post;
        $post_id = $post->ID;
        if(isset($_COOKIE['has_post_read'.COOKIEHASH]) && !empty($_COOKIE['has_post_read'.COOKIEHASH])) {
            $read_posts = trim($_COOKIE['has_post_read'.COOKIEHASH]);
            $read_posts = explode(',',$read_posts);
        }
        else {
            $read_posts = array();
        }
        if(!in_array($post_id,$read_posts)) {
            $read_posts[] = $post_id;
        }
        $read_posts = array_unique(array_filter($read_posts));
        $read_posts = implode(',',$read_posts);
        setcookie('has_post_read'.COOKIEHASH,$read_posts,time()+315360000,COOKIEPATH,COOKIE_DOMAIN,false);
    }
}