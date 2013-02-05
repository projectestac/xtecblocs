<?php ob_start(); ?>

<?php get_header(); ?>

	<div id="content">
	
	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
				
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Read', 'freshy'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>

				<small class="date">
					<span class="date_day"><?php the_time('j') ?></span>
					<span class="date_month"><?php the_time('m') ?></span>
					<span class="date_year"><?php the_time('Y') ?></span>
				</small>
					
				<div class="entry">
					<?php the_content('<span class="readmore">'.__('Read the rest of this entry &raquo;').'</span>', 'freshy'); ?>
				</div>
	
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
				
			</div>
			
			<p class="navigation">
				<span class="alignleft"><?php previous_post_link('&laquo; %link') ?></span>
				<span class="alignright"><?php next_post_link('%link &raquo;') ?></span>
			</p>
			<br/>
			<h3><?php _e('Actions', 'freshy'); ?></h3>
			<ul class="postmetadata">
		<?php if ('open' == $post-> comment_status) : ?>
			<li class="with_icon"><img class="icon" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/feed-icon-16x16.gif" alt="rss" />&nbsp;<?php comments_rss_link(__('Comments rss', 'freshy')); ?></li>
		<?php endif; ?>
		<?php if ('open' == $post->ping_status) : ?>
			<li class="with_icon"><img class="icon" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/trackback-icon-16x16.gif" alt="trackback" />&nbsp;<a href="<?php trackback_url(true); ?> " rel="trackback" title="make a trackback"><?php _e('Trackback', 'freshy'); ?></a></li>
		<?php endif; ?>
		<?php if ($user_ID) : ?>
			<li class="with_icon"><img class="icon" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/edit-icon-16x16.gif" alt="edit" />&nbsp;<?php edit_post_link(__('Edit', 'freshy'),'',''); ?></li>
		<?php endif; ?>
			</ul>
				
			<h3><?php _e('Informations', 'freshy'); ?></h3>
			<ul class="postmetadata">
					<!--<li><?php _e('Author', 'freshy'); ?> : <?php the_author() ?></li>-->
					<li><?php _e('Date', 'freshy'); ?> : <?php the_time('j F Y') ?></li>
				<?php if(function_exists('mdv_last_modified')) : ?>
					<li><?php _e('Last modified', 'freshy'); ?> : <?php mdv_last_modified('j F Y') ?></li>
				<?php endif; ?>
					<li><?php _e('Categories', 'freshy'); ?> : <?php the_category(', ') ?></li>
				<?php if(function_exists('the_bunny_tags')) : ?>
					<li><?php the_bunny_tags('Tags : ', '', ', '); ?></li>
				<?php endif; ?>
			</ul>
				
			<?php comments_template(); ?>
						
		<?php endwhile; ?>
	
	<!-- nothing found -->
	<?php else : ?>

		<h2><?php _e('Not Found', 'freshy'); ?></h2>
		<p><?php _e('Sorry, but you are looking for something that isn\'t here.', 'freshy'); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
		
	</div>
	
	<hr style="display:none"/>
	
	<!-- sidebar -->
	<?php get_sidebar(); ?>

	<br style="clear:both" /><!-- without this little <br /> NS6 and IE5PC do not stretch the frame div down to encopass the content DIVs -->
</div>
				
<!-- footer -->
<?php get_footer(); ?>

<? ob_end_flush();?>
