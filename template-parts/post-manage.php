<?php
// 编辑文章
if (current_user_can('manage_options')) {
  echo '<a href="' . esc_url(get_edit_post_link()) . '" rel="nofollow">编辑</a>';
  echo '<a href="' . esc_url(get_delete_post_link()) . '" rel="nofollow">删除</a>';
}
