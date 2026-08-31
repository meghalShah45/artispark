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

$navItems = [];
if (section_visible($c, 'about')) $navItems[] = ['#about', 'About'];
if (section_visible($c, 'services')) $navItems[] = ['#services', 'Services'];
if (section_visible($c, 'team')) $navItems[] = ['#team', 'Team'];
if (section_visible($c, 'testimonials')) $navItems[] = ['#testimonials', 'Testimonials'];
if ($galleryEnabled) $navItems[] = ['gallery.php', 'Gallery'];
$navItems[] = ['#contact', 'Contact'];
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
