<?php

/*
 * @template  Arclite
 * @revised   January 2, 2012
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User listing.
// This is a custom page template that can be applied to individual pages.


/* Template Name: User list */
?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('User list');

  atom()->template('header');

  $total_users = count_users();
  $total_users = $total_users['total_users'];

  $paged = get_query_var('paged');

  $number = atom()->post->getMeta('number');
  $number = $number ? (int)$number : 20;

  // note: if the blog_id argument is used, you must calculate the total_users variable above for that specific blog
  // (to do that - use switch_to_blog / restore_current_blog on the code above, and below on AtomObjectUser to retrieve accurate role)
  $users = get_users(array(
    'offset'       => $paged ? ($paged * $number) - $number : 0,    
    'count_total'  => false,
    'number'       => $number,
    'fields'       => 'all_with_meta',
  ));

  // post counts
  $user_ids = array();

  foreach($users as $user)
    $user_ids[] = $user->ID;

  $user_post_count = count_many_users_posts($user_ids);

  foreach($users as $id => $user){
    $user->post_count = $user_post_count[$user->ID];
    $users[$id] = atom()->user($user);
  }

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

              <table class="user-list">
                <thead>
                  <tr>
                    <th><?php atom()->te('Name'); ?></th>
                    <th><?php atom()->te('Role'); ?></th>
                    <th><?php atom()->te('Posts'); ?></th>
                    <th><?php atom()->te('Registered'); ?></th>
                  </tr>
                </thead>

                <tbody>
                  <?php $index = 0; ?>
                  <?php foreach($users as $user): ?>
                  <?php $index++; ?>
                  <tr class="<?php echo ($index % 2) ? 'even' : 'odd'; ?>">
                    <td class="clear-block">
                      <div class="alignleft">
                        <?php $user->Avatar(16); ?>
                      </div>
                      <div class="alignleft">
                        <strong><?php $user->NameAsLink(); ?></strong>
                      </div>
                    </td>
                    <td><?php $user->Role(); ?></td>
                    <td><?php $user->PostCount(); ?></td>
                    <td><?php atom()->timeSince(strtotime($user->get('user_registered'))); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

              <?php
                atom()->pagination(array(
                  'type'          => 'numbers',
                  'current_page'  => max(1, $paged),
                  'total_pages'   => ceil($total_users / $number),
                ));
              ?>
            </div>

            <?php atom()->post->pagination(); // (if post is multipart) might conflict with the user list pagination above? ?>

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
