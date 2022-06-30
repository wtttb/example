<?php
get_header();

if (have_posts()) :
  while (have_posts()) : the_post();

    // 内容
    the_content();
    get_template_part('template-parts/pagination');

    // 评论
    if (comments_open() || get_comments_number()) {
      echo '<div class="post__comments">';
      comments_template();
      echo '</div>';
    }
  endwhile;
endif;
rewind_posts(); // 相同查询

wp_reset_postdata(); // 不同查询

$secondary_query = new WP_Query('category_name=example-category');
if ($secondary_query->have_posts()) :
  while ($secondary_query->have_posts()) : $secondary_query->the_post();
    the_title();
  endwhile;
endif;
wp_reset_postdata();

get_footer();
