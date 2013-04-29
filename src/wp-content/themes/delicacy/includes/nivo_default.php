            <div class="slider-wrapper theme-default">
                <div id="slider" class="nivoSlider">
				<?php
				
					$tmp = $wp_query;
                    $variant = of_get_option('delicacy_slider_variant');
                    $limit = (int)of_get_option('delicacy_slider_limit');
                    if( $limit ) :
                        $limit = (int)of_get_option('delicacy_slider_limit');                        
                    else:
                        $limit = 4;
                    endif;
                    if( $variant == '1') :
                        $cat = of_get_option('slider_category');
                        $wp_query = new WP_Query(array( 'posts_per_page' => $limit, 'cat' => $cat, 'ignore_sticky_posts' => 1 ) );
                    else :
                        $wp_query = new WP_Query( array( 'posts_per_page' => $limit , 'post_type' => 'post', 'ignore_sticky_posts' => 1 ) );
                    endif;
                    
					if(have_posts()) :
					    while(have_posts()) :
					        the_post();
					?>
                    <?php if(wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'nivo-thumb')) { 
                      $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'nivo-thumb');
                    }else {
                      $image[0] = get_template_directory_uri()."/images/nivo-thumb-placeholder.png";
                    } 
                    ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>" title="#htmlcaption_<?php the_ID(); ?>" /></a>

					<?php
						endwhile;
					?>
					<?php endif; ?>
				</div><!-- close #slider -->

                <?php
                    if(have_posts()) :
                        while(have_posts()) :
                            the_post();
                    ?>
                    <div id="htmlcaption_<?php the_ID(); ?>"  class="nivo-html-caption">
                        <p><?php the_time( get_option('date_format') ); ?></p>
                        <h2><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
                    </div>
                    <?php
                        endwhile;
                    ?>
                    <h3 class="block-title"><?php _e('Newest','delicacy')?></h3>
                    <?php endif;
                    $wp_query = $tmp;
                    ?>
            </div>
