<?php
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
?>

<aside class="tj-aside">
  <h3><?= htmlspecialchars($menuTitle1) ?></h3>
  <nav class="tj-aside-group">
    <?php foreach ($menu1 as $item): 
      $active = (strpos($currentUrl, $item['href']) !== false) ? 'active' : '';
    ?>
      <a class="tj-aside-link <?= $active ?>" href="<?= htmlspecialchars($item['href']) ?>">
        <?= htmlspecialchars($item['label']) ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <h3><?= htmlspecialchars($menuTitle2) ?></h3>
  <nav class="tj-aside-group">
    <?php foreach ($menu2 as $item): 
      $active = (strpos($currentUrl, $item['href']) !== false) ? 'active' : '';
    ?>
      <a class="tj-aside-link <?= $active ?>" href="<?= htmlspecialchars($item['href']) ?>">
        <?= htmlspecialchars($item['label']) ?>
      </a>
    <?php endforeach; ?>
  </nav>
</aside>