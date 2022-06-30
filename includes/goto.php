<?php
if (
  strlen($_SERVER['REQUEST_URI']) > 384 || strpos($_SERVER['REQUEST_URI'], "eval(") || strpos($_SERVER['REQUEST_URI'], "base64")
) {
  @header("HTTP/1.1 414 Request-URI Too Long");
  @header("Status: 414 Request-URI Too Long");
  @header("Connection: Close");
  @exit;
}
$t_url = preg_replace('/^url=(.*)$/i', '$1', $_SERVER["QUERY_STRING"]);
if (!empty($t_url)) {
  if ($t_url == base64_encode(base64_decode($t_url))) {
    $t_url = base64_decode($t_url);
  }
  preg_match('/^(http|https|thunder|qqdl|ed2k|Flashget|qbrowser):\/\//i', $t_url, $matches);
  if ($matches) {
    $url   = $t_url;
    $title = '页面加载中,请稍候...';
  } else {
    preg_match('/\./i', $t_url, $matche);
    if ($matche) {
      $url   = 'http://' . $t_url;
      $title = '页面加载中,请稍候...';
    } else {
      $url   = 'http://' . $_SERVER['HTTP_HOST'];
      $title = '参数错误，正在返回首页...';
    }
  }
} else {
  $title = '参数缺失，正在返回首页...';
  $url   = 'http://' . $_SERVER['HTTP_HOST'];
}
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex, nofollow" />
  <noscript>
    <meta http-equiv="refresh" content="1;url='<?= $url; ?>';">
  </noscript>
  <script>
    function link_jump() {
      var MyHOST = new RegExp("<?= $_SERVER['HTTP_HOST']; ?>");
      if (!MyHOST.test(document.referrer)) {
        location.href = "http://" + MyHOST;
      }
      location.href = "<?= $url; ?>";
    }
    setTimeout(link_jump, 2000);
    setTimeout(function() {
      window.opener = null;
      window.close();
    }, 50000);
  </script>
  <title><?= $title; ?></title>
  <style type="text/css">
    *,:after,:before{position:relative;-webkit-box-sizing:border-box;box-sizing:border-box}body{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-orient:vertical;-webkit-box-direction:normal;-ms-flex-direction:column;flex-direction:column;height:100%;background:#191919}.loader{width:20vw;max-height:90vh;-webkit-transform-origin:50% 50%;transform-origin:50% 50%;overflow:visible}.loader .ci1{fill:#191919;-webkit-animation:toBig 3s -1.5s infinite;animation:toBig 3s -1.5s infinite}.loader .ci1,.loader .ciw{transform-box:fill-box;-webkit-transform-origin:50% 50%;transform-origin:50% 50%}.loader .ciw{-webkit-animation:breath 3s infinite;animation:breath 3s infinite}.loader .ci2{fill:#191919;-webkit-animation:toBig2 3s infinite;animation:toBig2 3s infinite}.loader .ci2,.points{transform-box:fill-box;-webkit-transform-origin:50% 50%;transform-origin:50% 50%}.points{-webkit-animation:rot 3s infinite;animation:rot 3s infinite}@-webkit-keyframes rot{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}30%{-webkit-transform:rotate(1turn);transform:rotate(1turn)}50%{-webkit-transform:rotate(1turn);transform:rotate(1turn)}80%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(0deg);transform:rotate(0deg)}}@keyframes rot{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}30%{-webkit-transform:rotate(1turn);transform:rotate(1turn)}50%{-webkit-transform:rotate(1turn);transform:rotate(1turn)}80%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(0deg);transform:rotate(0deg)}}@-webkit-keyframes toBig{0%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}30%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}50%{-webkit-transform:scale(10) translateX(-4.5px);transform:scale(10) translateX(-4.5px)}80%{-webkit-transform:scale(10) translateX(-4.5px);transform:scale(10) translateX(-4.5px)}to{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}}@keyframes toBig{0%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}30%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}50%{-webkit-transform:scale(10) translateX(-4.5px);transform:scale(10) translateX(-4.5px)}80%{-webkit-transform:scale(10) translateX(-4.5px);transform:scale(10) translateX(-4.5px)}to{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}}@-webkit-keyframes toBig2{0%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}30%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}50%{-webkit-transform:scale(10) translateX(4.5px);transform:scale(10) translateX(4.5px)}80%{-webkit-transform:scale(10) translateX(4.5px);transform:scale(10) translateX(4.5px)}to{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}}@keyframes toBig2{0%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}30%{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}50%{-webkit-transform:scale(10) translateX(4.5px);transform:scale(10) translateX(4.5px)}80%{-webkit-transform:scale(10) translateX(4.5px);transform:scale(10) translateX(4.5px)}to{-webkit-transform:scale(1) translateX(0);transform:scale(1) translateX(0)}}@-webkit-keyframes breath{15%{-webkit-transform:scale(1);transform:scale(1)}40%{-webkit-transform:scale(1.1);transform:scale(1.1)}65%{-webkit-transform:scale(1);transform:scale(1)}90%{-webkit-transform:scale(1.1);transform:scale(1.1)}}@keyframes breath{15%{-webkit-transform:scale(1);transform:scale(1)}40%{-webkit-transform:scale(1.1);transform:scale(1.1)}65%{-webkit-transform:scale(1);transform:scale(1)}90%{-webkit-transform:scale(1.1);transform:scale(1.1)}}
  </style>
</head>

<body>
  <svg class="loader" viewBox="0 0 100 100">
    <g class="points">
      <circle class="ciw" cx="50" cy="50" r="50" fill="#fff" />
      <circle class="ci2" cx="5" cy="50" r="4" />
      <circle class="ci1" cx="95" cy="50" r="4" />
    </g>
  </svg>
</body>

</html>