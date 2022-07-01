<?php

if (!defined('ABSPATH'))
  exit;

/*=============================================
  菜单 class
=============================================*/
add_filter('nav_menu_css_class', 'wtb_menu_class_filter', 100, true);
add_filter('nav_menu_item_id', 'wtb_menu_class_filter', 100, true);
add_filter('page_css_class', 'wtb_menu_class_filter', 100, true);
function wtb_menu_class_filter($var)
{
  return is_array($var) ? array_intersect($var, ['current-menu-item', 'current-menu-parent', 'current-post-ancestor']) : '';
}

/*=============================================
  标题添加数量
=============================================*/
add_filter('get_the_archive_title', function ($title) {
  $posts_nums = "(" . absint($GLOBALS['wp_query']->found_posts) . ")";
  if (is_category()) {
    $title  = single_cat_title('', false) . $posts_nums;
  } elseif (is_tag()) {
    $title  = single_tag_title('', false) . $posts_nums;
  } elseif (is_author()) {
    $title  = get_the_author() . $posts_nums;
  } elseif (is_year()) {
    $title  = get_the_date('Y') . $posts_nums;
  } elseif (is_month()) {
    $title  = get_the_date('Y m') . $posts_nums;
  } elseif (is_day()) {
    $title  = get_the_date('Y m d') . $posts_nums;
  } elseif (is_post_type_archive()) {
    $title  = post_type_archive_title('', false) . $posts_nums;
  } elseif (is_tax()) {
    $object = get_queried_object();
    if ($object) {
      $title  = single_term_title('', false) . $posts_nums;
    }
  } elseif (is_search()) {
    $title  = sprintf(
      __('搜索 %s 相关结果有: %s篇', 'wtb'),
      "<span>{ " . get_search_query() . " }</span>",
      "<span>{$posts_nums}</span>"
    );
  }
  return $title;
});

/*=============================================
  文章浏览量
=============================================*/
if (!function_exists('wtb_views_post_types')) {
  function wtb_views_post_types()
  {
    return ['post'];
  }
}

if (!function_exists('wtb_get_views_key')) {
  function wtb_get_views_key()
  {
    return get_theme_mod('wtb_views_key', 'views');
  }
}

if (!function_exists('wtb_get_post_views')) {
  function wtb_get_post_views($before = '', $after = '')
  {
    $views = get_post_meta(get_the_ID(), wtb_get_views_key(), true);
    $output = $views ? $views : '0';
    return $before . $output . $after;
  }
}

add_action('get_header', function () {
  if (is_singular(wtb_views_post_types())) {
    $id     = get_the_ID();
    $views  = wtb_get_post_views();
    $cookie = get_theme_mod('wtb_cookie_switch', false);
    if ($cookie == false) {
      update_post_meta($id, wtb_get_views_key(), $views + 1);
    } else if ($cookie == true) {
      $cookies = $_COOKIE[wtb_get_views_key()  . $id . COOKIEHASH];
      if (!isset($cookies) && $cookies != '1') {
        update_post_meta($id, wtb_get_views_key(), $views + 1);
        setcookie(wtb_get_views_key()  . $id . COOKIEHASH, '1', time() + 86400, COOKIEPATH, COOKIE_DOMAIN);
      }
    }
  }
});

foreach (wtb_views_post_types() as $value) {
  add_action('save_post_' . $value, function () {
    $id    = get_the_ID();
    $views = wtb_get_post_views();
    $rand  = get_theme_mod('wtb_rand_switch', true);
    $array = explode(',', get_theme_mod('wtb_randnum', '64,128'));
    if ($rand == true && ($views == '0' || $views == '')) {
      delete_post_meta($id, wtb_get_views_key());
      update_post_meta($id, wtb_get_views_key(), (int)rand($array[0], $array[1]));
    } else if ($rand == false && ($views == '0' || $views == '')) {
      delete_post_meta($id, wtb_get_views_key());
      update_post_meta($id, wtb_get_views_key(), '0');
    }
  });
}

/*=============================================
  时间显示几天前
=============================================*/
add_filter('the_time', function () {
  $time      = get_post_time('G', true);
  $time_diff = time() - $time;
  if ($time_diff > 0 && $time_diff < 24 * 60 * 60)
    $display = human_time_diff($time) . __('前', 'wtb');
  else
    $display = get_the_time(get_option('date_format'));
  return $display;
});

