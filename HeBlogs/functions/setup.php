<?php
/**
 * HeBlogs 테마 설정
 * 버전 상수 및 after_setup_theme 훅
 */

// 테마 버전
if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

/**
 * 테마 설정
 */
function heblogs_setup() {
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
