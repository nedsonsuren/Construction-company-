# Flidoh Construction Contact Form System

A complete contact form system for construction companies with MySQL database integration and admin panel.

## Features

- **Contact Form**: Professional contact form with validation
- **Database Storage**: MySQL database with proper schema
- **Email Notifications**: Automatic email notifications for new messages
- **Admin Panel**: Web-based dashboard to view and manage messages
- **Status Tracking**: Mark messages as read/responded
- **Statistics**: Dashboard with message statistics
- **Responsive Design**: Works on desktop and mobile devices

## Setup Instructions

### 1. Prerequisites
- XAMPP installed and running
- Apache and MySQL services started
- PHP 7.0 or higher

### 2. Database Setup
1. Open your browser and go to: `http://localhost/setup.php`
2. This will automatically create the database and tables

### 3. File Structure
```
htdocs/
├── index.html          # Main website with contact form
├── script.js           # Form submission JavaScript
├── submit_form.php     # Form handler (saves to database)
├── get_messages.php    # API to retrieve messages
├── update_status.php   # API to update message status
├── admin.php           # Admin panel (rename to admin.html if needed)
├── debug.html          # Testing tool
├── db_config.php       # Database configuration
├── database_schema.sql # Database schema
├── setup.php           # Database setup script
└── README.md           # This file
```

## Usage

### For Visitors
1. Visit `http://localhost/index.html`
2. Fill out the contact form
3. Submit the form
4. Messages are saved to database and email notification is sent

### For Administrators
1. Visit `http://localhost/admin.php`
2. View all submitted messages
3. Mark messages as read or responded
4. See statistics (total, unread, today, responded)

### Testing
- Use `http://localhost/debug.html` to test form submissions
- Check browser console for any JavaScript errors

## Database Schema

```sql
CREATE DATABASE flidoh_construction;
USE flidoh_construction;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    service VARCHAR(100),
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    status ENUM('unread', 'read', 'responded') DEFAULT 'unread',
    INDEX idx_timestamp (timestamp),
    INDEX idx_status (status),
    INDEX idx_email (email)
);
```

## Configuration

### Database Configuration (db_config.php)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'flidoh_construction');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Email Configuration (submit_form.php)
Update the email settings in the submit_form.php file:
```php
$to = 'your-email@example.com';
$subject = 'New Contact Form Message';
```

## Security Notes

- The admin panel has no authentication (add login system for production)
- Database credentials are in plain text (use environment variables)
- No input sanitization beyond basic validation (enhance for production)
- IP addresses are stored (check privacy regulations)

## Troubleshooting

### Form not submitting
1. Check if Apache is running in XAMPP
2. Check browser console for JavaScript errors
3. Check PHP error logs in XAMPP

### Database connection errors
1. Ensure MySQL is running in XAMPP
2. Run setup.php to initialize database
3. Check database credentials in db_config.php

### Admin panel not loading
1. Ensure all PHP files are in htdocs directory
2. Check file permissions
3. Try accessing directly: `http://localhost/admin.php`

## Development

### Adding New Features
- Form validation: Update script.js and submit_form.php
- New database fields: Update database_schema.sql and related PHP files
- Admin features: Modify admin.php and add new API endpoints

### Testing
- Use debug.html for form testing
- Check browser developer tools for network requests
- Monitor PHP error logs in XAMPP

## Support

For issues or questions, check:
1. XAMPP control panel for service status
2. Browser developer tools for errors
3. PHP error logs in XAMPP installation directory

## License

This project is provided as-is for educational and commercial use.
developed by Simwaba Nedson 
