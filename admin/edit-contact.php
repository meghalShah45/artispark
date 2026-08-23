<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$c = load_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = post_str('email', 180);
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        admin_redirect('edit-contact.php', ['error' => 'Please enter a valid email address.']);
    }

    $phones = [];
    foreach ([post_str('phone1', 40), post_str('phone2', 40)] as $phone) {
        if ($phone !== '') {
            $phones[] = $phone;
        }
    }

    $c['contact']['heading'] = post_str('heading', 200);
    $c['contact']['subtitle'] = post_str('subtitle', 500);
    $c['contact']['location'] = post_str('location', 300);
    $c['contact']['phones'] = $phones;
    $c['contact']['email'] = $email;

    $markers = [];
    for ($i = 1; $i <= 3; $i++) {
        $label = post_str("marker{$i}_label", 120);
        $lat = post_str("marker{$i}_lat", 30);
        $lng = post_str("marker{$i}_lng", 30);
        if ($lat === "" || $lng === "") {
            continue;
        }
        if (!is_numeric($lat) || !is_numeric($lng) || abs((float)$lat) > 90 || abs((float)$lng) > 180) {
            admin_redirect("edit-contact.php", ["error" => "Map pin {$i}: latitude/longitude must be numbers (e.g. -37.8531 and 145.2153)."]);
        }
        $markers[] = ["label" => $label, "lat" => (float)$lat, "lng" => (float)$lng];
    }
    $c["contact"]["map"] = ["enabled" => !empty($_POST["map_enabled"]), "markers" => $markers];

    if (!save_content($c)) {
        admin_redirect('edit-contact.php', ['error' => 'Could not save. Please try again.']);
    }
    admin_redirect('edit-contact.php', ['saved' => 1]);
}

admin_page_start('Contact information', 'edit-contact.php');
?>
        <p class="lead">This updates the contact details shown on the website. It does not change where the contact form emails are sent.</p>
        <form class="editor" method="post" action="edit-contact.php">
            <?= csrf_field() ?>
            <label for="heading">Section heading</label>
            <input type="text" id="heading" name="heading" value="<?= e($c['contact']['heading'] ?? '') ?>">
            <label for="subtitle">Subtitle</label>
            <input type="text" id="subtitle" name="subtitle" value="<?= e($c['contact']['subtitle'] ?? '') ?>">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="<?= e($c['contact']['location'] ?? '') ?>">
            <label for="phone1">Phone number 1</label>
            <input type="text" id="phone1" name="phone1" value="<?= e($c['contact']['phones'][0] ?? '') ?>">
            <label for="phone2">Phone number 2</label>
            <p class="hint">Leave empty to show only one number.</p>
            <input type="text" id="phone2" name="phone2" value="<?= e($c['contact']['phones'][1] ?? '') ?>">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="<?= e($c['contact']['email'] ?? '') ?>">
            <h2 style="margin-top:34px;">Map</h2>
            <label><input type="checkbox" name="map_enabled" value="1" <?= !empty($c["contact"]["map"]["enabled"]) ? "checked" : "" ?>> Show the map on the website</label>
            <p class="hint">Up to 3 pins. To find coordinates: open Google Maps, right-click the spot, and click the numbers shown (e.g. -37.8531, 145.2153). Leave latitude/longitude empty to remove a pin.</p>
<?php for ($i = 1; $i <= 3; $i++): $m = $c["contact"]["map"]["markers"][$i - 1] ?? []; ?>
            <div class="item-box">
                <h2>Pin <?= $i ?></h2>
                <label>Label</label>
                <input type="text" name="marker<?= $i ?>_label" value="<?= e((string)($m["label"] ?? "")) ?>">
                <div class="thumb-row">
                    <div style="flex:1"><label>Latitude</label><input type="text" name="marker<?= $i ?>_lat" value="<?= e(isset($m["lat"]) ? (string)$m["lat"] : "") ?>" inputmode="decimal"></div>
                    <div style="flex:1"><label>Longitude</label><input type="text" name="marker<?= $i ?>_lng" value="<?= e(isset($m["lng"]) ? (string)$m["lng"] : "") ?>" inputmode="decimal"></div>
                </div>
            </div>
<?php endfor; ?>
            <button type="submit" class="btn">Save</button>
        </form>
<?php admin_page_end(); ?>
