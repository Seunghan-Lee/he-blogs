<?php
/**
 * 댓글 폼/액션 커스터마이징
 */

/**
 * comment 필드를 마지막으로 이동해서 작성자 입력란을 위에 노출.
 */
function heblogs_reorder_comment_form_fields($fields) {
    if (!isset($fields['comment'])) {
        return $fields;
    }

    $comment_field = $fields['comment'];
    unset($fields['comment']);
    $fields['comment'] = $comment_field;

    return $fields;
}
add_filter('comment_form_fields', 'heblogs_reorder_comment_form_fields');

/**
 * 프론트 댓글은 이름/비밀번호 형태를 쓰므로 이메일 필수 옵션을 무시.
 */
function heblogs_disable_require_name_email($pre_option) {
    if (is_admin()) {
        return $pre_option;
    }

    return 0;
}
add_filter('pre_option_require_name_email', 'heblogs_disable_require_name_email');

/**
 * 댓글을 프론트에서 관리할 수 있는 사용자 판별.
 */
function heblogs_can_manage_comment($comment, $guest_password = '') {
    if (!$comment instanceof WP_Comment) {
        $comment = get_comment($comment);
    }

    if (!$comment) {
        return false;
    }

    if (current_user_can('moderate_comments')) {
        return true;
    }

    if (is_user_logged_in()) {
        return ((int) get_current_user_id() === (int) $comment->user_id);
    }

    $stored_password_hash = get_comment_meta($comment->comment_ID, '_heblogs_comment_password', true);
    if ('' === $stored_password_hash || '' === $guest_password) {
        return false;
    }

    return wp_check_password($guest_password, $stored_password_hash);
}

/**
 * 댓글 액션 버튼 노출 여부.
 */
function heblogs_can_show_comment_manage_actions($comment) {
    if (!$comment instanceof WP_Comment) {
        $comment = get_comment($comment);
    }

    if (!$comment) {
        return false;
    }

    if (heblogs_can_manage_comment($comment)) {
        return true;
    }

    if (is_user_logged_in()) {
        return false;
    }

    $stored_password_hash = get_comment_meta($comment->comment_ID, '_heblogs_comment_password', true);
    if ('' === $stored_password_hash) {
        return false;
    }

    $commenter = wp_get_current_commenter();
    if (empty($commenter['comment_author'])) {
        return false;
    }

    return $commenter['comment_author'] === $comment->comment_author;
}

/**
 * AJAX: 댓글 인라인 수정.
 */
