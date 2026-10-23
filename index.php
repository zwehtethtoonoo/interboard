<?php
require './inc/db.php';
$message = "";
$new_title = "";
$new_company = "";
$new_category = "Web";
$new_place = "";
$new_salary = "";
$new_deadline = "";
$new_url = "";
$new_note = "";
$new_intl = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'])) {
        $new_title = $_POST['title'];
    }
    if (isset($_POST['company'])) {
        $new_company = $_POST['company'];
    }
    if (isset($_POST['category'])) {
        $new_category = $_POST['category'];
    }
    if (isset($_POST['place'])) {
        $new_place = $_POST['place'];
    }
    if (isset($_POST['salary'])) {
        $new_salary = $_POST['salary'];
    }
    if (isset($_POST['deadline'])) {
        $new_deadline = $_POST['deadline'];
    }
    if (isset($_POST['url'])) {
        $new_url = $_POST['url'];
    }
    if (isset($_POST['note'])) {
        $new_note = $_POST['note'];
    }
    if (isset($_POST['intl'])) {
        $new_intl = 1;
    }

    if ($new_title === "" || $new_company === "" || $new_deadline === "" || $new_url === "") {
        $message = "エラー: タイトル・会社名・締切・URLは必須項目です。";
    } else {
        $sql = "INSERT INTO post (category, title, company, place, salary, deadline, intl, closed, url, note) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($new_category, $new_title, $new_company, $new_place, (int)$new_salary, $new_deadline, $new_intl, $new_url, $new_note));

        header('Location: index.php');
        exit;
    }
}

$intl = ($_GET['intl'] ?? '') === '1';
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

?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ECC インターンボード</title>
  <link rel="stylesheet" href="./inc/style.css?v=2">
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
      <input type="hidden" name="category" value="<?= h($category) ?>">
      <?php if ($intl): ?><input type="hidden" name="intl" value="<?= $intl ? '1' : '' ?>"><?php endif; ?>
      <button type="submit" class="btn-dark" value="">検索</button>
    </form>

    <div class="chips">
      <button type="button" class="chip <?= ($category === '' && !$intl) ? 'is-active' : '' ?>">すべて</button>
      <button type="button" class="chip <?= $category === 'Web' ? 'is-active' : '' ?>" data-category="Web">Web</button>
      <button type="button" class="chip <?= $category === 'ゲーム' ? 'is-active' : '' ?>" data-category="ゲーム">ゲーム</button>
      <button type="button" class="chip <?= $category === 'AI' ? 'is-active' : '' ?>" data-category="AI">AI</button>
      <button type="button" class="chip <?= $intl ? 'is-active' : '' ?>" data-intl="1">留学生</button>
      <button type="button" class="chip <?= $category === 'その他' ? 'is-active' : '' ?>" data-category="その他">その他</button>
    </div>

    <div id="results">
      <p class="count"><?= count($posts) ?> 件の情報</p>

      <div class="grid">
        <?php foreach ($posts as $p) : ?>
          <article class="card <?= $p['closed'] ? "is-closed" : "" ?>">
            <div class="card-top">
              <span class="tag tag-<?= h($p['category'])  ?>"><?= h($p['category'])  ?></span>
            </div>
            <h3><?= h($p['title'])  ?></h3>
            <p class="meta"><?= h($p['company'])  ?> ／ <?= h($p['place'])  ?></p>
            <div class="badges">
              <span class="badge"><?= $p['salary'] > 0 ? '時給 ' . number_format($p['salary']) . '円' : '無給' ?></span>
              <?php if ($p['intl']): ?><span class="badge badge-intl">留学生OK</span> <?php endif; ?>
              <span class="badge">締切 <?= h(date('n/j', strtotime($p['deadline']))) ?></span>
            </div>
            <div class="card-bottom"><span></span><a href="detail.php?id=<?= $p['id'] ?> ?>">詳しく見る →</a></div>
          </article>
        <?php endforeach; ?>
        <?php if (!$posts): ?><p class="empty">条件に合う情報がありません。</p><?php endif; ?>
      </div>
    </div>
  </main>
  <!-- Post dialog -->
<dialog id="postDialog" <?= $message !== "" ? 'data-open="1"' : '' ?>>
  <form method="post" action="index.php" class="dialog-body">
    <h2>インターン情報を投稿する</h2>

    <?php if ($message !== ""): ?>
      <p class="form-errors"><?= h($message) ?></p>
    <?php endif; ?>

    <label>タイトル<input name="title" value="<?= h($new_title) ?>" maxlength="100" required></label>
    <label>会社名<input name="company" value="<?= h($new_company) ?>" maxlength="100" required></label>
    <label>カテゴリ
      <select name="category">
        <?php foreach (['Web','ゲーム','AI','その他'] as $c): ?>
          <option value="<?= h($c) ?>" <?= $c === $new_category ? 'selected' : '' ?>><?= h($c) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>勤務地<input name="place" value="<?= h($new_place) ?>" maxlength="100" placeholder="例：東京・リモート可"></label>
    <label>時給（円）<input name="salary" type="number" min="0" value="<?= h($new_salary) ?>" placeholder="例：1200（無給は0）"></label>
    <label>締切<input name="deadline" type="date" value="<?= h($new_deadline) ?>" required></label>
    <label>募集ページのURL<input name="url" type="url" value="<?= h($new_url) ?>" maxlength="255" placeholder="https://" required></label>
    <label>ひとこと<textarea name="note" rows="3"><?= h($new_note) ?></textarea></label>
    <label class="check"><input name="intl" type="checkbox" value="1" <?= $new_intl ? 'checked' : '' ?>> 留学生も応募OK</label>

    <div class="actions">
      <button type="button" class="btn-ghost" id="cancelPost">キャンセル</button>
      <button type="submit" class="btn-primary">投稿する</button>
    </div>
  </form>
</dialog>
  <script src="./inc/script.js?v=1"></script>
</body>

</html>