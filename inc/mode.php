<?php
if (isset($_GET['mode'])) {
  $_COOKIE['mode'] = ($_GET['mode'] === 'editor') ? 'editor' : 'normal';
  setcookie('mode', $_COOKIE['mode'], time() + 60*60*24*30, '/');
}
$mode = (($_COOKIE['mode'] ?? '') === 'editor') ? 'editor' : 'normal';
$next = ($mode === 'editor') ? 'normal' : 'editor';
$mode_link = http_build_query(array_merge($_GET, ['mode' => $next]));
?>