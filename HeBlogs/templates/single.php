<?php
/**
 * 단일 포스트
 */
heblogs_header(); ?>

<div id="content" class="site-content">
    <main id="main" class="site-main">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title post-title"><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <?php the_date(); ?> · <?php the_author(); ?>
                    </div>
                </header>
                <div class="entry-content post-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
            the_post_navigation();
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </main>
</div>

<?php heblogs_footer(); ?>
