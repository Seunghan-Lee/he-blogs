<?php
/**
 * 댓글 템플릿
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ($comments_number == 1) {
                printf(__('댓글 %1$s개', 'heblogs'), number_format_i18n($comments_number));
            } else {
                printf(__('댓글 %1$s개', 'heblogs'), number_format_i18n($comments_number));
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'heblogs_comment',
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

    <?php
    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
    ?>
        <p class="no-comments"><?php esc_html_e('댓글이 닫혀있습니다.', 'heblogs'); ?></p>
    <?php endif; ?>

    <?php
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');

    comment_form(array(
        'title_reply'          => __('댓글 남기기', 'heblogs'),
        'title_reply_to'       => __('%s에게 답글 남기기', 'heblogs'),
        'cancel_reply_link'    => __('답글 취소', 'heblogs'),
        'label_submit'         => __('댓글 등록', 'heblogs'),
        'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . __('댓글', 'heblogs') . ($req ? ' <span class="required">*</span>' : '') . '</label><textarea id="comment" name="comment" cols="45" rows="8" required' . $aria_req . '></textarea></p>',
        'fields'               => array(
            'author' => '<p class="comment-form-author"><label for="author">' . __('이름', 'heblogs') . ($req ? ' <span class="required">*</span>' : '') . '</label><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
            'email'  => '<p class="comment-form-email"><label for="email">' . __('이메일', 'heblogs') . ($req ? ' <span class="required">*</span>' : '') . '</label><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
            'url'    => '<p class="comment-form-url"><label for="url">' . __('웹사이트', 'heblogs') . '</label><input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>',
        ),
    ));
    ?>
</div>

<?php
/**
 * 커스텀 댓글 콜백 함수
 */
function heblogs_comment($comment, $args, $depth) {
    if ('div' === $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
    <?php if ('div' != $args['style']) : ?>
        <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <?php endif; ?>
    <div class="comment-meta commentmetadata">
        <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
        <div class="comment-author vcard">
            <?php
            printf(
                __('<cite class="fn">%s</cite>', 'heblogs'),
                get_comment_author_link()
            );
            ?>
        </div>
        <div class="comment-metadata">
            <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                <time datetime="<?php comment_time('c'); ?>">
                    <?php
                    printf(
                        __('%1$s %2$s', 'heblogs'),
                        get_comment_date(),
                        get_comment_time()
                    );
                    ?>
                </time>
            </a>
            <?php edit_comment_link(__('수정', 'heblogs'), '<span class="edit-link">', '</span>'); ?>
        </div>
    </div>

    <?php if ('0' == $comment->comment_approved) : ?>
        <em class="comment-awaiting-moderation"><?php esc_html_e('댓글이 승인 대기 중입니다.', 'heblogs'); ?></em>
        <br />
    <?php endif; ?>

    <div class="comment-content">
        <?php comment_text(); ?>
    </div>

    <div class="reply">
        <?php
        comment_reply_link(
            array_merge(
                $args,
                array(
                    'add_below' => $add_below,
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                )
            )
        );
        ?>
    </div>
    <?php if ('div' != $args['style']) : ?>
        </div>
    <?php endif; ?>
    <?php
}
