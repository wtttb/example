<?php get_header(); ?>

<?php the_archive_title('<h2>', '</h2>'); ?>

<?php get_template_part('template-parts/query', 'posts'); ?>
<?php get_template_part('template-parts/pagination'); ?>

<?php get_footer(); ?>