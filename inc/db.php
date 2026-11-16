<?php
$pdo = new PDO('mysql:host=localhost;dbname=internboard;charset=utf8mb4', 'internboard_user', 'root', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec("SET autocommit=1");

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Auto-close posts whose deadline has passed
$pdo->exec("UPDATE post SET closed = 1
            WHERE closed = 0
              AND deadline IS NOT NULL
              AND deadline < CURDATE()");