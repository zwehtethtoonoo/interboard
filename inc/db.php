<?php
$pdo = new PDO('mysql:host=localhost;dbname=internboard;charset=utf8mb4', 'internboard_user', 'root', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }