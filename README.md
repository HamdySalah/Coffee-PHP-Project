# Coffee-PHP-Project
# File Structure
coffee-shop/
│── public/              # Public-facing files
│   ├── css/             # CSS styles
│   ├── js/              # JavaScript files
│   ├── images/          # Image assets
│   ├── index.php        # Main entry point
│── src/                 # Core PHP logic
│   ├── config.php       # Database configuration
│   ├── functions.php    # Helper functions
│   ├── templates/       # Reusable HTML templates (header, footer)
│── database/            # SQL scripts for setup
│── .gitignore           # Files to ignore in Git
│── README.md            # Documentation

# Database/
coffee-shop/
│── database/            
│   ├── migrations/        # SQL scripts for database schema changes
│   │   ├── 001_create_users_table.sql
│   │   ├── 002_create_products_table.sql
│   │   ├── 003_create_orders_table.sql
│   ├── seeders/           # SQL scripts to insert initial test data
│   │   ├── users_seeder.sql
│   │   ├── products_seeder.sql
│   ├── backup/            # Backup copies of the database
│   ├── db_connection.php  # PHP file to connect to MySQL
│   ├── db_init.sql        # Main script to create the database
