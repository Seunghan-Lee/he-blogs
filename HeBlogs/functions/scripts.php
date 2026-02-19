<?php
/**
 * HeBlogs 스타일/스크립트 등록
 * wp_enqueue_scripts 훅
 */

/**
 * 스타일시트 등록
 */
function heblogs_scripts() {
    wp_enqueue_style('heblogs-style', get_stylesheet_uri(), array(), _S_VERSION);
}
add_action('wp_enqueue_scripts', 'heblogs_scripts');
