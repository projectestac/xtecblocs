<?php get_header(); ?>

    <div id="content" class="widecolumn">

  <?php if (have_posts()) :
        while (have_posts()) :
            the_post(); ?>

        <div class="navigation">
            <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
            <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
        </div>

        <div class="post" id="post-<?php the_ID(); ?>">
            <h2><a href="<?php echo get_permalink() ?>" rel="bookmark"
                title="Enllaç permanent a <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

            <div class="entry">
                <?php the_content('<p class="serif">Llegeix la resta de l\'entrada &raquo;</p>'); ?>

                <?php wp_link_pages(['before' => '<p><strong>Pàgines:</strong> ', 'after' => '</p>']); ?>

                <p class="postmetadata alt">
                    <small>
                        Aquesta entrada es va publicar el <?php echo get_the_date(); ?> a les <?php the_time() ?>
                        i està classificada a <?php the_category(', ') ?>.
                        Pots seguir les respostes a aquesta entrada a través del canal
                            <?php post_comments_feed_link('RSS 2.0'); ?>.

                                <?php if ('open' === $post->comment_status && 'open' === $post->ping_status) {
                            // Both Comments and Pings are open ?>
                            Pots <a href="#respond">deixar una resposta</a> o fer un
                                <a href="<?php trackback_url(true); ?>" rel="trackback">retroenllaç</a>
                                des del teu lloc.

                                <?php } elseif ('open' !== $post->comment_status && 'open' === $post->ping_status) {
                            // Only Pings are Open ?>
                            Les respostes estan tancades, però pots fer un <a href="<?php trackback_url(true); ?> "
                                rel="trackback">retroenllaç</a> des del teu lloc.

                                <?php } elseif ('open' === $post->comment_status && 'open' !== $post->ping_status) {
                            // Comments are open, Pings are not ?>
                            Pots anar al final i deixar una resposta. Els retroenllaços estan desactivats.

                                <?php } elseif ('open' !== $post->comment_status && 'open' !== $post->ping_status) {
                            // Neither Comments, nor Pings are open ?>
                            Els comentaris i els retroenllaços estan tancats.

                                <?php } edit_post_link('Edita aquesta entrada.', '', ''); ?>

                    </small>
                </p>

            </div>
        </div>

                    <?php comments_template(); ?>

        <?php endwhile;
  else : ?>
        <p>No hi ha cap entrada que coincideixi amb la cerca.</p>

  <?php endif; ?>

    </div>

<?php get_footer(); ?>
