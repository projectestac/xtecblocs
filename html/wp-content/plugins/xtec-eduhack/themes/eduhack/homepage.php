<?php
/*
 * Template Name: Home page
 * Template Post Type: page
 */

get_header();

/* Query the home page category posts */

$query = new WP_Query([
    'category_name' => 'homepage'
]);

?>

<!-- Page contents -->

<div class="content home-post">
  <?php if (have_posts()): ?>
    <?php while (have_posts()) : the_post(); ?>
      <div class="post single">
        <div class="post-inner">
          <div class="post-content">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

<!-- Category posts -->

<?php if ( $query->have_posts() ): ?>
 <div class="content">
   <div class="section-inner">
     <div class="category">
       <div class="posts" id="posts">
         <?php while ( $query->have_posts() ): $query->the_post(); ?>
           <?php get_template_part('content', get_post_format()); ?>
         <?php endwhile; ?>
       </div>
     </div>
   </div>
 </div>
<?php endif; ?>

<?php get_footer(); ?>