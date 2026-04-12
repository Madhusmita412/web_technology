<?php
require_once __DIR__ . '/common.php';

$message = '';
$storedName = $_COOKIE['customer_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    if ($name !== '') {
        setcookie('customer_name', $name, time() + (86400 * 30), '/');
        $_COOKIE['customer_name'] = $name;
        $_SESSION['customer_name'] = $name;
        $storedName = $name;
        $message = 'Name saved in cookie successfully.';
    } else {
        $message = 'Please enter a valid name.';
    }
}

if (!isset($_SESSION['customer_name']) && $storedName !== '') {
    $_SESSION['customer_name'] = $storedName;
}

$recentProductId = (int)($_COOKIE['recent_product_id'] ?? 0);
$recentProduct = $products[$recentProductId]['name'] ?? 'None';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 10 - Sessions and Cookies</title>
    <style>
        .exp-nav { list-style:none; padding:10px 16px; margin:0; display:flex; gap:14px; flex-wrap:wrap; background:#0f172a; border-bottom:1px solid #1e293b; }
        .exp-nav a { color:#e2e8f0; text-decoration:none; font-weight:600; font-size:14px; }
        .exp-nav a:hover { color:#fff; text-decoration:underline; }
        body { margin:0; font-family:Segoe UI, Arial, sans-serif; background:#f8fafc; color:#0f172a; }
        .wrap { width:min(980px,96%); margin:20px auto; }
        .hero, .card { background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:18px; margin-bottom:14px; }
        h1 { margin-top:0; }
        .site-nav ul { list-style:none; display:flex; gap:12px; padding:0; margin:0 0 10px; }
        .site-nav a { text-decoration:none; color:#0369a1; font-weight:700; }
        .site-nav a.active { color:#0f172a; }
        input { padding:10px; border-radius:8px; border:1px solid #94a3b8; width:100%; max-width:320px; }
        button { padding:10px 14px; border:none; border-radius:8px; background:#0369a1; color:#fff; cursor:pointer; font-weight:700; }
        .meta { color:#475569; }
        .ok { color:#166534; font-weight:700; }
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
    <?php echo appNav('index.php'); ?>

    <section class="hero">
        <h1>Flipmart Session and Cookie Demo</h1>
        <p class="meta">Lab 10 uses sessions for cart and visit count, and cookies for user name and recently viewed product.</p>
    </section>

    <section class="card">
        <h2>Remember User Name (Cookie)</h2>
        <?php if ($message !== ''): ?><p class="ok"><?php echo h($message); ?></p><?php endif; ?>
        <form method="post" action="">
            <label for="customer_name">Customer Name</label><br>
            <input id="customer_name" name="customer_name" type="text" value="<?php echo h($storedName); ?>" placeholder="Enter your name">
            <button type="submit">Save Name</button>
        </form>
    </section>

    <section class="card">
        <h2>Stored Information Across Pages</h2>
        <p><strong>Session Visit Count:</strong> <?php echo (int)$_SESSION['visit_count']; ?></p>
        <p><strong>Session Customer Name:</strong> <?php echo h($_SESSION['customer_name'] ?? 'Not set'); ?></p>
        <p><strong>Cookie Customer Name:</strong> <?php echo h($storedName !== '' ? $storedName : 'Not set'); ?></p>
        <p><strong>Recently Viewed Product (Cookie):</strong> <?php echo h($recentProduct); ?></p>
        <p><strong>Cart Items in Session:</strong> <?php echo getCartCount(); ?></p>
    </section>
</div>
</body>
</html>
