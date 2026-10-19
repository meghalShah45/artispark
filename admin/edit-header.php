<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$c = load_content();

const NAV_FIXED_IDS = ['about', 'services', 'team', 'testimonials', 'gallery', 'contact'];

function nav_items(array $c): array
{
    $items = $c['nav']['items'] ?? [];
    $out = [];
    $seen = [];
    foreach ($items as $item) {
        $id = (string)($item['id'] ?? '');
        if (in_array($id, NAV_FIXED_IDS, true)) {
            $out[] = ['id' => $id, 'label' => (string)($item['label'] ?? '')];
            $seen[] = $id;
        }
    }
    // Ensure every built-in entry exists even if the JSON predates this feature.
    foreach (NAV_FIXED_IDS as $id) {
        if (!in_array($id, $seen, true)) {
            $out[] = ['id' => $id, 'label' => ucfirst($id)];
        }
    }
    return $out;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = post_str('action', 40);
    $items = nav_items($c);

    // Sizes and labels are part of the same form as the arrows, so a reorder
    // click saves any pending edits too instead of discarding them.
    $c['nav']['logo_height'] = max(40, min(140, (int)post_str('logo_height', 5)));
    $c['nav']['link_size'] = max(10, min(22, (int)post_str('link_size', 5)));
    $c['nav']['heading_size'] = max(20, min(60, (int)post_str('heading_size', 5)));
    foreach ($items as $i => $item) {
        $label = post_str('label_' . $item['id'], 40);
        $items[$i]['label'] = $label !== '' ? $label : ucfirst($item['id']);
    }

    if ($action === 'move_up' || $action === 'move_down') {
        $id = post_str('id', 40);
        foreach ($items as $i => $item) {
            if ($item['id'] === $id) {
                $j = $action === 'move_up' ? $i - 1 : $i + 1;
                if ($j >= 0 && $j < count($items)) {
                    [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
                }
                break;
            }
        }
    }
    $c['nav']['items'] = $items;

    if (!save_content($c)) {
        admin_redirect('edit-header.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-header.php', ['saved' => 1]);
}

$items = nav_items($c);
$logoHeight = (int)($c['nav']['logo_height'] ?? 88);
$linkSize = (int)($c['nav']['link_size'] ?? 13);
$headingSize = (int)($c['nav']['heading_size'] ?? 32);

admin_page_start('Header & Menu', 'edit-header.php');
?>
        <form class="editor" method="post" action="edit-header.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <!-- Default submit so pressing Enter saves instead of hitting an arrow button -->
            <button type="submit" name="action" value="save" style="position:absolute;left:-9999px;" aria-hidden="true" tabindex="-1"></button>

            <h2>Sizes</h2>
            <label for="logo_height">Logo size: <output id="logoOut"><?= $logoHeight ?></output>px</label>
            <input type="range" id="logo_height" name="logo_height" min="40" max="140" step="2" value="<?= $logoHeight ?>"
                   oninput="document.getElementById('logoOut').textContent = this.value">

            <label for="link_size">Menu text size: <output id="linkOut"><?= $linkSize ?></output>px</label>
            <input type="range" id="link_size" name="link_size" min="10" max="22" step="1" value="<?= $linkSize ?>"
                   oninput="document.getElementById('linkOut').textContent = this.value">

            <label for="heading_size">Section heading size: <output id="headingOut"><?= $headingSize ?></output>px</label>
            <p class="hint">The size of headings like "Our Services" and "Meet Our Team".</p>
            <input type="range" id="heading_size" name="heading_size" min="20" max="60" step="1" value="<?= $headingSize ?>"
                   oninput="document.getElementById('headingOut').textContent = this.value">

            <h2 style="margin-top:34px;">Menu items</h2>
            <p class="hint">Rename items here; use the arrows to change the order in the menu and footer. An item is only shown while its section is turned on (see each section's own page).</p>
<?php foreach ($items as $i => $item): ?>
            <div class="item-box">
                <div class="thumb-row" style="align-items:center;">
                    <div style="flex:1">
                        <label style="margin-top:0;"><?= e(ucfirst($item['id'])) ?> &middot; shown as</label>
                        <input type="text" name="label_<?= e($item['id']) ?>" value="<?= e($item['label']) ?>" maxlength="40">
                    </div>
                    <div class="item-toolbar" style="margin-top:22px;">
                        <button type="submit" class="btn btn-small btn-ghost" name="action" value="move_up"
                                onclick="this.form.id.value='<?= e($item['id']) ?>'" <?= $i === 0 ? 'disabled' : '' ?>>↑</button>
                        <button type="submit" class="btn btn-small btn-ghost" name="action" value="move_down"
                                onclick="this.form.id.value='<?= e($item['id']) ?>'" <?= $i === count($items) - 1 ? 'disabled' : '' ?>>↓</button>
                    </div>
                </div>
            </div>
<?php endforeach; ?>

            <input type="hidden" name="id" value="">
            <button type="submit" class="btn">Save</button>
        </form>
<?php admin_page_end(); ?>
