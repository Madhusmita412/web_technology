CREATE DATABASE IF NOT EXISTS web_technology;
USE web_technology;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    brand VARCHAR(80) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
