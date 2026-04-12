# Lab 9 - PHP + MySQL Product Details

This lab performs create, store, retrieve, and display operations for product details (based on Exercise 2) using PHP and MySQL.

## Files

- `index.php`: Product form + server-side validation + listing
- `db.php`: Database connection configuration
- `setup.sql`: MySQL schema/table setup script

## Setup

1. Create DB/table using MySQL:

```sql
SOURCE c:/web_tech_madhu/web_technology/lab9/setup.sql;
```

2. Update credentials in `db.php` if needed:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

3. Run PHP built-in server from the `web_technology` folder:

```powershell
cd c:\web_tech_madhu\web_technology
php -S localhost:8000
```

4. Open:

- `http://localhost:8000/lab9/index.php`

## Features

- Add new product details (create + store)
- Server-side validation
- Filter by category
- Retrieve products from MySQL
- Display products in table and cards
