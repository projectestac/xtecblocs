<?php get_header(); ?>
	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
                                <div class="postinfo">
			        	<div class="calborder">
			        	<div class="cal">
                                                <span class="cald<?php echo (get_option('mandigo_dates') ? ' cald2' : '') ?>"><?php the_time((get_option('mandigo_dates') ? 'M' : 'd')) ?></span>
                                                <span class="calm"><?php the_time((get_option('mandigo_dates') ? 'd' : 'm')) ?></span>
                                                <span class="caly"><?php the_time('Y') ?></span>
                                        </div>
                                        </div>
                                        <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','mandigo');?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
                                        <small><?php _e('Posted by','mandigo');?>: <?php the_author() ?>, <?php _e('in','mandigo')?> <?php the_category(', ') ?><?php edit_post_link('<img src="'. get_bloginfo('stylesheet_directory') .'/images/' . get_option('mandigo_scheme') .'/edit.gif" alt="'.__('Edit this post','mandigo').'" /> '.__('Edit','mandigo').'', ' - ', ''); ?>  </small>
                                </div>

				<div class="entry">
					<?php the_content( __('Read the rest of this entry &raquo;','mandigo') ); ?>
				</div>

				<p class="postmetadata"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/<?php echo get_option('mandigo_scheme'); ?>/comments.gif" alt="Comments" /> <?php comments_popup_link( __('No Comments','mandigo').' &#187;', __('1 Comment','mandigo').' &#187;', __('% Comments','mandigo').' &#187;'); ?></p>
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