/*=============================================
  摘要字数
=============================================*/
add_filter('excerpt_length', function ($length) {
  return get_theme_mod('wtb_excerpt_length', 96);
});

/*=============================================
  文章最后更新时间
=============================================*/
add_filter('the_content', function ($content) {
  $output   = '';
  $time     = get_the_time('U');
  $modified = get_the_modified_time('U');
  if ($modified >= $time + 86400) {
    $update = get_the_modified_time('F jS, Y');
    $uptime = get_the_modified_time('h:i a');
    $output .= '<p class="last-updated">Last updated on ' . $update . ' at ' . $uptime . '</p>';
  }
  $output .= $content;
  return $output;
});

/*=============================================
  连接 Goto 跳转
=============================================*/
add_filter('the_content', function ($content) {
  preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $post_matchs);
  if ($post_matchs) {
    foreach ($post_matchs[2] as $value) {
      if (strpos($value, '://') !== false && strpos($value, home_url()) === false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i', $value) && !preg_match('/(ed2k|thunder|Flashget|flashget|qqdl):\/\//i', $value)) {
        $req_to  = home_url('goto/?url=' . base64_encode($value));
        $content = str_replace('href="' . $value . '"', 'href="' . $req_to . '" target="_blank" rel="external nofollow noopener noreferrer"', $content);
      }
    }
  }
  return $content;
}, 999);

add_action('load-themes.php', function () {
  global $pagenow;
  $has_path = ABSPATH . 'goto';
  $old_file = get_theme_file_path('includes/goto.php');
  $new_file = ABSPATH . 'goto/index.php';
  if ($pagenow == 'themes.php' || (isset($_GET['activated']) && $_GET['activated'] == 'true')) {
    if (!file_exists($has_path)) {
      mkdir($has_path);
    }
    if (file_exists($old_file)) {
      copy($old_file, $new_file);
    }
  }
});

/*=============================================
  头条自动推送JS
=============================================*/
add_action('wp_footer', function () {
  if (get_theme_mod('wtb_ttautosubmit_switch', false))
    echo "<script type='text/javascript'>!function(){var e=document.createElement('script');e.src='https://lf1-cdn-tos.bytegoofy.com/goofy/ttzz/push.js?0e19cdc67b958008d1d6c017052f53ee7991cc1ed11bf34132b851984d3f927c3871f0d6a9220c04b06cd03d5ba8e733fe66d20303562cd119c1d6f449af6378',e.id='ttzz';var c=document.getElementsByTagName('script')[0];c.parentNode.insertBefore(e,c)}(window);</script>";
}, 999);

/*=============================================
  提交收录
=============================================*/
$submit_post_types = ['post'];
foreach ($submit_post_types as $spt_value) {

  add_action('save_post_' . $spt_value, function ($post_id, $post, $update) {

    $bs_submit = ['baidu' => get_theme_mod('wtb_baidu'), 'shenma' => get_theme_mod('wtb_shenma')];
    $by_submit = get_theme_mod('wtb_bingyandex');

    if ($post->post_status != 'publish') return;

    /*----------百度 + 神马----------*/
    if ($bs_submit) {
      foreach ($bs_submit as $key => $value) {
        if (get_post_meta($post_id, $key . '_submit', true) == 1) return;
        $bs_ch      = curl_init();
        $bs_options = [
          CURLOPT_URL            => $value,
          CURLOPT_POST           => true,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS     => get_permalink($post_id),
          CURLOPT_HTTPHEADER     => ['Content-Type: text/plain'],
        ];
        curl_setopt_array($bs_ch, $bs_options);
        $bs_result = json_decode(curl_exec($bs_ch, true));
        if (array_key_exists('success', $bs_result)) {
          add_post_meta($post_id, $key . '_submit', 1, true);
        }
      }
    }

    /*----------Bing/Yandex----------*/
    if ($by_submit) {
      wp_remote_post(
        add_query_arg(['url' => get_permalink($post_id), 'key' => $by_submit], 'https://api.indexnow.org/indexnow'),
        [
          'headers'   => ['Content-Type' => 'application/json; charset=utf-8'],
          'timeout'   => 10,
          'sslverify' => false,
          'blocking'  => true,
          'body'      => json_encode([
            'host'    => parse_url(get_permalink($post_id))['host'],
            'key'     => $by_submit,
            'urlList' => [get_permalink($post_id)],
          ])
        ],
      );
    }
  }, 10, 3);
}

