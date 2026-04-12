<?php
require_once __DIR__ . '/db.php';

$values = [
    'product_name' => '',
    'category' => '',
    'brand' => '',
    'price' => '',
    'stock' => '',
    'description' => '',
];
$errors = [];
$success = '';
$dbError = '';
$products = [];

$allowedCategories = ['Smartphones', 'Laptops', 'Audio', 'Accessories', 'Wearables'];

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo = getDbConnection();

    // Keep lab runnable even if setup.sql wasn't executed yet.
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_name VARCHAR(120) NOT NULL,
            category VARCHAR(80) NOT NULL,
            brand VARCHAR(80) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($values as $key => $unused) {
            $values[$key] = trim($_POST[$key] ?? '');
        }

        if (strlen($values['product_name']) < 3) {
            $errors['product_name'] = 'Product name must be at least 3 characters.';
        }

        if (!in_array($values['category'], $allowedCategories, true)) {
            $errors['category'] = 'Please choose a valid category.';
        }

        if ($values['brand'] === '') {
            $errors['brand'] = 'Brand is required.';
        }

        if (!is_numeric($values['price']) || (float)$values['price'] <= 0) {
            $errors['price'] = 'Price must be a positive number.';
        }

        if (!ctype_digit($values['stock']) || (int)$values['stock'] < 0) {
            $errors['stock'] = 'Stock must be a non-negative integer.';
        }

        if (strlen($values['description']) < 10) {
            $errors['description'] = 'Description must be at least 10 characters.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare(
                'INSERT INTO products (product_name, category, brand, price, stock, description)
                 VALUES (:product_name, :category, :brand, :price, :stock, :description)'
            );

            $stmt->execute([
                ':product_name' => $values['product_name'],
                ':category' => $values['category'],
                ':brand' => $values['brand'],
                ':price' => (float)$values['price'],
                ':stock' => (int)$values['stock'],
                ':description' => $values['description'],
            ]);

            $success = 'Product saved successfully.';
            foreach ($values as $key => $unused) {
                $values[$key] = '';
            }
        }
    }

    $selectedCategory = trim($_GET['category'] ?? '');
    if ($selectedCategory !== '' && in_array($selectedCategory, $allowedCategories, true)) {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE category = :category ORDER BY id DESC');
        $stmt->execute([':category' => $selectedCategory]);
        $products = $stmt->fetchAll();
    } else {
        $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC');
        $products = $stmt->fetchAll();
        $selectedCategory = '';
    }
} catch (Throwable $e) {
    $dbError = 'Database connection/query error. Check MySQL is running and verify credentials in db.php.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 9 - PHP & MySQL Product CRUD</title>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --bg: #f8fafc;
            --card: #ffffff;
            --nav: #0f172a;
            --ok: #166534;
            --err: #b91c1c;
            --brand: #0369a1;
        }

        * {
            box-sizing: border-box;
            font-family: Segoe UI, Arial, sans-serif;
        }

        body {
            margin: 0;
            color: var(--ink);
            background: linear-gradient(180deg, #e2e8f0 0%, var(--bg) 40%);
        }

        .exp-nav {
            list-style: none;
            padding: 10px 16px;
            margin: 0;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            background: var(--nav);
            border-bottom: 1px solid #1e293b;
        }

        .exp-nav a {
            color: #e2e8f0;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .exp-nav a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .wrap {
            width: min(1120px, 96%);
            margin: 20px auto 32px;
        }

        .card {
            background: var(--card);
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(2, 6, 23, 0.06);
        }

        h1 {
            margin: 0 0 6px;
        }

        .subtitle {
            margin: 0;
            color: var(--muted);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #94a3b8;
            border-radius: 8px;
            font-size: 14px;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .error {
            color: var(--err);
            min-height: 16px;
            margin-top: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .ok {
            color: var(--ok);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .db-error {
            color: var(--err);
            font-weight: 700;
            margin-bottom: 10px;
        }

        button {
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 11px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: #075985;
        }

        .filter-row {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 9px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #e2e8f0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .product-card {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px;
            background: #ffffff;
        }

        .product-card h3 {
            margin: 0 0 6px;
        }

        .muted {
            color: var(--muted);
            font-size: 13px;
            margin: 4px 0;
        }

        @media (max-width: 800px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
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
    <section class="card">
        <h1>Lab 9: Product Details using PHP and MySQL</h1>
        <p class="subtitle">Create, store, retrieve, and display product details for Exercise 2.</p>

        <?php if ($success !== ''): ?>
            <div class="ok"><?php echo h($success); ?></div>
        <?php endif; ?>

        <?php if ($dbError !== ''): ?>
            <div class="db-error"><?php echo h($dbError); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="grid">
                <div>
                    <label for="product_name">Product Name</label>
                    <input id="product_name" name="product_name" type="text" value="<?php echo h($values['product_name']); ?>" placeholder="e.g. iPhone 15 Pro">
                    <div class="error"><?php echo h($errors['product_name'] ?? ''); ?></div>
                </div>

                <div>
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">Select category</option>
                        <?php foreach ($allowedCategories as $cat): ?>
                            <option value="<?php echo h($cat); ?>" <?php echo $values['category'] === $cat ? 'selected' : ''; ?>><?php echo h($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error"><?php echo h($errors['category'] ?? ''); ?></div>
                </div>

                <div>
                    <label for="brand">Brand</label>
                    <input id="brand" name="brand" type="text" value="<?php echo h($values['brand']); ?>" placeholder="e.g. Apple">
                    <div class="error"><?php echo h($errors['brand'] ?? ''); ?></div>
                </div>

                <div>
                    <label for="price">Price</label>
                    <input id="price" name="price" type="number" step="0.01" value="<?php echo h($values['price']); ?>" placeholder="e.g. 999.99">
                    <div class="error"><?php echo h($errors['price'] ?? ''); ?></div>
                </div>

                <div>
                    <label for="stock">Stock</label>
                    <input id="stock" name="stock" type="number" step="1" value="<?php echo h($values['stock']); ?>" placeholder="e.g. 25">
                    <div class="error"><?php echo h($errors['stock'] ?? ''); ?></div>
                </div>

                <div class="full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Write product details..."><?php echo h($values['description']); ?></textarea>
                    <div class="error"><?php echo h($errors['description'] ?? ''); ?></div>
                </div>

                <div class="full">
                    <button type="submit">Save Product</button>
                </div>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>Retrieve and Display Products</h2>
        <form method="get" action="" class="filter-row">
            <div>
                <label for="filter-category">Filter by Category</label>
                <select id="filter-category" name="category">
                    <option value="">All categories</option>
                    <?php foreach ($allowedCategories as $cat): ?>
                        <option value="<?php echo h($cat); ?>" <?php echo $selectedCategory === $cat ? 'selected' : ''; ?>><?php echo h($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit">Apply Filter</button>
            </div>
        </form>

        <?php if (!empty($products)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo (int)$product['id']; ?></td>
                            <td><?php echo h($product['product_name']); ?></td>
                            <td><?php echo h($product['category']); ?></td>
                            <td><?php echo h($product['brand']); ?></td>
                            <td><?php echo number_format((float)$product['price'], 2); ?></td>
                            <td><?php echo (int)$product['stock']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cards">
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <h3><?php echo h($product['product_name']); ?></h3>
                        <p class="muted"><?php echo h($product['category']); ?> | <?php echo h($product['brand']); ?></p>
                        <p><strong>Price:</strong> $<?php echo number_format((float)$product['price'], 2); ?></p>
                        <p><strong>Stock:</strong> <?php echo (int)$product['stock']; ?></p>
                        <p class="muted"><?php echo h($product['description']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">No products found yet. Add your first product using the form above.</p>
        <?php endif; ?>
    </section>
</div>
</body>
</html>

