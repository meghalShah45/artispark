<?php
declare(strict_types=1);

require __DIR__ . '/includes/content.php';

$c = load_content();
$page = 'home';
require __DIR__ . '/includes/header.php';

$capsuleColors = ['purple', 'yellow', 'red', 'blue', 'orange', 'green'];
$bannerColumns = array_chunk($c['hero']['banner_images'] ?? [], 2);
?>
    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-background"></div>
        <!-- Decorative dots -->
        <div class="decorative-dot dot-1"></div>
        <div class="decorative-dot dot-2"></div>
        <div class="decorative-dot dot-3"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-left">
                    <h1 class="hero-title"><?= e($c['hero']['title'] ?? '') ?></h1>
                    <p class="hero-subtitle"><?= e($c['hero']['subtitle'] ?? '') ?></p>
                    <div class="hero-actions">
                        <a href="<?= e($c['hero']['cta_primary']['href'] ?? '#services') ?>" class="btn btn-primary"><?= e($c['hero']['cta_primary']['label'] ?? '') ?></a>
                        <a href="<?= e($c['hero']['cta_secondary']['href'] ?? '#contact') ?>" class="btn btn-primary"><?= e($c['hero']['cta_secondary']['label'] ?? '') ?></a>
                    </div>
                </div>
                <div class="hero-right">
                    <div class="capsule-gallery">
<?php foreach ($bannerColumns as $colIndex => $column): ?>
                        <div class="capsule-col col-<?= $colIndex + 1 ?>">
<?php foreach ($column as $imgIndex => $img):
        $color = $capsuleColors[($colIndex * 2 + $imgIndex) % count($capsuleColors)]; ?>
                            <div class="capsule-item capsule-<?= $color ?>">
                                <img src="<?= e($img['src']) ?>" alt="<?= e($img['alt'] ?? '') ?>" loading="lazy">
                            </div>
<?php endforeach; ?>
                        </div>
<?php endforeach; ?>
                        <!-- Extra decorative dots like in ref -->
                        <div class="ref-dot dot-yellow"></div>
                        <div class="ref-dot dot-red"></div>
                        <div class="ref-dot dot-green"></div>
                        <div class="ref-dot dot-orange"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php if (section_visible($c, "about")): ?>
    <!-- About Section -->
    <section id="about" class="about section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= e($c['about']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
            </div>
            <div class="about-content">
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

<?php if (section_visible($c, "services")): ?>
    <!-- Services Section -->
    <section id="services" class="services section-padding">
        <div class="grain-overlay"></div>
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= e($c['services']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
                <p class="section-intro-text"><?= e($c['services']['intro'] ?? '') ?></p>
                <p class="section-subtitle-minimal"><?= e($c['services']['subtitle'] ?? '') ?></p>
            </div>

            <div class="pebble-services-container">
                <div class="pebble-services-wrapper">
<?php foreach ($c['services']['items'] ?? [] as $i => $service): ?>
                    <div class="pebble-item">
                        <div class="pebble-shape shape-<?= $i % 2 === 0 ? '5' : '6' ?>">
                            <img src="<?= e($service['image']['src'] ?? '') ?>" alt="<?= e($service['image']['alt'] ?? $service['title'] ?? '') ?>">
                        </div>
                        <div class="pebble-content">
                            <h3 class="pebble-title"><?= e($service['title'] ?? '') ?></h3>
                            <p class="pebble-desc"><?= nl2br(e($service['description'] ?? ''), false) ?></p>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (section_visible($c, "testimonials")): ?>
    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= e($c['testimonials']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
                <p class="section-subtitle"><?= e($c['testimonials']['subtitle'] ?? '') ?></p>
            </div>
            <div class="testimonials-grid">
<?php foreach ($c['testimonials']['items'] ?? [] as $i => $testimonial): ?>
                <article class="testimonial-card testimonial-card--<?= $i % 2 === 0 ? 'a' : 'b' ?>">
                    <div class="testimonial-quote-mark">"</div>
                    <blockquote class="testimonial-text"><?= nl2br(e($testimonial['quote'] ?? ''), false) ?></blockquote>
                    <div class="testimonial-author">
                        <div class="testimonial-author-info">
                            <cite class="testimonial-name"><?= e($testimonial['author'] ?? '') ?></cite>
                        </div>
                    </div>
                </article>
<?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

    <!-- Contact Section -->
    <section id="contact" class="contact section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?= e($c['contact']['heading'] ?? '') ?></h2>
                <div class="title-underline"></div>
                <p class="section-subtitle"><?= e($c['contact']['subtitle'] ?? '') ?></p>
            </div>
            <div class="contact-wrapper">
                <div class="contact-info">
                    <div class="contact-item">
                        <h3 class="contact-label">Location</h3>
                        <p class="contact-text"><?= e($c['contact']['location'] ?? '') ?><br></p>
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
