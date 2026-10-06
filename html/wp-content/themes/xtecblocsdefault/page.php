<?php get_header(); ?>

    <div id="content" class="narrowcolumn">

    <?php if (have_posts()) :
        while (have_posts()) :
            the_post(); ?>
        <div class="post" id="post-<?php the_ID(); ?>">
        <h2><?php the_title(); ?></h2>
            <div class="entry">
                <?php the_content('<p class="serif">Llegeix la resta de la pàgina &raquo;</p>'); ?>

                <?php wp_link_pages(['before' => '<p><strong>Pàgines:</strong> ', 'after' => '</p>']); ?>

            </div>
        </div>
        <?php endwhile;
    endif; ?>
    <?php edit_post_link('Edita aquesta pàgina.', '<p>', '</p>'); ?>
    </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
