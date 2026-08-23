<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_images.php';

$c = load_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = post_str('action', 40);

    if ($action === 'save_text') {
        $title = post_str('title', 200);
        $subtitle = post_str('subtitle', 2000);
        $ctaPrimary = post_str('cta_primary', 60);
        $ctaSecondary = post_str('cta_secondary', 60);
        if ($title === '') {
            admin_redirect('edit-hero.php', ['error' => 'The headline cannot be empty.']);
        }
        $c['hero']['title'] = $title;
        $c['hero']['subtitle'] = $subtitle;
        $c['hero']['cta_primary']['label'] = $ctaPrimary !== '' ? $ctaPrimary : 'Start Today';
        $c['hero']['cta_secondary']['label'] = $ctaSecondary !== '' ? $ctaSecondary : 'Registration';
        $c["hero"]["text_theme"] = post_str("text_theme", 10) === "dark" ? "dark" : "light";
        if (!save_content($c)) {
            admin_redirect('edit-hero.php', ['error' => 'Could not save. Please try again.']);
        }
        admin_redirect('edit-hero.php', ['saved' => 1]);
    }

    if ($action === 'save_background') {
        if (empty($_FILES['image']['name'])) {
            admin_redirect('edit-hero.php', ['error' => 'Choose a photo to upload.']);
        }
        $result = process_uploaded_image($_FILES['image']);
        if (!$result['ok']) {
            admin_redirect('edit-hero.php', ['error' => $result['error']]);
        }
        $old = $c['hero']['background']['src'] ?? '';
        $c['hero']['background'] = ['src' => $result['path']];
        delete_uploaded_image($old);
        if (!save_content($c)) {
            admin_redirect('edit-hero.php', ['error' => 'Could not save. Please try again.']);
        }
        admin_redirect('edit-hero.php', ['saved' => 1]);
    }

    if ($action === 'save_banner') {
        $slot = (int)post_str('slot', 4);
        if ($slot < 0 || $slot > 5 || !isset($c['hero']['banner_images'][$slot])) {
            admin_redirect('edit-hero.php', ['error' => 'Unknown banner slot.']);
        }
        $alt = post_str('alt', 200);
        $c['hero']['banner_images'][$slot]['alt'] = $alt;

        if (!empty($_FILES['image']['name'])) {
            $result = process_uploaded_image($_FILES['image']);
            if (!$result['ok']) {
                admin_redirect('edit-hero.php', ['error' => $result['error']]);
            }
            $old = $c['hero']['banner_images'][$slot]['src'];
            $c['hero']['banner_images'][$slot]['src'] = $result['path'];
            delete_uploaded_image($old);
        }

        if (!save_content($c)) {
            admin_redirect('edit-hero.php', ['error' => 'Could not save. Please try again.']);
        }
        admin_redirect('edit-hero.php', ['saved' => 1]);
    }
}

admin_page_start('Hero (top of home page)', 'edit-hero.php');
?>
        <form class="editor" method="post" action="edit-hero.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_text">
            <label for="title">Main headline</label>
            <input type="text" id="title" name="title" value="<?= e($c['hero']['title'] ?? '') ?>" required>
            <label for="subtitle">Intro paragraph</label>
            <textarea id="subtitle" name="subtitle"><?= e($c['hero']['subtitle'] ?? '') ?></textarea>
            <label for="cta_primary">First button text</label>
            <input type="text" id="cta_primary" name="cta_primary" value="<?= e($c['hero']['cta_primary']['label'] ?? '') ?>">
            <label for="cta_secondary">Second button text</label>
            <input type="text" id="cta_secondary" name="cta_secondary" value="<?= e($c['hero']['cta_secondary']['label'] ?? '') ?>">
            <label for="text_theme">Text colour</label>
            <p class="hint">Choose dark text when the background photo is light (e.g. cream or white).</p>
            <select id="text_theme" name="text_theme">
                <option value="light" <?= ($c["hero"]["text_theme"] ?? "light") !== "dark" ? "selected" : "" ?>>White text (for dark photos)</option>
                <option value="dark" <?= ($c["hero"]["text_theme"] ?? "") === "dark" ? "selected" : "" ?>>Dark text (for light photos)</option>
            </select>
            <button type="submit" class="btn">Save text</button>
        </form>

        <h2 style="margin-top:36px;">Hero background photo</h2>
        <p class="hint">The large photo behind the headline at the top of the home page. Wide, landscape photos work best.</p>
        <form class="editor item-box" method="post" action="edit-hero.php" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_background">
            <div class="thumb-row">
                <img src="../<?= e($c['hero']['background']['src'] ?? ($c['hero']['banner_images'][0]['src'] ?? '')) ?>" alt="" class="thumb">
                <div><input type="file" name="image" accept="image/jpeg,image/png,image/webp"></div>
            </div>
            <button type="submit" class="btn btn-small" style="margin-top:12px;">Upload background</button>
        </form>

        <h2 style="margin-top:36px;">Gallery preview photos</h2>
        <p class="hint">These six photos are shown in the "Student Artworks" preview on the home page until you upload real gallery photos.</p>
        <div class="card-grid">
<?php foreach ($c['hero']['banner_images'] ?? [] as $i => $img): ?>
            <div class="item-box">
                <h2>Photo <?= $i + 1 ?></h2>
                <img src="../<?= e($img['src']) ?>" alt="" class="thumb">
                <form method="post" action="edit-hero.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save_banner">
                    <input type="hidden" name="slot" value="<?= $i ?>">
                    <label>Replace photo</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                    <label>Photo description (for accessibility)</label>
                    <input type="text" name="alt" value="<?= e($img['alt'] ?? '') ?>">
                    <button type="submit" class="btn btn-small">Save</button>
                </form>
            </div>
<?php endforeach; ?>
        </div>
<?php admin_page_end(); ?>
