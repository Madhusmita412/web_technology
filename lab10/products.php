<?php
require_once __DIR__ . '/common.php';

if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    if (isset($products[$id])) {
        setcookie('recent_product_id', (string)$id, time() + (86400 * 30), '/');
        $_COOKIE['recent_product_id'] = (string)$id;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = (int)$_POST['product_id'];
    if (isset($products[$id])) {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = 0;
        }
        $_SESSION['cart'][$id]++;
    }
    header('Location: products.php');
    exit;
}

$recentProductId = (int)($_COOKIE['recent_product_id'] ?? 0);
$recentProduct = $products[$recentProductId]['name'] ?? 'None';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 10 - Products</title>
    <style>
        .exp-nav { list-style:none; padding:10px 16px; margin:0; display:flex; gap:14px; flex-wrap:wrap; background:#0f172a; border-bottom:1px solid #1e293b; }
        .exp-nav a { color:#e2e8f0; text-decoration:none; font-weight:600; font-size:14px; }
        .exp-nav a:hover { color:#fff; text-decoration:underline; }
        body { margin:0; font-family:Segoe UI, Arial, sans-serif; background:#f8fafc; color:#0f172a; }
        .wrap { width:min(1080px,96%); margin:20px auto; }
        .site-nav ul { list-style:none; display:flex; gap:12px; padding:0; margin:0 0 12px; }
        .site-nav a { text-decoration:none; color:#0369a1; font-weight:700; }
        .site-nav a.active { color:#0f172a; }
        .meta { color:#475569; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px; }
        .card { background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:14px; }
        button { padding:9px 12px; border:none; border-radius:8px; background:#0369a1; color:#fff; cursor:pointer; font-weight:700; }
        .view-link { margin-right:8px; color:#0f172a; font-weight:600; }
    </style>
</head>
<body>
<ul class="exp-nav">
    <li><a href="/web_technology/lab1/index.html">Exp 1: Basic Website</a></li>
    <li><a href="/web_technology/lab2/index.html">Exp 2: E-commerce</a></li>
    <li><a href="/web_technology/lab3/index.html">Exp 3: CSS Enhanced (Exp1)</a></li>
    <li><a href="/web_technology/lab4/index.html">Exp 4: CSS Enhanced (Exp2)</a></li>
    <li><a href="/web_technology/lab5/scientific_calculator.html">Exp 5: Scientific Calculator</a></li>
    <li><a href="/web_technology/lab6/index.html">Exp 6: Form Validation</a></li>
    <li><a href="/web_technology/lab7/index.html">Exp 7: Event Handling</a></li>
    <li><a href="/web_technology/lab8/index.php">Exp 8: PHP Form Handling</a></li>
    <li><a href="/web_technology/lab9/index.php">Exp 9: PHP + MySQL Products</a></li>
    <li><a href="/web_technology/lab10/index.php">Exp 10: Sessions and Cookies</a></li>
</ul>

<div class="wrap">
    <?php echo appNav('products.php'); ?>
    <h1>Products</h1>
    <p class="meta">Recent product (from cookie): <?php echo h($recentProduct); ?> | Cart items (session): <?php echo getCartCount(); ?></p>

    <div class="grid">
        <?php foreach ($products as $product): ?>
            <article class="card">
                <h3><?php echo h($product['name']); ?></h3>
                <p class="meta"><?php echo h($product['category']); ?></p>
                <p><?php echo h($product['description']); ?></p>
                <p><strong>$<?php echo number_format((float)$product['price'], 2); ?></strong></p>
                <a class="view-link" href="products.php?view=<?php echo (int)$product['id']; ?>">View</a>
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                    <button type="submit">Add to Cart</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
