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

          <div class="post-links clear-block">
            <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
            <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
          </div>

          <!-- post -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!get_post_meta($post->ID, 'hide_title', true)): ?>
            <h1 class="title"><?php the_title(); ?></h1>
            <?php endif; ?>

            <div class="post-content clear-block">
             <?php the_content(); ?>
            </div>

            <?php // we need the pagination markup to match the others (page-navi, comment pages etc)
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

            <?php if($tags = get_the_tag_list('', ' ')): ?>
            <div class="post-tags clear-block">
              <?php echo $tags; ?>
            </div>
            <?php endif; ?>


            <?php if(!post_password_required()): ?>

            <div class="post-meta">

              <div class="details">
                <p>
                <?php
                  printf(__('This entry was posted by %1$s on %2$s at %3$s, and is filed under %4$s. Follow any responses to this post through %5$s.', 'mystique'), '<a href="'.get_author_posts_url(get_the_author_meta('ID')).'" title="'.sprintf(__('Posts by %s', 'mystique'), esc_attr(get_the_author())).' ">'.get_the_author().'</a>', get_the_time(get_option('date_format')), get_the_time(get_option('time_format')), get_the_category_list(', '), '<a href="'.get_post_comments_feed_link($post->ID).'" title="RSS 2.0">RSS 2.0</a>');
                ?>

                <?php
                 if((comments_open()) && pings_open()) // both comments and pings are open
                   printf(__('You can <a%1$s>leave a response</a> or <a%2$s>trackback</a> from your own site.', 'mystique'), ' href="#commentform"',' href="'.get_trackback_url().'" rel="trackback"');
                 elseif(!comments_open() && pings_open()) // only pings are open
                   printf(__('Responses are currently closed, but you can <a%1$s>trackback</a> from your own site.', 'mystique'), ' href="'.get_trackback_url().'" rel="trackback"');
                 elseif(comments_open() && !pings_open()) // comments are open, pings are closed
                   _e('You can skip to the end and leave a response. Pinging is currently not allowed.', 'mystique');
                 else // neither comments, nor pings are open
                   _e('Both comments and pings are currently closed.', 'mystique');
                ?>
                </p>
               </div>

            </div>
            <?php endif; ?>

            <div class="controls">
              <?php edit_post_link(__('Edit', 'mystique')); ?>
            </div>

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