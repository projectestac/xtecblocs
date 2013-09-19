<?php /* Mystique/digitalnature */ ?>

<?php
 get_header();
 the_post(); // sets up $authordata
 rewind_posts();
?>

  <!-- main content: primary + sidebar(s) -->
  <div id="mask-3" class="clear-block">
   <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">

        <div class="clear-block">

          <div class="alignleft">
            <?php echo get_avatar($authordata->user_email, '192', '', $authordata->display_name); ?>
          </div>

          <h1 class="title"><?php echo $authordata->display_name; ?></h1>

          <p class="large">
            <em><?php if($authordata->user_description<>'') echo $authordata->user_description; else _e('This user hasn\'t shared any profile information', 'mystique'); ?></em>
          </p>

          <?php if(($authordata->user_url <> 'http://') && ($authordata->user_url != '')): ?>
            <p class="im www"><?php _e('Home page:', 'mystique'); ?> <a href="<?php echo $authordata->user_url; ?>"><?php echo $authordata->user_url; ?></a></p>
          <?php endif; ?>

          <?php if(!empty($authordata->yim)): ?>
            <p class="im yahoo">Yahoo Messenger: <a href="ymsgr:sendIM?<?php echo $authordata->yim; ?>"><?php echo $authordata->yim; ?></a></p>
          <?php endif; ?>

          <?php if(!empty($authordata->jabber)): ?>
            <p class="im gtalk">Jabber/GTalk: <a href="gtalk:chat?jid=<?php echo $authordata->jabber; ?>"><?php echo $authordata->jabber; ?></a></p>
          <?php endif; ?>

          <?php if(!empty($authordata->aim)): ?>
            <p class="im aim">AIM: <a href="aim:goIM?screenname=<?php echo $authordata->aim; ?>"><?php echo $authordata->aim; ?></a></p>
          <?php endif; ?>

        </div>

        <div class="divider"></div>

        <?php if(have_posts()): ?>
        <h5 class="title"><?php printf(__('Posts by %s', 'mystique'), $authordata->display_name); ?></h5>

        <?php while(have_posts()): ?>
          <?php the_post(); ?>
          <?php get_template_part('teaser'); ?>
        <?php endwhile; ?>

        <?php if(function_exists('wp_pagenavi')): ?>
          <?php wp_pagenavi() ?>
        <?php else : ?>
        <div class="page-navi clear-block">
          <div class="alignleft"><?php previous_posts_link(__('&laquo; Previous', 'mystique')); ?></div>
          <div class="alignright"><?php next_posts_link(__('Next &raquo;', 'mystique')); ?></div>
        </div>
        <?php endif; ?>


        <?php echo '<a class="rss-block alignright" rel="rss" href="'.get_author_feed_link($authordata->ID).'">'.sprintf(__('%s\'s RSS Feed', 'mystique'), $authordata->display_name).'</a>'; ?>

        <?php else: ?>
        <p><?php printf(__('%s has\'t written any posts yet', 'mystique'), $authordata->display_name); ?></p>
        <?php endif; ?>


      </div>
      <!-- /primary content -->

      <?php get_sidebar(); ?>
    </div>
   </div>
  </div>
  <!-- /main content -->

<?php get_footer(); ?>
