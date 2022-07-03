<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('wtb_Walker_Comment')) {
  class wtb_Walker_Comment extends Walker_Comment
  {
    public function html5_comment($comment, $depth, $args)
    {
?>
      <li id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
        <article class="comment__body" id="div-comment-<?php comment_ID(); ?>">

          <figure class="comment__avatar">
            <?php
            $userid      = $comment->user_id;
            $userposturl = get_author_posts_url($comment);
            $useravatar  = wp_kses_post(get_avatar($comment, $args['avatar_size']));
            if ($userid > 0) {
              echo "<a href='{$userposturl}' rel='author'>{$useravatar}</a>";
            } else {
              echo $useravatar;
            }
            ?>
          </figure>

          <div class="comment__box">

            <header class="comment__head">
              <?= get_comment_author_link($comment); ?>

              <?php $comment_timestamp = get_comment_date('', $comment) . ' ' . get_comment_time('', $comment); ?>
              <time class="comment__head--time" datetime="<?= get_comment_time('c'); ?>" title="<?= esc_attr($comment_timestamp); ?>">
                <?= esc_html($comment_timestamp); ?>
              </time>
            </header>

            <div class="comment__content" id="comment-content">
              <?= $comment->comment_approved == '0' ? '<p class="comment__content--moderation"></p>' : ''; ?>
              <?= comment_text(); ?>
            </div>

            <footer class="comment__foo">
              <?= get_comment_reply_link(array_merge(
                $args,
                [
                  'add_below' => 'div-comment',
                  'depth'     => $depth,
                  'max_depth' => $args['max_depth'],
                  'reply_text' => __('回复', 'wtb'),
                ]
              )); ?>

              <?= current_user_can('manage_options') ? "<a class='comment__foo--edit' href=" . esc_url(get_edit_comment_link($comment)) . ">" . __('编辑', 'wtb') . "</a>" : ''; ?>

              <?= $this->comment_count(get_comment_text($comment)) > 128 ? "<button class='comment__foo--more' id='commentmoretoggle'>" . __('查看全文', 'wtb') . "</button>" : ''; ?>
            </footer>

          </div>
        </article>
  <?php
    }

    private function comment_count($str)
    {
      $count = substr_count($str, "龘");
      try {
        $str = preg_replace('/(\r\n+|\s+|　+)/', "龘", $str);
        $str = preg_replace('/[a-z_A-Z0-9-\.!@#\$%\\\^&\*\)\(\+=\{\}\[\]\/",\'<>~`\?:;|]/', "m", $str);
        $str = preg_replace('/m+/', "*", $str);
        $str = preg_replace('/龘+/', '', $str);
        return mb_strlen($str) + $count;
      } catch (Exception $e) {
        return 0;
      }
    }
  }
}
