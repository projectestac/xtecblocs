<?php get_header(); ?>

<div class="content">

	<article class="post single entry">
	
		<div class="post-inner section-inner thin">
		                
		<header class="post-header">
			<h1 class="post-title"><?php _e( 'Error 404', 'fukasawa' ); ?></h1>
		</header><!-- .post-header -->
	                                                	            
        <div class="post-content entry-content">
        	            
            <p><?php _e( "It seems like you have tried to open a page that doesn't exist. It could have been deleted, moved, or it never existed at all. You are welcome to search for what you are looking for with the form below.", 'fukasawa' ); ?></p>
            
            <?php get_search_form(); ?>
            
        </div><!-- .post-content -->
        	            	                        	
	</article><!-- .post -->
	
</div><!-- .content -->

<?php get_footer(); ?>
