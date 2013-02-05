<?php get_header(); ?>
<div id="content">
	<?php if (have_posts()) :?>
		<?php $postCount=0; ?>
		<?php while (have_posts()) : the_post();?>
			<?php $postCount++;?>
	<div class="entry entry-<?php echo $postCount ;?>">
		<div class="entrytitle">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','simpla');?> <?php the_title(); ?>"><?php the_title(); ?></a></h2> 
			<h3><?php the_time('F jS, Y') ?></h3>
		</div>
		<div class="entrybody">
			<?php the_content( __('Read the rest of this entry &raquo;','simpla')); ?>
		</div>
		
		<div class="entrymeta">
		<div class="postinfo">
			<span class="postedby"><?php _e('Posted by','simpla');?> <?php the_author() ?></span>
			<span class="filedto"><?php _e('Filed in','simpla');?> <?php the_category(', ') ?> <?php edit_post_link( __('Edit','simpla'), ' | ', ''); ?></span>
		</div>
		<?php comments_popup_link( __('No Comments','simpla') .' &#187;', __('1 Comment','simpla').' &#187;', __('% Comments','simpla').' &#187;', 'commentslink'); ?>
		</div>
		
	</div>
	<div class="commentsblock">
		<?php comments_template(); ?>
	</div>
	<?php endwhile; ?>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','simpla')) ?></div>
			<div class="alignright"><?php previous_posts_link( __('Next Entries &raquo;','simpla')) ?></div>
		</div>
		
	<?php else : ?>

		<h2><?php _e('Not Found','simpla');?></h2>
		<div class="entrybody"><?php _e('Sorry, but you are looking for something that isn\'t here.','simpla');?></div>

	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
