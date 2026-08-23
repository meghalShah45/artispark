<?php
/**
 * Shared footer + scripts. Expects: $c, $page (set by header.php include order).
 */
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="logo_artispark.jpg" alt="ArtiSpark Logo" class="footer-logo-img">
                    <p class="footer-tagline"><?= e($c['footer']['tagline'] ?? '') ?></p>
                </div>
                <div class="footer-links">
                <a href="<?= $isHome ? '#home' : 'index.php' ?>">Home</a>
<?php if (section_visible($c, "about")): ?>
                <a href="<?= $a ?>#about">About</a>
<?php endif; ?>
<?php if (section_visible($c, "services")): ?>
                <a href="<?= $a ?>#services">Services</a>
<?php endif; ?>
<?php if ($galleryEnabled): ?>
                <a href="gallery.php">Gallery</a>
<?php endif; ?>
<?php if (section_visible($c, "testimonials")): ?>
                <a href="<?= $a ?>#testimonials">Testimonials</a>
<?php endif; ?>
                <a href="<?= $a ?>#contact">Contact</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p><?= e($c['footer']['copyright'] ?? '') ?></p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/imagesloaded@5.0.0/imagesloaded.pkgd.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
