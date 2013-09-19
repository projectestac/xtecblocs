<?php /* Mystique/digitalnature */ ?>

   </div>
 </div>
 <!-- /main -->

 <?php
  wp_nav_menu(array(
    'container_class' => 'nav nav-bottom page-content',
    'menu_class'      => 'menu clear-block',
    'theme_location'  => 'footer',
    'fallback_cb'     => '',
  ));
 ?>

 <!-- footer -->
 <div class="shadow-left page-content">
   <div class="shadow-right">

     <div id="footer">

       <div id="copyright">

         <a href="<?php echo home_url('/') ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><strong><?php bloginfo('name'); ?></strong></a>

         &copy; <?php the_date('Y'); ?> |

         <?php printf(__('Powered by %1$s and %2$s theme by %3$s', 'mystique'), '<a href="http://wordpress.org/">WordPress</a>', 'Mystique', '<a href="http://digitalnature.eu/themes/mystique/">digitalnature</a>'); ?>

         <br />

         <?php printf(__('%1$s queries in %2$s seconds', 'mystique'), get_num_queries(), timer_stop(0, 2)); ?>

         <?php if(function_exists('memory_get_usage')) echo '('.number_format(memory_get_usage()/1024/1024, 2).'M)'; ?>                 

       </div>

     </div>

   </div>
 </div>
 <!-- /footer -->

 <a class="go-top" href="#page"><?php _e('Go to Top', 'mystique'); ?></a>

 </div>
 <!-- /page-ext -->

 </div>
 <!-- page -->

 <!-- <?php printf(__('%1$s queries in %2$s seconds', 'mystique'), get_num_queries(), timer_stop(0, 2)); ?> -->
 <?php wp_footer(); ?>
</body>
</html>
