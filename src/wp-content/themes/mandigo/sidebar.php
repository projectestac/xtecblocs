	<ul id="sidebar">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar()) : ?>
			<?php widget_mandigo_search(); ?>

			<?php widget_mandigo_calendar(); ?>

			<?php /* If this is a 404 page */ if (is_404()) { ?>
			<?php /* If this is a category archive */ } elseif (is_category()) { ?>
			<li><p><?php _e('You are currently browsing the archives for the','mandigo');?> '<?php single_cat_title(''); ?>' category.</p></li>

			<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<li><p><?php _e('You are currently browsing the','mandigo');?> <a href="<?php bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> <?php _e('weblog archives for the day','mandigo');?> <?php the_time('l, F jS, Y'); ?>.</p></li>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<li><p><?php _e('You are currently browsing the','mandigo');?> <a href="<?php bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> <?php _e('weblog archives for','mandigo');?> <?php the_time('F, Y'); ?>.</p></li>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<li><p><?php _e('You are currently browsing the','mandigo');?> <a href="<?php bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> <?php _e('weblog archives for the year','mandigo');?> <?php the_time('Y'); ?>.</p></li>

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<li><p><?php _e('You have searched the','mandigo');?> <a href="<?php echo bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> <?php _e('weblog archives for','mandigo');?> <strong>'<?php echo wp_specialchars($s); ?>'</strong>. <?php _e('If you are unable to find anything in these search results, you can try one of these links.','mandigo');?></p></li>

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<li><p><?php _e('You are currently browsing the','mandigo');?> <a href="<?php echo bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> <?php _e('weblog archives.','mandigo');?></p></li>

			<?php } ?>

			<?php wp_list_pages('title_li=<h2>'. __('Pages') .'</h2>' ); ?>

			<li><h2><?php _e('Categories','mandigo');?></h2>
				<ul>
				<?php wp_list_cats('sort_column=name&optioncount=1&hide_empty=0&hierarchical=1'); ?>
				</ul>
			</li>

			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
				<?php get_links_list(); ?>
			<?php } ?>

			<?php widget_mandigo_meta(); ?>

<?php endif; ?>
	</ul>

