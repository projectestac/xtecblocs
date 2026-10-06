<?php get_header(); ?>

    <div id="content" class="narrowcolumn">

        <?php if (have_posts()) : ?>
            <h2 class="pagetitle"><?php the_archive_title(); ?></h2>


        <div class="navigation">
            <div class="alignleft"><?php next_posts_link('&laquo; Entrades anteriors') ?></div>
            <div class="alignright"><?php previous_posts_link('Entrades següents &raquo;') ?></div>
        </div>

            <?php while (have_posts()) :
                the_post(); ?>
        <div class="post">
                <h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark"
                    title="Enllaç permanent a <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
                <small><?php echo get_the_date(); ?></small>

                <div class="entry">
                    <?php the_content() ?>
                </div>

                <p class="postmetadata">Publicat a <?php the_category(', ') ?> |
                    <?php edit_post_link('Edita', '', ' | '); ?>
                    <?php comments_popup_link(
                        'Cap comentari &#187;',
                        '1 comentari &#187;',
                        '% comentaris &#187;'
                    ); ?></p> 

            </div>

            <?php endwhile; ?>

        <div class="navigation">
            <div class="alignleft"><?php next_posts_link('&laquo; Entrades anteriors') ?></div>
            <div class="alignright"><?php previous_posts_link('Entrades següents &raquo;') ?></div>
        </div>

        <?php else : ?>
        <h2 class="center">No s'ha trobat</h2>
            <?php get_search_form(); ?>

        <?php endif; ?>

    </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
