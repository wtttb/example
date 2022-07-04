<?php

if (!defined('ABSPATH'))
  exit;

// Register User Contact Methods
function modify_user_contact_methods($user_contact)
{
  $user_contact['weibo']   = __('归还借款', 'textdomain');
  return $user_contact;
}
add_filter('user_contactmethods', 'modify_user_contact_methods');

/**
 * 钩子
 */
add_action('login_form_login', 'wtb_redirect_to_login');
add_filter('authenticate', 'wtb_redirect_at_authenticate', 101, 3);
add_filter('login_redirect', 'wtb_redirect_after_login', 10, 3);
add_action('wp_logout', 'wtb_redirect_after_logout');

add_action('login_form_register', 'wtb_redirect_to_register');
add_action('login_form_lostpassword', 'wtb_redirect_to_lostpassword');
add_action('login_form_rp', 'wtb_redirect_to_resetpassword');
add_action('login_form_resetpass', 'wtb_redirect_to_resetpassword');

// 表单提交
add_action('login_form_register', 'wtb_register_users');
add_action('login_form_lostpassword', 'wtb_password_lost');
add_action('login_form_rp', 'wtb_password_reset');
add_action('login_form_resetpass', 'wtb_password_reset');

// 简码
add_shortcode('wtb-login-form', 'wtb_render_login_form');
add_shortcode('wtb-register-form', 'wtb_render_register_form');
add_shortcode('wtb-lostpassword-form', 'wtb_render_lostpassword_form');
add_shortcode('wtb-resetpassword-form', 'wtb_render_resetpassword_form');
add_shortcode('wtb-account-form', 'wtb_render_account_form');

// 邮件
add_filter('retrieve_password_message', 'wtb_replace_retrieve_password_message', 10, 4);

/**
 * 引入模板文件
 */
function wtb_get_template_html($name, $attributes = null)
{
  if (!$attributes) {
    $attributes = [];
  }
  ob_start();
  do_action("user_before_{$name}");
  require("{$name}.php");
  do_action("user_after_{$name}");
  $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

/**
 * 错误信息
 */
function wtb_get_error_message($error)
{
  switch ($error) {
      // 登录错误信息
    case 'empty_username':
      return __('请输入用户名或邮箱。', 'wtb');
    case 'empty_password':
      return __('需要输入密码。', 'wtb');
    case 'invalid_username':
      return __('用户名或邮箱错误，请重新输入。', 'wtb');
    case 'incorrect_password':
      return __('输入的密码错误，请重新输入。', 'wtb');

      // 注册错误信息
    case 'login':
      return __('用户名不能为空，且需要大于5个字母。', 'wtb');
    case 'login_exists':
      return __('用户名已被使用，请重新输入。', 'wtb');
    case 'email':
      return __('输入的不是邮箱，请重新输入。', 'wtb');
    case 'email_exists':
      return __('邮箱已被注册，请重新输入。', 'wtb');
    case 'password':
      return __('密码不能为空，且需要大于5个字母。', 'wtb');
    case 'closed':
      return __('目前不允许注册新用户。', 'wtb');

      // 忘记密码错误信息
    case 'empty_username':
      return __('请输入邮箱。', 'wtb');
    case 'invalid_email':
    case 'invalidcombo':
      return __('没有使用此邮箱注册的用户。', 'wtb');

      // 重置密码错误信息
    case 'expiredkey':
    case 'invalidkey':
      return sprintf(__('重置密码链接已无效，请<a href="%s" rel="nofollow">重新申请</a>链接。', 'wtb'), wp_lostpassword_url());
    case 'password_mismatch':
      return __('两个密码不匹配，请重新输入。', 'wtb');
    case 'password_empty':
      return __('抱歉，不接受空密码，请重新输入。', 'wtb');
    default:
      break;
  }
  return __('出现未知错误，请稍后再试。', 'wtb');
}

/**
 * 页面数组 / 创建页面
 */
function wtb_get_pages()
{
  $pages = [
    'login' => [
      'title'    => __('登录', 'wtb'),
      'content'  => '[wtb-login-form]',
      'template' => 'page-user.php',
    ],
    'register' => [
      'title'    => __('注册', 'wtb'),
      'content'  => '[wtb-register-form]',
      'template' => 'page-user.php',
    ],
    'lostpassword' => [
      'title'    => __('忘记密码', 'wtb'),
      'content'  => '[wtb-lostpassword-form]',
      'template' => 'page-user.php',
    ],
    'resetpassword' => [
      'title'    => __('找回密码', 'wtb'),
      'content'  => '[wtb-resetpassword-form]',
      'template' => 'page-user.php',
    ],
    'account' => [
      'title'    => __('账户中心', 'wtb'),
      'content'  => '[wtb-account-form]',
      'template' => '',
    ],
  ];
  return $pages;
}

add_action('after_setup_theme', function () {
  foreach (wtb_get_pages() as $slug => $page) {
    $page_query = new WP_Query("pagename={$slug}");
    if (!$page_query->have_posts()) {
      wp_insert_post([
        'post_title'     => $page['title'],
        'post_name'      => $slug,
        'post_content'   => $page['content'],
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
        'page_template'  => $page['template'],
      ]);
    }
  }
});

/**
 * 登录
 */
// 登录简码
function wtb_render_login_form($attributes, $content = null)
{
  $attributes = shortcode_atts([], $attributes);
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (is_user_logged_in()) {
      wp_safe_redirect(home_url('account'));
      exit;
    }

    $attributes['redirect'] = '';
    if (isset($_REQUEST['redirect_to'])) {
      $attributes['redirect'] = wp_validate_redirect($_REQUEST['redirect_to'], $attributes['redirect']);
    }

    $errors     = [];
    if (isset($_REQUEST['login'])) {
      $error_codes = explode(',', $_REQUEST['login']);
      foreach ($error_codes as $code) {
        $errors[] = wtb_get_error_message($code);
      }
    }
    $attributes['errors']             = $errors;
    $attributes['logged_out']         = isset($_REQUEST['logged_out']) && $_REQUEST['logged_out'] == true;
    $attributes['registered']         = isset($_REQUEST['registered']);
    $attributes['lost_password_sent'] = isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm';
    $attributes['password_updated']   = isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed';
    return wtb_get_template_html('login_form', $attributes);
  }
}

