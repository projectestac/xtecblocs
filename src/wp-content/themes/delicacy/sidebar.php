<div id="sidebar">
			
    <?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Right Sidebar')) : else : ?>
    
        <!-- All this stuff in here only shows up if you DON'T have any widgets active in this zone -->
		<div class="widget">
		<h3><?php _e('Archives','delicacy'); ?></h3>
		<ul>
            <?php wp_get_archives( 'type=monthly' ); ?>
        </ul>
        </div>
		<div class="widget">
		<h3><?php _e('Meta','delicacy'); ?></h3>
		    <ul>
			<?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <?php wp_meta(); ?>
            </ul>
		</div>
	<?php endif; ?>

</div>