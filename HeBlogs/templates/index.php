<?php
/**
 * 기본 템플릿 (fallback)
 */
heblogs_header(); ?>

<div id="content" class="site-content">
    <main id="main" class="site-main">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title post-title"><?php the_title(); ?></h1>
                    </header>
                    <div class="entry-content post-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <article class="post">
                <header class="entry-header">
                    <h1 class="entry-title post-title"><?php esc_html_e('콘텐츠를 찾을 수 없습니다', 'heblogs'); ?></h1>
                </header>
                <div class="entry-content post-content">
                    <p><?php esc_html_e('요청하신 콘텐츠를 찾을 수 없습니다.', 'heblogs'); ?></p>
                </div>
            </article>
        <?php endif; ?>
    </main>
</div>

<?php heblogs_footer(); ?>
