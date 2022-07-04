<?php

if (!defined('ABSPATH'))
  exit;

/**
 * 删除角色
 */
add_action('admin_init',  function () {
  //remove_role('administrator');
  remove_role('editor');
  remove_role('author');
  //remove_role('contributor');
  remove_role('subscriber');
});


add_action('login_enqueue_scripts', function () { ?>
  <style type="text/css">
    html,
    body {
      min-height: 100vh;
      height: auto !important;
    }

    body.login {
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      background-image: url('https://bing-wallpaper.top/today.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
    }

    body.login::after {
      position: absolute;
      z-index: 1;
      width: 100%;
      height: 100%;
      content: '';
      background: rgba(0, 0, 0, .64);
    }

    body.login h1 {
      display: none;
    }

    body.login #login {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      z-index: 5;
      width: auto;
      max-width: 20rem;
      margin: 6vh 10vw;
      padding: 2rem;
      border-radius: 1rem;
      background: white;
    }

    body.login form .forgetmenot {
      margin-bottom: 1rem !important;
    }

    body.wp-core-ui .button-group.button-large .button,
    body.wp-core-ui .button.button-large {
      width: 100% !important;
    }

    body.login form {
      margin: 0;
      padding: 0;
      border: none;
    }

    body.login #nav,
    body.login #backtoblog {
      margin: 1rem 0 0;
      padding: 0;
    }

    body.login form .forgetmenot,
    body.login .button-primary {
      float: none;
    }

    body.login form .input,
    body.login form input[type=checkbox],
    body.login input[type=text] {
      background: rgba(0, 0, 0, .06);
    }
  </style>
<?php });
