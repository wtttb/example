<?php

if (post_password_required()) return;

$req     = get_option('require_name_email');
$date    = gmdate('H', time() + 8 * 3600);
$tips    = [
  '3'  => __('晚安，美美的睡一觉 ٩(●˙▿˙●)۶…⋆ฺ', 'wtb'),
  '6'  => __('以后不要熬夜了 (ఠ్ఠ ˓̭ ఠ్ఠ)', 'wtb'),
  '9'  => __('早安，打工人 (>_<)', 'wtb'),
  '12' => __('打起精神，奋斗ing (☉_☉)', 'wtb'),
  '18' => __('奖励自己一杯下午茶 ༼ •̀ ں •́ ༽', 'wtb'),
  '22' => __('属于自己的时间，满足 o(*￣︶￣*)o', 'wtb'),
  '24' => __('晚安，美美的睡一觉 ٩(●˙▿˙●)۶…⋆ฺ', 'wtb')
];
foreach ($tips as $key => $val) {
  if ($key > $date) {
    $textarea = $val;
    break;
  }
}
?>
<div class="cwpost-comments" id="comments">
  <?php
  if (comments_open()) {
    comment_form([
      'fields' => [
        'author'  => $req ? sprintf(
          '<p class="comment-input comment-flex"><input id="author" name="author" type="text" value="%s" size="30" maxlength="245" placeholder="%s">',
          esc_attr($commenter['comment_author']),
          esc_attr__('名称 *', 'wtb'),
        ) : '',
        'email'   => $req ? sprintf(
          '<input id="email" name="email" %s value="%s" size="30" maxlength="100" aria-describedby="email-notes" placeholder="%s"></p>',
          ($html5 ? 'type="email"' : 'type="text"'),
          esc_attr($commenter['comment_author_email']),
          esc_attr__('邮箱 *', 'wtb'),
        ) : '',
        'url' => '',
      ],
      'comment_field'      => '<p class="comment-textarea comment-flex"><textarea id="comment" name="comment" cols="45" rows="4" maxlength="65525" placeholder="' . esc_attr($textarea) . '"></textarea></p>',
      'class_container'    => 'comment-respond',
      'title_reply_before' => '<h3 class="comment-title">',
      'title_reply_after'  => '</h3>',
      'format'             => 'html5'
    ]);
  } else {
    echo '<p class="comment-closed">' . __('评论已被关闭。', 'wtb') . '</p>';
  }

  if (have_comments()) { ?>
    <div id="comments" class="comment-area">
      <h3 class="comment-title"> <?= __('全部评论', 'wtb') . get_comments_number(); ?></h3>
      <ol><?= wp_list_comments(['style' => 'ol', 'format' => 'html5', 'short_ping' => true, 'walker' => new wtb_Walker_Comment]); ?></ol>
      <?= the_comments_pagination(['prev_text' => '<i class="uil uil-angle-left"></i>', 'next_text' => '<i class="uil uil-angle-right"></i>']); ?>
    </div>
  <?php } ?>
</div>