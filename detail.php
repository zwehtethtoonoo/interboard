<?php
require './inc/db.php';
require './inc/mode.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM post WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
  http_response_code(404);
}
// only allow http(s) links for the apply button
$apply_url = ($p && preg_match('#^https?://#i', $p['url'])) ? $p['url'] : '';
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $p ? h($p['title']) . ' | ' : '' ?>ECC インターンボード</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap">
  <link rel="stylesheet" href="./inc/style.css?v=4">
</head>

<body class="<?= $mode ?>">
  <?php include_once "./inc/header.php" ?>
  <main class="page">
    <a class="back" href="search.php">← 一覧に戻る</a>

    <?php if (!$p): ?>
      <p class="empty">この情報は見つかりませんでした。</p>
    <?php else: ?>
      <div class="detail-layout">
        <section class="win detail-main" aria-labelledby="detail-title">
          <div class="win-bar"><span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span><span class="fname">Detail.java</span></div>
          <div class="win-body">
            <span class="tag tag-<?= h($p['category']) ?>"><?= h($p['category']) ?></span>
            <h1 id="detail-title" class="detail-title"><?= h($p['title']) ?></h1>
            <div class="r"><span class="ln" aria-hidden="true">1</span><span class="t"><span class="c">String</span> company = <span class="s">"<?= h($p['company']) ?>"</span>; <span class="m">// 会社名</span></span></div>
            <div class="r"><span class="ln" aria-hidden="true">2</span><span class="t"><span class="c">String</span> place = <span class="s">"<?= h($p['place']) ?>"</span>; <span class="m">// 勤務地</span></span></div>
            <div class="r"><span class="ln" aria-hidden="true">3</span><span class="t"><span class="k">int</span> salary = <?= (int)$p['salary'] ?>; <span class="m">// <?= $p['salary'] > 0 ? '時給（円）' : '無給' ?></span></span></div>
            <div class="r"><span class="ln" aria-hidden="true">4</span><span class="t"><span class="c">String</span> deadline = <span class="s">"<?= h(date('Y年n月j日', strtotime($p['deadline']))) ?>"</span>; <span class="m">// 締切</span></span></div>
            <div class="r"><span class="ln" aria-hidden="true">5</span><span class="t"><span class="k">boolean</span> intl = <span class="k"><?= $p['intl'] ? 'true' : 'false' ?></span>; <span class="m">// 留学生も応募OK</span></span></div>
            <div class="r"><span class="ln" aria-hidden="true">6</span><span class="t"><span class="k">boolean</span> closed = <span class="k"><?= $p['closed'] ? 'true' : 'false' ?></span>; <span class="m">// 募集終了</span></span></div>
            <div class="r"><span class="ln" aria-hidden="true">7</span><span class="t m">// 投稿者のひとこと</span></div>
            <div class="r"><span class="ln" aria-hidden="true">8</span><span class="t"><span class="c">String</span> note = <span class="s">"<?= nl2br(h($p['note'])) ?>"</span>;</span></div>
          </div>
        </section>

        <aside class="detail-side">
          <h2>応募したい方へ</h2>
          <p>応募は企業の募集ページから行います。外部サイトが新しいタブで開きます。</p>
          <?php if ($p['closed']): ?>
            <p class="m">この募集は終了しています。</p>
          <?php endif; ?>
          <?php if ($apply_url !== ''): ?>
            <a class="btn btn-primary btn-block" href="<?= h($apply_url) ?>" target="_blank" rel="noopener noreferrer">募集ページへ</a>
          <?php endif; ?>
          <a class="btn btn-ghost btn-block" href="search.php">一覧に戻る</a>
          <div class="small">投稿: 学生</div>
        </aside>
      </div>
    <?php endif; ?>
  </main>
</body>

</html>
