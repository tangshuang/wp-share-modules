<?php

/**
 * Name: 邮件回复评论
 * Description: 启用之后，上一条评论被回复时，它的作者会被邮件通知。
 */


function mail_to_comment($comment_id) {
	
	include_once(ABSPATH.'/wp-includes/pluggable.php');

	$comment = get_comment($comment_id);
	$comment_author = $comment->comment_author;
	$comment_author_url = $comment->comment_author_url ? '（Url：'.$comment->comment_author_url.' ）' : '';
	$comment_author_email = trim($comment->comment_author_email);
	$comment_content = $comment->comment_content;
	
	$comment_parent_id = $comment->comment_parent;
	$comment_parent = get_comment($comment_parent_id);
	$comment_parent_link = get_comment_link($comment_parent_id,array('type' => 'comment'));
	
	$comment_post_id = $comment->comment_post_ID;
	$comment_post_link = get_permalink($comment_post_id);
	$comment_post = get_post($comment_post_id);
	$comment_post_title = $comment_post->post_title;
	$comment_post_author = get_userdata($comment_post->post_author);
	$blogDomain = $_SERVER['SERVER_NAME'];
	$blogName = get_bloginfo('name');
		
	// if(isset($_POST['allow_mail_reply']) && $_POST['allow_mail_reply'] == 1){
	// 	update_comment_meta($comment_id,'allow_mail_reply',1) or add_comment_meta($comment_id,'allow_mail_reply',1,true);
	// }
	// $allow_mail_reply = get_comment_meta($comment_parent_id,'allow_mail_reply',true);
	// if(!$allow_mail_reply)$allow_mail_reply = 0;
	// if($comment_parent_id == 0)$allow_mail_reply = 1;
	
	// // 0:不同意接收邮件提示；1:同意只要有邮件就提示；
	// if(!$allow_mail_reply || $comment->comment_approved == 'spam' || $comment->comment_type == 'pingback')return;
	
	if($comment_parent_id == 0) {
		$mail_to = trim($comment_post_author->user_email);
		$mail_content = "{$comment_post_author->display_name}，您好！"
			."\n您的在{$blogName}上发表的文章“{$comment_post_title}”被{$comment_author} {$comment_author_url} 评论，请及时回复他"
			."\n链接：{$comment_post_link}"
			."\n内容：{$comment_content}";
	}
	else {
		$mail_to = $comment_parent->comment_author_email;
		$mail_content = "{$comment_parent->comment_author}，您好！"
			."\n您的在{$blogName}的文章“{$comment_post_title}”上发表的评论被{$comment_author} {$comment_author_url} 回复，请及时回复他。"
			."\n链接：{$comment_parent_link}"
			."\n内容：{$comment_content}";
	}
	$mail_subject = "您在“{$blogName}{$blogDomain}”有了新的评论动态";
	
	wp_mail($mail_to,$mail_subject,$mail_content);
}
add_action('comment_post','mail_to_comment',999);


// function add_mail_to_comment_checkbox() {
//   echo '<label id="add-mail-comment-checkbox"><input type="checkbox" name="allow_mail_reply" value="1" checked="checked" />有人回复时邮件通知我</label>';
// }
// add_action('comment_form', 'add_mail_to_comment_checkbox');

// -- END ----------------------------------------