function heblogs_ajax_update_comment() {
    check_ajax_referer('heblogs_comment_actions', 'nonce');

    $comment_id      = isset($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
    $content         = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';
    $guest_password  = isset($_POST['comment_password']) ? trim(wp_unslash($_POST['comment_password'])) : '';
    $content         = trim($content);

    if (!$comment_id || '' === $content) {
        wp_send_json_error(
            array('message' => __('수정할 내용을 입력해 주세요.', 'heblogs')),
            400
        );
    }

    $comment = get_comment($comment_id);
    if (!$comment || !heblogs_can_manage_comment($comment, $guest_password)) {
        wp_send_json_error(
            array('message' => __('댓글 수정 권한이 없습니다.', 'heblogs')),
            403
        );
    }

    $result = wp_update_comment(
        array(
            'comment_ID'      => $comment_id,
            'comment_content' => $content,
        ),
        true
    );

    if (is_wp_error($result)) {
        wp_send_json_error(
            array('message' => $result->get_error_message()),
            500
        );
    }

    $updated_comment = get_comment($comment_id);
    $formatted       = apply_filters('comment_text', $updated_comment->comment_content, $updated_comment);

    wp_send_json_success(
        array(
            'commentId' => $comment_id,
            'content'   => $formatted,
            'raw'       => $updated_comment->comment_content,
        )
    );
}
add_action('wp_ajax_heblogs_update_comment', 'heblogs_ajax_update_comment');
add_action('wp_ajax_nopriv_heblogs_update_comment', 'heblogs_ajax_update_comment');

/**
 * AJAX: 댓글 삭제(휴지통 이동).
 */
function heblogs_ajax_delete_comment() {
    check_ajax_referer('heblogs_comment_actions', 'nonce');

    $comment_id     = isset($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
    $guest_password = isset($_POST['comment_password']) ? trim(wp_unslash($_POST['comment_password'])) : '';
    if (!$comment_id) {
        wp_send_json_error(
            array('message' => __('유효하지 않은 댓글입니다.', 'heblogs')),
            400
        );
    }

    $comment = get_comment($comment_id);
    if (!$comment || !heblogs_can_manage_comment($comment, $guest_password)) {
        wp_send_json_error(
            array('message' => __('댓글 삭제 권한이 없습니다.', 'heblogs')),
            403
        );
    }

    $deleted = wp_trash_comment($comment_id);
    if (!$deleted) {
        wp_send_json_error(
            array('message' => __('댓글 삭제에 실패했습니다.', 'heblogs')),
            500
        );
    }

    wp_send_json_success(
        array(
            'commentId' => $comment_id,
        )
    );
}
add_action('wp_ajax_heblogs_delete_comment', 'heblogs_ajax_delete_comment');
add_action('wp_ajax_nopriv_heblogs_delete_comment', 'heblogs_ajax_delete_comment');

/**
 * 비로그인 댓글 비밀번호를 해시로 저장.
 */
function heblogs_store_comment_password_meta($comment_id) {
    if (is_user_logged_in()) {
        return;
    }

    if (!isset($_POST['comment_password']) || !is_string($_POST['comment_password'])) {
        return;
    }

    $raw_password = trim(wp_unslash($_POST['comment_password']));
    if ('' === $raw_password) {
        return;
    }

    update_comment_meta($comment_id, '_heblogs_comment_password', wp_hash_password($raw_password));
}
add_action('comment_post', 'heblogs_store_comment_password_meta');

/**
 * 댓글 depth 계산.
 */
function heblogs_get_comment_depth($comment) {
    if (!$comment instanceof WP_Comment) {
        $comment = get_comment($comment);
    }

    if (!$comment) {
        return 1;
    }

    $depth = 1;
    while (!empty($comment->comment_parent)) {
        $comment = get_comment((int) $comment->comment_parent);
        if (!$comment) {
            break;
        }
        $depth++;
    }

    return $depth;
}

/**
 * 단일 댓글 HTML 렌더링 (AJAX 응답용).
 */
function heblogs_render_single_comment_html($comment_id) {
    $comment = get_comment($comment_id);
    if (!$comment) {
        return '';
    }

    if (!function_exists('heblogs_comment')) {
        return '';
    }

    $depth = heblogs_get_comment_depth($comment);
    $args  = array(
        'style'       => 'ol',
        'short_ping'  => true,
        'avatar_size' => 48,
        'callback'    => 'heblogs_comment',
        'max_depth'   => (int) get_option('thread_comments_depth'),
        'format'      => current_theme_supports('html5', 'comment-list') ? 'html5' : 'xhtml',
        'has_children'=> false,
    );

    $walker = new Walker_Comment();
    $output = '';
    $walker->start_el($output, $comment, $depth - 1, $args, $comment->comment_ID);
    $walker->end_el($output, $comment, $depth - 1, $args);

    return trim($output);
}

/**
 * AJAX: 답글 저장.
 */
function heblogs_ajax_add_reply() {
    check_ajax_referer('heblogs_comment_actions', 'nonce');

    $post_id         = isset($_POST['comment_post_ID']) ? absint($_POST['comment_post_ID']) : 0;
    $parent_id       = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
    $content         = isset($_POST['comment']) ? trim(wp_unslash($_POST['comment'])) : '';
    $author          = isset($_POST['author']) ? trim(wp_unslash($_POST['author'])) : '';
    $guest_password  = isset($_POST['comment_password']) ? trim(wp_unslash($_POST['comment_password'])) : '';

    if (!$post_id || !$parent_id || '' === $content) {
        wp_send_json_error(
            array('message' => __('답글 내용을 확인해 주세요.', 'heblogs')),
            400
        );
    }

    if (!comments_open($post_id)) {
        wp_send_json_error(
            array('message' => __('댓글이 닫혀 있습니다.', 'heblogs')),
            403
        );
    }

    if (get_option('comment_registration') && !is_user_logged_in()) {
        wp_send_json_error(
            array('message' => __('로그인 후 답글을 작성할 수 있습니다.', 'heblogs')),
            403
        );
    }

    $parent_comment = get_comment($parent_id);
    if (!$parent_comment || (int) $parent_comment->comment_post_ID !== (int) $post_id) {
        wp_send_json_error(
            array('message' => __('유효하지 않은 부모 댓글입니다.', 'heblogs')),
            400
        );
    }

    if ((int) get_option('thread_comments_depth') <= heblogs_get_comment_depth($parent_comment)) {
        wp_send_json_error(
            array('message' => __('더 이상 답글을 달 수 없습니다.', 'heblogs')),
            400
        );
    }

    if (!is_user_logged_in()) {
        if ('' === $author || '' === $guest_password) {
            wp_send_json_error(
                array('message' => __('이름과 비밀번호를 입력해 주세요.', 'heblogs')),
                400
            );
        }
    }

    $submission = array(
        'comment_post_ID' => $post_id,
        'comment_parent'  => $parent_id,
        'comment'         => $content,
    );

    if (!is_user_logged_in()) {
        $submission['author'] = $author;
        $submission['email']  = '';
        $submission['url']    = '';
        $_POST['comment_password'] = $guest_password;
    }

    $new_comment = wp_handle_comment_submission($submission);
    if (is_wp_error($new_comment)) {
        wp_send_json_error(
            array('message' => $new_comment->get_error_message()),
            400
        );
    }

    do_action('set_comment_cookies', $new_comment, wp_get_current_user(), true);

    $html = heblogs_render_single_comment_html($new_comment->comment_ID);
    if ('' === $html) {
        wp_send_json_error(
            array('message' => __('답글 렌더링에 실패했습니다.', 'heblogs')),
            500
        );
    }

    wp_send_json_success(
        array(
            'commentId' => (int) $new_comment->comment_ID,
            'parentId'  => (int) $new_comment->comment_parent,
            'html'      => $html,
        )
    );
}
add_action('wp_ajax_heblogs_add_reply', 'heblogs_ajax_add_reply');
add_action('wp_ajax_nopriv_heblogs_add_reply', 'heblogs_ajax_add_reply');

/**
 * 커스텀 댓글 콜백 함수.
 */
function heblogs_comment($comment, $args, $depth) {
    if ('div' === $args['style']) {
        $tag = 'div';
    } else {
        $tag = 'li';
    }

    $can_manage_comment = heblogs_can_manage_comment($comment);
    $show_manage_action = heblogs_can_show_comment_manage_actions($comment);
    $require_password   = (!$can_manage_comment && $show_manage_action) ? '1' : '0';
    ?>
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
    <?php if ('div' != $args['style']) : ?>
        <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <?php endif; ?>
    <div class="comment-avatar">
        <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
    </div>
    <div class="comment-content-grp">
        <div class="comment-meta commentmetadata">
            <div class="comment-author vcard">
                <?php
                printf(
                    __('<cite class="fn">%s</cite>', 'heblogs'),
                    esc_html(get_comment_author())
                );
                ?>
            </div>
        </div>

        <?php if ('0' == $comment->comment_approved) : ?>
            <em class="comment-awaiting-moderation"><?php esc_html_e('댓글이 승인 대기 중입니다.', 'heblogs'); ?></em>
            <br />
        <?php endif; ?>

        <div class="comment-content">
            <div class="comment-text js-comment-text">
                <?php comment_text(); ?>
            </div>
            <?php if ($show_manage_action) : ?>
                <div class="comment-inline-editor js-comment-editor" hidden>
                    <textarea class="js-comment-editor-textarea" rows="4"><?php echo esc_textarea($comment->comment_content); ?></textarea>
                    <div class="comment-inline-editor-actions">
                        <button type="button" class="comment-action-link comment-save-link js-comment-edit-save" data-comment-id="<?php echo esc_attr($comment->comment_ID); ?>" data-require-password="<?php echo esc_attr($require_password); ?>">
                            <?php esc_html_e('저장', 'heblogs'); ?>
                        </button>
                        <button type="button" class="comment-action-link comment-cancel-link js-comment-edit-cancel">
                            <?php esc_html_e('취소', 'heblogs'); ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            <div class="comment-metadata">
                <time datetime="<?php comment_time('c'); ?>">
                    <?php
                    printf(
                        __('%1$s %2$s', 'heblogs'),
                        get_comment_date(),
                        get_comment_time()
                    );
                    ?>
                </time>
                <div class="comment-metadata-actions">
                    <?php if ($show_manage_action) : ?>
                        <button type="button" class="comment-action-link comment-delete-link js-comment-delete-trigger" data-comment-id="<?php echo esc_attr($comment->comment_ID); ?>" data-require-password="<?php echo esc_attr($require_password); ?>">
                            <?php esc_html_e('삭제', 'heblogs'); ?>
                        </button>
                        <button type="button" class="comment-action-link comment-edit-link js-comment-edit-trigger" data-comment-id="<?php echo esc_attr($comment->comment_ID); ?>" data-require-password="<?php echo esc_attr($require_password); ?>">
                            <?php esc_html_e('수정', 'heblogs'); ?>
                        </button>
                    <?php endif; ?>
                    <?php
                    $can_reply = comments_open($comment->comment_post_ID)
                        && get_option('thread_comments')
                        && ((int) $depth < (int) $args['max_depth']);

                    if (get_option('comment_registration') && !is_user_logged_in()) {
                        $can_reply = false;
                    }

                    if ($can_reply) :
                    ?>
                        <button type="button" class="comment-action-link comment-reply-link js-inline-reply-trigger" data-comment-id="<?php echo esc_attr($comment->comment_ID); ?>">
                            <?php esc_html_e('답글', 'heblogs'); ?>
                        </button>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php if ('div' != $args['style']) : ?>
        </div>
    <?php endif; ?>
    <?php
}
