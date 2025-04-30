Coffee Shop PHP Project
A dynamic e-commerce web application for a coffee shop, built with PHP and MySQL. This project demonstrates back-end development skills, including database management, server-side logic, and modular code organization. Developed as part of the ITI 9-Month Open-Source Program, it supports features like user management, product catalog, and order processing.
Table of Contents

Features
Technologies
File Structure
Setup Instructions
Database Setup
Usage
Contributing
License

Features

User registration and authentication.
Product catalog with coffee items and categories.
Order management for customers.
Responsive front-end with CSS and JavaScript.
Modular PHP codebase with reusable templates and helper functions.
Database migrations and seeders for easy setup and testing.

Technologies

Back-End: PHP, MySQL
Front-End: HTML, CSS, JavaScript
Database: MySQL (schema migrations, seeders)
Tools: Git, Composer (optional for dependency management)

File Structure
alya coffee-shop/
├── database/                    # SQL scripts for setup
│   ├── migrations/             # Database schema changes
│   │   ├── 001_create_users_table.sql
│   │   ├── 002_create_products_table.sql
│   │   ├── 003_create_orders_table.sql
│   ├── seeders/               # Initial test data
│   │   ├── users_seeder.sql
│   │   ├── products_seeder.sql
│   ├── backup/                # Database backups
│   ├── db_connection.php      # MySQL connection
│   ├── db_init.sql            # Database creation script
├── assets/                     # Public-facing files
│   ├── css/                   # CSS styles
│   ├── js/                    # JavaScript files
│   ├── images/                # Image assets
├── includes/                   # Reusable HTML templates
│   ├── header.php             # Navigation and header
│   ├── footer.php             # Footer
├── config.php                  # Database configuration
├── functions.php               # Helper functions
├── index.php                   # Main entry point
├── .gitignore                  # Files to ignore in Git
├── README.md                   # Project documentation

Setup Instructions

Clone the Repository:
git clone https://github.com/HamdySalah/Coffee-PHP-Project.git
cd Coffee-PHP-Project


Set Up Web Server:

Use a local server like XAMPP, WAMP, or MAMP.
Place the project in the server’s root directory (e.g., htdocs for XAMPP).
Ensure PHP and MySQL are installed and running.


Configure Database:

Update config.php with your MySQL credentials:define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'coffee_shop');




Install Dependencies (if applicable):

If using Composer for dependencies, run:composer install





Database Setup

Create the Database:

Log in to MySQL:mysql -u your_username -p


Run the database creation script:SOURCE database/db_init.sql;




Run Migrations:

Apply schema migrations in order:SOURCE database/migrations/001_create_users_table.sql;
SOURCE database/migrations/002_create_products_table.sql;
SOURCE database/migrations/003_create_orders_table.sql;




Seed the Database:

Populate with test data:SOURCE database/seeders/users_seeder.sql;
SOURCE database/seeders/products_seeder.sql;




Verify Connection:

Ensure db_connection.php is correctly configured and test the connection.



Usage

Access the application via your web server (e.g., http://localhost/Coffee-PHP-Project).
Register as a user or log in with seeded credentials (check users_seeder.sql).
Browse the product catalog, add items to the cart, and place orders.
Admin features (if implemented) are accessible via specific routes or credentials.

Contributing
Contributions are welcome! To contribute:

Fork the repository.
Create a feature branch (git checkout -b feature-name).
Commit changes (git commit -m "Add feature").
Push to the branch (git push origin feature-name).
Open a pull request.

Please follow coding standards and include tests where applicable.
License
This project is licensed under the MIT License. See the LICENSE file for details.
