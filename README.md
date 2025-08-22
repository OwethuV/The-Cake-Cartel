# The Cake Cartel

Welcome to **The Cake Cartel**, your go-to online shop for delicious baked goods! We specialize in sweet treats, including cakes and other delightful desserts. This README file will guide you through the setup and usage of our e-commerce website.

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Setup Instructions](#setup-instructions)
- [Configuration](#configuration)
- [Usage](#usage)
- [License](#license)

## Features

- User-friendly interface for browsing and purchasing baked goods.
- Secure user authentication and account management.
- Email notifications for order confirmations and updates.
- Responsive design for mobile and desktop users.
- Pre-populated database with products for easy setup.

## Technologies Used

- **PHP**: Server-side scripting language for dynamic content.
- **Composer**: Dependency manager for PHP to manage libraries.
- **PHPMailer**: Library for sending emails securely.
- **HTML/CSS/JavaScript**: Frontend technologies for building the user interface.
- **MySQL**: Database management system for storing product and user data.

## Setup Instructions

To set up **The Cake Cartel** on your local machine, follow these steps:

### 1. Clone the Repository:
   ```bash
   git clone https://github.com/OwethuV//The-Cake-Cartel.git
   ```
   ```bash
   cd the-cake-cartel
   ```
### 2.Install dependencies:
Make sure you have Composer installed then run the following command to install the required PHP packages:

```bash
composer install
```

### 3. Import the Database
Before running the system, you need to import the provided MySQL database (in the includes/ directory):

1. Open **phpMyAdmin**, **MySQL Workbench**, or any MySQL client.

2. Create a new database named:
```sql
dessertecommerce
```
3. Import the provided SQL dump file
   If you're using the terminal:
```bash
mysql -u root -p dessertecommerce < dessertecommerce.sql
```

4. Configure the Environment (.env)
- Create a `.env` file based on the provided `.env.example` file.
- Update the `.env` file with your database credentials and email configuration:
```env
DB_HOST=localhost
DB_NAME=dessertecommerce
DB_USER=your_database_user
DB_PASS=your_database_password

SMTP_HOST=smtp.your-email-provider.com
SMTP_USER=your_email@example.com
SMTP_PASS=your_email_password
SMTP_PORT=587

PAYFAST_MERCHANT_ID=your_merchant_id
PAYFAST_MERCHANT_KEY=your_merchant key
PAYFAST_RETURN_URL=http://localhost/return.php
PAYFAST_CANCEL_URL=http://localhost/cancel.php
PAYFAST_NOTIFY_URL=http://localhost/notify.php
```

### 5. Configuration
To run this application, you need a server environment. You can use:
- **XAMPP**: A free and open-source cross-platform web server solution stack package. (What we used)
- **MAMP**: A free, local server environment that can be installed under macOS and Windows with just a few clicks.
- **LAMP**: A Linux-based web server solution stack.

Make sure to start the server and place the project files in the appropriate directory (e.g., `htdocs` for XAMPP).

## 6. Usage
1. Start your local server (e.g., XAMPP, MAMP).

2. Open your web browser and navigate to `http://localhost/the-cake-cartel`.

3. Register yourself (or anyone) in the database to unlock more features.

4. Browse through our selection of baked goods, add items to your cart, and proceed to checkout

5. You will receive email notifications for order confirmations and updates.
   
## Team
- Sihle Sawula
- Leah Moosa
- Hanaa Richards
- Owethu Valantiya

## 7. License
This project is for educational purposes only.
