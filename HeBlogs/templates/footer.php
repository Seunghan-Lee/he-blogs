<?php
/**
 * 푸터 템플릿
 */
?>
<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="footer-wrapper">
        <?php if (is_active_sidebar('footer-1')) : ?>
            <div class="footer-widgets">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
        <?php endif; ?>
        <div class="site-info">
            <p>&copy; <?php echo esc_html(date('Y')); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
