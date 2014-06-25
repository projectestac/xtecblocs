<?php

/*
 * @template  Mystique
 * @revised   December 23, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// All blog posts grouped by month - year.
// This is a custom page template that can be applied to individual pages.


/* Template Name: Blog archives grouped by month */

?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('Blog archives grouped by month');

  atom()->template('header');

  global $wp_locale, $post;

  $month = $year = 0;

  $post_type = atom()->post->getMeta('post_type');
  $limit = (int)atom()->post->getMeta('limit');

  $archives = new WP_Query(array(
    'post_type'           => ($post_type !== false) && post_type_exists($post_type) ? $post_type : 'post',
    'ignore_sticky_posts' => true,
    'posts_per_page'      => $limit ? $limit : -1,
    'orderby'             => 'date',
    'order'               => 'DESC',
  ));

?>

<!-- main content: primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php atom()->action('before_primary'); ?>

          <?php the_post(); ?>

          <?php atom()->action('before_post'); ?>

          <!-- post -->
          <article id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!atom()->post->getMeta('hide_title')): ?>
            <h1 class="title"><?php the_title(); ?></h1>
            <?php endif; ?>

            <div class="clear-block">
              <?php the_content(); ?>

              <?php while($archives->have_posts()): ?>

                <?php
                  atom()->resetCurrentPost();
                  $archives->the_post();
                  $time = array('month' => get_the_date('n'), 'year' => get_the_date('Y'));

                  // display title if the month or year have changed
                  if(($month !== $time['month']) || ($year !== $time['year'])){

                    // close list if this is not the first section (there were items before)
                    if(($month !== 0) && ($year !== 0))
                      echo '</ul>';

                    extract($time);

                    echo '<h2 class="title">'.wptexturize(atom()->t('%1$s %2$d', $wp_locale->get_month($month), $year)).'</h2>';
                    echo '<ul class="posts">';
                  }

                ?>
                <li>
                  <p>
                    <?php atom()->post->date(atom()->t('D jS')); ?>: <a href="<?php atom()->post->URL(); ?>"><?php atom()->post->Title(70); ?></a> (<?php atom()->post->CommentCount(); ?>)
                  </p>
                </li>

              <?php endwhile; ?>

              <?php atom()->resetCurrentPost(); ?>

            </div>

            <?php atom()->post->pagination(); ?>

            <?php atom()->controls('post-edit'); ?>

          </article>
          <!-- /post -->

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
