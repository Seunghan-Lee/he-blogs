<?php
/**
 * HeBlogs 테마 옵션 페이지
 * 관리자 메뉴에 "테마 옵션" 추가
 */

/**
 * 테마 옵션 기본값
 */
function heblogs_get_default_theme_options() {
    return array(
        // 컬러 설정 기본값
        'color_base'      => '#004FFF',
        'color_accent'    => '#00F594',
        'color_background' => '#FFFEEB',
        'color_subtle'    => '#0071FF',
        
        // 폰트 설정 기본값
        'font_english'    => 'Goole Sans',
        'font_korean'     => 'Noto Sans KR',
    );
}

/**
 * 테마 옵션 가져오기
 */
function heblogs_get_theme_option($option_name, $default = '') {
    $defaults = heblogs_get_default_theme_options();
    $value = get_option('heblogs_' . $option_name, isset($defaults[$option_name]) ? $defaults[$option_name] : $default);
    return $value;
}

/**
 * 구글 폰트 목록 - 영문 (인기 20개)
 */
function heblogs_get_english_fonts() {
    return array(
        'Google Sans' => 'Google Sans',
        'Roboto' => 'Roboto',
        'Open Sans' => 'Open Sans',
        'Lato' => 'Lato',
        'Montserrat' => 'Montserrat',
        'Oswald' => 'Oswald',
        'Source Sans Pro' => 'Source Sans Pro',
        'Raleway' => 'Raleway',
        'PT Sans' => 'PT Sans',
        'Playfair Display' => 'Playfair Display',
        'Merriweather' => 'Merriweather',
        'Ubuntu' => 'Ubuntu',
        'Nunito' => 'Nunito',
        'Poppins' => 'Poppins',
        'Inter' => 'Inter',
        'Work Sans' => 'Work Sans',
        'Crimson Text' => 'Crimson Text',
        'Lora' => 'Lora',
        'Libre Baskerville' => 'Libre Baskerville',
        'Fira Sans' => 'Fira Sans',
        'Dancing Script' => 'Dancing Script',
    );
}

/**
 * 구글 폰트 목록 - 한국어 (5개)
 */
function heblogs_get_korean_fonts() {
    return array(
        'Noto Sans KR' => 'Noto Sans KR',
        'Nanum Gothic' => 'Nanum Gothic',
        'Nanum Myeongjo' => 'Nanum Myeongjo',
        'Nanum Pen Script' => 'Nanum Pen Script',
        'Gowun Dodum' => 'Gowun Dodum',
    );
}

/**
 * 관리자 메뉴에 테마 옵션 추가
 */
function heblogs_add_theme_options_menu() {
    add_menu_page(
        __('테마 옵션', 'heblogs'),
        __('테마 옵션', 'heblogs'),
        'manage_options',
        'heblogs-theme-options',
        'heblogs_theme_options_page',
        'dashicons-admin-appearance',
        30
    );
}
add_action('admin_menu', 'heblogs_add_theme_options_menu');

/**
 * 테마 옵션 페이지 렌더링
 */
