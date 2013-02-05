<?php global $freshy_options; ?>
	
	<div id="sidebar">
		<div>
		<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
		<?php if(function_exists('yy_menu')) : ?>
			<h2><?php _e('Navigation','xtec-11'); ?></h2>
			<ul>
			<?php yy_menu('sort_column=menu_order&title_li=',
								'hide_empty=0&sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image='.get_bloginfo('stylesheet_directory').'/images/icons/feed-icon-10x10.gif'); ?>
			</ul>
	
		<?php elseif (function_exists('freshy_menu')) : 
			freshy_menu($freshy_options['args_pages'],$freshy_options['args_cats']);
		endif; ?>
				
			<h2><?php _e('Search','xtec-11'); ?></h2>
			<?php include (TEMPLATEPATH . '/searchform.php'); ?>

			<?php
					
						$cats = get_categories("type=link&orderby=name&order=ASC&hierarchical=0");
				
					
					if ($cats) {
						foreach ($cats as $cat) {
							// Handle each category.
							// Display the category name
							echo '	<h2 id="linkcat-' . $cat->cat_ID . '">' . $cat->cat_name . "</h2>\n\t<ul>\n";
							// Call get_links() with all the appropriate params
							if (substr(get_bloginfo('version'), 0, 3) < 2.1)
							{
								get_links($cat->cat_id,'<li>',"</li>","<br />", FALSE, 'name', TRUE, FALSE, -1, FALSE);
							}
							else
							{
								get_links($cat->cat_ID,'<li>',"</li>","<br />", FALSE, 'name', TRUE, FALSE, -1, FALSE);
							}
							// Close the last category
							echo "\n\t</ul>\n\n";
						}
					}
				?>
		<?php endif; ?>
		</div>
	</div>
