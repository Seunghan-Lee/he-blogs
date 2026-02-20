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

    wp_enqueue_script(
        'heblogs-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        _S_VERSION,
        true
    );

    if (is_singular() && comments_open()) {
        wp_enqueue_script(
            'heblogs-comment-actions',
            get_template_directory_uri() . '/assets/js/comments.js',
            array(),
            _S_VERSION,
            true
        );

        wp_localize_script(
            'heblogs-comment-actions',
            'heblogsCommentActions',
            array(
                'ajaxUrl'        => admin_url('admin-ajax.php'),
                'nonce'          => wp_create_nonce('heblogs_comment_actions'),
                'confirmDelete'  => __('이 댓글을 삭제하시겠습니까?', 'heblogs'),
                'passwordPrompt' => __('댓글 비밀번호를 입력해 주세요.', 'heblogs'),
                'passwordNeeded' => __('비밀번호를 입력해 주세요.', 'heblogs'),
                'nameNeeded'     => __('이름을 입력해 주세요.', 'heblogs'),
                'emptyComment'   => __('내용을 입력해 주세요.', 'heblogs'),
                'saveFailed'     => __('댓글 저장에 실패했습니다.', 'heblogs'),
                'deleteFailed'   => __('댓글 삭제에 실패했습니다.', 'heblogs'),
                'replyFailed'    => __('답글 등록에 실패했습니다.', 'heblogs'),
                'networkError'   => __('요청 중 오류가 발생했습니다.', 'heblogs'),
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'heblogs_scripts');
