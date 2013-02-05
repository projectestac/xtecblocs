<?php get_header();?>

<div id="content">
<h1><?php _e('Error 404','light'); ?></h1>
  <h2 class="entrytitle"><?php _e('The page you requested is no longer here!','light');?> </h2>
  <p><?php _e('Visit the','light');?> <a href="<?php bloginfo('siteurl');?>"><?php _e('Home Page','light');?></a></p>
  <p><?php _e('In order to improve our service, can you inform us that someone else has an incorrect link to our site?','light');?></p>
  <p><a href="/contact"><?php _e('Report broken link','light');?></a> </p>
  <p>&nbsp;</p>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
