<?php
// theme: "dark" (default) or "light" - set with ?mode=light / ?mode=dark and kept in a cookie
if (isset($_GET['mode'])) {
  $_COOKIE['mode'] = ($_GET['mode'] === 'light') ? 'light' : 'dark';
  setcookie('mode', $_COOKIE['mode'], time() + 60*60*24*30, '/');
}
$mode = (($_COOKIE['mode'] ?? '') === 'light') ? 'light' : 'dark';
$next = ($mode === 'light') ? 'dark' : 'light';
$mode_link = http_build_query(array_merge($_GET, ['mode' => $next]));
