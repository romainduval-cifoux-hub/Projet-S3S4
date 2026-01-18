<?php

require_once(__DIR__ . '/../../../config.php'); 
require_once(__DIR__ . '/../../../Database/db.php');     


$menuTitle1 = "Gestion entreprise";
$menuTitle2 = "Création document";

$menu1 = [
  ['label'=>'Tableau de bord',  'href'=> BASE_URL.'/public/index.php?page=chef/facturation/dashboard'],
  ['label'=>'Gestion des documents', 'href'=> BASE_URL.'/public/index.php?page=chef/facturation/GestionFacturation'],
  ['label'=>"Informations de l'entreprise",  'href'=> BASE_URL.'/public/index.php?page=chef/facturation/EditBusinessInfo'],
];

$menu2 = [
  ['label'=>'Nouveau document', 'href'=> BASE_URL.'/public/index.php?page=chef/facturation/createFacture'],
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