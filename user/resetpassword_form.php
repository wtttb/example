<div class="logged logged__backg">
  <div class="logged__logreg">
    <?= the_title('<h1>', '</h1>'); ?>

    <?php if ($attributes['reset_invalid']) : ?>
      <p class="login__info">
        <?= sprintf(__('重置密码链接已无效，请<a href="%s" rel="nofollow">重新申请</a>链接。', 'wtb'), wp_lostpassword_url()); ?>
      </p>
    <?php else : ?>
      <p class="login__info">
        <?= __('输入您的电子邮件地址，我们将向您发送一个链接，您可以使用该链接选择新密码。', 'wtb'); ?>
      </p>
      <?php
      if (count($attributes['errors']) > 0) {
        foreach ($attributes['errors'] as $error) :
          echo "<p class='login__error'>{$error}</p>";
        endforeach;
      }
      ?>
      <form class="logged__form" method="post" action="<?= esc_url(site_url('wp-login.php?action=resetpass')); ?>">
        <input type="hidden" id="user_login" name="rp_login" value="<?= esc_attr($attributes['login']); ?>" autocomplete="off" />
        <input type="hidden" name="rp_key" value="<?= esc_attr($attributes['key']); ?>" />
        <p class="logged__form--input">
          <input type="password" name="pass1" id="pass1" autocomplete="off" autofocus placeholder="输入新密码">
        </p>
        <p class="logged__form--input">
          <input type="password" name="pass2" id="pass2" autocomplete="off" placeholder="再次输入新密码">
        </p>
        <p class="login__info"><?php echo wp_get_password_hint(); ?></p>
        <p class="logged__form--submit">
          <input type="submit" value="<?= __('重置密码', 'wtb'); ?>">
        </p>
      </form>
      <p class="logged__link">
        <a href="<?= esc_url(wp_login_url()); ?>"><?= __('登陆账户', 'wtb'); ?></a>
        <a href="<?= esc_url(home_url()); ?>"><?= __('返回首页', 'wtb'); ?></a>
      </p>
    <?php endif; ?>
  </div>
</div>