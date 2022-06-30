<?php
get_header();

the_archive_title('<h2>', '</h2>');
the_archive_description();

get_template_part('template-parts/query', 'posts');
get_template_part('template-parts/pagination');

get_sidebar();

get_footer();
