# WP SHARE MODULES

A wordpress plugin collecting useful functions of wordress in a plugin.
[Plugin](http://wordpress.org/plugins/wp-share-modules)
[Doc](http://www.tangshuang.net/wp-share-modules)

# Modules

Now, we have 16 modules. Every time I update the pulgin, I will add the new modules' docs here.

## 文章列表扩展Widget

启用后，可以在后台找到一个“文章推荐（选项丰富）”的小工具。

## 手机访问使用手机主题

如果你创建了一个专门为手机准备的<strong><u>子主题</u></strong>，比如说theme-mobile， 这个模块让你的网站在PC访问时使用theme主题，手机访问时使用theme-mobile主题。

注意，theme-mobile必须是theme的[子主题](https://codex.wordpress.org/zh-cn:%E5%AD%90%E4%B8%BB%E9%A2%98)。

## 浏览器缓存页面

在主题中使用wp_http_response_cache()函数可以实现将网页缓存在浏览器，下次打开时秒开，默认缓存15分钟。

例如，在single.php的get_header()之前加入

```
wp_http_response_cache('+30 minutes');
```

就可以缓存这个页面在浏览器客户端。用户按Ctrl+F5可以强制刷新。函数的参数和strtotime函数的参数差不多。

## 翻页导航

翻页函数，用在LOOP循环，wp_reset_query();之前使用the_pagenavi()函数输出一个翻页导航。

```
query_posts($arg);
while() {
	// ...
}
the_pagenavi();
wp_reset_query();
```

有两个参数($before,$after)，可以用html，表示在导航前后输出什么html代码。

## 获取文章缩略图

可以在主题中使用get_post_thumb_src, the_post_thumb_src, get_post_thumb, the_post_thumb几个函数来获取文章的缩略图。带_src的是返回和输出图片的地址，没有的则是输出img标签。

```
<img src="<?php echo get_post_thumb_src('full'); ?>" class="thumbnail">
```

函数首先会去找文章的“特色图片”，如果没有设置特色图片，会找meta_key为“thumbnail_src”的自定义域的值作为图片的地址，如果也没有，则会找到文章中的第一张图片作为缩略图。

## 倒计时型的文章时间

你可以使用the_post_time()代替the_time()输出类似“25分钟前”“1天前”这样的时间。

## 邮件回复评论

启用之后，上一条评论被回复时，它的作者会被邮件通知。

## 用户自定义头像

使用用户填写的avatar字段的url作为头像src。启用之后，在后台“用户-编辑我的资料”中可以找到一个“头像图片URL”选项，填写图片的url即可。

## 解决gravatar被墙

启用之后gravatar头像都可以更快加载出来。

## 移除不用的用户信息

移除的用户信息有first_name, last_name, aim, yim, jabber。

## 文章摘要修复

修复文章摘要的字数、结尾符号，增加get_excerpt()函数。
现在返回的文章摘要字数为120个字，符合为“……”，你可以通过修改源码自己修改。

## 文章索引

在主题中可以通过the_post_index()输出文章h2,h3的标题层次结构索引。必须在写文章的时候使用h2作为大标题，h3作为小标题。

## 文章附件字段

启用后可以利用自定义域为文章新增字段，使用前需要编辑get_post_metas_box_arr。
在撰写文章的时候，你还可以使用[meta key="your_key"]来调用任何填写的自定义域（不局限于get_post_metas_box_arr中配置的）。
get_post_metas_box_arr中配置的项，应该按照源文件里面的说明进行修改。

## 文章浏览次数

开启后可以统计文章浏览的次数，并且你可以在主题中使用get_post_view_count等函数获取。

## 移除头部多余信息

本模块移除的包括feed, generator, wlwmanifest_link, rsd_link, version, dns-prefetch。

## 清除头部信息（高级版）

本模块可以清除wp-json, moji, embed。