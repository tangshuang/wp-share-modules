<?php

/**
 * Name: 去除分类URL中的父级目录
 * Description: PERMALINK_CATEGORY_TYPE：0.后台不填写分类前缀时分类URL将没有前缀；1.去除分类URL父分类；去除文章URL的父分类（如果固定链接中包含%category%）。
 * Version: 1.0.0
 * Author: <a href="http://www.tangshuang.net/">否子戈</a>
 * Doc: http://www.tangshuang.net/wp-permalink-category
 */

if( !defined( 'PERMALINK_CATEGORY_TYPE' ) ) 
{
	define( 'PERMALINK_CATEGORY_TYPE', 1 ); // 你可以自己在wp-config.php中定义这个值，仅0|1可选，上面已经说明各自代表什么
}

if ( PERMALINK_CATEGORY_TYPE >= 0 ) 
{
	// refresh category permalink rewrite rules
	add_action( 'created_category', 'permalink_category_flush_rules' );
	add_action( 'edited_category', 'permalink_category_flush_rules' );
	add_action( 'delete_category', 'permalink_category_flush_rules' );
	add_action( 'wp_loaded', 'permalink_category_flush_rules' );
	function permalink_category_flush_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	// redirect from old url to new url, for SEO
	add_action('wp','permalink_category_redirect');
	function permalink_category_redirect() {
		if(!is_category()) 
		{
			return;
		}
		global $cat;
		$cat_link = get_category_link($cat);
		$cat_name = get_query_var('category_name');
		$_current_link = home_url( add_query_arg( NULL, NULL ) );
		if( strpos($_current_link,$cat_link) === false ) 
		{
			$_uri = substr( $_current_link, strrpos($_current_link,$cat_name) + strlen($cat_name) );
			$_url = $cat_link.$_uri;
			status_header(301);
			header("Location: $_url");
			exit();
		}
	}

	// 去除根
	add_filter( 'category_rewrite_rules', 'permalink_category_no_base_rewrite_rules' );
	function permalink_category_no_base_rewrite_rules( $category_rewrite_rules ) {
		if ( get_option('category_base') ) 
		{
			return $category_rewrite_rules;
		}

		$categories = get_categories(array('hide_empty' => false));
		foreach ($categories as $category) {
			$category_slug = $category -> slug;
			if ( $category->parent == $category->cat_ID )
			{
				$category->parent = 0;
			}
			elseif ( $category->parent != 0 )
			{
				$category_parents = get_category_parents( $category->parent, false, '/', true );
				$category_slug = $category_parents . $category_slug;
				if( $category_parents === '' )
				{
					// 解决分类slug和文章post name冲突问题
					$posts = get_posts (array("name" => $category_slug));
					if( !empty($posts) )
					{
						$category_slug = 'category_'.$category_slug;
					}
				}
			}

			$category_rewrite_rules['(' . $category_slug . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
			$category_rewrite_rules['(' . $category_slug . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
			$category_rewrite_rules['(' . $category_slug . ')/?$'] = 'index.php?category_name=$matches[1]';
		}

		return $category_rewrite_rules;
	}

	if ( PERMALINK_CATEGORY_TYPE > 0 )
	{
		// 去除分类URL的父分类
		add_filter( 'category_rewrite_rules', 'permalink_category_no_parent_rewrite_rules' );
		function permalink_category_no_parent_rewrite_rules( $category_rewrite_rules ) {
			$categories = get_categories(array('hide_empty' => false));
			foreach ($categories as $category) {
				$category_slug = $category -> slug;
				$category_base = get_option('category_base');
				if( $category_base )
				{
					$category_slug = $category_base . '/' . $category_slug;
				}
				else
				{
					$posts = get_posts (array("name" => $category_slug));
					if( !empty($posts) )
					{
						$category_slug = 'category_'.$category_slug;
					}
				}

				$category_rewrite_rules['(' . $category_slug . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
				$category_rewrite_rules['(' . $category_slug . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
				$category_rewrite_rules['(' . $category_slug . ')/?$'] = 'index.php?category_name=$matches[1]';
			}

			return $category_rewrite_rules;
		}
		add_filter ( "category_link", "permalink_category_link_filter" );
		function permalink_category_link_filter ( $cat_link )
		{
			// 要是没有采用固定链接形式，就不往下执行了
			if(preg_match ("/\?cat=/", $cat_link)){
				return $cat_link;
			}

			$link_uri_arr = explode("/", $cat_link);
			if( is_permalink_structure_end_with_trailing_slash() )
			{
				$last_uri = $link_uri_arr[count($link_uri_arr)-2];
			}
			else
			{
				$last_uri = $link_uri_arr[count($link_uri_arr)-1];
			}

			$category_slug = $last_uri;
			$category_base = get_option('category_base');

			if( $category_base )
			{
				$category_slug = $category_base . '/' . $category_slug;
			}
			else
			{
				$posts = get_posts (array("name" => $category_slug));
				if( !empty($posts) )
				{
					$category_slug = 'category_'.$category_slug;
				}
			}

			$cat_link = preg_replace ("/category.*?".$last_uri."/", $category_slug, $cat_link);
			
			return $cat_link;
		}

		// 去除文章URL中的父分类
		add_filter ("pre_post_link", "permalink_category_post_pre_filter");
		function permalink_category_post_pre_filter ($permalink){
			$permalink = str_replace ("%category%", "%my_category%", $permalink); 	
			return $permalink;
		}
		add_filter ("user_trailingslashit", "permalink_category_post_filter");
		function permalink_category_post_filter ($post_link){
			if( !preg_match ("/%my_category%/", $post_link) )
			{
				return $post_link;
			}
			
			$link_uri_arr = explode("/", $post_link);
			if(is_permalink_structure_end_with_trailing_slash())
			{
				$post_name = $link_uri_arr[count($link_uri_arr)-2];
			}
			else
			{
				$post_name = $link_uri_arr[count($link_uri_arr)-1];
			}
			$posts = get_posts (array("name" => $post_name));
			$post_cats = get_the_category($posts[0]->ID);
			if($post_cats){
				usort($post_cats, '_usort_terms_by_ID'); 
				$category_slug = $post_cats[0]->slug;
				$post_link = preg_replace("/%my_category%/", $category_slug, $post_link);
			}
			return $post_link;	
		}
	}
}

// 创建一个函数，用来判断固定链接形式的末尾是否以/斜线结束，如果是，返回true，如果不是，返回false
function is_permalink_structure_end_with_trailing_slash(){
	$permalink_structure = get_option('permalink_structure');
	if ( !$permalink_structure || '/' === substr($permalink_structure, -1) )
	{
		return true;
	}
	else
	{
		return false;
	}
}