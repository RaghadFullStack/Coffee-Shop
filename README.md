ShAGHAF Coffee Shop

A comprehensive e-commerce web application for a coffee machine store built with PHP, MySQL, HTML, CSS, and JavaScript.

Overview

ShAGHAF Coffee Shop is a full-featured e-commerce website for selling coffee machines. The application allows customers to browse products, add items to their cart, and manage favorites. Admin users can manage the product catalog by adding, editing, and deleting products.

Features

Customer Features

Browse coffee machines with detailed information
Add products to shopping cart
Save favorite products
View product details
Responsive design for all devices
Admin Features

Admin login system
Add new products with images
Edit existing product information
Delete products from catalog
Manage entire product inventory
View products in table format
Technical Features

Secure database connections with error handling
Session-based user authentication
Responsive web design
Consistent styling across all pages
Proper error handling and validation
Technologies Used

Frontend: HTML5, CSS3, JavaScript
Backend: PHP 7+
Database: MySQL
Server: Apache (XAMPP recommended)
Additional: AJAX for dynamic updates
Installation

Prerequisites

Install XAMPP or any LAMP/WAMP stack
Ensure Apache and MySQL services are running
Setup Steps

# Clone or download the repository
git clone <repository-url>

# Copy files to your web server directory
# For XAMPP: Copy to xampp/htdocs/master-coffee-shop
File Permissions

Ensure the uploads/ directory is writable for product image uploads
Web server should have read/write access to the project directory
Database Setup

Create Database

Open phpMyAdmin or MySQL command line
Execute the SQL commands in setup_database.sql:
CREATE DATABASE IF NOT EXISTS coffee_machine;
USE coffee_machine;
Create Tables

The required table will be created automatically by running the setup script
Product table structure:
id (INT, AUTO_INCREMENT, PRIMARY KEY)
name (VARCHAR)
Price (DECIMAL)
size (VARCHAR)
Color (VARCHAR)
ProductOverview (TEXT)
Vimage (VARCHAR)
created_at (TIMESTAMP)
Admin Access

Login Credentials

Username admin
Password password
Admin Functions

Navigate to Admin Login from the main menu
After login, admin features become available:
Add Products: Create new product entries
Manage Products: Edit or delete existing products
View detailed product tables
Security Notes

Change the default admin password in production
Keep admin credentials secure
Always logout after admin sessions
