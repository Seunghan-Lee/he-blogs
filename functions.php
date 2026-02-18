<?php
/**
 * HeBlogs 테마 함수
 * 블로그 전용 워드프레스 테마
 */

// 테마 설정
if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

/**
 * 테마 설정
 */
function thevoid_setup() {
    // 자동 피드 링크 추가
    add_theme_support('automatic-feed-links');

    // 제목 태그 지원
    add_theme_support('title-tag');

    // 포스트 썸네일 지원
    add_theme_support('post-thumbnails');

    // HTML5 마크업 지원
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // 커스텀 로고 지원
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'heblogs_setup');  

/**
 * 스타일시트 등록
 */
function heblogs_scripts() {
    wp_enqueue_style('heblogs-style', get_stylesheet_uri(), array(), _S_VERSION);
}
add_action('wp_enqueue_scripts', 'heblogs_scripts');

/**
 * 관리자 페이지에서 테마 정보 표시
 */
function heblogs_admin_notice() {
    if (isset($_GET['page']) && $_GET['page'] === 'themes.php') {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>HeBlogs 테마</strong>: 워드프레스 블로그 전용 테마입니다.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'heblogs_admin_notice');