// 如果有任何错误，在身份验证后重定向用户。
function wtb_redirect_at_authenticate($user, $username, $password)
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (is_wp_error($user)) {
      $error_codes = join(',', $user->get_error_codes());
      $login_url   = add_query_arg('login', $error_codes, home_url('login'));
      wp_safe_redirect($login_url);
      exit;
    }
  }
  return $user;
}

// 重定向到自定义登录页面
function wtb_redirect_to_login()
{
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (is_user_logged_in()) {
      wp_safe_redirect(home_url('account'));
      exit;
    }

    $redirect_url = home_url('login');
    if (!empty($_REQUEST['redirect_to'])) {
      $redirect_url = add_query_arg('redirect_to', urlencode($_REQUEST['redirect_to']), $redirect_url);
    }
    if (!empty($_REQUEST['checkemail'])) {
      $redirect_url = add_query_arg('checkemail', $_REQUEST['checkemail'], $redirect_url);
    }
    wp_safe_redirect($redirect_url);
    exit;
  }
}

// 登录后应重定向
function wtb_redirect_after_login($redirect_to, $requested_redirect_to, $user)
{
  $redirect_url = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : home_url('account');
  if ($user && is_object($user) && is_a($user, 'WP_User')) {
    if ($user->has_cap('administrator')) {
      $redirect = $redirect_to;
    } else {
      $redirect = $redirect_url;
    }
  }
  wp_safe_redirect($redirect);
  exit;
}

// 注销后重定向
function wtb_redirect_after_logout()
{
  $redirect_url = $_SERVER['HTTP_REFERER'];
  if ($redirect_url == admin_url()) {
    $redirect = home_url("login?redirect_to=" . urlencode($redirect_url) . "&logged_out=true");
  } else {
    $redirect = $redirect_url;
  }
  wp_safe_redirect($redirect);
  exit;
}


/**
 * 注册
 */
// 注册简码
function wtb_render_register_form($attributes, $content = null)
{
  $attributes = shortcode_atts([], $attributes);
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (is_user_logged_in()) {
      wp_safe_redirect(home_url('account'));
      exit;
    }

    $attributes['errors'] = [];
    if (isset($_REQUEST['register-errors'])) {
      $error_codes = explode(',', $_REQUEST['register-errors']);
      foreach ($error_codes as $error_code) {
        $attributes['errors'][] = wtb_get_error_message($error_code);
      }
    }

    //$attributes['recaptcha_site_key'] = get_option('personalize-login-recaptcha-site-key', null);

    return wtb_get_template_html('register_form', $attributes);
  }
}

