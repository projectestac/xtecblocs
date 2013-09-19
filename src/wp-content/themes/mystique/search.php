<?php /* Mystique/digitalnature */ ?>

<?php get_header(); ?>

  <!-- main content: primary + sidebar(s) -->
  <div id="mask-3" class="clear-block">
   <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">

        <?php if(have_posts()): ?>

        <h1 class="title">
         <?php
           printf(__('Search results for %1$s (%2$s)', 'mystique'),
             sprintf('<span class="alt">%s</span>', get_search_query()),
             $wp_query->found_posts
           );
         ?>
        </h1>

        <div class="divider"></div>

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

        <?php else: ?>
        <h1 class="title"><?php _e('Nothing found :(', 'mystique'); ?></h1>
        <p class="large"><em><?php _e('Try a different search...', 'mystique'); ?></em></p>
        <?php endif; ?>

      </div>
      <!-- /primary content -->

      <?php get_sidebar(); ?>

    </div>
   </div>
  </div>
  <!-- /main content -->

<?php get_footer(); ?>
