<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Display one post per category + 5 links to the next 5 posts.
// This is a custom page template that can be applied to individual pages.


/* Template Name: Categories */

?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('Categories');

  atom()->template('header');

  // if a different taxonomy is used, replace the 'category' argument below with the taxonomy slug
  $terms = get_terms($tax = 'category');

  // get the posts
  $main_posts = $next_posts = array();
  foreach($terms as $term){

    $results = get_posts(array(
       'numberposts'           => 6,              // one main post per category + 5 titles
       'category'              => $term->term_id, // the category ID
      ));

    // main post
    if($results)
      $main_posts[$term->term_id] = array_shift($results);

    // if we have more, retrieve title and url for additional posts
    if($results)
      foreach($results as $post){
        atom()->post = $post; // sets up post data (stupid WP globals)
        $next_posts[$term->term_id][] = array(
          'title' => atom()->post->getTitle(),
          'url'   => atom()->post->getURL(),
        );
      }

  }

  atom()->resetCurrentPost();

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

          <!-- user page content -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!atom()->post->getMeta('hide_title')): ?>
            <h1 class="title"><?php the_title(); ?></h1>
            <?php endif; ?>

            <div class="clear-block">
              <?php the_content(); ?>
            </div>

            <?php atom()->post->pagination(); ?>

            <?php atom()->controls('post-edit'); ?>
          </div>
          <!-- /user page content -->

          <?php atom()->action('after_post'); ?>

          <?php if(empty($main_posts)): // we don't have any posts, display the message ?>
          <h2 class="title error"><?php atom()->te('Oops, nothing here :('); ?></h2>

          <?php else: ?>

          <?php foreach($terms as $term): ?>

          <?php if(empty($main_posts[$term->term_id])) continue; ?>

          <h5 class="title"><?php echo $term->name; ?></h5>

          <?php atom()->post = $main_posts[$term->term_id]; // sets up the post data ?>

          <?php atom()->action('before_post'); ?>

          <!-- post -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('thumb-left'); ?>>

            <a class="post-thumb" href="<?php atom()->post->URL(); ?>" title="<?php atom()->post->title(); ?>">
              <?php atom()->post->thumbnail(); ?>
            </a>

            <!-- next posts -->
            <?php if(!empty($next_posts[$term->term_id])): ?>
            <div class="block alignright" style="width: 200px;">
              <ul class="menu fadeThis">
                <?php foreach($next_posts[$term->term_id] as $post): ?>
                <li><a href="<?php echo $post['url']; ?>"><?php echo $post['title']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php endif; ?>
            <!-- //next posts -->

            <h2 class="title"><a href="<?php atom()->post->URL(); ?>"><?php atom()->post->title(); ?></a></h2>

            <div class="post-details clear-block">
              <p>
                <?php
                   // main post content
                   atom()->post->content($limit = 500, array(
                     'cutoff'              => 'word',
                     'more_inline'         => true,
                     'allowed_tags'        => 'a,abbr,acronym,b,cite,code,del,dfn,em,i,ins,q,strong,sub,sup', // inline tags only, because the space is limited
                   ));
                ?>
              </p>
            </div>

            <?php atom()->controls('post-edit'); ?>
          </div>
          <!-- /post -->

          <?php atom()->action('after_post'); ?>

          <?php atom()->resetCurrentPost(); ?>

          <?php endforeach; ?>

          <?php endif; ?>

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
