<?php
include '../css/style.css';
include '../includes/functions.php';
session_start();
redirectIfNotLoggedIn();

if (isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];
    $data = file_get_contents("https://world.openfoodfacts.org/api/v0/product/$barcode.json");
    $product = json_decode($data, true);

    if ($product['status'] != 1) {
        echo "Product not found.";
        exit();
    }

    $name = $product['product']['product_name'] ?? 'N/A';
    $image = $product['product']['image_url'] ?? '';
    $ingredients_list = $product['product']['ingredients'] ?? [];
    $nutrients = $product['product']['nutriments'] ?? [];

    $warnings = [];

    // Get user health data from session
    $user = $_SESSION['user_data'];
    $restrictions = explode(",", $user['dietary_restrictions'] ?? '');

    // Check health conditions
    if ($user['condition_diabetes'] && isset($nutrients['sugars_100g']) && $nutrients['sugars_100g'] > 5) {
        $warnings[] = "High sugar content - not recommended for diabetes.";
    }
    if ($user['condition_cholesterol'] && isset($nutrients['saturated-fat_100g']) && $nutrients['saturated-fat_100g'] > 3) {
        $warnings[] = "High saturated fat - not suitable for cholesterol issues.";
    }
    if ($user['condition_hypertension'] && isset($nutrients['salt_100g']) && $nutrients['salt_100g'] > 0.3) {
        $warnings[] = "High salt content - may affect blood pressure.";
    }

    // Check dietary restrictions
    foreach ($restrictions as $restriction) {
        $restriction = trim(strtolower($restriction));
        if ($restriction === "sugar" && isset($nutrients['sugars_100g']) && $nutrients['sugars_100g'] > 5) {
            $warnings[] = "Contains sugar - restricted in your profile.";
        }
        if ($restriction === "salt" && isset($nutrients['salt_100g']) && $nutrients['salt_100g'] > 0.3) {
            $warnings[] = "Contains salt - restricted in your profile.";
        }
        if ($restriction === "fat" && isset($nutrients['fat_100g']) && $nutrients['fat_100g'] > 10) {
            $warnings[] = "High fat content - restricted in your profile.";
        }
    }
}
?>
<head>
    <title>Product Result - <?= htmlspecialchars($name) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<h1 style="text-align: center;"><?= htmlspecialchars($name) ?></h1>
<?php if ($image): ?>
    <div style="text-align: center;">
        <img src="<?= htmlspecialchars($image) ?>" alt="Product Image" width="300">
    </div><br>
<?php endif; ?>
<?php if (!empty($warnings)): ?>
    <div style="border: 2px solid red; background-color: #ffe5e5; padding: 15px; border-radius: 8px; margin: 20px auto; max-width: 600px; font-size: 20px;">
        <h4 style="text-align: center; color: red;">⚠️ Health Warnings:</h4>
        <ul style="list-style: none; padding-left: 0; margin: 0;">
            <?php foreach ($warnings as $warn): ?>
                <li style="text-align: center; color: #a70000; font-weight: bold;">
                    <?= htmlspecialchars($warn) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h4>Ingredients:</h4>
<table border="1" cellpadding="5">
    <tr>
        <th>Ingredient</th>
        <th>Vegetarian</th>
        <th>Vegan</th>
    </tr>
    <?php foreach ($ingredients_list as $ingredient): ?>
    <tr>
        <td>
    <?= htmlspecialchars($ingredient['text'] ?? '-') ?>
    <?php if (!empty($ingredient['text'])): ?>
        <sup>
    <a href="https://www.google.com/search?q=<?= urlencode($ingredient['text']) ?>" target="_blank" style="margin-left: 10px; font-size: 0.75em; text-decoration: none; color: #1a73e8;">
        (search online)
    </a>
</sup>

    <?php endif; ?>
</td>

        
        <?php
        $veg = strtolower($ingredient['vegetarian'] ?? '');
        $vegan = strtolower($ingredient['vegan'] ?? '');
        ?>

        <td style="<?= $veg === 'yes' ? 'background-color: #c6f6c677; font-weight: bold; color: green;' : '' ?>">
            <?= htmlspecialchars($veg ?: '-') ?>
        </td>
        <td style="<?= $vegan === 'yes' ? 'background-color: #c6f6c677; font-weight: bold; color: green;' : '' ?>">
            <?= htmlspecialchars($vegan ?: '-') ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>

<h4>Nutrition Information (per 100g):</h4>
<table border="1" cellpadding="5">
    <tr><th>Nutrient</th><th>Value</th></tr>
    <?php foreach ($nutrients as $key => $value): ?>
        <tr>
            <td>
    <?= htmlspecialchars($key) ?>
    <<sup>
    <a href="https://www.google.com/search?q=<?= urlencode($key) ?>" target="_blank" style="margin-left: 8px; font-size: 0.75em; text-decoration: none; color: #1a73e8;">
        (search online)
    </a>
</sup>

</td>

            <td><?= htmlspecialchars($value) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<a href="search.php" class="back-to-search">🔙 Back to Search</a>
<link rel="stylesheet" href="../css/style.css">