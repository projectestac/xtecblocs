<div class="post-container">
  <div id="post-<?php the_ID(); ?>"
       <?php post_class( is_sticky() ? 'sticky' : null ); ?>>

    <!-- Post thumbnail -->

    <?php if ( has_post_thumbnail() ): ?>
      <a class="featured-media" title="<?php the_title_attribute(); ?>"
         href="<?php the_permalink(); ?>">
        <?php the_post_thumbnail('post-thumb'); ?>
      </a>
    <?php endif; ?>

    <?php $post_title = get_the_title(); ?>

    <!-- Post title -->

    <?php if ( !empty( $post_title ) ): ?>
      <div class="post-header">
        <h2 class="post-title">
          <a href="<?php the_permalink(); ?>"
             title="<?php the_title_attribute(); ?>">
            <?php the_title(); ?>
          </a>
        </h2>
      </div>
    <?php endif; ?>

    <!-- Post excerpt -->

    <div class="post-excerpt">
      <?php the_excerpt(); ?>
    </div>

    <!-- Categories and tags -->

    <div class="post-terms">
      <?php if ( has_category() ): ?>
        <span title="<?php _e( 'Categories' ) ?>"
              class="entry-categories"><?php the_category( ' / ' ); ?></span>
      <?php endif; ?>
      <?php if ( has_tag() ): ?>
        <span title="<?php _e( 'Tags' ) ?>"
              class="entry-tags"><?php the_tags( '', ' / ', '' ); ?></span>
      <?php endif; ?>
    </div>

  </div>
</div>