// 注册一个新用户
function wtb_register_users()
{
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!get_option('users_can_register')) {
      $redirect_url = add_query_arg('register-errors', 'closed', home_url('register'));
    } /* elseif (!verify_recaptcha()) {
      $redirect_url = add_query_arg('register-errors', 'captcha', home_url('register'));
    } */ else {
      $login        = sanitize_text_field($_POST['user_login']);
      $email        = sanitize_email($_POST['user_email']);
      $display_name = sanitize_text_field($_POST['display_name']);
      $password     = $_POST['pwd'];
      $password2    = $_POST['pwd2'];
      $result       = wtb_register_user($login, $email, $display_name, $password, $password2);

      if (is_wp_error($result)) {
        $errors = join(',', $result->get_error_codes());
        $redirect_url = add_query_arg('register-errors', $errors, home_url('register'));
      } else {
        $redirect_url = add_query_arg('registered', $email, home_url('login'));
      }
    }
    wp_safe_redirect($redirect_url);
    exit;
  }
}

// 完成用户注册
function wtb_register_user($login, $email, $display_name, $password, $password2)
{
  $errors = new WP_Error();
  if (empty($login)) {
    $errors->add('login', wtb_get_error_message('login'));
    return $errors;
  }
  if (strlen($login) < 5) {
    $errors->add('login', wtb_get_error_message('login'));
    return $errors;
  }
  if (username_exists($login)) {
    $errors->add('login_exists', wtb_get_error_message('login_exists'));
    return $errors;
  }
  if (!is_email($email)) {
    $errors->add('email', wtb_get_error_message('email'));
    return $errors;
  }
  if (email_exists($email)) {
    $errors->add('email_exists', wtb_get_error_message('email_exists'));
    return $errors;
  }
  if (empty($password)) {
    $errors->add('password', wtb_get_error_message('password'));
  }
  if (strlen($password) < 5) {
    $errors->add('password', wtb_get_error_message('password'));
  }
  if ($password !== $password2) {
    $errors->add('password_mismatch', wtb_get_error_message('password_mismatch'));
    return $errors;
  }
  /* $password = wp_generate_password(12, false); */
  $user_data = [
    'user_login'   => $login,
    'user_email'   => $email,
    'display_name' => $display_name,
    'user_pass'    => $password,
  ];

  $user_id = wp_insert_user($user_data);
  wp_new_user_notification($user_id, $password);
  return $user_id;
}

// 重定向到自定义注册页面
function wtb_redirect_to_register()
{
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (is_user_logged_in()) {
      wp_safe_redirect(home_url('account'));
      exit;
    }
    wp_safe_redirect(home_url('register'));
    exit;
  }
}


/**
 * 忘记密码
 */
// 忘记密码简码
function wtb_render_lostpassword_form($attributes, $content = null)
{
  $attributes = shortcode_atts([], $attributes);
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (is_user_logged_in()) {
      wp_safe_redirect(home_url('account'));
      exit;
    }

    $attributes['errors'] = [];
    if (isset($_REQUEST['errors'])) {
      $error_codes = explode(',', $_REQUEST['errors']);
      foreach ($error_codes as $error_code) {
        $attributes['errors'][] = wtb_get_error_message($error_code);
      }
    }
    return wtb_get_template_html('lostpassword_form', $attributes);
  }
}

// 启动密码重置
function wtb_password_lost()
{
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = retrieve_password();
    if (is_wp_error($errors)) {
      $redirect_url = add_query_arg('errors', join(',', $errors->get_error_codes()), home_url('lostpassword'));
    } else {
      $redirect_url = add_query_arg('checkemail', 'confirm', home_url('login'));
      if (!empty($_REQUEST['redirect_to'])) {
        $redirect_url = $_REQUEST['redirect_to'];
      }
    }
    wp_safe_redirect($redirect_url);
    exit;
  }
}

// 重定向到自定义忘记密码页面
function wtb_redirect_to_lostpassword()
{
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (is_user_logged_in()) {
      wp_safe_redirect(home_url('account'));
      exit;
    }
    wp_safe_redirect(home_url('lostpassword'));
    exit;
  }
}


/**
 * 重置密码
 */
