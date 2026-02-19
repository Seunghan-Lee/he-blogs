<?php
/**
 * 카테고리 아카이브
 */
heblogs_header(); ?>

<div id="content" class="site-content">
    <div class="content-wrapper">
        <main id="main" class="site-main">
            <header class="archive-header">
                <h1 class="archive-title"><?php single_cat_title(); ?></h1>
                <?php if (category_description()) : ?>
                    <div class="archive-description"><?php echo category_description(); ?></div>
                <?php endif; ?>
            </header>

            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                        <header class="entry-header">
                            <h2 class="entry-title post-title">
                                <a href="<?php the_permalink(); ?>"><?php heblogs_the_title(); ?></a>
                            </h2>
                            <div class="entry-meta">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                <span class="meta-separator">·</span>
                                <span class="author"><?php the_author(); ?></span>
                            </div>
                        </header>
                        <div class="entry-content post-content">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
                <nav class="pagination-wrapper">
                    <?php
                    the_posts_pagination(array(
                        'mid_size'  => 2,
                        'prev_text' => __('이전', 'heblogs'),
                        'next_text' => __('다음', 'heblogs'),
                    ));
                    ?>
                </nav>
            <?php else : ?>
                <article class="post">
                    <header class="entry-header">
                        <h1 class="entry-title post-title"><?php esc_html_e('이 카테고리에 글이 없습니다.', 'heblogs'); ?></h1>
                    </header>
                    <div class="entry-content post-content">
                        <p><?php esc_html_e('요청한 카테고리에 작성된 글이 없습니다.', 'heblogs'); ?></p>
                    </div>
                </article>
            <?php endif; ?>
        </main>

        <?php if (is_active_sidebar('sidebar-1')) : ?>
            <aside id="secondary" class="widget-area sidebar">
                <?php dynamic_sidebar('sidebar-1'); ?>
            </aside>
        <?php endif; ?>
    </div>
</div>

<?php heblogs_footer(); ?>
