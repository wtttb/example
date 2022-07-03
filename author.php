<?php
get_header();

$id      = get_the_author_meta('ID');
$name    = get_the_author_meta('display_name', $id);
$slogn   = get_the_author_meta('user_slogn', $id);
$address = get_the_author_meta('user_address', $id);
$url     = get_the_author_meta('user_url', $id);
$desc    = get_the_author_meta('description', $id);
$email   = get_the_author_meta('user_email', $id);
$reg     = get_the_author_meta('user_registered', $id);
$status  = get_the_author_meta('user_status', $id);

get_footer();
