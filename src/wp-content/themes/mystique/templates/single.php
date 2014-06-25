<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Singular template, used to display a single post.
// For custom post types, a template named single-post_type.php will have priority over this one.

?>

<?php atom()->template('header'); ?>

<!-- main content: primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php atom()->action('before_primary'); ?>

          <?php if(atom()->options('single_links')): ?>
          <div class="post-links clear-block">
            <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
            <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
          </div>
          <?php endif; ?>

          <?php atom()->action('before_post'); ?>

          <!-- post content -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!atom()->post->getMeta('hide_title')): ?>
            <h1 class="title"><?php atom()->post->Title(); ?></h1>
            <?php endif; ?>

            <div class="post-content clear-block">
              <?php the_content(); ?>
            </div>

            <?php atom()->post->pagination(); ?>

            <?php if(atom()->post->getTerms()): ?>
            <div class="post-extra clear-block">
              <div class="post-tags">
                <?php atom()->post->Terms(); ?>
              </div>
            </div>
            <?php endif; ?>

            <?php if(!post_password_required()): ?>
            <div class="post-meta">

                <?php if(atom()->options('single_share')) atom()->post->ShareLinks(); ?>

                <?php if(atom()->options('single_meta')): ?>
                <div class="details">
                  <p>
                    <?php

                     atom()->te('This entry was posted by %1$s on %2$s at %3$s, and is filed under %4$s. Follow any responses to this post through %5$s.',
                       atom()->post->author->getNameAsLink(),
                       atom()->post->getDate(get_option('date_format')),
                       atom()->post->getDate(get_option('time_format')),
                       atom()->post->getTerms('category', ', '),
                       sprintf('<a href="%s" title="RSS 2.0">RSS 2.0</a>', get_post_comments_feed_link())
                     );

                     if(comments_open() && pings_open())
                       atom()->te('You can <a%1$s>leave a response</a> or <a%2$s>trackback</a> from your own site.', ' href="#commentform"',' href="'.get_trackback_url().'" rel="trackback"');

                     elseif(!comments_open() && pings_open())
                       atom()->te('Responses are currently closed, but you can <a%1$s>trackback</a> from your own site.', ' href="'.get_trackback_url().'" rel="trackback"');

                     elseif(comments_open() && !pings_open())
                       atom()->te('You can skip to the end and leave a response. Pinging is currently not allowed.');

                     elseif(!comments_open() && !pings_open())
                       atom()->te('Both comments and pings are currently closed.');
                    ?>
                  </p>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>

            <?php atom()->controls('post-edit', 'post-print'); ?>

          </div>
          <!-- /post content -->

          <?php atom()->action('after_post'); ?>

          <?php atom()->template('meta'); ?>

          <?php atom()->action('after_primary'); ?>

        </div>
      </div>
      <!-- /primary content -->

      <?php atom()->template('sidebar'); ?>

    </div>
  </div>
</div>
<!-- /main content -->

<?php atom()->template('footer'); ?>
