<?php
get_header();

if (have_posts()) :
  while (have_posts()) : the_post();

    // 标题
    the_title('<h1>', '</h1>');
    get_template_part('template-parts/post', 'manage');

    // 内容
    the_content();
    get_template_part('template-parts/pagination');

    // 带有缩略图上下篇
    get_template_part('template-parts/post', 'nav');

    // 评论
    if (comments_open() || get_comments_number()) {
      echo '<div class="post__comments">';
      comments_template();
      echo '</div>';
    }
  endwhile;
endif;
rewind_posts(); // 相同查询

// 同类相关随机文章
$rand_query = new WP_Query([
  'post_type' => 'post',
  'orderby'   => 'rand',
  'tax_query' => [[
    'taxonomy'         => 'category',
    'field'            => 'slug',
    'terms'            => wtb_get_term_slugs(),
    'include_children' => false,
    'no_found_rows'    => true,
  ]],
  'fields'              => 'ids',
  'posts_per_page'      => 3,
  'ignore_sticky_posts' => true,
]);
while ($rand_query->have_posts()) :
  $rand_query->the_post();
  the_title();
  echo '<br>';
endwhile;
wp_reset_postdata();

get_footer();
