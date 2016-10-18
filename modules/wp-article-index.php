<?php

/**
 * Name: 文章索引
 * Description: 可以通过the_post_index()输出文章h2,h3的标题层次结构索引
 */

function get_post_index(){
	global $post;
	$match = "/<h2>([^<]+)<\/h2>/im";
	$content = $post->post_content;
	$link = get_permalink($post->ID);
	$index = '<div class="post-index"><ul class="post-index-root">';

	if(preg_match_all($match,$content,$matches)) {
		$subContents = preg_split($match,$content);
		foreach($matches[1] as $num => $title) {
			$poser = $num + 1;
			$index .= '<li><a href="'.$link.'#title-'.$poser.'" title="'.$title.'">'.$title.'</a></li>';
			$subContent = $subContents[$poser];

			if(preg_match_all("/<h3>([^<]+)<\/h3>/im",$subContent,$children)){
				$index .= '<ul class="post-index-children">';
				foreach($children[1] as $n => $titleSub){
					$poserSub = $n + 1;
					$index .= '<li><a href="'.$link.'#title-'.$poser.'-'.$poserSub.'" title="'.$titleSub.'">'.$titleSub.'</a></li>';
				}
				$index .= '</ul>';
			}
		}
	}
	elseif(preg_match_all("/<h3>([^<]+)<\/h3>/im",$content,$matches)) {
		foreach($matches[1] as $num => $title) {
			$poser = $num + 1;
			$index .= '<li><a href="'.$link.'#title-'.$poser.'" title="'.$title.'">'.$title.'</a></li>';
		}
	}
	else {
		return false;
	}

	$index .= '</ul></div>';
	return $index;
}
function the_post_index(){
	echo get_post_index();
}

add_filter('the_content','filter_post_index',999);
function filter_post_index($content) {
	$match = "/<h2>([^<]+)<\/h2>/im";

	if(preg_match_all($match,$content,$matches)) {
		$subContents = preg_split($match,$content);
		foreach($matches[1] as $num => $title) {
			$poser = $num + 1;
			$content = str_replace($matches[0][$num],'<h2 id="title-'.$poser.'">'.$title.'</h2>',$content);
			$subContent = $subContents[$poser];

			if(preg_match_all("/<h3>([^<]+)<\/h3>/im",$subContent,$children)){
				foreach($children[1] as $n => $titleSub){
					$poserSub = $n + 1;
					$content = str_replace($children[0][$n],'<h3 id="title-'.$poser.'-'.$poserSub.'">'.$titleSub.'</h3>',$content);
				}
			}
		}
	}
	elseif(preg_match_all("/<h3>([^<]+)<\/h3>/im",$content,$matches)) {
		foreach($matches[1] as $num => $title) {
			$poser = $num + 1;
			$content = str_replace($matches[0][$num],'<h2 id="title-'.$poser.'">'.$title.'</h2>',$content);
		}
	}

	return $content;
}
