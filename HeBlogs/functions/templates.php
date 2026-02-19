<?php
/**
 * 템플릿 로더 – 모든 페이지 템플릿을 templates/ 디렉터리에서 로드
 * 루트에 개별 템플릿 파일 없이 한 곳에서 처리
 */

/**
 * 메인 템플릿 경로를 templates/ 로 연결
 */
function heblogs_template_include($template) {
    $dir = get_template_directory() . '/templates/';

    if (is_404()) {
        $t = $dir . '404.php';
    } elseif (is_single()) {
        $t = $dir . 'single.php';
    } elseif (is_category()) {
        $t = $dir . 'category.php';
    } elseif (is_home()) {
        // 최신글 목록(블로그 인덱스). 정적 프론트일 때는 else에서 index.php로 처리
        $t = $dir . 'home.php';
    } else {
        $t = $dir . 'index.php';
    }

    return file_exists($t) ? $t : $template;
}
add_filter('template_include', 'heblogs_template_include', 99);

/**
 * 헤더 출력 – templates/header.php 로드 (get_header 대체)
 */
function heblogs_header() {
    load_template(get_template_directory() . '/templates/header.php');
}

/**
 * 푸터 출력 – templates/footer.php 로드 (get_footer 대체)
 */
function heblogs_footer() {
    load_template(get_template_directory() . '/templates/footer.php');
}
