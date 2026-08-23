<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_images.php';

$c = load_content();

function service_index_by_id(array $items, string $id): ?int
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
    $items = $c['services']['items'] ?? [];

    if ($action === 'save_header') {
        $c['services']['heading'] = post_str('heading', 200);
        $c["services"]["visible"] = !empty($_POST["visible"]);
        $c['services']['intro'] = post_str('intro', 200);
        $c['services']['subtitle'] = post_str('subtitle', 2000);
    } elseif ($action === 'add') {
        $title = post_str('title', 200);
        $description = post_str('description', 10000);
        if ($title === '') {
            admin_redirect('edit-services.php', ['error' => 'A new service needs a title.']);
        }
        $item = [
            'id' => content_new_id('svc'),
            'title' => $title,
            'description' => $description,
            'image' => ['src' => '', 'alt' => $title],
        ];
        if (!empty($_FILES['image']['name'])) {
            $result = process_uploaded_image($_FILES['image']);
            if (!$result['ok']) {
                admin_redirect('edit-services.php', ['error' => $result['error']]);
            }
            $item['image']['src'] = $result['path'];
        }
        $items[] = $item;
        $c['services']['items'] = $items;
    } else {
        $id = post_str('id', 60);
        $index = service_index_by_id($items, $id);
        if ($index === null) {
            admin_redirect('edit-services.php', ['error' => 'That service no longer exists.']);
        }

        if ($action === 'save') {
            $title = post_str('title', 200);
            if ($title === '') {
                admin_redirect('edit-services.php', ['error' => 'The title cannot be empty.']);
            }
            $items[$index]['title'] = $title;
            $items[$index]['description'] = post_str('description', 10000);
            $items[$index]['image']['alt'] = post_str('alt', 200);
            if (!empty($_FILES['image']['name'])) {
                $result = process_uploaded_image($_FILES['image']);
                if (!$result['ok']) {
                    admin_redirect('edit-services.php', ['error' => $result['error']]);
                }
                $old = $items[$index]['image']['src'] ?? '';
                $items[$index]['image']['src'] = $result['path'];
                delete_uploaded_image($old);
            }
        } elseif ($action === 'delete') {
            delete_uploaded_image($items[$index]['image']['src'] ?? '');
            array_splice($items, $index, 1);
        } elseif ($action === 'move_up' && $index > 0) {
            [$items[$index - 1], $items[$index]] = [$items[$index], $items[$index - 1]];
        } elseif ($action === 'move_down' && $index < count($items) - 1) {
            [$items[$index + 1], $items[$index]] = [$items[$index], $items[$index + 1]];
        }
        $c['services']['items'] = array_values($items);
    }

    if (!save_content($c)) {
        admin_redirect('edit-services.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-services.php', ['saved' => 1]);
}

$items = $c['services']['items'] ?? [];
admin_page_start('Services', 'edit-services.php');
?>
        <form class="editor item-box" method="post" action="edit-services.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_header">
            <h2>Section header</h2>
            <label><input type="checkbox" name="visible" value="1" <?= section_visible($c, "services") ? "checked" : "" ?>> Show this section on the website</label>
            <label for="heading">Heading</label>
            <input type="text" id="heading" name="heading" value="<?= e($c['services']['heading'] ?? '') ?>">
            <label for="intro">Small line above the intro</label>
            <input type="text" id="intro" name="intro" value="<?= e($c['services']['intro'] ?? '') ?>">
            <label for="subtitle">Intro text</label>
            <textarea id="subtitle" name="subtitle"><?= e($c['services']['subtitle'] ?? '') ?></textarea>
            <button type="submit" class="btn btn-small">Save header</button>
        </form>

        <h2 style="margin-top:30px;">Service cards</h2>
<?php foreach ($items as $i => $item): ?>
        <div class="item-box">
            <h2><?= $i + 1 ?>. <?= e($item['title'] ?? '') ?></h2>
            <form method="post" action="edit-services.php" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                <label>Title</label>
                <input type="text" name="title" value="<?= e($item['title'] ?? '') ?>" required>
                <label>Description</label>
                <p class="hint">Press Enter to start a new line.</p>
                <textarea name="description"><?= e($item['description'] ?? '') ?></textarea>
                <label>Photo</label>
                <div class="thumb-row">
<?php if (!empty($item['image']['src'])): ?>
                    <img src="../<?= e($item['image']['src']) ?>" alt="" class="thumb">
<?php endif; ?>
                    <div>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                        <p class="hint">Leave empty to keep the current photo.</p>
                    </div>
                </div>
                <label>Photo description (for accessibility)</label>
                <input type="text" name="alt" value="<?= e($item['image']['alt'] ?? '') ?>">
                <button type="submit" class="btn btn-small" style="margin-top:12px;">Save changes</button>
            </form>
            <div class="item-toolbar">
                <form method="post" action="edit-services.php" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="move_up">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-ghost" <?= $i === 0 ? 'disabled' : '' ?>>↑ Move up</button>
                </form>
                <form method="post" action="edit-services.php" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="move_down">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-ghost" <?= $i === count($items) - 1 ? 'disabled' : '' ?>>↓ Move down</button>
                </form>
                <form method="post" action="edit-services.php" class="inline-form" onsubmit="return confirm('Delete this service card? This cannot be undone.');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                </form>
            </div>
        </div>
<?php endforeach; ?>

        <div class="item-box">
            <h2>Add a new service</h2>
            <form method="post" action="edit-services.php" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <label>Title</label>
                <input type="text" name="title" required>
                <label>Description</label>
                <textarea name="description"></textarea>
                <label>Photo</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <button type="submit" class="btn btn-small" style="margin-top:12px;">Add service</button>
            </form>
        </div>
<?php admin_page_end(); ?>
