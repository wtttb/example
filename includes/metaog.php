<?php

if (!defined('ABSPATH'))
  exit;

/*=============================================
  SEO
=============================================*/
add_action('wp_head', function () {


  $cat_id      = get_query_var('cat');
  $tag_id      = get_query_var('tag_id');
  $query_s     = get_query_var('s');
  $site_search = home_url('?s=' . $query_s);
  $site_url    = home_url();
  $site_key    = get_theme_mod('wtb_keywords');
  $site_desc   = get_theme_mod('wtb_description');
  $site_share  = wp_get_attachment_image_url(get_theme_mod('wtb_share', '1200x627.jpg'));

  if (is_archive()) {
    $objects  = get_queried_object($cat_id);
    $taxonomy = $objects->taxonomy;
    $term_id  = $objects->term_id;
    $cat_key  = get_term_meta($term_id, 'term_keywords', true) ? get_term_meta($term_id, 'term_keywords', true) : '';
    $cat_name = get_term($term_id)->name;
    $cat_desc = get_term($term_id)->description;
  }

  if (is_singular()) {
    global $post;
    $post_id         = get_the_ID();
    $post_title      = get_the_title($post_id);
    $post_url        = get_permalink($post_id);
    $post_thumb      = get_the_post_thumbnail_url($post_id, '1200x627.jpg');
    $post_excerpt    = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt($post_id)), true);
    $post_catag      = wtb_get_postcattag_linknames();
    $post_time       = get_the_time('Y-m-d');
    $post_modified   = get_the_modified_date('Y-m-d');
    $post_taxonomies = get_object_taxonomies($post);
    $post_terms      = wp_get_object_terms($post->ID, $post_taxonomies);
    if ($post_terms) {
      foreach ($post_terms as $t) {
        if ($t->taxonomy != 'post_tag') {
          $post_cat = $t->slug;
        }
      }
    }
  }

  if (is_singular() || is_author()) {
    $author_id   = get_the_author_meta('ID');
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_desc = get_the_author_meta('description', $author_id);
    $author_url  = get_author_posts_url($author_id);
  }

  /*----------Meta SEO----------*/
  $seo_types = [
    is_singular('book') ? 'book'  : 'website',
    is_singular('product') ? 'product'  : 'website',
    is_singular('music')  ? 'music.song'  : 'website',
    is_singular('video') ? 'video'  : 'website',
    is_singular('profile') || is_author() ? 'profile'  : 'website',
    is_single() ? 'article'  : 'website',
  ];

  foreach ($seo_types as $type) {
    $seo_type = $type;
  }

  if (is_singular()) {
    $seo_key   = $post_catag;
    $seo_desc  = $post_excerpt;
    $seo_url   = $post_url;
    $seo_title = $post_title;
    $seo_img   = $post_thumb ? $post_thumb : $site_share;
  } else
  if (is_author()) {
    $seo_key   = $author_name . ',' . $site_key;
    $seo_desc  = $author_desc ? $author_desc : $site_desc;
    $seo_url   = get_author_posts_url($author_id);
    $seo_title = $author_name;
    $seo_img   = get_avatar_url($author_id) ? get_avatar_url($author_id) : $site_share;
  } else
   if (is_tag()) {
    $seo_key   = get_tag($tag_id)->name;
    $seo_desc  = get_tag($tag_id)->name;
    $seo_url   = get_tag_link($tag_id);
    $seo_title = get_tag($tag_id)->name;
    $seo_img   = $site_share;
  } else
  if (is_search()) {
    $seo_key   = $query_s;
    $seo_desc  = $query_s;
    $seo_url   = get_search_link($query_s);
    $seo_title = $query_s;
    $seo_img   = $site_share;
  } else
  if (is_archive()) {
    $seo_key   = $cat_key ? $cat_key : $site_key;
    $seo_desc  = $cat_desc ? $cat_desc : $site_desc;
    $seo_url   = get_term_link($term_id, $taxonomy);
    $seo_title = $cat_name;
    $seo_img   = $site_share;
  } else {
    $seo_key   = $site_key;
    $seo_desc  = $site_desc;
    $seo_url   = get_bloginfo('url');
    $seo_title = get_bloginfo('name');
    $seo_img   = $site_share;
  }
  echo "
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no'>
    <meta name='robots' content='index, follow'>
    <meta name='rating' content='general'>
    <meta name='googlebot' content='index,follow'>
    <meta name='google' content='nositelinkssearchbox'>
    <meta name='keywords' content='{$seo_key}'>
    <meta name='description' content='{$seo_desc}'>
  ";

  echo "
    <meta property='og:type' content='{$seo_type}'>
    <meta property='og:title' content='{$seo_title}'>
    <meta property='og:url' content='{$seo_url}'>
    <meta property='og:image' content='{$seo_img}'>
    <meta property='og:description' content='{$seo_desc}'>

    <meta property='twitter:card' content='summary_large_image'>
    <meta property='twitter:url' content='{$seo_url}'>
    <meta property='twitter:title' content='{$seo_title}'>
    <meta property='twitter:description' content='{$seo_desc}'>
    <meta property='twitter:image' content='{$seo_img}'>
  ";

  if (is_single()) {
    echo "
      <meta property='article:author' content='{$author_name}'>
      <meta property='article:published_time' content='{$post_time}'>
      <meta property='article:modified_time' content='{$post_modified}'>
      <meta property='article:section' content='{$post_cat}'>
      <meta property='article:tag' content='{$post_catag}'>
    ";
  }

  if (is_singular('profile') || is_author()) {
    $first_name = get_post_meta($post_id, 'first_name', true);  // 名字
    $last_name  = get_post_meta($post_id, 'last_name', true);   // 姓氏
    echo "
      <meta property='profile:first_name' content='{$first_name}'>
      <meta property='profile:last_name' content='{$last_name}'>
      <meta property='profile:username' content='{$author_name}'>
    ";
  }

  if (is_singular('book')) {
    $book_isbn    = get_post_meta($post_id, 'book_isbn', true);     // 编码
    $book_author  = get_post_meta($post_id, 'book_author', true);   // 作者
    $book_release = get_post_meta($post_id, 'book_release', true);  // 发布日期
    echo "
      <meta property='book:isbn' content='{$book_isbn}'>
      <meta property='book:author' content='{$book_author}'>
      <meta property='book:release_date' content='{$book_release}'>
      <meta property='book:tag' content='{$post_catag}'>
    ";
  }

  if (is_singular('business')) {
    $business_country = get_post_meta($post_id, 'business_country', true);  // 国家
    $business_city    = get_post_meta($post_id, 'business_city', true);     // 城市
    $business_region  = get_post_meta($post_id, 'business_region', true);   // 地区
    $business_address = get_post_meta($post_id, 'business_address', true);  // 地址
    $business_zipcode = get_post_meta($post_id, 'business_zipcode', true);  // 邮编
    echo "
      <meta property='business:contact_data:country_name' content='{$business_country}'>
      <meta property='business:contact_data:locality' content='{$business_city}'>
      <meta property='business:contact_data:region' content='{$business_region}'>
      <meta property='business:contact_data:street_address' content='{$business_address}'>
      <meta property='business:contact_data:postal_code' content='{$business_zipcode}'>
    ";
  }

  if (is_singular('product')) {
    $metap_currency = get_post_meta($post_id, 'product_currency', true);  // 币种
    $metap_price    = get_post_meta($post_id, 'product_price', true);     // 价格
    echo "
      <meta property='product:plural_title' content='{$post_title}'>
      <meta property='product:price.currency' content='{$metap_currency}'>
      <meta property='product:price.amount' content='{$metap_price}'>
    ";
  }

  if (is_singular('video')) {
    $video_series   = get_post_meta($post_id, 'video_series', true);    // 系列
    $video_director = get_post_meta($post_id, 'video_director', true);  // 导演
    $video_author   = get_post_meta($post_id, 'video_author', true);    // 作者
    $video_actor    = get_post_meta($post_id, 'video_actor', true);     // 演员
    $video_release  = get_post_meta($post_id, 'video_release', true);   // 发布日期
    $metav_duration = get_post_meta($post_id, 'video_duration', true);  // 长度
    if ($metav_duration) {
      $metav_s = $metav_duration[0] * 3600 + $metav_duration[1] * 60 + $metav_duration[2];
    }
    echo "
      <meta property='video:series' content='{$video_series}'>
      <meta property='video:director' content='{$video_director}'>
      <meta property='video:writer' content='{$video_author}'>
      <meta property='video:actor' content='{$video_actor}'>
      <meta property='video:duration' content='{$metav_s}'>
      <meta property='video:release_date' content='{$video_release}'>
      <meta property='video:tag' content='{$post_catag}'>
    ";
  }

  if (is_singular('music')) {
    $music_album    = get_post_meta($post_id, 'music_album', true);     // 专辑链接
    $music_creator  = get_post_meta($post_id, 'music_creator', true);   // 作词
    $music_musician = get_post_meta($post_id, 'music_musician', true);  // 作曲
    $music_duration = get_post_meta($post_id, 'music_duration', true);  // 长度
    $music_release  = get_post_meta($post_id, 'music_release', true);   // 发布日期
    echo "
      <meta property='og:audio' content='{$music_album}'>
      <meta property='music:album' content='{$music_album}'>
      <meta property='music:creator' content='{$music_creator}'>
      <meta property='music:musician' content='{$music_musician}'>
      <meta property='music:duration' content='{$music_duration}'>
      <meta property='music:release_date' content='{$music_release}'>
    ";
  }

  /*----------application/ld+json----------*/
  if (is_search() || is_singular()) {
    echo "<script type='application/ld+json'>{ '@context': 'https://schema.org',";

    if (is_search())
      echo "
      '@type': 'WebSite',
      'url': '{$site_url}',
      'potentialAction': {
        '@type': 'SearchAction',
        'target': '{$site_search}',
        'query': 'required name={$query_s}',
      }
    ";

    if (is_single())
      echo "
      '@type': 'Article',
      'mainEntityOfPage': {
        '@type': 'WebPage',
        '@id': '{$post_url}',
      },
      'headline': '{$post_title}',
      'description': '{$post_excerpt}',
      'image': '{$post_thumb}',
      'author': {
        '@type': 'Person',
        'name': '{$author_name}',
        'url': '{$author_url}',
      },
      'datePublished': '{$post_time}'
      'dateModified': '{$post_modified}',
    ";

    if (is_singular('product')) {
      $josnp_currency    = get_post_meta($post_id, 'product_currency', true);                                  // 币种
      $josnp_price       = get_post_meta($post_id, 'product_price', true);                                     // 价格
      $jsonp_brand       = get_the_title(get_post_meta($post_id, 'product_brand', true));                      // 品牌
      $jsonp_ratingValue = post_rating_number();                                                               // 评分
      $jsonp_ratingCount = wp_count_comments($post_id)->approved;                                              // 评论数量
      $jsonp_code        = preg_replace('/( |　|\s)*/', '', get_post_meta($post_id, 'product_barcode', true));  // 条码
      $code_len          = mb_strlen($jsonp_code, 'utf8');
      switch ($code_len) {
        case ($code_len < 9):
          $jsonp_bar_code = "'gtin8':'{$jsonp_code}'";
          break;
        case ($code_len > 8 && $code_len < 14):
          $jsonp_bar_code = "'gtin13':'{$jsonp_code}'";
          break;
        case ($code_len > 13):
          $jsonp_bar_code = "'gtin14':'{$jsonp_code}'";
          break;
        default:
      }
      echo "
      '@type': 'Product',
      'name': '{$post_title}',
      'image': '{$post_thumb}',
      'description': '{$post_excerpt}',
      'brand': {
        '@type': 'Brand',
        'name': '{$jsonp_brand}',
      },
      {$jsonp_bar_code}
      ,
      'offers': {
        '@type': 'Offer',
        'url': '{$post_url}';
        'priceCurrency': {$josnp_currency},
        'price': '{$josnp_price}',
        'availability': 'https://schema.org/InStock',
      },
      'aggregateRating': {
        '@type': 'AggregateRating',
        'ratingValue': '{$jsonp_ratingValue}',
        'ratingCount': '{$jsonp_ratingCount}',
      }
    ";
    }

    if (is_singular('video')) {
      $video_url      = get_post_meta($post_id, 'video_url', true);                     // 视频地址
      $jsonv_duration = explode(':', get_post_meta($post_id, 'video_duration', true));  // 长度
      if ($jsonv_duration) {
        $jsonv_m  = $jsonv_duration[0] * 60 + $jsonv_duration[1];
        $jsonv_ms = "PT{$jsonv_m}M{$jsonv_duration[2]}S";
      }
      echo "
      '@type': 'VideoObject',
      'name': '{$post_title}',
      'description': '{$post_excerpt}',
      'thumbnailUrl' : '{$post_thumb}',
      'uploadDate': '{$post_time}',
      'duration': '{$jsonv_ms}',
      'publisher': {
        '@type': 'Organization',
        'name': '{$author_name}',
        'logo': {
          '@type': 'ImageObject',
          'url': '{$site_share}',
          'width': '',
          'height': ''
        }
      },
      'contentUrl': '{$post_url}',
      'embedUrl' : '{$video_url}',
    ";
    }

    echo "}</script>";
  }
});
