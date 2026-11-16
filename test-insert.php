<?php
require './inc/db.php';
header('Content-Type: text/plain; charset=utf-8');

function q($pdo, $sql) { return $pdo->query($sql)->fetchAll(PDO::FETCH_NUM); }

echo "server: ", json_encode(q($pdo, "SELECT @@hostname, @@port, @@datadir, DATABASE(), USER()")), "\n";
echo "autocommit: ", q($pdo, "SELECT @@autocommit")[0][0], "  inTransaction: ", var_export($pdo->inTransaction(), true), "\n";
echo "engine: ", json_encode(q($pdo, "SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA='internboard' AND TABLE_NAME='post'")), "\n";
echo "rows before: ", q($pdo, "SELECT COUNT(*) FROM post")[0][0], "\n";

$stmt = $pdo->prepare("INSERT INTO post (category, title, company, place, salary, deadline, intl, closed, url, note) VALUES ('Web','TEST','TEST','',0,'2026-12-31',0,0,'https://example.com','')");
$stmt->execute();
$id = $pdo->lastInsertId();
echo "inserted id: $id  rowCount: ", $stmt->rowCount(), "\n";
echo "rows after: ", q($pdo, "SELECT COUNT(*) FROM post")[0][0], "\n";
echo "my row: ", json_encode(q($pdo, "SELECT id, title FROM post WHERE id = " . (int)$id)), "\n";