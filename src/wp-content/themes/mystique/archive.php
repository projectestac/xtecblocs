<?php /* Mystique/digitalnature */ ?>

<?php get_header(); ?>

  <!-- main content: primary + sidebar(s) -->
  <div id="mask-3" class="clear-block">
   <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">

        <?php if(is_category()): ?>
        <h1 class="title archive-category"><?php echo single_cat_title(); ?></h1>

        <?php if($desc = category_description()): ?>
        <div class="large">
          <p><em><?php echo $desc; ?></em></p>
        </div>

        <div class="divider"></div>
        <?php endif; ?>

        <?php elseif(is_tag()): ?>
        <h1 class="title"><?php printf(__('Posts tagged %s', 'mystique'), '<span class="alt">'.single_cat_title('', false).'</span>'); ?></h1>

        <?php elseif(is_day()): ?>
        <h1 class="title"><?php printf(__('Archive for %s', 'mystique'), '<span class="alt">'.get_the_date().'</span>'); ?></h1>

        <?php elseif(is_month()): ?>
        <h1 class="title"><?php printf(__('Archive for %s', 'mystique'), '<span class="alt">'.get_the_time('F, Y').'</span>'); ?></h1>

        <?php elseif(is_year()): ?>
        <h1 class="title"><?php printf(__('Archive for year %s', 'mystique'), '<span class="alt">'.get_the_time('Y').'</span>'); ?></h1>

        <?php else: ?>
        <h1 class="title"><?php _e('Blog Archives', 'mystique'); ?></h1>

        <?php endif; ?>

        <?php if(have_posts()): ?>

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

        <?php if(is_category()): ?>
        <h1 class="title"> <?php printf(__('Oops, there aren\'t any posts in the %s category yet :(', 'mystique'), single_cat_title('',false)); ?></h1>
        <?php elseif(is_date()): ?>
        <h1 class="title"> <?php _e('There aren\'t any posts within this date :(', 'mystique'); ?> </h1>
        <?php else: ?>
        <h1 class="title"><?php _e('Oops, nothing here :(', 'mystique'); ?></h1>
        <?php endif; ?>

        <?php endif; ?>

      </div>
      <!-- /primary content -->

      <?php get_sidebar(); ?>
    </div>
   </div>
  </div>
  <!-- /main content -->

<?php get_footer(); ?>
