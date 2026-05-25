# 🍲 Mie Ayam Wengi 57 - Premium Late-Night Culinary Order System

Welcome to the official repository of **Mie Ayam Wengi 57**, a premium web-based culinary ordering system designed specifically for a high-fidelity dining experience. Dressed in the premium **Warm Cozy Ivory** theme, this portal allows customers to order food directly from their tables using digital payment choices, and empowers administrators to manage menus and process order history.

---

## ✨ Features

### 🍜 Customer Portal (`index.php`)
* **Responsive Culinary Catalog**: Filterable menu tabs (All, Mie Ayam, Sides, Drinks) with a polished warm light glow design.
* **Dynamic Cart System**: Floating drawer cart utilizing Bootstrap Offcanvas with live quantity controllers.
* **Dual Payment Methods**: Supports seamless choice between **Cash / Tunai (Bayar di Kasir)** and **QRIS / E-Wallet** with dynamic instruction boxes on checkout.
* **Real-time Digital Invoice**: Detailed post-checkout order confirmation popup highlighting Meja number, total amount, and selected payment method.

### 🛡️ Administrator Portal (`admin/`)
* **Secure Portal**: Protected via session authentication checks (`admin/auth_check.php`).
* **Interactive Dashboard**: Highlights statistical summaries (Total Income, Pending/Completed orders, total registered menus).
* **Order History Management**: 
  * Live status transitions (Pending ⏳, Cooking 🔥, Completed ✅, Cancelled ❌).
  * Filter, search, and dynamic AJAX order detail popup showcasing client parameters and payment status.
  * **Accident-safe Order Deletion**: Secure trash controls coupled with double-confirm warning dialogs.
* **Full CRUD Menu Management**: 
  * Add, update availability, or delete menus.
  * Supports local file upload or remote online image URL imports.
  * Sleek cozy ivory input stylings (fully free from default blue background blocks).

---

## 🛠️ Technology Stack

* **Frontend**: HTML5, Vanilla CSS3 (Custom warm amber glowing layout tokens), JavaScript (ES6, LocalStorage integration), Bootstrap 5, Bootstrap Icons.
* **Backend**: PHP (Object-oriented PDO integration with strict exception handling and SQL Injection protection).
* **Database**: MySQL / MariaDB.

---

## 📦 Project Structure

```bash
MieAyamWengi_57/
├── admin/                     # Administrator Portal Views & Controllers
│   ├── auth_check.php         # Admin session security gatekeeper
│   ├── dashboard.php          # Admin statistics dashboard
│   ├── get-order-details.php  # AJAX controller rendering order breakdowns
│   ├── menu-manage.php        # CRUD interface for menu cards
│   └── orders.php             # Order History & status deletion console
├── assets/                    # Static Assets
│   ├── css/
│   │   └── style.css          # Custom Cozy Ivory styles
│   └── js/
│       └── cart.js            # Cart logic and dynamic success modals
├── config/                    # Global Configurations
│   └── db.php                 # Safe PDO database connection settings
├── uploads/                   # Local uploads directory for menu photos
├── database.sql               # Ready-to-import database structure and seed data
├── checkout-process.php       # POST API processing order transactions securely
├── index.php                  # Primary Customer Entry Point
├── login.php                  # Admin gate portal
└── logout.php                 # Session destroy script
```

---

## 🚀 Installation & Local Setup

Follow these simple steps to host Mie Ayam Wengi 57 on your local environment using a software bundle like **XAMPP** or **Laragon**:

### 1. Clone or Download the Project
Download this repository as a `.zip` file and extract it, or clone it using git:
```bash
git clone https://github.com/yourusername/MieAyamWengi_57.git
```

### 2. Move to Local Host Server Directory
Move the extracted folder `MieAyamWengi_57` into your local server's web root directory:
* For **XAMPP**: `C:\xampp\htdocs\MieAyamWengi_57`
* For **Laragon**: `C:\laragon\www\MieAyamWengi_57`

### 3. Start Apache & MySQL Server
Open your XAMPP Control Panel or Laragon console, and start the **Apache** and **MySQL** services.

### 4. Create and Import Database
1. Open your web browser and navigate to **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Click on **New** to create a new database.
3. Name the database **`mie_ayam_wengi_57`** and click **Create**.
4. Click on the newly created database, select the **Import** tab on the top menu.
5. Click **Choose File**, select the **`database.sql`** file from the root folder of your project, and click **Import** (or **Go**).

### 5. Configure Database Connection
Open `config/db.php` in a code editor and verify your database settings. The default configuration fits standard XAMPP localhost parameters:
```php
$host = 'localhost';
$dbname = 'mie_ayam_wengi_57';
$username = 'root';
$password = ''; // Leave blank for standard XAMPP setup
```

### 6. Run the Application!
Open your web browser and navigate to the application using the following URLs:
* **Customer Portal**: `http://localhost/MieAyamWengi_57/index.php`
* **Admin Portal Login**: `http://localhost/MieAyamWengi_57/login.php`

---

## 🔑 Default Administrator Credentials

To test the administrator dashboard features (Menu CRUD, Order History, Deletion), log in with the following seeded parameters:

* **Username**: `admin`
* **Password**: `wengi57`

*(You may update this credentials profile directly under the `admins` table inside your local phpMyAdmin panel).*

---

## 📄 License & Credits
Developed as part of the Web Design & Development course project. Images utilized as mock placeholders belong to their respective creators under the public domain Unsplash License. 

⭐ **If you like this project, feel free to give it a star!**
