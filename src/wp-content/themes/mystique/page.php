<?php /* Mystique/digitalnature */ ?>

<?php get_header(); ?>

  <!-- main content: primary + sidebar(s) -->
  <div id="mask-3" class="clear-block">
    <div id="mask-2">
      <div id="mask-1">

        <!-- primary content -->
        <div id="primary-content">

          <?php if(have_posts()): ?>

          <?php while(have_posts()): ?>

          <?php the_post(); ?>

          <!-- post -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!get_post_meta($post->ID, 'hide_title', true)): ?>
            <h1 class="title"><?php the_title(); ?></h1>
            <?php endif; ?>

            <div class="clear-block">
              <?php the_content(); ?>
            </div>

            <?php
              $pages =
                wp_link_pages(array(
                 'before'         => '<div class="page-navi clear-block"><span class="pages">'.__('Pages &raquo;', 'mystique').'</span>',
                 'after'          => '</div>',
                 'link_before'    => '<span class="current">',
                 'link_after'     => '</span>',
                 'next_or_number' => 'number',
                 'echo'           => 0,
                ));

              // remove the <span class="current> & </span> tags (that we added above) from inside links
              if($pages) echo preg_replace('@\<a([^>]*)>\<span([^>]*)>(.*?)\<\/span>@i', '<a$1>$3', $pages);
            ?>

            <div class="controls"> <?php edit_post_link(); ?> </div>

          </div>
          <!-- /post -->

          <?php endwhile; ?>


          <?php comments_template(); ?>

          <?php else: ?>
          <h1 class="title error"><?php _e('Oops, nothing here :(', 'mystique'); ?></h1>
          <?php endif; ?>


        </div>
        <!-- /primary content -->

        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>
  <!-- /main content -->

<?php get_footer(); ?>
