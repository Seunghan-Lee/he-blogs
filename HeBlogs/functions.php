<?php
/**
 * HeBlogs 테마 함수
 * 블로그 전용 워드프레스 테마
 *
 * 기능별로 functions 디렉토리의 개별 파일을 로드합니다.
 */

// 테마 설정 (버전 상수, add_theme_support 등)
require_once get_template_directory() . '/functions/setup.php';

// 스타일/스크립트 등록
require_once get_template_directory() . '/functions/scripts.php';

// 템플릿 로더 (templates/ 디렉터리 자동 인식)
require_once get_template_directory() . '/functions/templates.php';

// 파비콘 (images 폴더의 favicon 자동 사용)
require_once get_template_directory() . '/functions/favicon.php';

// 관리자 기능 (알림 등)
require_once get_template_directory() . '/functions/admin.php';
