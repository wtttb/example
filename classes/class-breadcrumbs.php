<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('wtb_Breadcrumbs')) {
  class wtb_Breadcrumbs
  {
    public $lists = [];

    public function __construct()
    {
      add_action('wtb_breadcrumbs', [$this, 'breadcrumbs']);
      add_action('wp_head', [$this, 'schema']);
    }

    public function breadcrumbs()
    {
      $items = $this->lists();
      if ($items && !is_home() && !is_front_page()) {
        echo '<nav class="breadcrumbs"><ol>';
        foreach ($items as $item) :
          $title = !empty($item['title']) ? $item['title'] : '';
          $link  = isset($item['link']) ? $item['link'] : '';
          if ($link == '#') {
            echo "<li>{$title}</li>";
          } else {
            echo "<li><a href='{$link}'>{$title}</a></li>";
          }
        endforeach;
        echo '</ol></nav>';
      }
    }

    public function schema()
    {
      $items = $this->lists();
      if ($items && !is_home() && !is_front_page()) {
        $count = count($items);
        echo '<script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [';
        $i = 1;
        if (!empty($items)) {
          foreach ($items as $item) :
            $title = !empty($item['title']) ? $item['title'] : '';
            $link  = isset($item['link']) ? $item['link'] : '';
            if (!empty($title)) {
              echo "{
          '@type': 'ListItem',
          'position': $i,
          'item': {
            '@id': '$link',
            'name': '$title'
          }
        }";
              if ($i < $count) echo ',';
            }
            $i++;
          endforeach;
        }
        echo ']
      }
      </script>';
      }
    }

    public function lists()
    {
      $lists = [];
      $home  = get_bloginfo('url');
      $title = '首页';

      // 首页
      if (is_front_page() && is_home()) {
        $lists[] = [];
      }

      // 日期归档
      else if (is_date()) {

        $year  = get_the_date('Y');
        $month = get_the_date('m');
        $day   = get_the_date('d');

        // 年
        if (is_year()) {
          $lists[] = ['link' => $home, 'title' => $title];
          $lists[] = [
            'link'  => get_year_link($year),
            'title' => $year,
          ];
        }

        // 月
        else if (is_month()) {
          $lists[] = ['link' => $home, 'title' => $title];
          $lists[] = [
            'link'  => get_year_link($year),
            'title' => $year,
          ];
          $lists[] = [
            'link'  => get_month_link($year, $month),
            'title' => $month,
          ];
        }

        // 日
        else if (is_date()) {
          $lists[] = ['link' => $home, 'title' => $title];
          $lists[] = [
            'link'  => get_year_link($year),
            'title' => $year,
          ];
          $lists[] = [
            'link'  => get_month_link($year, $month),
            'title' => $month,
          ];
          $lists[] = [
            'link'  => get_day_link($year, $month, $day),
            'title' => $day,
          ];
        }
      }

      // 标签
      else if (is_tag()) {
        $tag_id = get_query_var('tag_id');

        $lists[] = ['link' => $home, 'title' => $title];
        $lists[] = [
          'link'  => get_tag_link($tag_id),
          'title' => get_tag($tag_id)->name,
        ];
      }

      // 作者页面
      else if (is_author()) {
        $lists[] = ['link' => $home, 'title' => $title];
        $lists[] = [
          'link'  => get_author_posts_url(get_the_author_meta("ID")),
          'title' => get_the_author(),
        ];
      }

      // 媒体
      else if (is_attachment()) {
        $lists[] = ['link' => $home, 'title' => $title];
        $lists[] = [
          'link'  => '#',
          'title' => '附件',
        ];
      }

      // 搜索
      else if (is_search()) {
        $lists[] = ['link' => $home, 'title' => $title];
        $lists[] = [
          'link'  => get_search_link(get_query_var('s')),
          'title' => sanitize_text_field(get_query_var('s')),
        ];
      }

      // 404
      else if (is_404()) {
        $lists[] = ['link' => $home, 'title' => $title];
        $lists[] = [
          'link'  => '#',
          'title' => '404',
        ];
      }

      // 页面
      else if (is_page()) {
        global $post;
        $home_page = get_post(get_option('page_on_front'));
        for ($i = count($post->ancestors) - 1; $i >= 0; $i--) {
          if (($home_page->ID) != ($post->ancestors[$i])) {
            $lists[] = [
              'link'  => get_permalink($post->ancestors[$i]),
              'title' => get_the_title($post->ancestors[$i]),
            ];
          }
        }
      }

      // 文章
      else if (is_singular()) {
        $lists[] = ['link' => $home, 'title' => $title];

        global $post;
        $post_taxonomies = get_object_taxonomies($post);
        $post_terms      = wp_get_object_terms($post->ID, $post_taxonomies);

        if (!empty($post_terms)) {
          $taxonomy         = $post_terms[0]->taxonomy;
          $term_id          = $post_terms[0]->term_id;
          $cat_parent_id    = get_ancestors($term_id, $taxonomy);
          $cat_parent_id_re = array_reverse($cat_parent_id);

          foreach ($cat_parent_id_re as $id) {

            $parent_link = get_term_link($id, $taxonomy);
            $parent_name = get_term_by('id', $id, $taxonomy);

            $lists[] = [
              'link'  => $parent_link,
              'title' => $parent_name->name,
            ];
          }

          $lists[] = [
            'link'  => get_term_link($term_id, $taxonomy),
            'title' => $post_terms[0]->name,
          ];
        }

        $lists[] = [
          'link'  => get_permalink($post->ID),
          'title' => get_the_title($post->ID),
        ];
      }

      // 分类
      else if (is_archive()) {
        $lists[] = ['link' => $home, 'title' => $title];

        $cat_id            = get_query_var('cat');
        $objects           = get_queried_object($cat_id);
        $taxonomy          = $objects->taxonomy;
        $term_id           = $objects->term_id;
        $cat_parents_id    = get_ancestors($term_id, $taxonomy);
        $cat_parents_id_re = array_reverse($cat_parents_id);

        foreach ($cat_parents_id_re as $id) {
          $parent_link = get_term_link($id, $taxonomy);
          $parent_name = get_term_by('id', $id, $taxonomy);
          $lists[] = [
            'link'  => $parent_link,
            'title' => $parent_name->name,
          ];
        }

        $lists[] = [
          'link'  => get_term_link($term_id, $taxonomy),
          'title' => $objects->name,
        ];
      }

      return $lists;
    }
  }
}

new wtb_Breadcrumbs;
