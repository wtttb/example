<?php

if (is_tag()) {
  $main_add = ['tag' => get_query_var('tag')];
} else
  if (is_search()) {
  $main_add = ['s' => get_query_var('s')];
} else {
  $main_add = ['cat' => get_query_var('cat')];
}

$main_args = [
  'post_type'           => 'post',
  'fields'              => 'ids',
  'orderby'             => 'modified',
  'posts_per_page'      => get_option('posts_per_page'),
  'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
  'ignore_sticky_posts' => true,
];

$main_query = new WP_Query(array_merge($main_args, $main_add));

while ($main_query->have_posts()) :
  $main_query->the_post();
  get_template_part('template-parts/content', 'posts');
endwhile;
wp_reset_postdata();
