<?php
/**
 * HeBlogs 관리자 기능
 * 관리자 페이지 알림 등
 */

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
