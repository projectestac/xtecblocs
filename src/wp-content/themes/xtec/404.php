<?php ob_start(); ?>

<?php get_header(); ?>

	<!-- sidebar -->
	<?php get_sidebar(); ?>
	<div id="content">
	
		<div class="post" id="post-none">
			<h2 class="center"><?php _e('Error 404','xtec'); ?></h2>
			<p class="center"><?php _e("Sorry, but you are looking for something that is not here",'xtec'); ?></p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
		</div>
		
	</div>
	
	<hr/>

	<br style="clear:both" /><!-- without this little <br /> NS6 and IE5PC do not stretch the frame div down to encopass the content DIVs -->
</div>
<!-- footer -->
<?php get_footer(); ?>

<? ob_end_flush();?>