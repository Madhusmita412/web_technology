<?php
session_start();

$products = require __DIR__ . '/data.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['visit_count'])) {
    $_SESSION['visit_count'] = 0;
}
$_SESSION['visit_count']++;

function h(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function getCartCount(): int
{
    return array_sum($_SESSION['cart']);
}

function getCartTotal(array $products): float
{
    $total = 0.0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        if (isset($products[$id])) {
            $total += $products[$id]['price'] * $qty;
        }
    }
    return $total;
}

function appNav(string $currentPage): string
{
    $links = [
        'index.php' => 'Home',
        'products.php' => 'Products',
        'cart.php' => 'Cart',
    ];

    $html = '<nav class="site-nav"><ul>';
    foreach ($links as $file => $label) {
        $class = $file === $currentPage ? ' class="active"' : '';
        $html .= '<li><a' . $class . ' href="' . $file . '">' . h($label) . '</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}
