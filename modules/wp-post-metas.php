<?php

/**
 * Name: 文章附件字段
 * Description: 启用后可以利用自定义域为文章新增字段，使用前需要编辑get_post_metas_box_arr
 */

/**
 * 设置本系统要使用哪些附加字段
 * 设置的array字段包括：
 * type = text|textarea|checkbox|radio|select等，key:数据库中保存的字段名，title:字段项的标题，description:字段项的提示信息，options:checkbox、radio、select的选项，选项由子数组组成，得有value和text字段，value是选项的值，text是提示文本
 * @return array
 */
function get_post_metas_box_arr() {
    return array(
        //array('type' => 'text','key' => '_seo_title','title' => 'SEO标题'),
        //array('type' => 'textarea','key' => '_seo_description','title' => 'SEO描述'),
        /*array('type' => 'radio','key' => '_seo_type','title' => 'SEO形式','options' => array(
            array('value' => 1,'text' => '形式1'),
            array('value' => 2,'text' => '形式2')
        )),
        array('type' => 'checkbox','key' => '_not_in_category_list','title' => '不在分类列表中显示','description' => '勾选后不显示在分类列表中','value' => '1','text' => '不显示'),
        array('type' => 'checkboxes','key' => '_hot_tags','title' => '热词关联','description' => '勾选后，该文章会显示在相关热词区域','options' => array(
            array('value' => 'mysql','text' => 'MySQL'),
            array('value' => 'git','text' => 'GIT')
        )),
        array('type' => 'select','key' => '_show_in_focus','title' => '首页焦点','description' => '将这篇文章加到首页焦点区域内','options' => array(
            array('value' => 0,'text' => '不'),
            array('value' => 1,'text' => '头条新闻，x6'),
            array('value' => 2,'text' => '右侧焦点，x3'),
            array('value' => 3,'text' => '焦点列表，x2'),
        )),*/
        // array('type' => 'text','key' => '_buy_link','title' => '商品链接','description' => '商品的推广链接'),
        // array('type' => 'text','key' => '_buy_shop','title' => '店铺链接','description' => '店铺的推广链接'),
        // array('type' => 'number','key' => '_buy_price','title' => '价格','description' => '填写整数，单位RMB','step' => '0.01'),
        // array('type' => 'textarea','key' => '_buy_text','title' => '推荐','description' => 'HTML代码，里面的链接会自动转换')
    );
}

if(empty(get_post_metas_box_arr())) return; // 如果没有提供选项，直接就不执行下面的

// 添加后台界面meta_box
add_action('add_meta_boxes','add_post_metas_box_init');
function add_post_metas_box_init(){
    add_meta_box(
        'post-metas',
        '附加选项',
        'add_post_metas_box',
        'post',
        'side',
        'high'
    );
}
function add_post_metas_box($post){
    $metas = get_post_metas_box_arr();
    $tags = '';
    $fields = '';
    foreach($metas as $meta) {
        $value = get_post_meta($post->ID,$meta['key'],true);

        if(!$value) {
            $json = str_replace('"','【double quote】',json_encode($meta));
            $tags .= '<a href="javascript:void(0)" class="post-meta-tag" data="'.$json.'">'.$meta['title'].'</a>';
            continue;
        }

        if($meta['type'] == 'text') {
            $fields .= '<p><label>';
            $fields .= '<strong>'.$meta['title'].'</strong><br>';
            $fields .= '<input type="text" class="regular-text" style="max-width:90%;" name="post_meta['.$meta['key'].']" value="'.$value.'">';
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</label></p>';
        }
        elseif($meta['type'] == 'number') {
            $fields .= '<p><label>';
            $fields .= '<strong>'.$meta['title'].'</strong><br>';
            $fields .= '<input type="number" class="regular-text" style="max-width:90%;" name="post_meta['.$meta['key'].']" value="'.$value.'"'.(isset($meta['step']) ? ' step="'.$meta['step'].'"' : '').'>';
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</label></p>';
        }
        elseif($meta['type'] == 'textarea') {
            $fields .= '<p><label>';
            $fields .= '<strong>'.$meta['title'].'</strong><br>';
            $fields .= '<textarea class="large-text" style="max-width:90%;" name="post_meta['.$meta['key'].']">'.$value.'</textarea>';
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</label></p>';
        }
        elseif($meta['type'] == 'checkbox') {
            $fields .= '<p><label>';
            $fields .= '<strong>'.$meta['title'].'</strong><br>';
            $fields .= '<input type="checkbox" name="post_meta['.$meta['key'].']" value="'.$meta['value'].'" '.checked($value,$meta['value'],false).'> '.$meta['text'];
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</label></p>';
        }
        elseif($meta['type'] == 'checkboxes') {
            $fields .= '<p>';
            $fields .= '<strong>'.$meta['title'].'</strong><br>';
            foreach($meta['options'] as $option)
                $fields .= '<label><input type="checkbox" name="post_meta['.$meta['key'].'][]" value="'.$option['value'].'"'.(in_array($option['value'],$value) ? ' checked' : '').'> '.$option['text'].'</label>';
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</p>';
        }
        elseif($meta['type'] == 'radio') {
            $fields .= '<p>';
            $fields .= '<strong>'.$meta['title'].'</strong><br>';
            foreach($meta['options'] as $option)
                $fields .= '<label><input type="radio" name="post_meta['.$meta['key'].']" value="'.$option['value'].'" '.checked($value,$option['value'],false).'> '.$option['text'].'</label>';
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</p>';
        }
        elseif($meta['type'] == 'select') {
            $fields .= '<p>';
            $fields .= '<strong>'.$meta['title'].'</strong> ';
            $fields .= '<select name="post_meta['.$meta['key'].']">';
            foreach($meta['options'] as $option)
                $fields .= '<option value="'.$option['value'].'" '.selected($value,$option['value']).'>'.$option['text'].'</option>';
            $fields .= '</select>';
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</p>';
        }
        else {
            $fields .= '<p><label>';
            $fields .= '<strong>'.$meta['title'].'</strong><br>';
            $fields .= '<input type="'.$meta['type'].'" class="regular-text" style="max-width:90%;" name="post_meta['.$meta['key'].']" value="'.$value.'">';
            if(isset($meta['description']))
                $fields .= '<br><small>'.$meta['description'].'</small>';
            $fields .= '</label></p>';
        }
    }

    echo '<div class="post-metas-tags">'.$tags.'</div>';
    echo '<div class="post-metas-fields">'.$fields.'</div>';
}

