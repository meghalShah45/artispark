<?php
declare(strict_types=1);

require __DIR__ . '/includes/content.php';

$c = load_content();
$page = 'gallery';
$pageTitle = 'Gallery | ' . ($c['meta']['title'] ?? 'ArtiSpark');
require __DIR__ . '/includes/header.php';
?>
    <!-- Gallery Section -->
    <section id="gallery" class="gallery section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= e($c['gallery']['heading'] ?? 'Gallery') ?></h2>
                <div class="title-underline"></div>
                <p class="section-subtitle"><?= e($c['gallery']['subtitle'] ?? '') ?></p>
            </div>
<?php if (empty($c['gallery']['items'])): ?>
            <p class="section-subtitle" style="text-align:center;">New photos are coming soon — check back shortly!</p>
<?php else: ?>
            <div class="masonry-grid" id="galleryGrid">
                <div class="grid-sizer"></div>
<?php foreach ($c['gallery']['items'] as $item): ?>
                <div class="masonry-item gallery-item<?= ($item['size'] ?? '') === 'large' ? ' gallery-item-large' : '' ?>">
                    <div class="gallery-card">
                        <img src="<?= e($item['src'] ?? '') ?>" alt="<?= e($item['caption'] ?? 'Gallery photo') ?>" class="gallery-image" loading="lazy">
<?php if (trim((string)($item['caption'] ?? '')) !== ''): ?>
                        <div class="gallery-overlay">
                            <p class="gallery-caption"><?= e($item['caption']) ?></p>
                        </div>
<?php endif; ?>
                    </div>
                </div>
<?php endforeach; ?>
            </div>
<?php endif; ?>
        </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
