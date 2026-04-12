# Lab 10 - Sessions and Cookies (Flipmart Style)

This lab demonstrates storing and retrieving information across multiple pages using PHP sessions and cookies, inspired by the Lab 2 e-commerce idea.

## Pages

- `index.php`: set/read customer name with cookie; shows session info
- `products.php`: browse products, set recently viewed product cookie, add products to session cart
- `cart.php`: retrieve and display cart from session; update qty/remove/clear items
- `common.php`: shared session helpers
- `data.php`: static product catalog

## What Uses Session

- `$_SESSION['visit_count']`
- `$_SESSION['customer_name']`
- `$_SESSION['cart']`

## What Uses Cookies

- `customer_name` (remembered for 30 days)
- `recent_product_id` (remembered for 30 days)

## Run

```powershell
cd c:\web_tech_madhu\web_technology
php -S localhost:8000
```

Open:

- `http://localhost:8000/lab10/index.php`
