<?php
if (post_password_required()) return;
$post_id   = get_the_ID();
$commenter = wp_get_current_commenter();
$user      = wp_get_current_user();
$user_name = $user->exists() ? $user->display_name : '';
$star      = __('必需的地方已做标记 *', 'wtb');
$req       = get_option('require_name_email');
$cookies   = has_action('set_comment_cookies', 'wp_set_comment_cookies') && get_option('show_comments_cookies_opt_in');
$logout    = wp_logout_url(apply_filters('the_permalink', get_permalink($post_id), $post_id));
$login     = wp_login_url(apply_filters('the_permalink', get_permalink($post_id), $post_id));
$date      = gmdate('H', time() + 8 * 3600);
$tips      = [
  '3'  => __('晚安，美美的睡一觉 ٩(●˙▿˙●)۶…⋆ฺ', 'wtb'),
  '6'  => __('以后不要熬夜了 (ఠ్ఠ ˓̭ ఠ్ఠ)', 'wtb'),
  '9'  => __('早安，打工人 (>_<)', 'wtb'),
  '12' => __('打起精神，奋斗ing (☉_☉)', 'wtb'),
  '18' => __('奖励自己一杯下午茶 ༼ •̀ ں •́ ༽', 'wtb'),
  '22' => __('属于自己的时间，满足 o(*￣︶￣*)o', 'wtb'),
  '24' => __('晚安，美美的睡一觉 ٩(●˙▿˙●)۶…⋆ฺ', 'wtb'),
];
foreach ($tips as $key => $val) {
  if ($key > $date) {
    $textarea = $val;
    break;
  }
}
?>

<?php
if (comments_open()) {
  comment_form([
    'fields' => [
      'author'  => $req ? sprintf(
        '<p class="comment__form--input"><input id="author" name="author" type="text" value="%s" size="30" maxlength="245" placeholder="%s" required>',
        esc_attr($commenter['comment_author']),
        esc_attr__('名称 *', 'wtb'),
      ) : '',
      'email'   => $req ? sprintf(
        '<input id="email" name="email" %s value="%s" size="30" maxlength="100" aria-describedby="email-notes" placeholder="%s" required></p>',
        ($html5 ? 'type="email"' : 'type="text"'),
        esc_attr($commenter['comment_author_email']),
        esc_attr__('邮箱 *', 'wtb'),
      ) : '',
      'url'     => '',
      'cookies' => $cookies ? sprintf('<p class="comment__cookies"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" %s><label for="wp-comment-cookies-consent">%s</label></p>', empty($commenter['comment_author_email']) ? '' : 'checked', __('在此站中保存我的信息，以备下次评论时使用。', 'wtb')) : '',
    ],
    'comment_field'        => '<p class="comment__form--textarea"><textarea id="comment" name="comment" cols="45" rows="4" maxlength="65525" placeholder="' . esc_attr($textarea) . '" required></textarea></p>',
    'must_log_in'          => sprintf('<p class="comment__mustlogin">%s<a href="%s">%s</a></p>', __('你必须登录才能发表评论！', 'wtb'), $login, __('登录', 'wtb')),
    'logged_in_as'         => sprintf('<p class="comment__loginas">%s<a href="%s">%s</a>， <a href="%s">%s</a>%s</p>', __('登录为', 'wtb'), get_edit_user_link(), $user_name, $logout, __('注销?', 'wtb'), $star_text),
    'comment_notes_before' => sprintf('<p class="comment__notes">%s<span>%s</span></p>', __('您的电子邮件地址不会被公开。', 'wtb'), $star),
    'class_container'      => 'comment__respond',
    'class_form'           => 'comment__form',
    'title_reply_before'   => '<h3 class="comment__title">',
    'title_reply_after'    => '</h3>',
    'submit_field'         => '<p class="comment__submit">%1$s %2$s</p>',
  ]);
} else {
  echo '<div id="respond" class="comment__respond"><p class="comment__closed">' . __('评论已被关闭。', 'wtb') . '</p></div>';
}
?>

<?php if (have_comments()) { ?>
  <div class="comment__area">
    <h3 class="comment__title"> <?= __('全部评论', 'wtb') . get_comments_number(); ?></h3>

    <ul><?= wp_list_comments(['avatar_size' => 48, 'walker' => new wtb_Walker_Comment]); ?></ul>

    <?= the_comments_pagination([
      'prev_text' => '<svg width="24" height="24" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 12H6M6 12L12 6M6 12L12 18" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>',
      'next_text' => '<svg width="24" height="24" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 12H18.5M18.5 12L12.5 6M18.5 12L12.5 18" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    ]); ?>
  </div>
<?php } ?>