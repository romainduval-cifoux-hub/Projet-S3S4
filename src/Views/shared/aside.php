<?php
$menuTitle1 = $menuTitle1 ?? 'Gestion des chantiers';
$menuTitle2 = $menuTitle2 ?? 'Gestion Employé';

$menu1 = $menu1 ?? [
  ['label' => 'Nouveau chantier', 'href' => BASE_URL . '/public/index.php?page=chantier/create'],
  ['label' => 'Éditer chantier',  'href' => BASE_URL . '/public/index.php?page=chantier/list'],
];

$menu2 = $menu2 ?? [
  ['label' => 'Ajouter employé', 'href' => BASE_URL . '/public/index.php?page=employe/create'],
  ['label' => 'Éditer employé',  'href' => BASE_URL . '/public/index.php?page=employe/list'],
];

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