// 重置密码简码
function wtb_render_resetpassword_form($attributes, $content = null)
{
  $attributes = shortcode_atts([], $attributes);
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (is_user_logged_in()) {
      wp_safe_redirect(home_url('account'));
      exit;
    }

    if (isset($_REQUEST['login']) && isset($_REQUEST['key'])) {
      $attributes['login'] = $_REQUEST['login'];
      $attributes['key']   = $_REQUEST['key'];
      $errors  = [];
      if (isset($_REQUEST['error'])) {
        $error_codes = explode(',', $_REQUEST['error']);
        foreach ($error_codes as $code) {
          $errors[] = wtb_get_error_message($code);
        }
      }
      $attributes['errors'] = $errors;
      return wtb_get_template_html('resetpassword_form', $attributes);
    } else {

      if (isset($_REQUEST['error'])) {
        $error_codes = explode(',', $_REQUEST['error']);
        foreach ($error_codes as $code) {
          $errors[] = wtb_get_error_message($code);
        }
      }
      $attributes['errors'] = $errors;
      $attributes['reset_invalid'] = isset($_REQUEST['reset_invalid']);

      return wtb_get_template_html('resetpassword_form', $attributes);
    }
  }
}

// 如果密码重置表单已提交，则重置用户的密码
function wtb_password_reset()
{
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $key      = $_REQUEST['rp_key'];
    $login    = $_REQUEST['rp_login'];
    $user     = check_password_reset_key($key, $login);
    $redirect = home_url('resetpassword');

    if (!$user || is_wp_error($user)) {
      if ($user && $user->get_error_code() === 'expired_key') {
        wp_safe_redirect(home_url('login?login=expiredkey'));
      } else {
        wp_safe_redirect(home_url('login?login=invalidkey'));
      }
      exit;
    }

    if (isset($_POST['pass1'])) {
      if ($_POST['pass1'] != $_POST['pass2']) {
        $redirect = add_query_arg('key', $key, $redirect);
        $redirect = add_query_arg('login', $login, $redirect);
        $redirect = add_query_arg('error', 'password_mismatch', $redirect);
        wp_safe_redirect($redirect);
        exit;
      }

      if (empty($_POST['pass1'])) {
        $redirect = add_query_arg('key', $key, $redirect);
        $redirect = add_query_arg('login', $login, $redirect);
        $redirect = add_query_arg('error', 'password_empty', $redirect);
        wp_safe_redirect($redirect);
        exit;
      }
      reset_password($user, $_POST['pass1']);
      wp_safe_redirect(home_url('login?password=changed'));
    } else {
      echo __('无效的请求。', 'wtb');
    }
    exit;
  }
}

// 重定向到自定义重置密码页面
function wtb_redirect_to_resetpassword()
{
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
    if (!$user || is_wp_error($user)) {
      if ($user && $user->get_error_code() === 'expired_key') {
        wp_safe_redirect(home_url('login?login=expiredkey'));
      } else {
        wp_safe_redirect(home_url('login?login=invalidkey'));
      }
      exit;
    }
    $redirect = home_url('resetpassword');
    $redirect = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect);
    $redirect = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect);
    wp_safe_redirect($redirect);
    exit;
  }
}

// 返回密码重置邮件的邮件正文
function wtb_replace_retrieve_password_message($message, $key, $user_login, $user_data)
{
  $msg  = __('你好！', 'wtb') . "\r\n\r\n";
  $msg .= sprintf(__('您要求我们使用电子邮件地址 %s 重置您帐户的密码。', 'wtb'), $user_login) . "\r\n\r\n";
  $msg .= __('如果这是一个错误，或者您没有要求重置密码，请忽略此电子邮件，不会发生任何事情。', 'wtb') . "\r\n\r\n";
  $msg .= __('要重置您的密码，请访问以下地址：', 'wtb') . "\r\n\r\n";
  $msg .= site_url("wp-login.php?action=rp&key={$key}&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
  $msg .= __('谢谢!', 'wtb') . "\r\n";
  return $msg;
}

/**
 * 账户中心
 */
// 账户中心简码
function wtb_render_account_form($attributes, $content = null)
{

  $attributes = shortcode_atts([], $attributes);
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!is_user_logged_in()) {
      wp_safe_redirect(home_url('login'));
      exit;
    }
  }

  $attributes['errors'] = [];
  if (isset($_REQUEST['errors'])) {
    $error_codes = explode(',', $_REQUEST['errors']);
    foreach ($error_codes as $error_code) {
      $attributes['errors'][] = wtb_get_error_message($error_code);
    }
  }

  return wtb_get_template_html('account_form', $attributes);
}
