<?php
get_header();

if (have_posts()) :
  while (have_posts()) : the_post();

    // 内容
    the_content();
    get_template_part('template-parts/pagination');

  endwhile;
endif;
rewind_posts(); // 相同查询

get_footer();
