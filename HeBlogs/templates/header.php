<?php
/**
 * 헤더 템플릿
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header" role="banner">
    <div class="site-branding">
        <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
        <?php else : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
        <?php endif; ?>
    </div>
    <nav id="site-navigation" class="main-navigation" role="navigation">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'fallback_cb'    => false,
        ));
        ?>
    </nav>
</header>
