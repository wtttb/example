<?php

if (is_single()) {
  $main_adds = [
    'posts_per_page' => 5,
    'orderby'        => 'rand',
    'tax_query'      => [[
      'taxonomy'         => 'category',
      'field'            => 'slug',
      'terms'            => wtb_get_term_linkslugs(),
      'include_children' => false,
      'no_found_rows'    => true,
    ]],
  ];
} else {

  if (is_tag()) {
    $main_add = ['tag' => get_query_var('tag')];
  } else
  if (is_search()) {
    $main_add = ['s' => get_query_var('s')];
  } else {
    $main_add = ['cat' => get_query_var('cat')];
  }

  $main_arrs = [
    'orderby'        => 'modified',
    'posts_per_page' => get_option('posts_per_page'),
    'paged'          => (get_query_var('paged')) ? get_query_var('paged') : 1,
  ];

  $main_adds = array_merge($main_arrs, $main_add);
}

$main_args = [
  'post_type'           => 'post',
  'fields'              => 'ids',
  'orderby'             => 'modified',
  'posts_per_page'      => get_option('posts_per_page'),
  'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
  'ignore_sticky_posts' => true,
];

$main_query = new WP_Query(array_merge($main_args, $main_adds));

while ($main_query->have_posts()) :
  $main_query->the_post();
  $excerpt = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt()));
?>
  <article>
    <!-- 特色图像 || 文章第一个图 -->
    <?php if (has_post_thumbnail() || wtb_get_postfirst_image(true)) : ?>
      <figure>
        <?php
        if (has_post_thumbnail()) {
          echo get_the_post_thumbnail($post, 'full');
        } else {
          echo wtb_get_postfirst_image(true);
        }
        ?>
      </figure>
    <?php endif; ?>

    <!-- 标题 -->
    <?= the_title('<h3><a href="' . esc_url(get_permalink()) . '" title="' . esc_attr(the_title_attribute('echo=0')) . '" rel="bookmark">', '</a></h3>'); ?>

    <!-- 作者 -->
    <?= the_author_posts_link(); ?>
    <?= wtb_get_user_role(get_post($id)->post_author); ?>

    <!-- 时间 -->
    <time datetime="<?= get_the_date('Y-m-d A G:i:s'); ?>"><?= get_the_time(); ?></time>

    <!-- 评论 -->
    <a href="<?= esc_url(get_comments_link()); ?>" title="<?= esc_attr(the_title_attribute('echo=0') . __('的评论')); ?>">
      <?= get_comments_number(0, 1, '%s'); ?>评论
    </a>

    <!-- 浏览量 -->
    <?= wtb_get_post_views('<span>', '阅读</span>'); ?>

    <!-- 摘要（去除空格的） -->
    <?= $excerpt ? "<p>{$excerpt}</p>" : ''; ?>

    <!-- 分类标签集合 -->
    <?= wtb_get_postcattag_linknames(true, '<div class="posts__catag">', '</div>'); ?>

    <!-- 编辑文章 -->
    <?= get_template_part('template-parts/post', 'manage'); ?>

  </article>
<?php
endwhile;
wp_reset_postdata();
