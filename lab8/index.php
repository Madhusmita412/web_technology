<?php
$values = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'password' => '',
    'confirm_password' => '',
    'course' => '',
    'age' => ''
];

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $value) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    if ($values['full_name'] === '' || strlen($values['full_name']) < 3) {
        $errors['full_name'] = 'Name must be at least 3 characters.';
    }

    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if (!preg_match('/^\d{10}$/', $values['phone'])) {
        $errors['phone'] = 'Phone number must be exactly 10 digits.';
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $values['password'])) {
        $errors['password'] = 'Password must be 8+ chars and include upper, lower, number, and symbol.';
    }

    if ($values['confirm_password'] !== $values['password']) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    $allowedCourses = ['Web Technology', 'DBMS', 'Operating Systems', 'Computer Networks'];
    if (!in_array($values['course'], $allowedCourses, true)) {
        $errors['course'] = 'Select a valid course.';
    }

    if (!ctype_digit($values['age']) || (int)$values['age'] < 17 || (int)$values['age'] > 60) {
        $errors['age'] = 'Age must be between 17 and 60.';
    }

    if (empty($_POST['terms'])) {
        $errors['terms'] = 'You must accept terms and conditions.';
    }

    if (empty($errors)) {
        $successMessage = 'Form submitted successfully.';
        $values['password'] = '';
        $values['confirm_password'] = '';
    }
}

function h(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 8 - PHP Form Handling and Validation</title>
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #475569;
            --brand: #0f172a;
            --brandText: #e2e8f0;
            --accent: #0369a1;
            --error: #b91c1c;
            --ok: #166534;
        }

        * {
            box-sizing: border-box;
            font-family: Segoe UI, Arial, sans-serif;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, #e2e8f0 0%, var(--bg) 35%);
            color: var(--ink);
        }

        .exp-nav {
            list-style: none;
            padding: 10px 16px;
            margin: 0;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            background: var(--brand);
            border-bottom: 1px solid #1e293b;
        }

        .exp-nav a {
            color: var(--brandText);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .exp-nav a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .wrap {
            max-width: 860px;
            margin: 24px auto;
            padding: 0 16px 28px;
        }

        .card {
            background: var(--card);
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
            padding: 22px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 6px;
            font-size: 28px;
        }

        .subtitle {
            margin: 0 0 18px;
            color: var(--muted);
        }

        .success {
            background: #dcfce7;
            color: var(--ok);
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 14px;
            font-weight: 600;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #94a3b8;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            outline: 2px solid #93c5fd;
            border-color: #3b82f6;
        }

        .error {
            min-height: 16px;
            margin-top: 5px;
            color: var(--error);
            font-size: 12px;
            font-weight: 600;
        }

        .checkbox-wrap {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 3px;
        }

        .checkbox-wrap input {
            width: auto;
        }

        button {
            border: none;
            background: var(--accent);
            color: white;
            padding: 11px 16px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: #075985;
        }

        @media (max-width: 700px) {
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


<main class="wrap">
        <section class="card">
            <h1>Lab 8: PHP Form Handling and Validation</h1>
            <p class="subtitle">Server-side validation using PHP with sticky form values and inline error messages.</p>

            <?php if ($successMessage !== ''): ?>
                <div class="success"><?php echo h($successMessage); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="grid">
                    <div>
                        <label for="full_name">Full Name</label>
                        <input id="full_name" name="full_name" type="text" value="<?php echo h($values['full_name']); ?>" placeholder="Enter full name">
                        <div class="error"><?php echo h($errors['full_name'] ?? ''); ?></div>
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="<?php echo h($values['email']); ?>" placeholder="Enter email">
                        <div class="error"><?php echo h($errors['email'] ?? ''); ?></div>
                    </div>

                    <div>
                        <label for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="text" value="<?php echo h($values['phone']); ?>" placeholder="10-digit phone">
                        <div class="error"><?php echo h($errors['phone'] ?? ''); ?></div>
                    </div>

                    <div>
                        <label for="age">Age</label>
                        <input id="age" name="age" type="number" value="<?php echo h($values['age']); ?>" placeholder="17 to 60">
                        <div class="error"><?php echo h($errors['age'] ?? ''); ?></div>
                    </div>

                    <div>
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" value="" placeholder="Strong password">
                        <div class="error"><?php echo h($errors['password'] ?? ''); ?></div>
                    </div>

                    <div>
                        <label for="confirm_password">Confirm Password</label>
                        <input id="confirm_password" name="confirm_password" type="password" value="" placeholder="Re-enter password">
                        <div class="error"><?php echo h($errors['confirm_password'] ?? ''); ?></div>
                    </div>

                    <div class="full">
                        <label for="course">Course</label>
                        <select id="course" name="course">
                            <option value="">Select course</option>
                            <option value="Web Technology" <?php echo $values['course'] === 'Web Technology' ? 'selected' : ''; ?>>Web Technology</option>
                            <option value="DBMS" <?php echo $values['course'] === 'DBMS' ? 'selected' : ''; ?>>DBMS</option>
                            <option value="Operating Systems" <?php echo $values['course'] === 'Operating Systems' ? 'selected' : ''; ?>>Operating Systems</option>
                            <option value="Computer Networks" <?php echo $values['course'] === 'Computer Networks' ? 'selected' : ''; ?>>Computer Networks</option>
                        </select>
                        <div class="error"><?php echo h($errors['course'] ?? ''); ?></div>
                    </div>

                    <div class="full">
                        <label class="checkbox-wrap" for="terms">
                            <input id="terms" name="terms" type="checkbox" <?php echo empty($_POST) ? '' : (!empty($_POST['terms']) ? 'checked' : ''); ?>>
                            I accept the terms and conditions
                        </label>
                        <div class="error"><?php echo h($errors['terms'] ?? ''); ?></div>
                    </div>

                    <div class="full">
                        <button type="submit">Submit Form</button>
                    </div>
                </div>
            </form>
        </section>
    </main>
</body>
</html>






