<header class="site-header">
  <a class="brand" href="index.php">ECC インターンボード</a>
  <nav class="nav-actions" aria-label="メインメニュー">
    <?php ?>
    <a class="mode-switch" href="?<?= h($mode_link) ?>" aria-label="darkMode() <?= $mode === 'dark' ? 'ON' : 'OFF' ?>"> <?= $mode !== 'dark' ? 'day()' : 'night()' ?>
      <span class="mode-track">
        <span class="mode-knob"></span>
      </span>
    </a>
    <a class="nav-link" href="search.php">search()</a>
    <a class="btn btn-primary" href="index.php#post">intern.newPost()</a>
  </nav>
</header>