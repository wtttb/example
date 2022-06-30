<?php

if (!defined('ABSPATH'))
  exit;

/**
 * 精简古腾堡区块
 */
add_filter('allowed_block_types_all', function ($allowed_block_types, $block_editor_context) {

  /* if ($post->post_type === 'page') {
    $allowed_block_types[] = 'core/shortcode';
  } */

  $allowed_block_types = [
    'core/paragraph',
    'core/image',
    'core/heading',
    'core/gallery',
    'core/list',
    'core/quote',
    'core/cover',

    'core/code',
    'core/classic',
    'core/html',
    'core/preformatted',
    'core/pullquote',
    'core/table',
    /* 'core/verse', */

    'core/page-break',
    'core/spacer',
    'core/buttons',
    'core/columns',
    'core/group',
    'core/media-text',
    /* 'core/more', */
    'core/reusable-block',
    'core/separator',

    'core/shortcode',
    /* 'core/archives',
    'core/calendar',
    'core/categories',
    'core/latest-comments',
    'core/latest-posts',
    'core/rss',
    'core/search',
    'core/social',
    'core/tag-cloud', */
  ];

  if (current_user_can('administrator')) {
    $allowed_block_types[] = 'core/audio';
    $allowed_block_types[] = 'core/file';
    $allowed_block_types[] = 'core/video';
  }

  return $allowed_block_types;
}, 10, 2);

$aud     = get_theme_mod('wtb_aud_switch', true);
$aup     = get_theme_mod('wtb_aup_switch', true);
$aut     = get_theme_mod('wtb_aut_switch', true);
$uwbe    = get_theme_mod('wtb_uwbe_switch', true);
$guwbe   = get_theme_mod('wtb_guwbe_switch', true);
$ubefp   = get_theme_mod('wtb_ubefp_switch', false);
$restapi = get_theme_mod('wtb_restapi_switch', true);

if ($aud) add_filter('automatic_updater_disabled', '__return_true'); // 禁用 WP 自动更新
if ($aup) add_filter('auto_update_plugin', '__return_false'); // 禁止插件自动更新
if ($aut) add_filter('auto_update_theme', '__return_false'); // 禁止主题自动更新
if ($uwbe) add_filter('use_widgets_block_editor', '__return_false'); // 使用旧版小工具
if ($guwbe) add_filter('gutenberg_use_widgets_block_editor', '__return_false'); // 古腾堡移除旧版小部件
if ($ubefp) add_filter('use_block_editor_for_post', '__return_false');  // 使用旧版文章编辑器

if ($restapi) {
  add_filter('rest_enabled', '_return_false'); // REST API 功能失效
  add_filter('rest_jsonp_enabled', '_return_false'); // REST API 功能失效
}

add_filter('pre_site_transient_browser_' . md5($_SERVER['HTTP_USER_AGENT']), '__return_null');  // 禁止浏览器版本检查
add_filter('pre_option_link_manager_enabled', '__return_true'); // 添加自带友情链接

remove_action('xmlrpc_rsd_apis', 'rest_output_rsd'); // REST API RSD 不影响api功能
remove_action('wp_head', 'rest_output_link_wp_head', 10); // REST API 仅标头，不影响api功能
remove_action('template_redirect', 'rest_output_link_header', 11, 0);  // REST API 仅标头，不影响api功能

add_filter('xmlrpc_enabled', '__return_false'); // XMLRPC.php
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters'); // 5.9.1 前端 SVG
remove_action('wp_head', 'wp_resource_hints', 2); // dns-prefetch for w.org 点击链接之前进行 DNS 查询
remove_action('wp_head', 'rsd_link'); // EditURI 第三方工具发布帖子
remove_action('wp_head', 'wlwmanifest_link'); // wlwmanifest 离线发布，一般没用
remove_action('wp_head', 'wp_generator'); // WP版本
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0); // shortlink 短链接，使通过即时消息共享链接更容易
remove_action('wp_head', 'feed_links', 2); // add_theme_support('automatic-feed-links') 添加的RSS
remove_action('wp_head', 'feed_links_extra', 3); // 评论RSS
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // next/prev 链接

add_action('wp_enqueue_scripts', function () {
  wp_dequeue_style('wp-block-library'); // LINK CSS id="wp-block-library-css"
  wp_dequeue_style('wp-block-library-theme'); // 内联CSS id="wp-block-library-theme-inline-css"
  wp_dequeue_style('wc-blocks-style'); // WOOCOMMERCE CSS
}, 10);
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles'); // 内联 id="global-styles-inline-css"
//remove_action('wp_head', 'wp_robots', 1); // robots 使用默认设置，建议保留
//remove_action('wp_head', 'rel_canonical'); // canonical 索引首选连接，防止重复问题，建议保留

