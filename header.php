<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <?php wp_head(); ?>
</head>

<body>
  <?php do_action('wp_body_open');  ?>

  <?php
  if (has_nav_menu('header_menu')) {
    wp_nav_menu([
      'theme_location'  => 'header_menu',
      'container'       => 'nav',
      'container_class' => 'cwmbtab-nav',
      'fallback_cb'     => false,
      'depth'           => 2,
    ]);
  }
  ?>

  <?php
  $homeurl  = esc_url(home_url());
  $sitename = esc_html(get_bloginfo('name'));
  $logo     = esc_url(wp_get_attachment_url(get_theme_mod('custom_logo')));
  $span     = !get_theme_mod('wtb_sitename_switch', true) ? esc_attr('heading__logo--hide') : '';
  $tag      = is_singular() ? "div" : "h1";
  ?>
  <<?= $tag; ?> class="heading__logo">
    <a href="<?= $homeurl; ?>" title="<?= $sitename; ?>">
      <?= $logo ? "<img src='{$logo}' loading='lazy'>" : ''; ?>
      <span class="<?= $span; ?>"><?= $sitename; ?></span>
    </a>
  </<?= $tag; ?>>

  <?php

  $curl    = add_query_arg($wp->query_string, '', home_url($wp->request));
  $userid  = get_current_user_id();
  if (is_user_logged_in()) :
  ?>

    <?= get_avatar($userid, 48); ?>

    <a href="<?= get_author_posts_url($userid); ?>" rel='author'> <span><?= __('我的主页', 'wtb'); ?></span> </a>

    <?php if (!current_user_can('subscriber')) : ?>
      <a href="<?= esc_url(admin_url('edit')); ?>" target="_blank"> <span><?= __('发布投稿', 'wtb'); ?></span> </a>
    <?php endif; ?>

    <a href="<?= admin_url('profile'); ?>" target="_blank"> <span><?= __('我的账户', 'wtb'); ?></span> </a>

  <?php else : ?>

  <?php endif; ?>

  <a class="cwhead-logbtn" href="<?= wp_login_url($curl); ?>"> <span><?= __('登录', 'wtb'); ?></span> </a>

  <a class="cwhead-logbtn" href="<?= wp_registration_url(); ?>"> <span><?= __('注册', 'wtb'); ?></span> </a>

  <a href="<?= wp_logout_url($curl); ?>"> <span><?= __('注销', 'wtb'); ?></span> </a>

  <?php get_search_form(); ?>

  <?php do_action('wtb_breadcrumbs'); ?>