<?php
/**
 * HeBlogs 파비콘
 * 테마 images 폴더의 favicon.ico / favicon.png를 자동으로 사이트 파비콘으로 사용
 */

/**
 * head에 파비콘 링크 출력
 * images/favicon.ico 또는 images/favicon.png 우선 사용
 */
function heblogs_favicon() {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    if (file_exists($dir . '/assets/images/favicon.ico')) {
        echo '<link rel="icon" href="' . esc_url($uri . '/assets/images/favicon.ico') . '" type="image/x-icon">' . "\n";
        return;
    }
    if (file_exists($dir . '/assets/images/favicon.png')) {
        echo '<link rel="icon" href="' . esc_url($uri . '/assets/images/favicon.png') . '" type="image/png">' . "\n";
    }
}
add_action('wp_head', 'heblogs_favicon', 2);
