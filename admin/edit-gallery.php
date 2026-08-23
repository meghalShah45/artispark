<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_images.php';

$c = load_content();

function gallery_index_by_id(array $items, string $id): ?int
{
    foreach ($items as $i => $item) {
        if (($item['id'] ?? '') === $id) {
            return $i;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = post_str('action', 40);
    $items = $c['gallery']['items'] ?? [];

    if ($action === 'save_header') {
        $c['gallery']['heading'] = post_str('heading', 200);
        $c['gallery']['subtitle'] = post_str('subtitle', 500);
        $c['gallery']['intro'] = post_str('intro', 200);
        $c['gallery']['button_label'] = post_str('button_label', 60);
        $c['gallery']['enabled'] = !empty($_POST['enabled']);
    } elseif ($action === 'upload') {
        if (empty($_FILES['images']['name'][0])) {
            admin_redirect('edit-gallery.php', ['error' => 'Choose at least one photo to upload.']);
        }
        $uploaded = 0;
        $firstError = '';
        foreach (array_keys($_FILES['images']['name']) as $k) {
            $file = [
                'name' => $_FILES['images']['name'][$k],
                'tmp_name' => $_FILES['images']['tmp_name'][$k],
                'error' => $_FILES['images']['error'][$k],
                'size' => $_FILES['images']['size'][$k],
            ];
            $result = process_uploaded_image($file);
            if ($result['ok']) {
                $items[] = ['id' => content_new_id('gal'), 'src' => $result['path'], 'caption' => '', 'size' => 'normal'];
                $uploaded++;
            } elseif ($firstError === '') {
                $firstError = $result['error'];
            }
        }
        $c['gallery']['items'] = $items;
        if ($uploaded === 0) {
            admin_redirect('edit-gallery.php', ['error' => $firstError !== '' ? $firstError : 'Upload failed.']);
        }
        if ($firstError !== '') {
            save_content($c);
            admin_redirect('edit-gallery.php', ['error' => 'Some photos were added, but one failed: ' . $firstError]);
        }
    } else {
        $id = post_str('id', 60);
        $index = gallery_index_by_id($items, $id);
        if ($index === null) {
            admin_redirect('edit-gallery.php', ['error' => 'That photo no longer exists.']);
        }
        if ($action === 'save') {
            $items[$index]['caption'] = post_str('caption', 200);
            $items[$index]['size'] = post_str('size', 10) === 'large' ? 'large' : 'normal';
        } elseif ($action === 'delete') {
            delete_uploaded_image($items[$index]['src'] ?? '');
            array_splice($items, $index, 1);
        } elseif ($action === 'move_up' && $index > 0) {
            [$items[$index - 1], $items[$index]] = [$items[$index], $items[$index - 1]];
        } elseif ($action === 'move_down' && $index < count($items) - 1) {
            [$items[$index + 1], $items[$index]] = [$items[$index], $items[$index + 1]];
        }
        $c['gallery']['items'] = array_values($items);
    }

    if (!save_content($c)) {
        admin_redirect('edit-gallery.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-gallery.php', ['saved' => 1]);
}

$items = $c['gallery']['items'] ?? [];
$enabled = !empty($c['gallery']['enabled']);
admin_page_start('Gallery', 'edit-gallery.php');
?>
        <form class="editor item-box" method="post" action="edit-gallery.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_header">
            <h2>Gallery page settings</h2>
            <label>
                <input type="checkbox" name="enabled" value="1" <?= $enabled ? 'checked' : '' ?>>
                Show the Gallery page in the website menu
            </label>
            <p class="hint">Tip: upload a few photos first, then turn the page on.</p>
            <label for="intro">Small line above the heading</label>
            <input type="text" id="intro" name="intro" value="<?= e($c['gallery']['intro'] ?? '') ?>">
            <label for="heading">Heading</label>
            <input type="text" id="heading" name="heading" value="<?= e($c['gallery']['heading'] ?? '') ?>">
            <label for="subtitle">Subtitle</label>
            <input type="text" id="subtitle" name="subtitle" value="<?= e($c['gallery']['subtitle'] ?? '') ?>">
            <label for="button_label">"View gallery" button text</label>
            <input type="text" id="button_label" name="button_label" value="<?= e($c['gallery']['button_label'] ?? '') ?>">
            <button type="submit" class="btn btn-small">Save settings</button>
        </form>

        <div class="item-box">
            <h2>Upload photos</h2>
            <form method="post" action="edit-gallery.php" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload">
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                <p class="hint">You can select several photos at once. JPG, PNG or WebP, up to 8 MB each.</p>
                <button type="submit" class="btn btn-small">Upload</button>
            </form>
        </div>

<?php if ($items): ?>
        <h2 style="margin-top:30px;">Photos (<?= count($items) ?>)</h2>
        <div class="gallery-admin-grid">
<?php foreach ($items as $i => $item): ?>
            <div class="item-box">
                <img src="../<?= e($item['src'] ?? '') ?>" alt="" class="thumb">
                <form method="post" action="edit-gallery.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <label>Caption</label>
                    <input type="text" name="caption" value="<?= e($item['caption'] ?? '') ?>">
                    <label>Size</label>
                    <select name="size">
                        <option value="normal" <?= ($item['size'] ?? '') !== 'large' ? 'selected' : '' ?>>Normal</option>
                        <option value="large" <?= ($item['size'] ?? '') === 'large' ? 'selected' : '' ?>>Large</option>
                    </select>
                    <button type="submit" class="btn btn-small" style="margin-top:10px;">Save</button>
                </form>
                <div class="item-toolbar">
                    <form method="post" action="edit-gallery.php" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="move_up">
                        <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                        <button type="submit" class="btn btn-small btn-ghost" <?= $i === 0 ? 'disabled' : '' ?>>↑</button>
                    </form>
                    <form method="post" action="edit-gallery.php" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="move_down">
                        <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                        <button type="submit" class="btn btn-small btn-ghost" <?= $i === count($items) - 1 ? 'disabled' : '' ?>>↓</button>
                    </form>
                    <form method="post" action="edit-gallery.php" class="inline-form" onsubmit="return confirm('Delete this photo? This cannot be undone.');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                    </form>
                </div>
            </div>
<?php endforeach; ?>
        </div>
<?php endif; ?>
<?php admin_page_end(); ?>
