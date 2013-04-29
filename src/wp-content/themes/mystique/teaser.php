<?php /* Mystique/digitalnature */ ?>

  <!-- post -->
  <div id="post-<?php the_ID(); ?>" <?php post_class('clear-block thumb-left'); ?>>

    <div class="post-details">

       <?php if(has_post_thumbnail()): ?>
       <a class="post-thumb" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" > <?php the_post_thumbnail(); ?> </a>
       <?php endif; ?>

       <h2 class="title"><a href="<?php the_permalink(); ?>" title="<?php printf(__('Permalink to %s', 'mystique'), the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

       <?php if(comments_open()): ?>
       <a class="comments" href="<?php the_permalink(); ?>#comments"><?php echo $post->comment_count; ?></a>
       <?php endif; ?>

       <div class="post-std clear-block">

         <div class="post-date"><span class="ext"><?php echo human_time_diff(get_the_time('U')); ?></span></div>

         <div class="post-info">

           <span class="a">
            <?php
              printf(__('by %s', 'mystique'),
                sprintf(
	             '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
	               get_author_posts_url(get_the_author_meta('ID'), get_the_author()),
	               esc_attr(sprintf(__( 'Posts by %s', 'mystique'), get_the_author())),
	               get_the_author()
	             ));
            ?>
           </span>

           <?php if(count(get_the_category())) printf(__('in %s', 'mystique'), get_the_category_list(', ')); ?>
         </div>
       </div>


       <div class="post-content clear-block">
         <?php the_content(); ?>
       </div>

       <?php if($tags = get_the_tag_list('', ' ')): ?>
       <div class="post-tags clear-block">
         <?php echo $tags; ?>
       </div>
       <?php endif; ?>

    </div>

    <div class="controls"> <?php edit_post_link(); ?> </div>
  </div>
  <!-- /post -->
