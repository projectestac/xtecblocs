<?php /* Mystique/digitalnature */ ?>

<?php get_header(); ?>

  <!-- main content: primary + sidebar(s) -->
  <div id="mask-3" class="clear-block">
    <div id="mask-2">
      <div id="mask-1">

        <!-- primary content -->
        <div id="primary-content">
          <div class="blocks clear-block">

            <h1 class="title error">404</h1>
            <p><?php _e('The requested page was not found.', 'mystique'); ?></p>

          </div>
        </div>
        <!-- /primary content -->

        <?php get_sidebar(); ?>

      </div>
    </div>
  </div>
  <!-- /main content -->

<?php get_footer(); ?>