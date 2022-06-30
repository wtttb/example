<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('wtb_Walker_Comment')) {
  class wtb_Walker_Comment extends Walker_Comment
  {
    public function html5_comment($comment, $depth, $args)
    {
      $tag = ('div' === $args['style']) ? 'div' : 'li';
?>
      <<?= $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">

          <figure class="comment-body-pic">
            <?= 0 != $args['avatar_size'] ? "<a href=" . get_author_posts_url($comment->user_id) . " rel='author'>" . get_avatar($comment, 48) . "</a>" : ''; ?>
          </figure>

          <div class="comment-body-box">
            <header class="comment-head">
              <?= get_comment_author_link($comment); ?>
              <time datetime="<?= comment_time('c'); ?>">
                <?= get_comment_date("", $comment) . ' ' . get_comment_time("", $comment); ?>
              </time>
            </header>
            <div class="comment-content" id="comment-content">
              <?= '0' == $comment->comment_approved ? '<p class="comment-content-moderation">您的评论正在等待审核。</p>' : ''; ?>
              <?php comment_text(); ?>
            </div>
            <footer class="comment-foo">
              <!-- <a href="">赞</a> -->
              <?= get_comment_reply_link(array_merge($args, ['add_below' => 'div-comment', 'depth' => $depth, 'reply_text' => '<i class="uil uil-comment-dots"></i>回复', 'max_depth' => $args['max_depth']])); ?>
              <?= current_user_can('manage_options') ? "<a class='comment-edit-edit' href=" . get_edit_comment_link($comment) . " rel='nofollow'><i class='uil uil-edit'></i>编辑</a>" : ''; ?>
              <?= $this->count(get_comment_text($comment)) > 128 ? "<button type='button' id='commentmoretoggle'><i class='uil uil-angle-double-down'></i>查看全文<i class='fa-solid fa-angles-down'></i></button>" : ''; ?>
            </footer>
          </div>
        </article>
  <?php
    }

    private function count($str)
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
