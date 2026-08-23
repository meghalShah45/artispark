<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$c = load_content();

function testimonial_index_by_id(array $items, string $id): ?int
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
    $items = $c['testimonials']['items'] ?? [];

    if ($action === 'save_header') {
        $c['testimonials']['heading'] = post_str('heading', 200);
        $c["testimonials"]["visible"] = !empty($_POST["visible"]);
        $c['testimonials']['subtitle'] = post_str('subtitle', 500);
    } elseif ($action === 'add') {
        $quote = post_str('quote', 5000);
        $author = post_str('author', 120);
        if ($quote === '' || $author === '') {
            admin_redirect('edit-testimonials.php', ['error' => 'A testimonial needs both a quote and a name.']);
        }
        $items[] = ['id' => content_new_id('tst'), 'quote' => $quote, 'author' => $author];
        $c['testimonials']['items'] = $items;
    } else {
        $id = post_str('id', 60);
        $index = testimonial_index_by_id($items, $id);
        if ($index === null) {
            admin_redirect('edit-testimonials.php', ['error' => 'That testimonial no longer exists.']);
        }
        if ($action === 'save') {
            $quote = post_str('quote', 5000);
            $author = post_str('author', 120);
            if ($quote === '' || $author === '') {
                admin_redirect('edit-testimonials.php', ['error' => 'A testimonial needs both a quote and a name.']);
            }
            $items[$index]['quote'] = $quote;
            $items[$index]['author'] = $author;
        } elseif ($action === 'delete') {
            array_splice($items, $index, 1);
        } elseif ($action === 'move_up' && $index > 0) {
            [$items[$index - 1], $items[$index]] = [$items[$index], $items[$index - 1]];
        } elseif ($action === 'move_down' && $index < count($items) - 1) {
            [$items[$index + 1], $items[$index]] = [$items[$index], $items[$index + 1]];
        }
        $c['testimonials']['items'] = array_values($items);
    }

    if (!save_content($c)) {
        admin_redirect('edit-testimonials.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-testimonials.php', ['saved' => 1]);
}

$items = $c['testimonials']['items'] ?? [];
admin_page_start('Testimonials', 'edit-testimonials.php');
?>
        <form class="editor item-box" method="post" action="edit-testimonials.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_header">
            <h2>Section header</h2>
            <label><input type="checkbox" name="visible" value="1" <?= section_visible($c, "testimonials") ? "checked" : "" ?>> Show this section on the website</label>
            <label for="heading">Heading</label>
            <input type="text" id="heading" name="heading" value="<?= e($c['testimonials']['heading'] ?? '') ?>">
            <label for="subtitle">Subtitle</label>
            <input type="text" id="subtitle" name="subtitle" value="<?= e($c['testimonials']['subtitle'] ?? '') ?>">
            <button type="submit" class="btn btn-small">Save header</button>
        </form>

        <h2 style="margin-top:30px;">Testimonials</h2>
<?php foreach ($items as $i => $item): ?>
        <div class="item-box">
            <h2><?= $i + 1 ?>. <?= e($item['author'] ?? '') ?></h2>
            <form method="post" action="edit-testimonials.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                <label>Quote</label>
                <textarea name="quote"><?= e($item['quote'] ?? '') ?></textarea>
                <label>Name</label>
                <input type="text" name="author" value="<?= e($item['author'] ?? '') ?>" required>
                <button type="submit" class="btn btn-small" style="margin-top:12px;">Save changes</button>
            </form>
            <div class="item-toolbar">
                <form method="post" action="edit-testimonials.php" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="move_up">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-ghost" <?= $i === 0 ? 'disabled' : '' ?>>↑ Move up</button>
                </form>
                <form method="post" action="edit-testimonials.php" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="move_down">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-ghost" <?= $i === count($items) - 1 ? 'disabled' : '' ?>>↓ Move down</button>
                </form>
                <form method="post" action="edit-testimonials.php" class="inline-form" onsubmit="return confirm('Delete this testimonial?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                </form>
            </div>
        </div>
<?php endforeach; ?>

        <div class="item-box">
            <h2>Add a testimonial</h2>
            <form method="post" action="edit-testimonials.php">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <label>Quote</label>
                <textarea name="quote" required></textarea>
                <label>Name</label>
                <input type="text" name="author" required>
                <button type="submit" class="btn btn-small" style="margin-top:12px;">Add testimonial</button>
            </form>
        </div>
<?php admin_page_end(); ?>
