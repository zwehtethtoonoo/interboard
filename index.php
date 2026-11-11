<?php
require './inc/db.php';
require './inc/mode.php';

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
$categories = ['Web', 'ゲーム', 'AI', 'その他'];

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
  if (!in_array($new_category, $categories, true)) {
    $new_category = 'Web';
  }

  if ($new_title === "" || $new_company === "" || $new_deadline === "" || $new_url === "") {
    $message = "エラー: タイトル・会社名・締切・URLは必須項目です。";
  } elseif (!preg_match('#^https?://#i', $new_url)) {
    $message = "エラー: URLは http:// か https:// で始めてください。";
  } else {
    $sql = "INSERT INTO post (category, title, company, place, salary, deadline, intl, closed, url, note) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($new_category, $new_title, $new_company, $new_place, (int)$new_salary, $new_deadline, $new_intl, $new_url, $new_note));

    header('Location: search.php');
    exit;
  }
}

$open_count = (int)$pdo->query("SELECT COUNT(*) FROM post WHERE closed = 0")->fetchColumn();
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ECC インターンボード</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap">
  <link rel="stylesheet" href="./inc/style.css?v=1">
</head>

<body class="<?= $mode ?>">
  <?php include_once "./inc/header.php" ?>
  <main class="page">
    <section class="win" aria-labelledby="welcome">
      <div class="win-bar"><span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span><span class="fname">Welcome.java</span></div>
      <div class="win-body">
        <div class="r">
          <span class="ln" aria-hidden="true">1</span>
          <span class="t"><span class="k">package</span> internboard;</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">2</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">3</span>
          <span class="t"><span class="k">public class</span>
            <span class="c">Welcome</span> {</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">4</span>
          <span class="t i1"><span class="k">public static void</span> main(<span class="c">String</span>[] args) {</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">5</span>
          <span class="t i2 m">// ようこそ！ ゆっくり見ていってね。</span>
        </div>
        <div class="r mid">
          <span class="ln" aria-hidden="true" style="font-size:19px">6</span>
          <span class="t i2"><span class="c">Intern</span> intern = <span class="k">new</span>
            <span class="c">Intern</span>(<span class="s">"AI"</span>);</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">7</span>
          <span class="t i2"><span class="k">int</span> openPosts = <?= $open_count ?>; <span class="m">// 募集中の情報</span></span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">8</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">9</span>
          <span class="t i2"><span class="k">if</span> (intern.isReady()) {</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">10</span>
          <span class="t i3">System.out.println(<span class="s">"ECC インターンボードへようこそ！"</span>);</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">11</span>
          <span class="t i2">}</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">12</span>
          <span class="t i1">}</span>
        </div>
        <div class="r">
          <span class="ln" aria-hidden="true">13</span>
          <span class="t">}</span>
        </div>
      </div>
    </section>
    <p class="lead">先輩や友だちが見つけたインターン情報を、学生みんなでシェアしよう。</p>
    <div class="actions">
      <a class="btn btn-primary btn-lg" href="search.php">intern.browseAll()</a>
      <a class="btn btn-ghost btn-lg" href="#post">intern.newPost()</a>
    </div>
  </main>

  <!-- Post form 5 (opens with #post, or automatically when there is an error) -->
  <div class="overlay <?= $message !== "" ? 'is-open' : '' ?>" id="post">
    <a class="overlay-close" href="index.php" aria-label="閉じる" tabindex="-1"></a>
    <div class="win dialog" role="dialog" aria-modal="true" aria-labelledby="post-title">
      <div class="win-bar"><span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span><span class="fname">Post.java</span></div>
      <form method="post" action="index.php" class="win-body form">
        <h2 id="post-title">インターン情報を投稿する</h2>

        <?php if ($message !== ""): ?>
          <p class="form-errors" role="alert"><?= h($message) ?></p>
        <?php endif; ?>

        <div class="f">
          <span class="m" id="cat-label">// カテゴリ</span>
          <div class="row" role="radiogroup" aria-labelledby="cat-label"><span class="c">String</span> category =
            <?php foreach ($categories as $i => $c): ?>
              <span class="cat"><input type="radio" name="category" id="cat-<?= $i ?>" value="<?= h($c) ?>" <?= $c === $new_category ? 'checked' : '' ?>><label for="cat-<?= $i ?>">"<?= h($c) ?>"</label></span>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="f"><label class="m" for="f-title">// タイトル</label>
          <div class="row"><span class="c">String</span> title = <input class="in" id="f-title" name="title" value="<?= h($new_title) ?>" maxlength="100" required></div>
        </div>
        <div class="f"><label class="m" for="f-company">// 会社名</label>
          <div class="row"><span class="c">String</span> company = <input class="in" id="f-company" name="company" value="<?= h($new_company) ?>" maxlength="100" required></div>
        </div>
        <div class="f"><label class="m" for="f-place">// 勤務地</label>
          <div class="row"><span class="c">String</span> place = <input class="in" id="f-place" name="place" value="<?= h($new_place) ?>" maxlength="100" placeholder="例：東京・リモート可"></div>
        </div>
        <div class="f"><label class="m" for="f-salary">// 時給（円）</label>
          <div class="row"><span class="k">int</span> salary = <input class="in" id="f-salary" name="salary" type="number" min="0" value="<?= h($new_salary) ?>" placeholder="例：1200（無給は0）"></div>
        </div>
        <div class="f"><label class="m" for="f-deadline">// 締切</label>
          <div class="row"><span class="c">String</span> deadline = <input class="in" id="f-deadline" name="deadline" type="date" value="<?= h($new_deadline) ?>" required></div>
        </div>
        <div class="f"><label class="m" for="f-url">// 募集ページのURL</label>
          <div class="row"><span class="c">String</span> url = <input class="in" id="f-url" name="url" type="url" value="<?= h($new_url) ?>" maxlength="255" placeholder="https://" required></div>
        </div>
        <div class="f"><label class="m" for="f-note">// ひとこと</label>
          <textarea class="in" id="f-note" name="note" rows="3"><?= h($new_note) ?></textarea>
        </div>
        <div class="row"><input class="check" type="checkbox" id="f-intl" name="intl" value="1" <?= $new_intl ? 'checked' : '' ?>><label for="f-intl"><span class="k">boolean</span> intl = <span class="k">true</span>; <span class="m">// 留学生も応募OK</span></label></div>

        <div class="actions end">
          <a class="btn btn-ghost" href="index.php">キャンセル</a>
          <button type="submit" class="btn btn-primary">投稿する</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>