<?php

/*=============================================
  多个侧边栏添加
=============================================*/
add_action('widgets_init', function () {
  wtb_widget_registr('默认侧边栏', 'sidebar');
  wtb_widget_registr('文章页侧边栏', 'single-sidebar');
  wtb_widget_registr('底部小工具', 'bottom-sidebar', '', '<div class="cwfoo-widget-item">', '</div>');
});
function wtb_widget_registr($name, $id, $description = '', $before_widget = '<section class="widget %2$s">', $after_widget = '</section>', $before_title = '<h3>', $after_title = '</h3>')
{
  register_sidebar([
    'name'          => $name,
    'id'            => $id,
    'description'   => $description,
    'before_widget' => $before_widget,
    'after_widget'  => $after_widget,
    'before_title'  => $before_title,
    'after_title'   => $after_title,
  ]);
}

//require_once get_theme_file_path('widgets/tabs.php');
//require_once get_theme_file_path('widgets/videos.php');

add_action(
  'widgets_init',
  function () {
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Block');
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Media_Audio');
    unregister_widget('WP_Widget_Calendar');
    /* unregister_widget('WP_Nav_Menu_Widget');
    unregister_widget('WP_Widget_Custom_HTML');
    unregister_widget('WP_Widget_Media_Gallery');
    unregister_widget('WP_Widget_Media_Image');
    unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Widget_Text');
    unregister_widget('WP_Widget_Media_Video'); */
  },
  11
);
