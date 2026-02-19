<?php
/**
 * 404 Not Found
 */
heblogs_header(); ?>

<div id="content" class="site-content">
    <main id="main" class="site-main">
        <article class="post post-404">
            <header class="entry-header">
                <h1 class="entry-title post-title"><?php esc_html_e('페이지를 찾을 수 없습니다', 'heblogs'); ?></h1>
            </header>
            <div class="entry-content post-content">
                <p><?php esc_html_e('요청하신 주소의 페이지가 없거나 이동되었을 수 있습니다.', 'heblogs'); ?></p>
                <p><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('홈으로 돌아가기', 'heblogs'); ?></a></p>
            </div>
        </article>
    </main>
</div>

<?php heblogs_footer(); ?>
