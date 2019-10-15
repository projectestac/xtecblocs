<?php
/*
 * Template Name: Exercise
 * Template Post Type: post, page
 */

get_header();

?>

<div class="content thin eduhack-exercise">

  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php
    
      $post_links = ehth_category_links();
      $post_id = get_the_ID();
    
    ?>
    
    <!-- Exercise header -->
    
    <div class="exercise-header">
      <div class="exercise-categories">
        <?php ehth_cateogry_image() ?>
        <?php the_category(' / ') ?>
      </div>
      <div class="exercise-pagination">
        <?php if ( count($post_links) ): ?>
          <ul class="category-pages">
            <?php foreach ($post_links as $index => $link): ?>
              <li <?= ($link['id'] == $post_id) ? 'class="active"' : ''; ?>>
                <a href="<?= $link['href'] ?>" title="<?= $link['title'] ?>">
                  <?= 1 + $index ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Exercise contents -->
    
    <div id="post-<?php the_ID(); ?>" <?php post_class('single'); ?>>

      <?php $post_format = get_post_format(); ?>
      
      <?php if ( $post_format == 'video' ) : ?>
      
        <?php if ($pos=strpos($post->post_content, '<!--more-->')): ?>
    
          <div class="featured-media">
          
            <?php
                
              // Fetch post content
              $content = get_post_field( 'post_content', get_the_ID() );
              
              // Get content parts
              $content_parts = get_extended( $content );
              
              // oEmbed part before <!--more--> tag
              $embed_code = wp_oembed_get($content_parts['main']); 
              
              echo $embed_code;
            
            ?>
          
          </div> <!-- /featured-media -->
        
        <?php endif; ?>
        
      <?php elseif ( $post_format == 'gallery' ) : ?>
      
        <div class="featured-media">  
  
          <?php fukasawa_flexslider('post-image'); ?>
          
          <div class="clear"></div>
          
        </div> <!-- /featured-media -->
              
      <?php elseif ( has_post_thumbnail() ) : ?>
          
        <div class="featured-media">
    
          <?php the_post_thumbnail('post-image'); ?>
          
        </div> <!-- /featured-media -->
          
      <?php endif; ?>
      
      <div class="post-inner">
        
        <div class="post-header">
                          
          <h1 class="post-title"><?php the_title(); ?></h1>
                              
        </div> <!-- /post-header -->
            
          <div class="post-content">
          
            <?php 
            if ($post_format == 'video') { 
              $content = $content_parts['extended'];
              $content = apply_filters('the_content', $content);
              echo $content;
            } else {
              the_content();
            }
          ?>
          
          </div> <!-- /post-content -->
          
          <div class="clear"></div>
        
        <div class="post-meta-bottom">
        
          <?php 
              $args = array(
              'before'           => '<div class="clear"></div><p class="page-links"><span class="title">' . __( 'Pages:','fukasawa' ) . '</span>',
              'after'            => '</p>',
              'link_before'      => '<span>',
              'link_after'       => '</span>',
              'separator'        => '',
              'pagelink'         => '%',
              'echo'             => 1
            );
            
              wp_link_pages($args); 
          ?>
          
          <div class="clear"></div>
          
        </div> <!-- /post-meta-bottom -->
      
      </div> <!-- /post-inner -->
      
      <!-- Post navigation -->
      
      <?php

        $next_link = null;
        $prev_link = null;
        
        foreach ( $post_links as $i => $link ) {
            if ( $link['id'] == $post_id ) {
                if ( key_exists($i + 1, $post_links) ) {
                    $next_link = $post_links[$i + 1];
                }
                
                if ( key_exists($i - 1, $post_links) ) {
                    $prev_link = $post_links[$i - 1];
                }
                break;
            }
        }

      ?>
      
      <div class="post-navigation">
        <?php if (!is_null( $prev_link )): ?>
          <a class="post-nav-prev" href="<?= $prev_link['href'] ?>">
            <p>&larr; <?php _e('Previous step', 'xtec-eduhack'); ?></p>
            <p><?= $prev_link['title'] ?></p>
          </a>
        <?php endif; ?>
        
        <?php if (!is_null( $next_link )): ?>
          <a class="post-nav-next" href="<?= $next_link['href'] ?>">
            <p><?php _e('Next step', 'xtec-eduhack'); ?> &rarr;</p>
            <p><?= $next_link['title'] ?></p>
          </a>
        <?php endif; ?>
      </div>

    </div> <!-- /post -->

     <?php endwhile; else: ?>

    <p><?php _e("We couldn't find any posts that matched your query. Please try again.", "fukasawa"); ?></p>
  
  <?php endif; ?>    

</div> <!-- /content -->
    
<?php get_footer(); ?>