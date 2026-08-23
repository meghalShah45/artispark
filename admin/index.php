<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$c = load_content();

$sections = [
    ['href' => 'edit-hero.php', 'title' => 'Hero', 'desc' => 'Main headline, intro text, buttons and the 6 banner photos.'],
    ['href' => 'edit-about.php', 'title' => 'About', 'desc' => 'The "About ArtiSpark" story and photo.'],
    ['href' => 'edit-services.php', 'title' => 'Services', 'desc' => 'Add, edit, reorder or remove service cards.', 'count' => count($c['services']['items'] ?? [])],
    ['href' => 'edit-testimonials.php', 'title' => 'Testimonials', 'desc' => 'Customer quotes shown on the home page.', 'count' => count($c['testimonials']['items'] ?? [])],
    ['href' => 'edit-team.php', 'title' => 'Team', 'desc' => 'The people behind ArtiSpark — names, roles and photos.', 'count' => count($c['team']['members'] ?? [])],
    ['href' => 'edit-gallery.php', 'title' => 'Gallery', 'desc' => 'Upload and arrange gallery photos.', 'count' => count($c['gallery']['items'] ?? [])],
    ['href' => 'edit-contact.php', 'title' => 'Contact Info', 'desc' => 'Location, phone numbers and email address.'],
    ['href' => 'edit-seo.php', 'title' => 'SEO & Footer', 'desc' => 'Browser title, search description, footer text.'],
];

admin_page_start('Dashboard', 'index.php');
?>
        <p class="lead">Choose a section to edit. Changes go live on the website as soon as you press Save.</p>
        <div class="card-grid">
<?php foreach ($sections as $s): ?>
            <a class="card" href="<?= e($s['href']) ?>">
                <h2><?= e($s['title']) ?><?php if (isset($s['count'])): ?> <span class="badge"><?= (int)$s['count'] ?></span><?php endif; ?></h2>
                <p><?= e($s['desc']) ?></p>
            </a>
<?php endforeach; ?>
        </div>
<?php admin_page_end(); ?>
