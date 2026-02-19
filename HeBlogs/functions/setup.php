<?php
/**
 * HeBlogs 기본 테마 설정
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

/**
 * 테마 지원 기능 등록 및 전역 값 설정
 */
function heblogs_setup() {
    // RSS 피드 링크 자동 추가
    add_theme_support('automatic-feed-links');

    // <title> 태그를 WP가 관리하도록 설정
    add_theme_support('title-tag');

    // 썸네일 사용
    add_theme_support('post-thumbnails');

    // 내비게이션 메뉴
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'heblogs'),
    ));

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

    // 커스텀 로고
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'heblogs_setup');

/**
 * 콘텐츠 폭 기본값
 */
function heblogs_content_width() {
    $GLOBALS['content_width'] = apply_filters('heblogs_content_width', 800);
}
add_action('after_setup_theme', 'heblogs_content_width', 0);
