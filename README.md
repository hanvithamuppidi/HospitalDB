# Hospital Management System Setup Guide

This guide will walk you through the steps to set up the Hospital Management System using XAMPP and phpMyAdmin. This was a group project for our Database Systems class. 

## Installation Steps:

1. **Download XAMPP:**
    - Download and install XAMPP from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html).

2. **Clone or Download Repository:**
    - Clone or download the Hospital Management System repository.

3. **Move Files:**
    - Under the XAMPP directory, locate the `htdocs` folder.
    - Copy the `hospitalDB` directory from the downloaded repository into the `htdocs` folder.

## Setting Up Database:

1. **Start XAMPP Servers:**
    - Start the Apache and MySQL servers using XAMPP control panel.

2. **Access phpMyAdmin:**
    - Open your web browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).

3. **Create Database:**
    - Click on the "New" button on the left-hand side to create a new database.
    - Name the new database as `hospitaldb`.

4. **Import SQL File:**
    - Select the newly created `hospitaldb` from the left sidebar.
    - Click on the "Import" tab.
    - Choose the file option and navigate to the `hospitaldb.sql` file provided in the `hospitalDB` directory.
    - Click "Go" to import the SQL file.

## Testing Login:

1. **Access Login Page:**
    - Open your web browser and go to [http://localhost/hospitaldb/login.php](http://localhost/hospitaldb/login.php).

2. **Login Credentials:**
    - Use the following credentials to log in:
        - Username: `admin1`
        - Password: `password`

3. **Verify Login:**
    - After successful login, you should be redirected to `dashboard.php`.

## Usage:

- You can now use the Hospital Management System as needed.
