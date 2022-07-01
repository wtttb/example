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

          <figure class="comment-body-avatar">
            <?php
            $userid      = $comment->user_id;
            $userposturl = get_author_posts_url($userid);
            $useravatar  = wp_kses_post(get_avatar($comment, 48));
            if ($userid > 0) {
              echo "<a href='{$userposturl}' rel='author'>{$useravatar}</a>";
            } else {
              echo $useravatar;
            }
            ?>
          </figure>

          <div class="comment-body-box">
            <header class="comment-head">
              <?php $comment_timestamp = get_comment_date('', $comment) . ' ' . get_comment_time('', $comment); ?>
              <?= get_comment_author_link($comment); ?>
              <time datetime="<?= get_comment_time('c'); ?>" title="<?= esc_attr($comment_timestamp); ?>">
                <?= esc_html($comment_timestamp); ?>
              </time>
            </header>

            <div class="comment-content" id="comment-content">
              <?= '0' == $comment->comment_approved ? '<p class="comment-content-moderation">您的评论正在等待审核。</p>' : ''; ?>
              <?php comment_text(); ?>
            </div>

            <footer class="comment-foo">
              <!-- <a href=''>赞</a> -->
              <?= get_comment_reply_link(
                array_merge(
                  $args,
                  [
                    'add_below' => 'div-comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '',
                    'after'     => '',
                    'reply_text' => __('回复', 'wtb'),
                  ]
                )
              ); ?>

              <?= current_user_can('manage_options') ? "<a class='comment-edit-edit' href=" . get_edit_comment_link($comment) . " rel='nofollow'>编辑</a>" : ''; ?>

              <?= $this->comment_count(get_comment_text($comment)) > 128 ? "<button type='button' id='commentmoretoggle'>查看全文</button>" : ''; ?>
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

    private function by_post_author($comment = null)
    {

      if (is_object($comment) && $comment->user_id > 0) {

        $user = get_userdata($comment->user_id);
        $post = get_post($comment->comment_post_ID);

        if (!empty($user) && !empty($post)) {

          return $comment->user_id === $post->post_author;
        }
      }
      return false;
    }
  }
}
