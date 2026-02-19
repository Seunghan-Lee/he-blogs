<?php
/**
 * 푸터 템플릿
 */
?>
<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="site-info">
        &copy; <?php echo esc_html(date('Y')); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
