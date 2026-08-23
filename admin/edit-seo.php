<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$c = load_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title = post_str('title', 120);
    if ($title === '') {
        admin_redirect('edit-seo.php', ['error' => 'The browser title cannot be empty.']);
    }
    $c['meta']['title'] = $title;
    $c['meta']['description'] = post_str('description', 300);
    $c['footer']['tagline'] = post_str('tagline', 120);
    $c['footer']['copyright'] = post_str('copyright', 200);

    if (!save_content($c)) {
        admin_redirect('edit-seo.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-seo.php', ['saved' => 1]);
}

admin_page_start('SEO & Footer', 'edit-seo.php');
?>
        <form class="editor" method="post" action="edit-seo.php">
            <?= csrf_field() ?>
            <label for="title">Browser title</label>
            <p class="hint">Shown in the browser tab and as the headline in Google results.</p>
            <input type="text" id="title" name="title" value="<?= e($c['meta']['title'] ?? '') ?>" required>

            <label for="description">Search description</label>
            <p class="hint">The short text under your site name in Google results. Aim for under 160 characters — <span id="descCount"></span></p>
            <textarea id="description" name="description" maxlength="300"><?= e($c['meta']['description'] ?? '') ?></textarea>

            <label for="tagline">Footer tagline</label>
            <input type="text" id="tagline" name="tagline" value="<?= e($c['footer']['tagline'] ?? '') ?>">

            <label for="copyright">Copyright line</label>
            <input type="text" id="copyright" name="copyright" value="<?= e($c['footer']['copyright'] ?? '') ?>">

            <button type="submit" class="btn">Save</button>
        </form>
        <script>
        (function () {
            var box = document.getElementById('description');
            var out = document.getElementById('descCount');
            function update() { out.textContent = box.value.length + ' characters'; }
            box.addEventListener('input', update);
            update();
        })();
        </script>
<?php admin_page_end(); ?>
