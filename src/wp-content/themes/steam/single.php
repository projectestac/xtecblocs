<?php get_header(); ?>

	<div id="content" class="widecolumn">
				
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="navigation">
			<div class="alignleft"><?php previous_post('&laquo; %','','yes') ?></div>
			<div class="alignright"><?php next_post(' % &raquo;','','yes') ?></div>
		</div>
	
		<div class="post">
			<h2 id="post-<?php the_ID(); ?>"><a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link:','steam');?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
	
			<div class="entrytext">
				<?php the_content('<p class="serif">'.__('Read the rest of this entry &raquo;','steam') .'</p>'); ?>
	
				<?php link_pages('<p><strong>'. __('Pages:','steam').'</strong> ', '</p>', 'number'); ?>
	
				<p class="postmetadata alt">
					<small>
						<?php _e('This entry was posted','steam');?>
						<?php /* This is commented, because it requires a little adjusting sometimes.
							You'll need to download this plugin, and follow the instructions:
							http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
							/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */ ?> 
						<?php _e('on','steam');?> <?php the_time('d F Y') ?> at <?php the_time() ?>
						<?php _e('and is filed under','steam');?> <?php the_category(', ') ?>.
						<?php _e('You can follow any responses to this entry through the','steam');?> <?php comments_rss_link('RSS 2.0'); ?> <?php _e('feed','steam');?>. 
						
						<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Both Comments and Pings are open ?>
							<?php _e('You can','steam');?> <a href="#respond"><?php _e('leave a response','steam');?></a>, <?php _e('or','steam');?> <a href="<?php trackback_url(display); ?>"><?php _e('trackback','steam');?></a> <?php _e('from your own site.','steam');?>
						
						<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Only Pings are Open ?>
							<?php _e('Responses are currently closed, but you can','steam');?> <a href="<?php trackback_url(display); ?> ">trackback</a> <?php _e('from your own site.','steam');?>
						
						<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Comments are open, Pings are not ?>
							<?php _e('You can skip to the end and leave a response. Pinging is currently not allowed.','steam');?>
			
						<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Neither Comments, nor Pings are open ?>
							<?php _e('Both comments and pings are currently closed.','steam');?>			
						
						<?php } edit_post_link(__('Edit this entry.','steam'),'',''); ?>
						
					</small>
				</p>
	
			</div>
		</div>
		
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
	
		<p><?php _e('Sorry, no posts matched your criteria.','steam'); ?></p>
	
<?php endif; ?>
	
	</div>

<?php get_footer(); ?>
