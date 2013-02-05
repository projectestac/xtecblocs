<?php get_header(); ?>

			<div class="narrow_column">

<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">

	<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<div class="entry">

		<?php the_content(); ?>
		<?php link_pages('<p><strong>'. __('Pages','quadruple-blue') .':</strong> ', '</p>', 'number'); ?>

		<?php edit_post_link( __('Edit','quadruple-blue'), '<p>', '</p>'); ?>

		<!--
		<?php trackback_rdf(); ?>
		-->

		<div class="comments-template"><?php comments_template(); ?></div>

	</div><!-- end entry -->

</div><!-- end post -->
<?php endwhile; ?>

<?php include (TEMPLATEPATH . '/browse.php'); ?>

<?php else : ?>

<div class="post">

	<h2><?php _e('Not Found','quadruple-blue'); ?></h2>
	<div class="entry">
		<p class="notfound"><?php _e('Sorry, but you are looking for something that isn&#39;t here.','quadruple-blue'); ?></p>
	</div>

</div>

<?php endif; ?>

			</div><!-- end narrow column -->

<?php get_footer(); ?>
