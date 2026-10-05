<?php get_header(); ?>

    <div id="content" class="narrowcolumn">

        <?php if (have_posts()) : ?>
            <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
            <?php if (is_category()) : // If this is a category archive ?>
                <h2 class="pagetitle">Archive for the '<?php echo single_cat_title(); ?>' Category</h2>
            <?php elseif (is_day()) : // If this is a daily archive ?>
                <h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
            <?php elseif (is_month()) : // If this is a monthly archive ?>
                <h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
            <?php elseif (is_year()) : // If this is a yearly archive ?>
                <h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
            <?php elseif (is_search()) : // If this is a search ?>
                <h2 class="pagetitle">Search Results</h2>
            <?php elseif (is_author()) : // If this is an author archive ?>
                <h2 class="pagetitle">Author Archive</h2>
            <?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : // If this is a paged archive ?>
                <h2 class="pagetitle">Blog Archives</h2>
            <?php endif; ?>


        <div class="navigation">
            <div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
            <div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
        </div>

            <?php while (have_posts()) :
                the_post(); ?>
        <div class="post">
                <h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
                <small><?php the_time('l, F jS, Y') ?></small>

                <div class="entry">
                    <?php the_content() ?>
                </div>

                <p class="postmetadata">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p> 

            </div>

            <?php endwhile; ?>

        <div class="navigation">
            <div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
            <div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
        </div>

        <?php else : ?>
        <h2 class="center">Not Found</h2>
            <?php get_search_form(); ?>

        <?php endif; ?>

    </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
