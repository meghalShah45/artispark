<?php
declare(strict_types=1);

require __DIR__ . '/includes/content.php';

$c = load_content();
$page = 'home';
require __DIR__ . '/includes/header.php';

$heroBg = $c['hero']['background']['src'] ?? ($c['hero']['banner_images'][0]['src'] ?? '');
$galleryItems = $c['gallery']['items'] ?? [];
// Home preview: first 6 gallery photos; fall back to the banner photos until real ones are uploaded.
$galleryPreview = $galleryItems
    ? array_slice($galleryItems, 0, 6)
    : array_map(fn($b) => ['src' => $b['src'], 'caption' => $b['alt'] ?? '', 'size' => 'normal'], $c['hero']['banner_images'] ?? []);
?>
    <!-- Hero Section -->
    <section id="home" class="hero<?= ($c["hero"]["text_theme"] ?? "light") === "dark" ? " hero--dark" : "" ?>" style="background-image:url('<?= e($heroBg) ?>')">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><?= e($c['hero']['title'] ?? '') ?></h1>
<?php if (trim((string)($c['hero']['subtitle'] ?? '')) !== ''): ?>
                <p class="hero-subtitle"><?= e($c['hero']['subtitle']) ?></p>
<?php endif; ?>
                <div class="hero-actions">
                    <a href="<?= e($c['hero']['cta_primary']['href'] ?? '#services') ?>" class="btn btn-primary"><?= e($c['hero']['cta_primary']['label'] ?? '') ?></a>
                    <a href="<?= e($c['hero']['cta_secondary']['href'] ?? '#contact') ?>" class="btn btn-secondary"><?= e($c['hero']['cta_secondary']['label'] ?? '') ?></a>
                </div>
            </div>
        </div>
    </section>

