<div class="logged logged__backg" style="background-image: url(https://bing.com/th?id=OHR.ClingmansDome_ZH-CN0900594339_1920x1080.jpg);">
  <div class="logged__logo">
    <img src="<?= esc_url(wp_get_attachment_url(get_theme_mod('custom_logo'))); ?>" loading="lazy">
  </div>
  <div class="logged__logreg">
    <?php
    the_title('<h1>', '</h1>');
    if (count($attributes['errors']) > 0) {
      foreach ($attributes['errors'] as $error) :
        echo "<p class='login__error'>{$error}</p>";
      endforeach;
    }
    if (!get_option('users_can_register')) {
      echo "<p class='login__info'>" . __('本站已关闭注册，有任何疑问请给管理员发送邮件！', 'wtb') . sprintf('<a href="mailto:%s" rel="external nofollow noopener noreferrer">发送邮件</a>', get_bloginfo('admin_email')) . "</p>";
    } else {
    ?>
      <form class="logged__form" method="post" action="<?= esc_url(wp_registration_url()); ?>">
        <p class="logged__form--input">
          <input type="text" name="user_login" placeholder="<?= __('用户名 *', 'wtb'); ?>">
        </p>
        <p class="logged__form--input">
          <input type="email" name="user_email" placeholder="<?= __('邮箱 *', 'wtb'); ?>">
        </p>
        <p class="logged__form--input">
          <input type="text" name="display_name" placeholder="<?= __('昵称', 'wtb'); ?>">
        </p>
        <p class="logged__form--input">
          <input type="password" name="pwd" placeholder="<?= __('密码 *', 'wtb'); ?>">
        </p>
        <p class="logged__form--input">
          <input type="password" name="pwd2" placeholder="<?= __('重复密码 *', 'wtb'); ?>">
        </p>
        <p class="logged__form--submit">
          <input type="submit" value="<?= __('注册账户', 'wtb'); ?>">
        </p>
      </form>
    <?php
    }
    ?>
    <p class="logged__link">
      <a href="<?= esc_url(wp_login_url($_GET['redirect_to'])); ?>"><?= __('登陆账户', 'wtb'); ?></a>
      <a href="<?= esc_url(home_url()); ?>"><?= __('返回首页', 'wtb'); ?></a>
    </p>
  </div>
</div>