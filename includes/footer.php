<?php
/**
 * Shared footer + scripts. Expects: $c, $isHome, $a, $navItems (set by header.php).
 */
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <img src="logo_artispark.jpg" alt="ArtiSpark Logo" class="footer-logo-img">
            <p class="footer-tagline"><?= e($c['footer']['tagline'] ?? '') ?></p>
            <div class="footer-links">
                <a href="<?= $isHome ? '#home' : 'index.php' ?>">Home</a>
<?php foreach ($navItems as [$href, $label]):
    $url = $href[0] === '#' ? $a . $href : $href; ?>
                <a href="<?= e($url) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
            </div>
            <div class="footer-bottom">
                <p><?= e($c['footer']['copyright'] ?? '') ?></p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="script.js"></script>
</body>
</html>
