<?php
require './inc/db.php';
$posts = $pdo->query("SELECT * FROM post ")->fetchAll();
$category = $_GET['category'] ?? '';
$q = trim($_GET['q'] ?? '');
$intl = isset($_GET['intl']);

$sql = "SELECT * FROM post WHERE 1=1";
$params = [];

if ($category !== '') {
  $sql .= " AND category = ?";
  $params[] = $category;
}
if ($q !== '') {
  $sql .= " AND (company LIKE ? OR title LIKE ? OR note LIKE ?)";
  $like = "%$q%";
  array_push($params, $like, $like, $like);
}
if ($intl) {
  $sql .= " AND intl = 1";
}
$sql .= " ORDER BY closed ASC, deadline ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ECC インターンボード</title>
  <link rel="stylesheet" href="./inc/style.css">
</head>

<body>
  <?php include_once "./inc/header.php" ?>
  <main class="wrap">
    <section class="hero">
      <h1>見つけたインターン情報を、<br>学生みんなでシェアしよう。</h1>
      <p>「この会社で募集してた！」を投稿して、後輩や友だちの就職活動に役立てましょう。</p>
    </section>
    <form class="search" method="get">
      <input type="search" name="q" value="<?= h($q) ?>" placeholder="企業名・キーワードで検索">
      <button type="submit" class="btn-dark">検索</button>
    </form>

    <div class="chips">
      <a class="chip <?= ($category === '' && !$intl) ? 'is-active' : '' ?>" href="index.php">すべて</a>
      <a class="chip <?= $category === 'Web' ? 'is-active' : '' ?>" href="index.php?category=Web">Web</a>
      <a class="chip <?= $category === 'ゲーム' ? 'is-active' : '' ?>" href="index.php?category=<?= urlencode('ゲーム') ?>">ゲーム</a>
      <a class="chip <?= $category === 'AI' ? 'is-active' : '' ?>" href="index.php?category=AI">AI</a>
      <a class="chip <?= $intl ? 'is-active' : '' ?>" href="index.php?intl=1">留学生</a>
      <a class="chip <?= $category === 'その他' ? 'is-active' : '' ?>" href="index.php?category=<?= urlencode('その他') ?>">その他</a>
    </div>


    <p class="count">4 件の情報</p>

    <div class="grid">

      <article class="card">
        <div class="card-top">
          <span class="tag tag-Web">Web</span>
        </div>
        <h3>フロントエンド開発 夏季インターン</h3>
        <p class="meta">サンプル株式会社 ／ 東京・リモート可</p>
        <div class="badges">
          <span class="badge">時給 1,300円</span>
          <span class="badge badge-intl">留学生OK</span>
          <span class="badge">締切 10/31</span>
        </div>
        <div class="card-bottom"><span></span><a href="detail.php?id=1">詳しく見る →</a></div>
      </article>

      <article class="card">
        <div class="card-top">
          <span class="tag tag-ゲーム">ゲーム</span>
        </div>
        <h3>ゲームプランナー体験インターン</h3>
        <p class="meta">ダミーゲームス ／ 大阪</p>
        <div class="badges">
          <span class="badge">時給 1,200円</span>
          <span class="badge">締切 11/15</span>
        </div>
        <div class="card-bottom"><span></span><a href="detail.php?id=2">詳しく見る →</a></div>
      </article>

      <article class="card">
        <div class="card-top">
          <span class="tag tag-AI">AI</span>
        </div>
        <h3>AIデータ分析アシスタント</h3>
        <p class="meta">テスト・ラボ株式会社 ／ リモート</p>
        <div class="badges">
          <span class="badge">無給</span>
          <span class="badge badge-intl">留学生OK</span>
          <span class="badge">締切 11/30</span>
        </div>
        <div class="card-bottom"><span></span><a href="detail.php?id=3">詳しく見る →</a></div>
      </article>

      <article class="card is-closed">
        <div class="card-top">
          <span class="tag tag-Web">Web</span>
          <span class="tag tag-closed">募集終了</span>
        </div>
        <h3>PHPバックエンド職場体験</h3>
        <p class="meta">サンプルシステムズ ／ 名古屋</p>
        <div class="badges">
          <span class="badge">時給 1,400円</span>
          <span class="badge">締切 9/30</span>
        </div>
        <div class="card-bottom"><span></span><a href="detail.php?id=4">詳しく見る →</a></div>
      </article>

    </div>
  </main>
</body>

</html>