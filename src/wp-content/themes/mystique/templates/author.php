<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Author archive template.
// Templates with higher priority: author-nicename.php, author-id.php, author-role.php

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

          <div class="clear-block">

            <div class="alignleft">
              <?php atom()->author->Avatar($size = 192); ?>
            </div>

            <h1 class="title"><?php atom()->author->Name(); ?></h1>

            <div class="summary">
              <?php if(atom()->author->getKarma()): ?>
              <h4><?php atom()->te('%s karma', atom()->author->getKarma()); ?></h4>
              <?php endif; ?>
              <p><?php atom()->te('(%1$s comments, %2$s posts)', atom()->getCount(atom()->author->getID(), 'comment'), atom()->getCount(atom()->author->getID(), 'post')); ?></p>
            </div>

            <?php if(atom()->author->getDescription()): ?>
            <div class="author-description large">
              <?php atom()->author->Description(); ?>
            </div>

            <?php else: ?>
            <p class="large"><em><?php atom()->te("This user hasn't shared any profile information"); ?></em></p>
            <?php endif; ?>


            <?php if((atom()->author->get('user_url')) && (atom()->author->get('user_url')!== 'http://')): ?>
            <p class="im www">
              <?php atom()->te('Home page:'); ?> <a href="<?php echo atom()->author->get('user_url'); ?>"><?php echo atom()->author->get('user_url'); ?></a>
            </p>
            <?php endif; ?>

            <?php if(atom()->author->get('yim')): ?>
            <p class="im yahoo">
              Yahoo Messenger: <a href="ymsgr:sendIM?<?php echo atom()->author->get('yim'); ?>"><?php echo atom()->author->get('yim'); ?></a>
            </p>
            <?php endif; ?>

            <?php if(atom()->author->get('jabber')): ?>
            <p class="im gtalk">
              Jabber/GTalk: <a href="gtalk:chat?jid=<?php echo atom()->author->get('jabber'); ?>"><?php echo atom()->author->get('jabber'); ?></a>
            </p>
            <?php endif; ?>

            <?php if(atom()->author->get('aim')): ?>
            <p class="im aim">
              AIM: <a href="aim:goIM?screenname=<?php echo atom()->author->get('aim'); ?>"><?php echo atom()->author->get('aim'); ?></a>
            </p>
            <?php endif; ?>

          </div>

          <div class="divider"></div>

          <?php if(have_posts()): ?>
            <h5 class="title"><?php atom()->te('Posts by %s', atom()->author->getName()); ?></h5>

            <div class="posts clear-block">
              <?php while(have_posts()) atom()->template('teaser'); ?>
            </div>

            <?php atom()->pagination(); ?>

            <a class="rss-block alignright" rel="rss" href="<?php atom()->author->FeedURL(); ?>"><?php atom()->te("%s's RSS Feed", atom()->author->getName()); ?></a>

          <?php else: ?>
            <p><?php atom()->te("%s hasn't written any posts yet", atom()->author->getName()); ?></p>
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
