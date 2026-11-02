<?php
include_once './inc/db.php';
include_once './inc/mode.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM post WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    http_response_code(404);
}
?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $p ? h($p['title']) . ' | ' : '' ?>ECC インターンボード</title>
  <link rel="stylesheet" href="./inc/style.css?v=4">
</head>

<body>

    <?php include_once "./inc/header.php" ?>
    <main class="wrap">
        <a class="back-link" href="index.php">← 一覧に戻る</a>

        <?php if (!$p): ?>

            <p class="empty">この情報は見つかりませんでした。</p>

        <?php else: ?>

            <div class="detail-layout">

                <article class="detail-main">
                    <div class="badges">
                        <span class="tag tag-<?= h($p['category']) ?>"><?= h($p['category']) ?></span>
                        <?php if ($p['intl']): ?><span class="tag badge-intl">留学生OK</span><?php endif; ?>
                        <?php if ($p['closed']): ?><span class="tag tag-closed">募集終了</span><?php endif; ?>
                    </div>

                    <h1><?= h($p['title']) ?></h1>
                    <div class="detail-company"><?= h($p['company']) ?></div>

                    <div class="info-grid">
                        <div>
                            <div class="info-label">勤務地</div>
                            <div class="info-value"><?= h($p['place']) ?></div>
                        </div>
                        <div>
                            <div class="info-label">時給</div>
                            <div class="info-value"><?= $p['salary'] > 0 ? number_format($p['salary']) . '円' : '無給' ?></div>
                        </div>
                        <div>
                            <div class="info-label">締切</div>
                            <div class="info-value"><?= h(date('Y年n月j日', strtotime($p['deadline']))) ?></div>
                        </div>
                    </div>

                    <div>
                        <h2>投稿者のひとこと</h2>
                        <p class="detail-note"><?= $p['note'] ? nl2br(h($p['note'])) : 'ひとことはありません。' ?></p>
                    </div>
                </article>

                <aside class="detail-side">
                    <div class="side-title">応募したい方へ</div>

                    <?php if ($p['closed']): ?>
                        <p>この募集は終了しています。</p>
                    <?php elseif (preg_match('#^https?://#i', $p['url'])): ?>
                        <p>応募は企業の募集ページから行います。外部サイトが新しいタブで開きます。</p>
                        <a class="btn-block primary" href="<?= h($p['url']) ?>" target="_blank" rel="noopener">募集ページへ</a>
                    <?php endif; ?>

                    <a class="btn-block outline" href="index.php">一覧に戻る</a>
                    <div style="font-size:13px;color:#6B7194">投稿: 学生</div>
                </aside>

            </div>

        <?php endif; ?>
    </main>
</body>

</html>