<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

<div id="content">
<div class="entry">


<h2><?php _e('Archives by Month:','big-blue');?></h2>
	<ul>
		<?php wp_get_archives('type=monthly'); ?>
	</ul>

<h2><?php _e('Archives by Subject:','big-blue');?></h2>
	<ul>
		 <?php wp_list_categories(); ?>
	</ul>

</div></div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
