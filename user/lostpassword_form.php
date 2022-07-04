<div class="logged logged__backg">
  <div class="logged__logreg">
    <?php
    the_title('<h1>', '</h1>');
    if (count($attributes['errors']) > 0) {
      foreach ($attributes['errors'] as $error) :
        echo "<p class='login__error'>{$error}</p>";
      endforeach;
    }
    ?>
    <p class="login__info"><?= __('输入您的电子邮件地址，我们将向您发送一个链接，您可以使用该链接选择新密码。', 'wtb'); ?></p>
    <form class="logged__form" method="post" action="<?= esc_url(wp_lostpassword_url()); ?>">
      <p class="logged__form--input">
        <input type="text" name="user_login" id="user_login" autocomplete="off" autofocus placeholder="用户名或邮箱">
      </p>
      <p class="logged__form--submit">
        <input type="submit" value="<?= __('获取新密码', 'wtb'); ?>">
      </p>
    </form>
    <p class="logged__link">
      <a href="<?= esc_url(wp_login_url()); ?>"><?= __('登陆账户', 'wtb'); ?></a>
      <a href="<?= esc_url(home_url()); ?>"><?= __('返回首页', 'wtb'); ?></a>
    </p>
  </div>
</div>