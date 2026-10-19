<?php
/**
 * Shared document head + navigation.
 * Expects: $c (content array), $page ('home' | 'gallery').
 */
$page = $page ?? 'home';
$isHome = $page === 'home';
// On the home page anchors are plain (#about); on other pages they point back to index.php#about.
$a = $isHome ? '' : 'index.php';
$pageTitle = $pageTitle ?? ($c['meta']['title'] ?? 'ArtiSpark');
$metaDescription = $metaDescription ?? ($c['meta']['description'] ?? '');
$galleryEnabled = !empty($c['gallery']['enabled']);

// Menu built from the editable nav settings (admin > Header & Menu), in the
// saved order with saved labels. A section's item only shows while that
// section is visible; Contact is always shown.
$navDefaults = [
    ['id' => 'about', 'label' => 'About'],
    ['id' => 'services', 'label' => 'Services'],
    ['id' => 'team', 'label' => 'Team'],
    ['id' => 'testimonials', 'label' => 'Testimonials'],
    ['id' => 'gallery', 'label' => 'Gallery'],
    ['id' => 'contact', 'label' => 'Contact'],
];
$navItems = [];
foreach (($c['nav']['items'] ?? $navDefaults) as $item) {
    $id = $item['id'] ?? '';
    $label = trim((string)($item['label'] ?? '')) !== '' ? $item['label'] : ucfirst($id);
    if ($id === 'gallery') {
        if ($galleryEnabled) $navItems[] = ['gallery.php', $label];
    } elseif ($id === 'contact') {
        $navItems[] = ['#contact', $label];
    } elseif ($id !== '' && section_visible($c, $id)) {
        $navItems[] = ['#' . $id, $label];
    }
}

$logoHeight = max(40, min(140, (int)($c['nav']['logo_height'] ?? 88)));
$navLinkSize = max(10, min(22, (int)($c['nav']['link_size'] ?? 13)));
$headingSize = max(20, min(60, (int)($c['nav']['heading_size'] ?? 32)));
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription) ?>">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&family=Gentium+Basic:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="styles.css">
    <style>:root { --logo-h: <?= $logoHeight ?>px; --nav-size: <?= $navLinkSize ?>px; --title-size: <?= $headingSize ?>px; }</style>
</head>
<body class="<?= $isHome ? 'home-page' : 'gallery-page' ?>">
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <a href="<?= $isHome ? '#home' : 'index.php' ?>" class="logo-link">
                    <img src="logo_artispark.jpg" alt="ArtiSpark Logo" class="logo">
                </a>
                <ul class="nav-menu" id="navMenu">
<?php foreach ($navItems as [$href, $label]):
    $url = $href[0] === '#' ? $a . $href : $href; ?>
                    <li><a href="<?= e($url) ?>" class="nav-link<?= (!$isHome && $href === 'gallery.php') ? ' active' : '' ?>"><?= e($label) ?></a></li>
<?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