add_action('admin_print_footer_scripts','add_post_metas_scripts');
function add_post_metas_scripts() {
    ?>
    <script>
        jQuery(function($){
            $('#post-metas .post-metas-tags a').on('click',function(e){
                e.preventDefault();
                var $this = $(this),
                    data = $this.attr('data'),
                    html = '';
                // 解析为json对象
                data = data.replace(/【double quote】/g,'"');
                data = JSON.parse(data);
                var type = data.type,
                    title = data.title,
                    key = data.key,
                    description = data.description;

                console.log(data);

                // 开始构建
                if(type == 'text') {
                    html += '<p>';
                    html += '<strong>' + title + '</strong><br>';
                    html += '<input type="text" class="regular-text" style="max-width:90%;" name="post_meta[' + key + ']">';
                    if(description !== undefined)
                        html += '<br><small>' + description + '</small>';
                    html += '</p>';
                }
                else if(type == 'textarea') {
                    html += '<p>';
                    html += '<strong>' + title + '</strong><br>';
                    html += '<textarea class="large-text" style="max-width:90%;" name="post_meta[' + key + ']"></textarea>';
                    if(description !== undefined)
                        html += '<br><small>' + description + '</small>';
                    html += '</p>';
                }
                else if(type == 'checkbox') {
                    html += '<p><label>';
                    html += '<strong>' + title + '</strong><br>';
                    html += '<input type="checkbox" name="post_meta[' + key + ']" value="' + data.value + '"> ' + data.text;
                    if(description !== undefined)
                        html += '<br><small>' + description + '</small>';
                    html += '</label></p>';
                }
                else if(type == 'checkboxes') {
                    options = data.options;
                    html += '<p>';
                    html += '<strong>' + title + '</strong><br>';
                    for(i = 0;i < options.length;i ++) {
                        option = options[i];
                        html += '<label><input type="checkbox" name="post_meta[' + key + '][]" value="' + option.value + '"> ' + option.text + '</label>';
                    }
                    if(description !== undefined)
                        html += '<br><small>' + description + '</small>';
                    html += '</p>';
                }
                else if(type == 'radio') {
                    options = data.options;
                    html += '<p>';
                    html += '<strong>' + title + '</strong><br>';
                    for(i = 0;i < options.length;i ++) {
                        option = options[i];
                        html += '<label><input type="radio" name="post_meta[' + key + ']" value="' + option.value + '"> ' + option.text + '</label>';
                    }
                    if(description !== undefined)
                        html += '<br><small>' + description + '</small>';
                    html += '</p>';
                }
                else if(type == 'select') {
                    options = data.options;
                    html += '<p>';
                    html += '<strong>' + title + '</strong> ';
                    html += '<select name="post_meta[' + key + ']">';
                    for(i = 0;i < options.length;i ++) {
                        option = options[i];
                        html += '<option value="' + option.value + '">' + option.text + '</option>';
                    }
                    html += '</select>';
                    if(description !== undefined)
                        html += '<br><small>' + description + '</small>';
                    html += '</p>';
                }
                else {
                    html += '<p>';
                    html += '<strong>' + title + '</strong><br>';
                    html += '<input type="' + type + '" class="regular-text" style="max-width:90%;" name="post_meta[' + key + ']">';
                    if(description !== undefined)
                        html += '<br><small>' + description + '</small>';
                    html += '</p>';
                }

                $('#post-metas .post-metas-fields').append(html);
                $this.remove();
            });
        });
    </script>
<?php
}

add_action('admin_print_styles','add_post_metas_styles');
function add_post_metas_styles() {
    ?>
    <style>
        .post-metas-tags a {padding: 3px 5px;background: #eee;border-radius: 2px;text-decoration: none;display:inline-block;white-space:nowrap;margin-right: 5px;}
        .post-metas-fields strong {font-weight: bold !important;}
    </style>
<?php
}

// 保存填写的meta信息
add_action('save_post','add_post_metas_box_save');
function add_post_metas_box_save($post_id){
    $metas = get_post_metas_box_arr();
    if(isset($_POST['post_meta']) && !empty($_POST['post_meta'])) {
        $data = $_POST['post_meta'];
        if(is_array($data) && !empty($data)) {
            foreach($metas as $meta) {
                $key = $meta['key'];
                if(isset($data[$key]) && !empty($data[$key])) {
                    $value = $data[$key];
                    if(is_string($value))
                        $value = trim($value);
                    if($value)
                        update_post_meta($post_id,$key,$value);
                    else
                        delete_post_meta($post_id,$key);
                }
                else {
                    delete_post_meta($post_id,$key);
                }
            }
        }
    }
}

// 添加meta短代码
add_shortcode('meta','add_meta_shortcode_in_post');
function add_meta_shortcode_in_post($atts){
    extract(shortcode_atts(array(
        'key' => ''
    ),$atts));
    global $post;
    $value = get_post_meta($post->ID,$key,true);
    return $value;
}