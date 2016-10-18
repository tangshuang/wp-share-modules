<ul class="widget-posts-recommend">
	<?php while(have_posts()): the_post(); ?>
	<li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
</ul>