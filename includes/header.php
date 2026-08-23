<?php
/**
 * Shared document head + navigation.
 * Expects: $c (content array), $page ('home' | 'gallery').
 */
$page = $page ?? 'home';
$isHome = $page === 'home';
// On the home page anchors are plain (#about) so script.js smooth-scroll intercepts
// them; on other pages they point back to index.php#about.
$a = $isHome ? '' : 'index.php';
$pageTitle = $pageTitle ?? ($c['meta']['title'] ?? 'ArtiSpark');
$metaDescription = $metaDescription ?? ($c['meta']['description'] ?? '');
$galleryEnabled = !empty($c['gallery']['enabled']);
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription) ?>">
    <title><?= e($pageTitle) ?></title>
    <!-- RecklessNeue Fonts - Self-hosted from assets/fonts/ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Encode+Sans+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="<?= $isHome ? '#home' : 'index.php' ?>" class="logo-link">
                    <img src="logo_artispark.jpg" alt="ArtiSpark Logo" class="logo">
                </a>
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="nav-menu" id="navMenu">
<?php if (section_visible($c, "about")): ?>
                    <li><a href="<?= $a ?>#about" class="nav-link">About</a></li>
<?php endif; ?>
<?php if (section_visible($c, "services")): ?>
                    <li><a href="<?= $a ?>#services" class="nav-link">Service</a></li>
<?php endif; ?>
<?php if ($galleryEnabled): ?>
                    <li><a href="gallery.php" class="nav-link">Gallery</a></li>
<?php endif; ?>
<?php if (section_visible($c, "testimonials")): ?>
                    <li><a href="<?= $a ?>#testimonials" class="nav-link">Testimonials</a></li>
<?php endif; ?>
                    <li><a href="<?= $a ?>#contact" class="nav-link">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
