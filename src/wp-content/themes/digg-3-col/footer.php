<?php get_sidebar(); ?>

</div></div><!-- End pagewrapper and page classes -->

</div><!-- End container id -->
		<?php $current_site = get_current_site(); ?>
		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
<?php wp_footer(); ?>

</body>
</html>