<?php if (section_visible($c, 'services')): ?>
    <!-- Services Section -->
    <section id="services" class="services section-padding">
        <div class="container">
            <div class="section-header reveal">
                <p class="section-intro"><?= e($c['services']['intro'] ?? '') ?></p>
                <h2 class="section-title"><?= e($c['services']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
                <p class="section-quote">"<?= e($c['services']['subtitle'] ?? '') ?>"</p>
            </div>
            <div class="services-grid">
<?php foreach ($c['services']['items'] ?? [] as $service): ?>
                <div class="service-tile reveal" tabindex="0">
                    <img src="<?= e($service['image']['src'] ?? '') ?>" alt="<?= e($service['image']['alt'] ?? $service['title'] ?? '') ?>" loading="lazy">
                    <span class="btn btn-secondary service-label"><?= e($service['title'] ?? '') ?></span>
                    <div class="service-overlay">
                        <h3><?= e($service['title'] ?? '') ?></h3>
                        <p><?= nl2br(e($service['description'] ?? ''), false) ?></p>
                    </div>
                </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (section_visible($c, 'about')): ?>
    <!-- About Section -->
    <section id="about" class="about section-padding">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-title"><?= e($c['about']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
            </div>
            <div class="about-content reveal">
                <div class="about-image">
                    <img src="<?= e($c['about']['image']['src'] ?? '') ?>" alt="<?= e($c['about']['image']['alt'] ?? '') ?>" loading="lazy">
                </div>
                <div class="about-text">
<?= nl2p($c['about']['body'] ?? '') ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (section_visible($c, 'team') && !empty($c['team']['members'])): ?>
    <!-- Team Section -->
    <section id="team" class="team section-padding">
        <div class="container">
            <div class="section-header reveal">
                <p class="section-intro"><?= e($c['team']['intro'] ?? '') ?></p>
                <h2 class="section-title"><?= e($c['team']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
            </div>
            <div class="team-grid">
<?php foreach ($c['team']['members'] as $member): ?>
                <div class="team-member reveal">
                    <img src="<?= e($member['image']['src'] ?? '') ?>" alt="<?= e($member['image']['alt'] ?? $member['name'] ?? '') ?>" loading="lazy">
                    <h3 class="team-name"><?= e($member['name'] ?? '') ?></h3>
                    <p class="team-credentials"><?= nl2br(e($member['credentials'] ?? ''), false) ?></p>
                </div>
<?php endforeach; ?>
            </div>
<?php if (trim((string)($c['team']['button_label'] ?? '')) !== '' && section_visible($c, 'about')): ?>
            <div class="section-action reveal"><a href="#about" class="btn btn-secondary"><?= e($c['team']['button_label']) ?></a></div>
<?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php if (section_visible($c, 'testimonials') && !empty($c['testimonials']['items'])): ?>
    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section-padding">
        <div class="container">
            <div class="section-header reveal">
                <p class="section-intro"><?= e($c['testimonials']['subtitle'] ?? '') ?></p>
                <h2 class="section-title"><?= e($c['testimonials']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
            </div>
            <div class="testimonials-grid">
<?php foreach ($c['testimonials']['items'] as $testimonial): ?>
                <article class="testimonial-card reveal">
                    <div class="testimonial-quote-mark">"</div>
                    <blockquote class="testimonial-text"><?= nl2br(e($testimonial['quote'] ?? ''), false) ?></blockquote>
                    <cite class="testimonial-name"><?= e($testimonial['author'] ?? '') ?></cite>
                </article>
<?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($galleryPreview): ?>
    <!-- Gallery Preview -->
    <section id="gallery" class="gallery section-padding">
        <div class="container">
            <div class="section-header reveal">
                <p class="section-intro"><?= e($c['gallery']['intro'] ?? '') ?></p>
                <h2 class="section-title"><?= e($c['gallery']['heading'] ?? 'Gallery') ?></h2>
                <div class="title-underline"></div>
            </div>
            <div class="gallery-grid reveal">
<?php foreach ($galleryPreview as $item): ?>
                <div class="gallery-item<?= ($item['size'] ?? '') === 'large' ? ' gallery-item-large' : '' ?>">
                    <img src="<?= e($item['src'] ?? '') ?>" alt="<?= e($item['caption'] ?? 'Gallery photo') ?>" loading="lazy">
<?php if (trim((string)($item['caption'] ?? '')) !== ''): ?>
                    <div class="gallery-overlay"><p class="gallery-caption"><?= e($item['caption']) ?></p></div>
<?php endif; ?>
                </div>
<?php endforeach; ?>
            </div>
<?php if ($galleryEnabled): ?>
            <div class="section-action reveal"><a href="gallery.php" class="btn btn-secondary"><?= e($c['gallery']['button_label'] ?? 'Gallery') ?></a></div>
<?php endif; ?>
        </div>
    </section>
<?php endif; ?>

    <!-- Contact Section -->
    <section id="contact" class="contact section-padding">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-title"><?= e($c['contact']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
                <p class="section-subtitle"><?= e($c['contact']['subtitle'] ?? '') ?></p>
            </div>
<?php $markers = array_values(array_filter($c["contact"]["map"]["markers"] ?? [], fn($m) => isset($m["lat"], $m["lng"])));
if (!empty($c["contact"]["map"]["enabled"]) && $markers): ?>
            <div id="contactMap" class="contact-map reveal" data-markers="<?= e(json_encode($markers)) ?>" aria-label="Map of our studio locations"></div>
<?php endif; ?>
            <div class="contact-wrapper reveal">
                <div class="contact-info">
                    <div class="contact-item">
                        <h3 class="contact-label">Location</h3>
                        <p class="contact-text"><?= e($c['contact']['location'] ?? '') ?></p>
                    </div>
                    <div class="contact-item">
                        <h3 class="contact-label">Phone</h3>
<?php foreach ($c['contact']['phones'] ?? [] as $phone): if (trim((string)$phone) === '') continue; ?>
                        <p class="contact-text"><a href="tel:<?= e(preg_replace('/\s+/', '', $phone)) ?>"><?= e($phone) ?></a></p>
<?php endforeach; ?>
                    </div>
                    <div class="contact-item">
                        <h3 class="contact-label">Email</h3>
                        <p class="contact-text"><a href="mailto:<?= e($c['contact']['email'] ?? '') ?>"><?= e($c['contact']['email'] ?? '') ?></a></p>
                    </div>
                </div>
                <form class="contact-form" id="contactForm" action="contact.php" method="post">
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-input form-textarea" rows="5" required></textarea>
                    </div>
                    <input type="text" name="website" class="form-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <button type="submit" class="btn btn-primary" id="contactSubmit">Send Message</button>
                    <p class="form-status" id="contactStatus" role="status" aria-live="polite"></p>
                </form>
            </div>
        </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