/*=============================================
  评论作者链接
=============================================*/
add_filter('get_comment_author_link', function ($user_url, $author, $comment_ID) {
  $comment      = get_comment($comment_ID);
  $comment_user = $comment->comment_author;
  $id           = $comment->user_id;
  $url          = get_author_posts_url($id);
  $role         = wtb_get_user_role($id);
  $user_url     = "<a href='{$url}' title='{$comment_user}' rel='author'>{$comment_user}</a>{$role}";
  return $user_url;
}, 10, 3);

/*=============================================
  用户级别
=============================================*/
if (!function_exists('wtb_get_user_role')) {
  /**
   * @param int $id     登录用户或作者 ID
   * @return mixed
   */
  function wtb_get_user_role($id = '')
  {
    $role_info = get_user_by('id', $id);
    if ($role_info) {
      $role      = $role_info->roles[0];
      $roles     = [
        'administrator' => '管理员',  // 管理员
        'editor'        => '编辑',    // 编辑
        'author'        => '作者',    // 作者
        'contributor'   => '贡献者',  // 贡献者
        'subscriber'    => '订阅者',  // 订阅者
      ];
      foreach ($roles as $key => $val) {
        if ($role == $key) $output = "<span class='role-{$key}'>{$val}</span>";
      }
      return $output;
    }
  }
}

/*=============================================
  文章第一个图
=============================================*/
if (!function_exists('wtb_get_postfirst_image')) {
  /**
   * @param bool $imgORurl true：图片，false：链接
   * @return string
   */
  function wtb_get_postfirst_image($imgORurl = true)
  {
    global $post;
    preg_match_all('/<img.*?src=[\'|\"](.+?)[\'|\"].*?>/i', get_post($post)->post_content, $matches);
    if (empty($matches[1])) return;
    $output = '';
    $url    = esc_url($matches[1][0]);
    $title  = get_post($post)->post_title;
    if ($url) {
      if ($imgORurl == true) {
        $output = "<img src='{$url}' alt='{$title}' loading='lazy'>";
      } else {
        $output = $url;
      }
    }
    return $output;
  }
}

/*=============================================
  当前帖子（类别 + 标签集合）
=============================================*/
if (!function_exists('wtb_get_postcattag_linknames')) {
  /**
   * @param bool $aORtext   true：链接，false：文本
   * @param string $before  前 html
   * @param string $after   后 html
   * @return string
   */
  function wtb_get_postcattag_linknames($aORtext = false, $before = '', $after = '')
  {
    global $post;
    $output     = '';
    $apost      = is_singular() ? $post : get_post($post);
    $taxonomies = get_object_taxonomies($apost);
    $terms      = wp_get_object_terms($apost->ID, $taxonomies);
    if ($terms) {
      $output .= $before;
      foreach ($terms as $term) {
        $url  = esc_url(get_term_link($term->term_id, $term->taxonomy));
        $name = esc_html($term->name);
        if ($aORtext == true) {
          $output .= "<a href='{$url}' rel='category tag'>{$name}</a>";
        } else {
          $output .= "{$name},";
        }
      }
      $output .= $after;
    }
    return $output;
  }
}

/*=============================================
  当前分类法下所有分类 slug 数组
=============================================*/
if (!function_exists('wtb_get_term_slugs')) {
  /**
   * @param string|array $taxonomy  分类法名称
   * @return array
   */
  function wtb_get_term_slugs($taxonomy = '')
  {
    $output = '';
    // https://developer.wordpress.org/reference/functions/get_terms/
    foreach (get_terms(['taxonomy' => $taxonomy == '' ? 'category' : $taxonomy]) as $term) {
      $output .= esc_html($term->slug) . ',';
    }
    return array_filter(array_unique(explode(',', $output)));
  }
}

/*=============================================
  当前文章所属分类 slug 数组
=============================================*/
if (!function_exists('wtb_get_term_slugs')) {
  /**
   * @param string|array $taxonomy  分类法名称
   * @return array
   */
  function wtb_get_term_slugs($taxonomy = '')
  {
    $output = '';
    // https://developer.wordpress.org/reference/functions/get_the_terms/
    foreach (get_the_terms(get_the_ID(), $taxonomy == '' ? 'category' : $taxonomy) as $term) {
      $output .= esc_html($term->slug) . ',';
    }
    return array_filter(array_unique(explode(',', $output)));
  }
}
