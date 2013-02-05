<?php ob_start(); ?>

<?php get_header(); ?>

	<!-- sidebar -->
	<?php get_sidebar(); ?>
	<div id="content">
	
	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
				<small class="post_date">
					<span class="date_day"><?php the_time('j') ?></span>
					<span class="date_month"><?php the_time('m') ?></span>
					<span class="date_year"><?php the_time('Y') ?></span>
				</small>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Read','xtec'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>

				
					
				<div class="entry">
					<?php the_content('<span class="readmore">'.__('Read the rest of this entry &raquo;','xtec').'</span>'); ?>
				</div>
	
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
				
			</div>
			
			<p class="navigation">
				<span class="alignleft"><?php previous_post_link('&laquo; %link') ?></span>
				<span class="alignright"><?php next_post_link('%link &raquo;') ?></span>
			</p>
			<br/>
		<div class="contentwo">
			<h3><?php _e('Actions','xtec'); ?></h3>
			<ul class="postmetadata">
		<?php if ('open' == $post-> comment_status) : ?>
			<li class="with_icon"><img class="icon" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/feed-icon-16x16.gif" alt="rss" />&nbsp;<?php comments_rss_link(__('Comments rss','xtec')); ?></li>
		<?php endif; ?>
		<?php if ('open' == $post->ping_status) : ?>
			<li class="with_icon"><img class="icon" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/trackback-icon-16x16.gif" alt="trackback" />&nbsp;<a href="<?php trackback_url(true); ?> " rel="trackback" title="make a trackback"><?php _e('Trackback','xtec'); ?></a></li>
		<?php endif; ?>
		<?php if ($user_ID) : ?>
			<li class="with_icon"><img class="icon" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/edit-icon-16x16.gif" alt="edit" />&nbsp;<?php edit_post_link(__('Edit','xtec'),'',''); ?></li>
		<?php endif; ?>
			</ul>
				
			<h3><?php _e('Informations','xtec'); ?></h3>
			<ul class="postmetadata">
					<!--<li><?php _e('Author','xtec'); ?> : <?php the_author() ?></li>-->
					<li><?php _e('Date','xtec'); ?> : <?php the_time('d F Y') ?></li>
				<?php if(function_exists('mdv_last_modified')) : ?>
					<li><?php _e('Last modified','xtec'); ?> : <?php mdv_last_modified('j F Y') ?></li>
				<?php endif; ?>
					<li class="tag"><?php _e('Categories','xtec'); ?> : <?php the_category(', ') ?></li>
				<?php if(function_exists('the_bunny_tags')) : ?>
					<li><?php the_bunny_tags('Tags : ', '', ', '); ?></li>
				<?php endif; ?>
			</ul>
				
			<?php comments_template(); ?>
		</div>				
		<?php endwhile; ?>
	
	<!-- nothing found -->
	<?php else : ?>

		<h2><?php _e('Not Found','xtec'); ?></h2>
		<p><?php _e('Sorry, but you are looking for something that isn\'t here.','xtec'); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
		
	</div>
	
	<hr style="display:none"/>

	<br style="clear:both" /><!-- without this little <br /> NS6 and IE5PC do not stretch the frame div down to encopass the content DIVs -->
	
</div>
		
<!-- footer -->
<?php get_footer(); ?>

<? ob_end_flush();?>
