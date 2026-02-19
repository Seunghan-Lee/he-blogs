<?php
/**
 * 블로그 포스트 목록 (최신글)
 */
heblogs_header(); ?>

<div id="content" class="site-content">
    <main id="main" class="site-main">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                    <header class="entry-header">
                        <h2 class="entry-title post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="entry-meta">
                            <?php the_date(); ?> · <?php the_author(); ?>
                        </div>
                    </header>
                    <div class="entry-content post-content">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <article class="post">
                <header class="entry-header">
                    <h1 class="entry-title post-title"><?php esc_html_e('글이 없습니다.', 'heblogs'); ?></h1>
                </header>
                <div class="entry-content post-content">
                    <p><?php esc_html_e('아직 작성된 글이 없습니다.', 'heblogs'); ?></p>
                </div>
            </article>
        <?php endif; ?>
    </main>
</div>

<?php heblogs_footer(); ?>
