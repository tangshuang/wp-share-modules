<ul class="widget-posts-recommend widget-posts-with-image">
	<?php while(have_posts()): the_post(); ?>
	<li class="widget-post">
	    <div class="widget-post-thumb"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" style="background-image: url(<?php the_post_thumb_src(); ?>);"></a></div>
	    <div class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
	    <div class="post-info">
	        <span class="view-count"><i class="fa fa-paper-plane-o"></i><?php the_post_view_count(); ?> views</span><br />
	        <span class="comment-count"><i class="fa fa-comments-o"></i><?php comments_number('0 Comment','1 Comment','% Comments'); ?></span>
	    </div>
	    <div class="clear"></div>
	</li>
	<?php endwhile; ?>
</ul>