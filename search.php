<?php
require './inc/db.php';
require './inc/mode.php';

$category = $_GET['category'] ?? '';
$q = trim($_GET['q'] ?? '');
$intl = ($_GET['intl'] ?? '') === '1';

$sql = "SELECT * FROM post WHERE 1=1";
$params = [];

if ($category !== '') {
  $sql .= " AND category = ?";
  $params[] = $category;
}
if ($q !== '') {
  $qk = mb_convert_kana($q, 'C');   // hiragana -> katakana
  $text = "CONCAT_WS(' ', category, title, company, place, salary, deadline, note, IF(intl = 1, '留学生OK', ''), IF(closed = 1, '募集終了', ''))";
  $sql .= " AND ($text LIKE ? OR $text LIKE ?)";
  $params[] = "%$q%";
  $params[] = "%$qk%";
}
if ($intl) {
  $sql .= " AND intl = 1";
}
$sql .= " ORDER BY closed ASC, deadline ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// link to this page with a different category / intl setting (keeps the keyword)
function search_url($q, $category, $intl)
{
  $p = [];
  if ($q !== '') {
    $p['q'] = $q;
  }
  if ($category !== '') {
    $p['category'] = $category;
  }
  if ($intl) {
    $p['intl'] = '1';
  }
  return 'search.php' . ($p ? '?' . http_build_query($p) : '');
}
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>探す | ECC インターンボード</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap">
  <link rel="stylesheet" href="./inc/style.css?v=2">
</head>

<body class="<?= $mode ?>">
  <?php include_once "./inc/header.php" ?>
  <main class="page">
    <section class="win" aria-labelledby="search-title">
      <div class="win-bar"><span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span><span class="fname">Search.java</span></div>
      <form class="win-body search-body" method="get" action="search.php">
        <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?= h($category) ?>"><?php endif; ?>
        <?php if ($intl): ?><input type="hidden" name="intl" value="1"><?php endif; ?>

        <div class="r" style="min-height:72px"><span class="ln" aria-hidden="true">1</span><h1 id="search-title" class="t big"><span class="m">//</span> インターン情報を検索</h1></div>
        <div class="r"><span class="ln" aria-hidden="true">2</span><span class="c">String</span> keyword =
          <label for="q" style="position:absolute;left:-9999px">企業名・キーワード</label>
          <input class="in" id="q" type="search" name="q" value="<?= h($q) ?>" placeholder="企業名・キーワードで検索" style="max-width:340px">;
        </div>
        <div class="r"><span class="ln" aria-hidden="true">3</span><span class="c">String</span> category =
          <a class="chip <?= ($category === '' && !$intl) ? 'is-active' : '' ?>" href="<?= h(search_url($q, '', false)) ?>">"すべて"</a>
          <?php foreach (['Web', 'ゲーム', 'AI', 'その他'] as $c): ?>
            <a class="chip <?= $category === $c ? 'is-active' : '' ?>" href="<?= h(search_url($q, $c, $intl)) ?>">"<?= h($c) ?>"</a>
          <?php endforeach; ?>;
        </div>
        <div class="r"><span class="ln" aria-hidden="true">4</span><span class="k">boolean</span> international =
          <a class="chip <?= $intl ? 'is-active' : '' ?>" href="<?= h(search_url($q, $category, true)) ?>">true</a>
          <a class="chip <?= !$intl ? 'is-active' : '' ?>" href="<?= h(search_url($q, $category, false)) ?>">false</a>;
          <span class="m">// 留学生OKだけ表示</span>
        </div>
        <div class="r" style="margin-top:8px"><span class="ln" aria-hidden="true">5</span><button type="submit" class="btn btn-primary">results = search();</button></div>
      </form>
    </section>

    <p class="out">&gt; OUTPUT　<?= count($posts) ?> 件の情報</p>

    <div class="list">
      <?php foreach ($posts as $p): ?>
        <article class="post <?= $p['closed'] ? 'is-closed' : '' ?>">
          <div class="post-left">
            <div class="cat-tag tag-<?= h($p['category']) ?>"><?= h($p['category']) ?></div>
            <h3><?= h($p['title']) ?></h3>
            <div class="meta"><?= h($p['company']) ?> ／ <?= h($p['place']) ?> ／ <?= $p['salary'] > 0 ? '時給 ' . number_format($p['salary']) . '円' : '無給' ?> ／ 締切 <?= h(date('n/j', strtotime($p['deadline']))) ?></div>
          </div>
          <div class="post-side">
            <span class="status"><?= $p['closed'] ? 'CLOSED' : 'OPEN' ?></span>
            <?php if ($p['intl']): ?><span class="badge">留学生OK</span><?php endif; ?>
            <a href="detail.php?id=<?= (int)$p['id'] ?>">詳しく見る →</a>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (!$posts): ?><p class="empty">条件に合う情報がありません。</p><?php endif; ?>
    </div>
  </main>
</body>

</html>
