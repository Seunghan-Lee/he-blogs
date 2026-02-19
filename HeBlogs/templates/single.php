<?php
/**
 * 단일 포스트
 */
heblogs_header(); ?>

<div id="content" class="site-content">
    <div class="content-wrapper">
        <main id="main" class="site-main">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post post-single'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title post-title"><?php heblogs_the_title(); ?></h1>
                        <div class="entry-meta">
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                        </div>
                    </header>
                    <div class="divider"></div>
                    <div class="entry-content post-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
                the_post_navigation(array(
                    'prev_text' => __('이전 글', 'heblogs'),
                    'next_text' => __('다음 글', 'heblogs'),
                ));
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
            <?php endwhile; ?>
        </main>

        <?php if (is_active_sidebar('sidebar-1')) : ?>
            <aside id="secondary" class="widget-area sidebar">
                <?php dynamic_sidebar('sidebar-1'); ?>
            </aside>
        <?php endif; ?>
    </div>
</div>

<?php heblogs_footer(); ?>
