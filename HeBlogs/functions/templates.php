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

/**
 * 댓글 템플릿 경로를 templates/comments.php로 지정
 */
function heblogs_comments_template($template) {
    $comments_template = get_template_directory() . '/templates/comments.php';
    if (file_exists($comments_template)) {
        return $comments_template;
    }
    return $template;
}
add_filter('comments_template', 'heblogs_comments_template');

/**
 * 포스트 제목 출력 (br 태그 허용)
 */
function heblogs_the_title($post_id = 0) {
    if ($post_id == 0) {
        $post_id = get_the_ID();
    }
    
    // 원본 제목 가져오기 (필터 없이)
    $title = get_post_field('post_title', $post_id);
    
    // HTML 엔티티 디코드 (이스케이프된 경우 대비)
    $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    
    // &lt;br&gt; 같은 이스케이프된 태그도 처리
    $title = str_replace(array('&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'), '<br>', $title);
    
    // <br> 태그를 임시로 보호 (고유한 플레이스홀더 사용)
    $placeholder = '___HEBLOGS_BR_TAG___';
    $br_matches = array();
    $br_count = 0;
    
    // 정규식으로 <br> 태그만 찾아서 보호
    $title = preg_replace_callback('/<br\s*\/?>/i', function($matches) use (&$br_matches, &$br_count, $placeholder) {
        $br_matches[$br_count] = $matches[0];
        return $placeholder . $br_count++ . '___';
    }, $title);
    
    // 나머지 HTML 태그는 이스케이프만 하고 제거하지 않음
    // 일반 텍스트의 < > 기호는 그대로 유지
    $title = esc_html($title);
    
    // 이스케이프된 <br> 태그를 다시 디코드
    foreach ($br_matches as $index => $br_tag) {
        $title = str_replace(htmlspecialchars($placeholder . $index . '___', ENT_QUOTES, 'UTF-8'), $br_tag, $title);
    }
    
    // 최종적으로 <br> 태그만 허용하여 보안 처리
    $allowed_tags = array(
        'br' => array(),
    );
    $title = wp_kses($title, $allowed_tags);
    
    echo $title;
}

/**
 * the_title 필터를 통해 모든 제목 출력에 br 태그 허용 적용
 * 위젯, 아카이브 등 모든 곳에서 사용됨
 */
function heblogs_filter_the_title($title, $post_id = 0) {
    // 관리자 페이지에서는 필터 적용 안 함
    if (is_admin()) {
        return $title;
    }
    
    // 빈 제목 처리
    if (empty($title)) {
        return $title;
    }
    
    // HTML 엔티티 디코드 (이스케이프된 경우 대비)
    $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    
    // &lt;br&gt; 같은 이스케이프된 태그도 처리
    $title = str_replace(array('&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'), '<br>', $title);
    
    // <br> 태그를 임시로 보호 (고유한 플레이스홀더 사용)
    $placeholder = '___HEBLOGS_BR_TAG___';
    $br_matches = array();
    $br_count = 0;
    
    // 정규식으로 <br> 태그만 찾아서 보호
    $title = preg_replace_callback('/<br\s*\/?>/i', function($matches) use (&$br_matches, &$br_count, $placeholder) {
        $br_matches[$br_count] = $matches[0];
        return $placeholder . $br_count++ . '___';
    }, $title);
    
    // 나머지 HTML 태그는 이스케이프만 하고 제거하지 않음
    // 일반 텍스트의 < > 기호는 그대로 유지
    $title = esc_html($title);
    
    // 이스케이프된 <br> 태그를 다시 디코드
    foreach ($br_matches as $index => $br_tag) {
        $title = str_replace(htmlspecialchars($placeholder . $index . '___', ENT_QUOTES, 'UTF-8'), $br_tag, $title);
    }
    
    // 최종적으로 <br> 태그만 허용하여 보안 처리
    $allowed_tags = array(
        'br' => array(),
    );
    $title = wp_kses($title, $allowed_tags);
    
    return $title;
}
add_filter('the_title', 'heblogs_filter_the_title', 10, 2);
