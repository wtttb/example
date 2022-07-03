<div class="logged logged__backg">
  <div class="logged__logo">
    <img src="<?= esc_url(wp_get_attachment_url(get_theme_mod('custom_logo'))); ?>" loading="lazy">
  </div>
  <div class="logged__logreg">
    <?php the_title('<h1>', '</h1>'); ?>
    <div class="logged__other">
      <a href="" class="logged__other--apple"><svg width="24" height="24" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M16 2C16.3632 4.17921 14.0879 5.83084 12.8158 6.57142C12.4406 6.78988 12.0172 6.5117 12.0819 6.08234C12.2993 4.63878 13.0941 2.00008 16 2Z" stroke="currentColor" />
          <path d="M9 6.5C9.89676 6.5 10.6905 6.69941 11.2945 6.92013C12.0563 7.19855 12.9437 7.19854 13.7055 6.92012C14.3094 6.6994 15.1032 6.5 15.9999 6.5C17.0852 6.5 18.4649 7.08889 19.4999 8.26666C16 11 17 15.5 20.269 16.6916C19.2253 19.5592 17.2413 21.5 15.4999 21.5C13.9999 21.5 14 20.8 12.5 20.8C11 20.8 11 21.5 9.5 21.5C7 21.5 4 17.5 4 12.5C4 8.5 7 6.5 9 6.5Z" stroke="currentColor" />
        </svg></a>
      <a href="" class="logged__other--tiktok"><svg width="24" height="24" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21 8V16C21 18.7614 18.7614 21 16 21H8C5.23858 21 3 18.7614 3 16V8C3 5.23858 5.23858 3 8 3H16C18.7614 3 21 5.23858 21 8Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M10 12C8.34315 12 7 13.3431 7 15C7 16.6569 8.34315 18 10 18C11.6569 18 13 16.6569 13 15V6C13.3333 7 14.6 9 17 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
        </svg></a>
    </div>
    <h2><?= __('使用邮箱继续登录', 'wtb'); ?></h2>
    <?php
    if (count($attributes['errors']) > 0) {
      foreach ($attributes['errors'] as $error) :
        echo "<p class='login__error'>{$error}</p>";
      endforeach;
    }
    if ($attributes['logged_out']) {
      echo "<p class='login__info'>" . __('您已退出，您要重新登录吗？', 'wtb') . "</p>";
    }
    if ($attributes['registered']) {
      echo "<p class='login__info'>" . sprintf(__('您已成功注册到 <strong>%s</strong>。', 'wtb'), get_bloginfo('name')) . "</p>";
    }
    if ($attributes['lost_password_sent']) {
      echo "<p class='login__info'>" . __('检查您的电子邮件以获取重置密码的链接。', 'wtb') . "</p>";
    }
    if ($attributes['password_updated']) {
      echo "<p class='login__info'>" . __('您的密码已被更改，您现在可以登录了。', 'wtb') . "</p>";
    }
    ?>
    <form class="logged__form" method="post" action="<?= esc_url(wp_login_url()); ?>">
      <p class="logged__form--input">
        <input type="text" name="log" autofocus placeholder="<?= __('用户名或邮箱', 'wtb'); ?>">
      </p>
      <p class="logged__form--input">
        <input type="password" name="pwd" placeholder="<?= __('密码', 'wtb'); ?>">
      </p>
      <p class="logged__form--submit">
        <input type="submit" value="<?= __('登录', 'wtb'); ?>">
      </p>
    </form>
    <p class="logged__link">
      <a href="<?= esc_url(wp_registration_url()); ?>"><?= __('注册帐户', 'wtb'); ?></a>
      <a href="<?= esc_url(home_url()); ?>"><?= __('返回首页', 'wtb'); ?></a>
      <a href="<?= esc_url(wp_lostpassword_url()); ?>"><?= __('忘记密码？', 'wtb'); ?></a>
    </p>
  </div>
</div>