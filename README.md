# CarolinianPOS

Web-based POS system for University of San Carlos as an academic project during Web Development 1.

## Stack
- HTML/CSS/JavaScript
- PHP 8+
- MySQL
- XAMPP

## Setup
1. Copy `carolinianpos` to `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import `database/carolinianpos.sql`.
4. Visit `http://localhost/carolinianpos/`.
5. Demo accounts:
   - `admin` / `password`
   - `manager` / `password`
   - `cashier` / `password`

The SQL seed uses PHP-compatible password hashes.

## MVP scope
Authentication, role-based access, POS checkout, printable receipts, products,
inventory adjustments, sales history, voids, reports, and admin user management.