function heblogs_theme_options_page() {
    // 권한 확인
    if (!current_user_can('manage_options')) {
        wp_die(__('권한이 없습니다.', 'heblogs'));
    }

    // 설정 초기화 처리
    if (isset($_POST['heblogs_reset_options']) && check_admin_referer('heblogs_theme_options_nonce')) {
        $defaults = heblogs_get_default_theme_options();
        
        // 모든 옵션을 기본값으로 초기화
        foreach ($defaults as $option_name => $default_value) {
            update_option('heblogs_' . $option_name, $default_value);
        }

        // 확장 가능한 필터 - 다른 설정 초기화를 위한 훅
        do_action('heblogs_reset_theme_options');

        echo '<div class="notice notice-success is-dismissible"><p>' . __('모든 설정이 기본값으로 초기화되었습니다.', 'heblogs') . '</p></div>';
    }

    // 설정 저장 처리
    if (isset($_POST['heblogs_save_options']) && check_admin_referer('heblogs_theme_options_nonce')) {
        // 컬러 설정 저장
        if (isset($_POST['heblogs_color_base'])) {
            update_option('heblogs_color_base', sanitize_hex_color($_POST['heblogs_color_base']));
        }
        if (isset($_POST['heblogs_color_accent'])) {
            update_option('heblogs_color_accent', sanitize_hex_color($_POST['heblogs_color_accent']));
        }
        if (isset($_POST['heblogs_color_background'])) {
            update_option('heblogs_color_background', sanitize_hex_color($_POST['heblogs_color_background']));
        }
        if (isset($_POST['heblogs_color_subtle'])) {
            update_option('heblogs_color_subtle', sanitize_hex_color($_POST['heblogs_color_subtle']));
        }

        // 폰트 설정 저장
        if (isset($_POST['heblogs_font_english'])) {
            update_option('heblogs_font_english', sanitize_text_field($_POST['heblogs_font_english']));
        }
        if (isset($_POST['heblogs_font_korean'])) {
            update_option('heblogs_font_korean', sanitize_text_field($_POST['heblogs_font_korean']));
        }

        // 확장 가능한 필터 - 다른 설정 저장을 위한 훅
        do_action('heblogs_save_theme_options');

        echo '<div class="notice notice-success is-dismissible"><p>' . __('설정이 저장되었습니다.', 'heblogs') . '</p></div>';
    }

    // 현재 설정값 가져오기
    $color_base = heblogs_get_theme_option('color_base');
    $color_accent = heblogs_get_theme_option('color_accent');
    $color_background = heblogs_get_theme_option('color_background');
    $color_subtle = heblogs_get_theme_option('color_subtle');
    $font_english = heblogs_get_theme_option('font_english');
    $font_korean = heblogs_get_theme_option('font_korean');

    $english_fonts = heblogs_get_english_fonts();
    $korean_fonts = heblogs_get_korean_fonts();
    ?>
    <div class="wrap">
        <h1>He Blogs Theme Options</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('heblogs_theme_options_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#colors" class="nav-tab nav-tab-active"><?php _e('Color', 'heblogs'); ?></a>
                <a href="#fonts" class="nav-tab"><?php _e('Fonts', 'heblogs'); ?></a>
                <?php
                // 확장 가능한 탭 추가를 위한 필터
                do_action('heblogs_theme_options_tabs');
                ?>
            </h2>

            <div id="colors" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="heblogs_color_base"><?php _e('Base', 'heblogs'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="heblogs_color_base" name="heblogs_color_base" value="<?php echo esc_attr($color_base); ?>" />
                            <input type="text" class="regular-text" value="<?php echo esc_attr($color_base); ?>" readonly />
                            <p class="description"><?php _e('이 테마에서 가장 많이 사용될 컬러입니다. 주로 배경 혹은 버튼, 강조 텍스트 등에 사용됩니다.', 'heblogs'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="heblogs_color_accent"><?php _e('Accent', 'heblogs'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="heblogs_color_accent" name="heblogs_color_accent" value="<?php echo esc_attr($color_accent); ?>" />
                            <input type="text" class="regular-text" value="<?php echo esc_attr($color_accent); ?>" readonly />
                            <p class="description"><?php _e('이 테마의 가장 핵심이 되는 포인트 컬러입니다. 주로 Base 컬러와 함께 사용되어 강조된 효과를 줍니다.', 'heblogs'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="heblogs_color_background"><?php _e('Background', 'heblogs'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="heblogs_color_background" name="heblogs_color_background" value="<?php echo esc_attr($color_background); ?>" />
                            <input type="text" class="regular-text" value="<?php echo esc_attr($color_background); ?>" readonly />
                            <p class="description"><?php _e('강조될 영역의 배경에 사용되는 컬러입니다.', 'heblogs'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="heblogs_color_subtle"><?php _e('Subtle', 'heblogs'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="heblogs_color_subtle" name="heblogs_color_subtle" value="<?php echo esc_attr($color_subtle); ?>" />
                            <input type="text" class="regular-text" value="<?php echo esc_attr($color_subtle); ?>" readonly />
                            <p class="description"><?php _e('경계선 및 미묘한 구분에 사용되는 컬러입니다.', 'heblogs'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="fonts" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="heblogs_font_english"><?php _e('영문 폰트', 'heblogs'); ?></label>
                        </th>
                        <td>
                            <select id="heblogs_font_english" name="heblogs_font_english" class="regular-text">
                                <?php foreach ($english_fonts as $font_value => $font_label) : ?>
                                    <option value="<?php echo esc_attr($font_value); ?>" <?php selected($font_english, $font_value); ?>>
                                        <?php echo esc_html($font_label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('영문 텍스트에 사용될 폰트를 선택하세요.', 'heblogs'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="heblogs_font_korean"><?php _e('한국어 폰트', 'heblogs'); ?></label>
                        </th>
                        <td>
                            <select id="heblogs_font_korean" name="heblogs_font_korean" class="regular-text">
                                <?php foreach ($korean_fonts as $font_value => $font_label) : ?>
                                    <option value="<?php echo esc_attr($font_value); ?>" <?php selected($font_korean, $font_value); ?>>
                                        <?php echo esc_html($font_label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('한국어 텍스트에 사용될 폰트를 선택하세요.', 'heblogs'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <?php
            // 확장 가능한 설정 섹션 추가를 위한 필터
            do_action('heblogs_theme_options_sections');
            ?>

            <p class="submit">
                <?php submit_button(__('설정 저장', 'heblogs'), 'primary', 'heblogs_save_options', false); ?>
                <?php submit_button(__('모든 설정 초기화', 'heblogs'), 'secondary', 'heblogs_reset_options', false, array('onclick' => 'return confirm("' . esc_js(__('정말로 모든 설정을 기본값으로 초기화하시겠습니까? 이 작업은 되돌릴 수 없습니다.', 'heblogs')) . '");')); ?>
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // 탭 전환
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $(target).show();
        });

        // 컬러 피커 값 동기화
        $('input[type="color"]').on('change', function() {
            $(this).next('input[type="text"]').val($(this).val());
        });
    });
    </script>

    <style>
    .tab-content {
        margin-top: 20px;
    }
    input[type="color"] {
        width: 60px;
        height: 35px;
        margin-right: 10px;
        vertical-align: middle;
    }
    </style>
    <?php
}

/**
 * CSS 변수로 테마 옵션 출력
 */
function heblogs_output_theme_options_css() {
    $color_base = heblogs_get_theme_option('color_base');
    $color_accent = heblogs_get_theme_option('color_accent');
    $color_background = heblogs_get_theme_option('color_background');
    $color_subtle = heblogs_get_theme_option('color_subtle');
    $font_english = heblogs_get_theme_option('font_english');
    $font_korean = heblogs_get_theme_option('font_korean');

    // 구글 폰트 URL 생성
    $fonts = array();
    if ($font_english) {
        // Google Fonts API 형식: 공백을 +로 변환하고 특수문자 처리
        $font_name = str_replace(' ', '+', $font_english);
        $fonts[] = $font_name;
    }
    if ($font_korean && $font_korean !== $font_english) {
        $font_name = str_replace(' ', '+', $font_korean);
        $fonts[] = $font_name;
    }
    
    $font_url = '';
    if (!empty($fonts)) {
        $font_url = 'https://fonts.googleapis.com/css2?';
        foreach ($fonts as $font) {
            // Google Fonts API v2 형식: family=Font+Name:wght@400;500;700
            $font_url .= 'family=' . urlencode($font) . ':wght@400;500;700&';
        }
        $font_url = rtrim($font_url, '&') . '&display=swap';
    }

    ?>
    <style id="heblogs-theme-options">
        :root {
            --heblogs-color-base: <?php echo esc_attr($color_base); ?>;
            --heblogs-color-accent: <?php echo esc_attr($color_accent); ?>;
            --heblogs-color-background: <?php echo esc_attr($color_background); ?>;
            --heblogs-color-subtle: <?php echo esc_attr($color_subtle); ?>;
            --heblogs-font-english: '<?php echo esc_js($font_english); ?>', sans-serif;
            --heblogs-font-korean: '<?php echo esc_js($font_korean); ?>', sans-serif;
        }
        body, div, h1, h2, h3, h4, h5, h6, p, a, span, li, ul, ol, table, tr, td, th, textarea, input, button {
            font-family: var(--heblogs-font-english), var(--heblogs-font-korean), sans-serif;
        }
    </style>
    <?php if ($font_url) : ?>
        <link rel="stylesheet" href="<?php echo esc_url($font_url); ?>" />
    <?php endif; ?>
    <?php
}
add_action('wp_head', 'heblogs_output_theme_options_css', 99);