// Oembed
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
add_filter('rewrite_rules_array',   function ($rules) {
  foreach ($rules as $rule => $rewrite) {
    if (false !== strpos($rewrite, 'embed=true')) {
      unset($rules[$rule]);
    }
  }
  return $rules;
});
remove_filter('the_content', [$GLOBALS['wp_embed'], 'run_shortcode'], 8);
remove_filter('widget_text_content', [$GLOBALS['wp_embed'], 'run_shortcode'], 8);
remove_filter('the_content', [$GLOBALS['wp_embed'], 'autoembed'], 8);
remove_filter('widget_text_content', [$GLOBALS['wp_embed'], 'autoembed'], 8);
remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
remove_action('wp_head', 'wp_oembed_add_host_js');
add_filter('embed_oembed_discover', '__return_false');
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
add_filter('tiny_mce_plugins', 'tiny_mce_plugins_oembed');
function tiny_mce_plugins_oembed($plugins)
{
  return array_diff($plugins, array('wpembed'));
}

// Emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('embed_head', 'print_emoji_detection_script');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
add_filter('tiny_mce_plugins', 'tiny_mce_plugins_emoji');
function tiny_mce_plugins($plugins)
{
  if (is_array($plugins)) {
    $plugins = array_diff($plugins, ['wpemoji']);
  }
  return $plugins;
}
if ((int) get_option('use_smilies') === 1) {
  update_option('use_smilies', 0);
}

/**
 * 移除 jquery-migrate
 */
add_action('wp_default_scripts', function ($scripts) {
  if (!is_admin() && isset($scripts->registered['jquery'])) {
    $script = $scripts->registered['jquery'];
    if ($script->deps) {
      $script->deps = array_diff($script->deps, ['jquery-migrate']);
    }
  }
});

/**
 * 禁用 PingBack
 */
add_action('pre_ping', function (&$links) {
  foreach ($links as $l => $link)
    if (0 === strpos($link, get_option('home'))) {
      unset($links[$l]);
    }
});

/**
 * 移除 Google Fonts
 */
add_action('init', function () {
  wp_deregister_style('open-sans');
  wp_register_style('open-sans', false);
  wp_enqueue_style('open-sans', '');
});

/**
 * Cravatar 替代 Gravatar
 */
add_filter('um_user_avatar_url_filter', 'get_cn_avatar_url', 1);
add_filter('bp_gravatar_url', 'get_cn_avatar_url', 1);
add_filter('get_avatar_url', 'get_cn_avatar_url', 1);
function get_cn_avatar_url($url)
{
  return str_replace(['www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com', 'cn.gravatar.com', 'gravatar.com'], 'cravatar.cn', $url);
}
add_filter('avatar_defaults', function ($avatar_defaults) {
  $avatar_defaults['gravatar_default'] = 'Cravatar 标志';
  return $avatar_defaults;
}, 1);
add_filter('user_profile_picture_description', function () {
  return '<a href="https://cravatar.cn" target="_blank">您可以在 Cravatar 修改您的资料图片</a>';
}, 1);

/*==================================================================*/

/**
 * 帖子元框
 */
add_action('admin_menu',  function () {
  $post_type = 'post';
  $context   = 'normal';
  remove_meta_box('trackbacksdiv', $post_type, $context); // 引用元框
});

/**
 * 后台页脚
 */
add_filter('admin_footer_text', '__return_empty_string'); // 删除左管理员页脚文本
add_filter('update_footer', '__return_empty_string', 11); // 删除右管理员页脚文本
//add_filter('screen_options_show_screen', '__return_false'); // 删除屏幕选项选项卡

/**
 * 仪表盘清理
 */
add_action('wp_dashboard_setup', function () {
  remove_action('welcome_panel', 'wp_welcome_panel');               // 移除 “欢迎” 面板
  remove_meta_box('dashboard_site_health', 'dashboard', 'normal');  // 删除 “站点健康” 元框
  remove_meta_box('dashboard_quick_press', 'dashboard', 'side');    // 删除 “快速草稿” 元框
  remove_meta_box('dashboard_primary', 'dashboard', 'side');        // 删除 “新闻” 元框
  //remove_meta_box('dashboard_right_now', 'dashboard', 'normal');    // 删除 “概览” 元框
  //remove_meta_box('dashboard_activity', 'dashboard', 'normal');     // 移除 “动态” 元框
});

/**
 * 所有帮助标签
 */
add_action('admin_head', function () {
  $screen = get_current_screen();
  $screen->remove_help_tabs();
});

/**
 * 管理栏
 */
add_action('wp_before_admin_bar_render',  function () {
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu('wp-logo');  // 删除 WordPress 徽标
  $wp_admin_bar->remove_menu('comments'); // 删除评论链接
}, 999);
