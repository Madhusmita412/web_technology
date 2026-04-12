<?php
require_once __DIR__ . '/common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['remove_id'])) {
        $removeId = (int)$_POST['remove_id'];
        unset($_SESSION['cart'][$removeId]);
        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['update_qty']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            $id = (int)$id;
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } elseif (isset($products[$id])) {
                $_SESSION['cart'][$id] = $qty;
            }
        }
        header('Location: cart.php');
        exit;
    }
}

$total = getCartTotal($products);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 10 - Cart</title>
    <style>
        .exp-nav { list-style:none; padding:10px 16px; margin:0; display:flex; gap:14px; flex-wrap:wrap; background:#0f172a; border-bottom:1px solid #1e293b; }
        .exp-nav a { color:#e2e8f0; text-decoration:none; font-weight:600; font-size:14px; }
        .exp-nav a:hover { color:#fff; text-decoration:underline; }
        body { margin:0; font-family:Segoe UI, Arial, sans-serif; background:#f8fafc; color:#0f172a; }
        .wrap { width:min(980px,96%); margin:20px auto; }
        .site-nav ul { list-style:none; display:flex; gap:12px; padding:0; margin:0 0 12px; }
        .site-nav a { text-decoration:none; color:#0369a1; font-weight:700; }
        .site-nav a.active { color:#0f172a; }
        .card { background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:16px; }
        table { width:100%; border-collapse:collapse; margin:10px 0; }
        th, td { border:1px solid #cbd5e1; padding:8px; text-align:left; }
        th { background:#e2e8f0; }
        input[type='number'] { width:70px; padding:6px; }
        button { padding:8px 12px; border:none; border-radius:8px; background:#0369a1; color:#fff; cursor:pointer; font-weight:700; }
        .muted { color:#475569; }
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
    <?php echo appNav('cart.php'); ?>
    <section class="card">
        <h1>Your Cart</h1>
        <p class="muted">Customer: <?php echo h($_SESSION['customer_name'] ?? ($_COOKIE['customer_name'] ?? 'Guest')); ?></p>

        <?php if (empty($_SESSION['cart'])): ?>
            <p class="muted">Cart is empty. Go to <a href="products.php">Products</a> and add items.</p>
        <?php else: ?>
            <form method="post" action="">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Line Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $qty): ?>
                            <?php if (!isset($products[$id])) { continue; } ?>
                            <tr>
                                <td><?php echo h($products[$id]['name']); ?></td>
                                <td>$<?php echo number_format((float)$products[$id]['price'], 2); ?></td>
                                <td><input type="number" min="1" name="qty[<?php echo (int)$id; ?>]" value="<?php echo (int)$qty; ?>"></td>
                                <td>$<?php echo number_format((float)$products[$id]['price'] * (int)$qty, 2); ?></td>
                                <td>
                                    <button type="submit" name="remove_id" value="<?php echo (int)$id; ?>">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><strong>Grand Total: $<?php echo number_format($total, 2); ?></strong></p>
                <button type="submit" name="update_qty" value="1">Update Quantities</button>
                <button type="submit" name="clear_cart" value="1">Clear Cart</button>
            </form>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
