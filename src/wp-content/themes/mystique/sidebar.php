<?php /* Mystique/digitalnature */ ?>

  <!-- 1st sidebar -->
  <div id="sidebar">

    <ul class="blocks">

      <?php if(!dynamic_sidebar('sidebar-1')): ?>

      <li class="block block-search">
        <?php get_search_form(); ?>
      </li>

      <li class="block">
        <div class="block-content block-archives clear-block" id="instance-archives">
          <div class="title">
            <h3 class="widget-title"><?php _e('Archives', 'mystique'); ?></h3>
            <div class="bl"></div><div class="br"></div>
          </div>
          <ul>
            <?php wp_get_archives( 'type=monthly' ); ?>
          </ul>
        </div>
      </li>

      <li class="block">
        <div class="block-content block-meta clear-block" id="instance-meta">
          <div class="title">
            <h3 class="widget-title"><?php _e('Meta', 'mystique'); ?></h3>
            <div class="bl"></div><div class="br"></div>
          </div>
          <ul>
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <?php wp_meta(); ?>
          </ul>
        </div>
      </li>

      <?php endif; // end primary widget area ?>
    </ul>

  </div>
  <!-- /1st sidebar -->
