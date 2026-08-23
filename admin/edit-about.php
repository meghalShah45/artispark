<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_images.php';

$c = load_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $heading = post_str('heading', 200);
    $body = post_str('body', 20000);
    $alt = post_str('alt', 200);
    if ($heading === '') {
        admin_redirect('edit-about.php', ['error' => 'The heading cannot be empty.']);
    }

    $c['about']['heading'] = $heading;
    $c["about"]["visible"] = !empty($_POST["visible"]);
    $c['about']['body'] = $body;
    $c['about']['image']['alt'] = $alt;

    if (!empty($_FILES['image']['name'])) {
        $result = process_uploaded_image($_FILES['image']);
        if (!$result['ok']) {
            admin_redirect('edit-about.php', ['error' => $result['error']]);
        }
        $old = $c['about']['image']['src'] ?? '';
        $c['about']['image']['src'] = $result['path'];
        delete_uploaded_image($old);
    }

    if (!save_content($c)) {
        admin_redirect('edit-about.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-about.php', ['saved' => 1]);
}

admin_page_start('About section', 'edit-about.php');
?>
        <form class="editor" method="post" action="edit-about.php" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <label><input type="checkbox" name="visible" value="1" <?= section_visible($c, "about") ? "checked" : "" ?>> Show this section on the website</label>
            <label for="heading">Heading</label>
            <input type="text" id="heading" name="heading" value="<?= e($c['about']['heading'] ?? '') ?>" required>

            <label for="body">Story text</label>
            <p class="hint">Leave a blank line between paragraphs.</p>
            <textarea id="body" name="body" class="tall"><?= e($c['about']['body'] ?? '') ?></textarea>

            <label>Photo</label>
            <div class="thumb-row">
                <img src="../<?= e($c['about']['image']['src'] ?? '') ?>" alt="" class="thumb">
                <div>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                    <p class="hint">Leave empty to keep the current photo.</p>
                </div>
            </div>
            <label for="alt">Photo description (for accessibility)</label>
            <input type="text" id="alt" name="alt" value="<?= e($c['about']['image']['alt'] ?? '') ?>">

            <button type="submit" class="btn">Save</button>
        </form>
<?php admin_page_end(); ?>
