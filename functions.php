<?php
// WP升级失败，删除core_updater.lock文件
//delete_option('core_updater.lock');
//delete_option('auto_updater.lock');
// 移除所有特色图像
//delete_post_meta_by_key('_thumbnail_id');

if (!isset($content_width)) $content_width = 768;

add_action('after_setup_theme',  function () {
  //load_theme_textdomain('wtb', get_theme_file_path('/languages'));
  add_theme_support('post-formats', ['video', 'image', 'audio', 'status', 'aside', /* 'link', 'quote', 'chat', 'gallery' */]);
  add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets']);
  add_theme_support('post-thumbnails');
  add_theme_support('custom-logo');
  add_theme_support('title-tag');
  //add_theme_support('custom-header');
  //add_theme_support('custom-background');
  add_theme_support('customize-selective-refresh-widgets');
  add_theme_support('responsive-embeds');
  add_theme_support('align-wide');
  register_nav_menus(['header_menu' => '顶部菜单']);
  //add_theme_support('automatic-feed-links');
  //add_theme_support('featured-content');
  /*----------块样式支持----------*/
  add_theme_support('wp-block-styles');
  /*----------修改编辑器样式（影响古腾堡编辑器比较严重）----------*/
  //add_theme_support('editor-styles');
  //add_editor_style(get_theme_file_uri( 'assets/css/style-editor.min.css' ));
  /*----------启用深色模式----------*/
  //add_theme_support('dark-editor-style');
  /*----------自定义字体大小选择----------*/
  //add_theme_support('editor-font-sizes', [['name' => '小']]);
  /*----------禁用编辑器自定义字号----------*/
  //add_theme_support('disable-custom-font-sizes');
  /*----------自定义颜色面板----------*/
  //add_theme_support('editor-color-palette', [['name' => '黑灰']]);
  /*----------禁用编辑器自定义颜色----------*/
  //add_theme_support('disable-custom-colors');

});

/*=============================================
  加载样式
=============================================*/
add_action('wp_enqueue_scripts',  function () {

  wp_enqueue_style('modern-normalize', get_theme_file_uri('assets/css/modern-normalize.css'), [], false, 'all');
  wp_enqueue_style('style', get_stylesheet_uri(), [], filemtime(get_theme_file_path('style.css')), 'all');
  wp_enqueue_style('logged', get_theme_file_uri('assets/css/logged.css'), [], filemtime(get_theme_file_path('assets/css/logged.css')), 'all');

  wp_enqueue_script('jquery');
  wp_enqueue_script('resizesensor-min', get_theme_file_uri('assets/js/resizesensor.min.js'), [], false, true);
  wp_enqueue_script('stickysidebar-min', get_theme_file_uri('assets/js/stickysidebar.min.js'), [], false, true);
  wp_enqueue_script('scripts', get_theme_file_uri('assets/js/scripts.js'), [], filemtime(get_theme_file_path('assets/js/scripts.js')), true);

  if (comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  /* $custom_css = '';
  $main_color = get_theme_mod('wtb_main_color', 'default');

  if ($main_color != 'default') $custom_css .= ":root { --cw-base: var(--cw-{$main_color}) !important; --cw-base-rgb: var(--cw-{$main_color}-rgb) !important; --cw-base-dark: var(--cw-{$main_color}-dark) !important;}";

  wp_add_inline_style('style', $custom_css); */
});

/*=============================================
  禁止自动生成的图片尺寸
=============================================*/
add_action('intermediate_image_sizes_advanced', function ($sizes) {
  unset($sizes['thumbnail']);     // 150
  unset($sizes['medium']);        // 300
  unset($sizes['medium_large']);  // 768
  unset($sizes['large']);         // 1024
  unset($sizes['1536x1536']);
  unset($sizes['2048x2048']);
  return $sizes;
}, 10);

/*=============================================
  低于 IE11 浏览器
=============================================*/
add_action('wp_body_open', function () {
  if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~Trident/7.0(; Touch)?; rv:11.0~', $_SERVER['HTTP_USER_AGENT'])) {
    printf(
      '<div class="browser alert alert-danger">%s%s',
      __('浏览器版本可能过低！该网站将在此浏览器中提供有限的显示效果与功能，建议升级为Chrome，Firefox，Safari，Edge等以获取更好的体验！', 'wtb'),
      sprintf(
        '<a href="https://browsehappy.com/" target="_blank" rel="external nofollow noopener noreferrer">%s</a>',
        __('最新版本浏览器', 'wtb'),
      ),
    );
  }
}, 10);

require_once get_theme_file_path('includes/optimize.php');
require_once get_theme_file_path('includes/hooks.php');
require_once get_theme_file_path('includes/metaog.php');

require_once get_theme_file_path('widgets/widget.php');

require_once get_theme_file_path('user/user.php');

require_once get_theme_file_path('wp-clean-up/wp-clean-up.php');

require_once get_theme_file_path('classes/class-breadcrumbs.php');
require_once get_theme_file_path('classes/class-duplicatepost.php');
require_once get_theme_file_path('classes/class-commentshtml5.php');
