# 🚗 Nepal Ride Hub — Premium Vehicle Rental Platform

> A full-stack PHP vehicle rental web application for Nepal, featuring AI-powered support, real-time GPS tracking, document verification, and a complete admin management system.

**🌐 Live Website:** [http://nepalridehub.free.nf/](http://nepalridehub.free.nf/)

---

## 📌 Table of Contents

- [About the Project](#about-the-project)
- [Live Demo](#live-demo)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation (Local)](#installation-local)
- [Database Setup](#database-setup)
- [Default Login Credentials](#default-login-credentials)
- [Deployment (InfinityFree)](#deployment-infinityfree)
- [Screenshots](#screenshots)
- [License](#license)

---

## 📖 About the Project

**Nepal Ride Hub** is a vehicle rental management platform built for the Nepali market. It allows customers to browse and book vehicles (bikes, cars, SUVs, Mahindra Thar) online, upload their identity documents for verification, track their bookings, and get AI-powered support 24/7.

Administrators can manage vehicles, approve bookings, verify customer documents, reply to reviews, and monitor live GPS tracking of active rentals — all from a dedicated dashboard.

---

## 🌐 Live Demo

| Link | Description |
|------|-------------|
| [http://nepalridehub.free.nf/](http://nepalridehub.free.nf/) | Live Production Website |
| [http://nepalridehub.free.nf/login.php](http://nepalridehub.free.nf/login.php) | Login Page |
| [http://nepalridehub.free.nf/register.php](http://nepalridehub.free.nf/register.php) | Register Page |
| [http://nepalridehub.free.nf/vehicles.php](http://nepalridehub.free.nf/vehicles.php) | Browse Vehicles |

---

## ✨ Features

### 👤 Customer Features
- 📝 **Register & Login** with email/password
- 🚗 **Browse Fleet** — Filter by vehicle type, price, and availability
- 📅 **Book Vehicles** — Choose dates, purpose, pickup & dropoff location
- 📂 **Document Upload** — Citizenship, driving license, passport
- 🧾 **Customer Dashboard** — View booking history and status
- ⭐ **Reviews & Ratings** — Leave feedback on vehicles and services
- 🆘 **Emergency SOS** — Report incidents with GPS coordinates
- 💬 **AI Chatbot Assistant** — 24/7 support powered by Gemini AI

### 🔐 Admin Features
- 📊 **Admin Dashboard** — Overview of bookings, users, and revenue
- 🚘 **Vehicle CRUD** — Add, edit, delete, and update vehicle status
- 📋 **Manage Bookings** — Confirm, complete, or cancel bookings
- 👥 **Manage Users** — View and manage all registered customers
- ✅ **Document Verification** — Approve or reject user-submitted documents
- 💬 **Review Management** — Approve reviews and post admin replies
- 🗺️ **Live GPS Tracking** — Real-time vehicle location map
- 📞 **Emergency Contacts** — Manage SOS contacts

### 🤖 AI Chatbot
- Powered by **Google Gemini AI** with a robust local fallback
- Knows about bookings, pricing, branches, vehicles, documents, routes
- Aware of the **logged-in user's** name, role, and account details
- Works even when the API is unavailable (smart keyword matching)

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 8.x (PDO, Sessions) |
| **Database** | MySQL / MariaDB |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Icons** | Font Awesome 6 |
| **Fonts** | Google Fonts (Inter, Outfit) |
| **Maps** | Leaflet.js (GPS Tracking) |
| **AI** | Google Gemini API |
| **Local Server** | XAMPP (Apache + MySQL) |
| **Production Host** | InfinityFree |

---

## 📁 Project Structure

```
Nepal-Ride-Hub/
├── api/                        # API endpoints (JSON responses)
│   ├── auth.php                # Login, logout, registration
│   ├── manage_bookings.php     # Booking CRUD
│   ├── manage_vehicles.php     # Vehicle & GPS management
│   ├── manage_users.php        # User profile & document APIs
│   └── manage_reviews.php      # Review & feedback APIs
│
├── css/
│   └── style.css               # Main stylesheet
│
├── includes/
│   ├── header.php              # Global HTML header & navigation
│   ├── footer.php              # Global footer & scripts
│   ├── db_connect.php          # Database connection (production)
│   └── db_connect_production.php # Production DB config reference
│
├── js/
│   ├── support_helper.js       # AI Chatbot (Gemini + local fallback)
│   └── script.js               # General UI scripts
│
├── uploads/
│   ├── vehicles/               # Vehicle images
│   └── documents/              # User-uploaded documents
│
├── index.php                   # Homepage
├── login.php                   # Login page
├── register.php                # Registration page
├── vehicles.php                # Vehicle listing (customers only)
├── vehicle_details.php         # Vehicle detail & booking form
├── admin_dashboard.php         # Admin overview dashboard
├── manage_vehicles_ui.php      # Admin vehicle management
├── manage_bookings_ui.php      # Admin booking management
├── manage_users_ui.php         # Admin user management
├── manage_reviews.php          # Admin review management
├── customer_dashboard.php      # Customer booking dashboard
├── profile.php                 # Edit profile page
├── user_details.php            # User document details
├── reviews.php                 # Public reviews page
├── emergency.php               # Emergency SOS page
├── track_vehicles.php          # Admin live GPS tracking map
├── about.php                   # About us page
├── contact.php                 # Contact page
├── blog.php                    # Blog page
├── verify.php                  # Email/2FA verification
├── forgot_password.php         # Password reset request
├── reset_password.php          # Password reset form
├── setup_db.php                # Initial DB schema setup
├── fix_db.php                  # DB migration helper
├── database_export.sql         # Latest database dump
└── README.md                   # This file
```

---

## ⚙️ Installation (Local)

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (PHP 8.x + MySQL)
- A web browser

### Steps

1. **Clone or download** this repository into your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\Nepal-Ride-Hub\
   ```

2. **Start XAMPP** — Start both **Apache** and **MySQL** from the XAMPP Control Panel.

3. **Open the app** in your browser:
   ```
   http://localhost/Nepal-Ride-Hub/
   ```

---

## 🗄️ Database Setup

1. Open **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **"New"** and create a database named:
   ```
   nepal_ride_hub
   ```
3. Select the database, go to the **Import** tab, and upload:
   ```
   database_export.sql
   ```
4. The database configuration file is at:
   ```
   includes/db_connect.php
   ```
   Default local settings (no changes needed for XAMPP):
   ```php
   $host     = 'localhost';
   $dbname   = 'nepal_ride_hub';
   $username = 'root';
   $password = '';
   ```

---

## 🔑 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@nepalridehub.com | `admin123` |
| **Customer** | samshedkhan741@gmail.com | *(set during registration)* |

> ⚠️ **Security Note:** Change the admin password immediately after first login in production.

---

## 🚀 Deployment (InfinityFree)

The live version is deployed at: **[http://nepalridehub.free.nf/](http://nepalridehub.free.nf/)**

### Production Database Config (`includes/db_connect.php`)

```php
$host     = 'sql111.infinityfree.com';
$dbname   = 'if0_41924868_nepal_ride_hub';
$username = 'if0_41924868';
$password = 'SaHID786';
```

### Deployment Steps

1. **Export** local database using `mysqldump`:
   ```
   mysqldump -u root nepal_ride_hub > database_export.sql
   ```
2. **Update** `includes/db_connect.php` with InfinityFree credentials.
3. **Upload** all project files to the `htdocs` folder via InfinityFree File Manager.
4. **Import** `database_export.sql` via InfinityFree phpMyAdmin.
5. Visit your live URL and verify the site is working.

---

## 📞 Contact & Support

| Field | Details |
|-------|---------|
| **Website** | [http://nepalridehub.free.nf/](http://nepalridehub.free.nf/) |
| **Email** | support@nepalridehub.com |
| **Phone** | +977 9706421709 |
| **Location** | Dillibazar, Kathmandu, Nepal |
| **Branches** | Kathmandu, Pokhara, Janakpur, Lahan, Butwal, Dang, Palpa, Jhapa |

---

## 📄 License

This project is developed for educational and portfolio purposes.  
© 2026 Nepal Ride Hub. All rights reserved.
