<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

		<?php if (have_posts()) : ?>

		 <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for the','mandigo');?> '<?php echo single_cat_title(); ?>' <?php _e('Category','mandigo');?></h2>

 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','mandigo');?> <?php the_time('F jS, Y'); ?></h2>

	 <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','mandigo');?> <?php the_time('F, Y'); ?></h2>

		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','mandigo');?> <?php the_time('Y'); ?></h2>

	  <?php /* If this is a search */ } elseif (is_search()) { ?>
		<h2 class="pagetitle"><?php _e('Search Results','mandigo');?></h2>

	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle"><?php _e('Author Archive','mandigo');?></h2>

		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle"><?php _e('Blog Archives','mandigo');?></h2>

		<?php } ?>


		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( __('&laquo; Previous Entries','mandigo') )?></div>
			<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','mandigo') )?></div>
		</div>

		<?php while (have_posts()) : the_post(); ?>
		<div class="post">
                                <div class="postinfo">
			        	<div class="calborder">
			        	<div class="cal">
                                                <span class="cald<?php echo (get_option('mandigo_dates') ? ' cald2' : '') ?>"><?php the_time((get_option('mandigo_dates') ? 'M' : 'd')) ?></span>
                                                <span class="calm"><?php the_time((get_option('mandigo_dates') ? 'd' : 'm')) ?></span>
                                                <span class="caly"><?php the_time('Y') ?></span>
                                        </div>
                                        </div>
                                        <h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','mandigo');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
                                        <small><?php _e('Posted by','mandigo');?>: <?php the_author() ?>, <?php _e('in','mandigo');?> <?php the_category(', ') ?><?php edit_post_link('<img src="' . get_bloginfo('stylesheet_directory') . '/images/'. get_option('mandigo_scheme') .'/edit.gif" alt="'. __('Edit this post','mandigo') .'" />'. __('Edit','mandigo') .'', ' - ', ''); ?>  </small>
                                </div>

				<div class="entry">
					<?php the_content() ?>
				</div>

				<p class="postmetadata"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/<?php echo get_option('mandigo_scheme'); ?>/comments.gif" alt="<?php _e('Comments');?>" /> <?php comments_popup_link( __('No Comments').' &#187;', __('1 Comment').' &#187;', __('% Comments').' &#187;'); ?></p>

			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( __('&laquo; Previous Entries','mandigo') )?></div>
			<div class="alignright"><?php previous_posts_link( __('Next Entries &raquo;','mandigo') )?></div>
		</div>

	<?php else : ?>

		<h2 class="center"><?php _e('Not Found','mandigo');?></h2>
		<p class="center"><?php _e('Sorry, but you are looking for something that isn\'t here.','mandigo');?></p>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
