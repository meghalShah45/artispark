<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_images.php';

$c = load_content();

function member_index_by_id(array $items, string $id): ?int
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
    $items = $c['team']['members'] ?? [];

    if ($action === 'save_header') {
        $c['team']['visible'] = !empty($_POST['visible']);
        $c['team']['intro'] = post_str('intro', 200);
        $c['team']['heading'] = post_str('heading', 200);
        $c['team']['button_label'] = post_str('button_label', 60);
    } elseif ($action === 'add') {
        $name = post_str('name', 120);
        if ($name === '') {
            admin_redirect('edit-team.php', ['error' => 'A team member needs a name.']);
        }
        $item = ['id' => content_new_id('tm'), 'name' => $name, 'credentials' => post_str('credentials', 1000), 'image' => ['src' => '', 'alt' => $name]];
        if (!empty($_FILES['image']['name'])) {
            $result = process_uploaded_image($_FILES['image']);
            if (!$result['ok']) {
                admin_redirect('edit-team.php', ['error' => $result['error']]);
            }
            $item['image']['src'] = $result['path'];
        }
        $items[] = $item;
        $c['team']['members'] = $items;
    } else {
        $id = post_str('id', 60);
        $index = member_index_by_id($items, $id);
        if ($index === null) {
            admin_redirect('edit-team.php', ['error' => 'That team member no longer exists.']);
        }
        if ($action === 'save') {
            $name = post_str('name', 120);
            if ($name === '') {
                admin_redirect('edit-team.php', ['error' => 'The name cannot be empty.']);
            }
            $items[$index]['name'] = $name;
            $items[$index]['credentials'] = post_str('credentials', 1000);
            $items[$index]['image']['alt'] = $name;
            if (!empty($_FILES['image']['name'])) {
                $result = process_uploaded_image($_FILES['image']);
                if (!$result['ok']) {
                    admin_redirect('edit-team.php', ['error' => $result['error']]);
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
        $c['team']['members'] = array_values($items);
    }

    if (!save_content($c)) {
        admin_redirect('edit-team.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-team.php', ['saved' => 1]);
}

$items = $c['team']['members'] ?? [];
admin_page_start('Team', 'edit-team.php');
?>
        <form class="editor item-box" method="post" action="edit-team.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_header">
            <h2>Section header</h2>
            <label><input type="checkbox" name="visible" value="1" <?= section_visible($c, 'team') ? 'checked' : '' ?>> Show this section on the website</label>
            <label for="intro">Small line above the heading</label>
            <input type="text" id="intro" name="intro" value="<?= e($c['team']['intro'] ?? '') ?>">
            <label for="heading">Heading</label>
            <input type="text" id="heading" name="heading" value="<?= e($c['team']['heading'] ?? '') ?>">
            <label for="button_label">Button text (links to the About section; leave empty to hide)</label>
            <input type="text" id="button_label" name="button_label" value="<?= e($c['team']['button_label'] ?? '') ?>">
            <button type="submit" class="btn btn-small">Save header</button>
        </form>

        <h2 style="margin-top:30px;">Team members</h2>
        <p class="hint">Square photos work best — they are shown as large squares.</p>
<?php foreach ($items as $i => $item): ?>
        <div class="item-box">
            <h2><?= $i + 1 ?>. <?= e($item['name'] ?? '') ?></h2>
            <form method="post" action="edit-team.php" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                <label>Name</label>
                <input type="text" name="name" value="<?= e($item['name'] ?? '') ?>" required>
                <label>Role / qualifications</label>
                <p class="hint">One per line.</p>
                <textarea name="credentials"><?= e($item['credentials'] ?? '') ?></textarea>
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
                <button type="submit" class="btn btn-small" style="margin-top:12px;">Save changes</button>
            </form>
            <div class="item-toolbar">
                <form method="post" action="edit-team.php" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="move_up">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-ghost" <?= $i === 0 ? 'disabled' : '' ?>>↑ Move up</button>
                </form>
                <form method="post" action="edit-team.php" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="move_down">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-ghost" <?= $i === count($items) - 1 ? 'disabled' : '' ?>>↓ Move down</button>
                </form>
                <form method="post" action="edit-team.php" class="inline-form" onsubmit="return confirm('Remove this team member?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                </form>
            </div>
        </div>
<?php endforeach; ?>

        <div class="item-box">
            <h2>Add a team member</h2>
            <form method="post" action="edit-team.php" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <label>Name</label>
                <input type="text" name="name" required>
                <label>Role / qualifications</label>
                <textarea name="credentials"></textarea>
                <label>Photo</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <button type="submit" class="btn btn-small" style="margin-top:12px;">Add member</button>
            </form>
        </div>
<?php admin_page_end(); ?>
