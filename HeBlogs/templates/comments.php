<?php
/**
 * 댓글 템플릿
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <div class="comments-header">
        <div class="comments-header-info">
            <h2 class="comments-title">댓글 남기기</h2>
            <div class="comments-number">
                <?php
                $comments_number = get_comments_number();
                if ($comments_number == 1) {
                    printf(__('댓글 %1$s개', 'heblogs'), number_format_i18n($comments_number));
                } else {
                    printf(__('댓글 %1$s개', 'heblogs'), number_format_i18n($comments_number));
                }
                ?>
            </div>
        </div>
        <?php
            $comment_login_html = '';

            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                $comment_login_html = sprintf(
                    '<div class="comment-login-info"><span class="comment-login-username">%1$s</span><span class="comment-login-separator" aria-hidden="true">|</span><a class="comment-logout-link" href="%2$s">%3$s</a></div>',
                    esc_html($current_user->user_login),
                    esc_url(wp_logout_url(get_permalink())),
                    esc_html__('로그아웃', 'heblogs')
                );
            }
            echo wp_kses_post($comment_login_html);
        ?>
    </div>
    <?php
    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
    ?>
        <p class="no-comments"><?php esc_html_e('댓글이 닫혀있습니다.', 'heblogs'); ?></p>
    <?php endif; ?>
    <?php
    $commenter = wp_get_current_commenter();

    comment_form(array(
        'title_reply'          => __('', 'heblogs'),
        'title_reply_to'       => __('%s에게 답글 남기기', 'heblogs'),
        'label_submit'         => __('입력', 'heblogs'),
        'logged_in_as'         => '',
        'fields'               => array(
            'author' => '<div class="comment-form-author-grp"><p class="comment-form-author"><input id="author" name="author" type="text" placeholder="' . __('이름', 'heblogs') . '" value="' . esc_attr($commenter['comment_author']) . '" size="10" required aria-required="true" /></p>',
            'email'  => '<p class="comment-form-password"><input id="comment_password" name="comment_password" type="password" placeholder="' . __('비밀번호', 'heblogs') . '" size="10" required aria-required="true" autocomplete="off" /></p></div>',
        ),
        'comment_field'        => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="2" required aria-required="true"></textarea></p>',
    ));
    ?>
    <template id="heblogs-inline-reply-template">
        <div class="comment-inline-reply">
            <form class="comment-form comment-inline-reply-form" action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="post">
                <?php if (!is_user_logged_in()) : ?>
                    <div class="comment-form-author-grp">
                        <p class="comment-form-author">
                            <input name="author" type="text" placeholder="<?php esc_attr_e('이름', 'heblogs'); ?>" value="<?php echo esc_attr($commenter['comment_author']); ?>" size="10" required aria-required="true" />
                        </p>
                        <p class="comment-form-password">
                            <input name="comment_password" type="password" placeholder="<?php esc_attr_e('비밀번호', 'heblogs'); ?>" size="10" required aria-required="true" autocomplete="off" />
                        </p>
                    </div>
                <?php endif; ?>
                <p class="comment-form-comment">
                    <textarea name="comment" cols="45" rows="3" required aria-required="true"></textarea>
                </p>
                <p class="form-submit comment-inline-reply-actions">
                    <button type="button" class="comment-action-link comment-reply-cancel js-inline-reply-cancel">
                        <?php esc_html_e('취소', 'heblogs'); ?>
                    </button>
                    <input name="submit" type="submit" class="submit" value="<?php echo esc_attr__('입력', 'heblogs'); ?>" />
                </p>
                <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr(get_the_ID()); ?>" />
                <input type="hidden" name="comment_parent" value="" />
            </form>
        </div>
    </template>

    <?php if (have_comments()) : ?>
        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'heblogs_comment',
                'reverse_top_level' => true,
                'reverse_children'  => true,
            ));
            ?>
        </ol>

        <?php
        the_comments_pagination(array(
            'prev_text' => __('이전 댓글', 'heblogs'),
            'next_text' => __('다음 댓글', 'heblogs'),
        ));
        ?>
    <?php endif; ?>
</div>
