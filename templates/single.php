<?php
get_header();
?>
<div id="main">
	<div id="content">
	<p>hi there this is my first template for the custom post "My_Keywords"</p>
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<h1><?php the_title();?></h1>
			<p><?php the_post_thumbnail('medium');?><?php the_content();?></p>

	<?php endwhile; ?>
	</div>
<?php get_sidebar('right'); ?>
</div>
<?php
get_footer();
?>