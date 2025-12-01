# Waste Collection Web Application

A fully functional waste collection management system built with PHP, MySQL, HTML, CSS, JavaScript, and Bootstrap. This application allows users to book waste pickups, track their status, and provides admin and staff panels for managing the entire waste collection process.

## Features

### User Features
- User registration and login
- Book waste pickup requests
- Track pickup status using tracking ID
- Upload waste images
- Dark mode support

### Admin Features
- Admin dashboard with statistics
- Manage all pickup requests
- Assign staff to pickups
- Update pickup status
- CRUD operations for staff management
- Export pickup data to CSV
- Search and filter pickups

### Staff Features
- Staff login
- View assigned pickups
- Update pickup status (In-Progress, Completed)

## Technology Stack

- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3
- **Security**: Prepared statements, password hashing, session management

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- Apache web server (XAMPP/WAMP recommended)
- Web browser with JavaScript enabled

## Installation & Setup

### Step 1: Database Setup

1. Open phpMyAdmin (if using XAMPP/WAMP) or your MySQL client
2. Create a new database (or use existing one)
3. Import the `database.sql` file:
   - In phpMyAdmin: Select your database → Import → Choose `database.sql` → Go
   - Or via command line: `mysql -u root -p database_name < database.sql`

### Step 2: Configure Database Connection

1. Open `config.php` in the root directory
2. Update the database credentials:

```php
define('DB_HOST', 'localhost');      // Usually 'localhost'
define('DB_NAME', 'waste_collection_db');  // Your database name
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password (empty for XAMPP default)
```

### Step 3: Set Up File Permissions

1. Ensure the `assets/images/uploads/` directory exists and is writable:
   - On Windows: Usually no action needed
   - On Linux/Mac: `chmod 755 assets/images/uploads/`

### Step 4: Set Up Admin Password

**IMPORTANT**: After importing the database, you must set up the admin password hash:

1. Open `http://localhost/your-project-folder/setup_admin.php` in your browser
2. This will generate and set the correct password hash for the admin account
3. You should see a success message with the generated hash
4. **Delete `setup_admin.php` after use for security**

**Alternative**: If you prefer to generate the hash manually:
- Run: `php generate_hash.php` (or open it in browser)
- Copy the generated hash
- Update the admin table: `UPDATE admin SET password = 'your_hash_here' WHERE username = 'admin';`

### Step 5: Access the Application

1. Start your web server (Apache) and MySQL from XAMPP/WAMP control panel
2. Open your web browser
3. Navigate to: `http://localhost/your-project-folder/` or `http://localhost/your-project-folder/index.php`

## Default Login Credentials

### Admin Panel
- **URL**: `http://localhost/your-project-folder/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

**⚠️ IMPORTANT**: 
- You MUST run `setup_admin.php` first to set the correct password hash
- Change the default admin password immediately after first login for security!

### Staff Panel
- **URL**: `http://localhost/your-project-folder/staff/login.php`
- Staff accounts need to be created by admin first

### User Registration
- Users can register at: `http://localhost/your-project-folder/register.php`

## Project Structure

```
waste-collection-app/
│
├── config.php                 # Database configuration
├── config.sample.php          # Sample configuration file
├── index.php                  # Homepage
├── register.php               # User registration
├── login.php                  # User login
├── book_pickup.php           # Book waste pickup
├── track.php                 # Track pickup status
├── history.php               # User pickup history
├── profile.php               # User profile management
├── logout.php                # Logout
├── database.sql              # Database schema
│
├── admin/                    # Admin panel
│   ├── login.php
│   ├── dashboard.php
│   ├── pickups.php
│   ├── edit_pickup.php
│   ├── manage_wards.php      # Manage dynamic wards
│   ├── monthly_report.php    # Generate reports
│   ├── staff.php
│   └── logout.php
│
├── staff/                    # Staff panel
│   ├── login.php
│   ├── dashboard.php
│   ├── update_status.php
│   └── logout.php
│
├── includes/                 # Reusable components
│   ├── chatbot.php           # Chatbot widget
│   └── report_helpers.php    # Helper functions for reports
│
├── api/                      # API endpoints
│   └── chat.php              # Chatbot API
│
└── assets/                   # Static assets
    ├── css/
    │   └── style.css         # Main stylesheet with dark mode
    ├── js/
    │   └── theme.js          # Dark mode toggle functionality
    └── images/
        ├── chatbot_logo.png  # Chatbot branding
        └── uploads/          # Uploaded waste images
```

## Database Schema

The application uses the following tables:

- **users**: Registered user accounts
- **admin**: Admin accounts
- **staff**: Staff member accounts
- **pickup_requests**: Waste pickup requests with tracking

See `database.sql` for complete schema details.

## Features in Detail

### Dark Mode
- Global dark mode toggle available on all pages
- Preference saved in browser localStorage
- Smooth transitions between light and dark themes
- CSS variables used for easy theme customization

### Security Features
- Password hashing using `password_hash()` and `password_verify()`
- Prepared statements for all database queries (prevents SQL injection)
- Session-based authentication
- Access control for admin and staff panels

### File Upload
- Waste images can be uploaded when booking pickup
- Supported formats: JPG, JPEG, PNG
- Files stored in `assets/images/uploads/`
- File names include tracking ID for easy identification

### Tracking System
- Unique tracking ID generated for each pickup (format: WC-YYYYMMDD-RANDOM)
- Public tracking page accessible without login
- Real-time status updates

## Troubleshooting

### Database Connection Error
- Verify database credentials in `config.php`
- Ensure MySQL service is running
- Check if database exists and is imported correctly

### File Upload Not Working
- Check `assets/images/uploads/` directory exists
- Verify directory permissions (should be writable)
- Check PHP `upload_max_filesize` and `post_max_size` settings

### Session Issues
- Ensure PHP sessions are enabled
- Check `session.save_path` in php.ini
- Clear browser cookies if experiencing login issues

### Dark Mode Not Working
- Ensure JavaScript is enabled in browser
- Check browser console for errors
- Verify `assets/js/theme.js` is loaded correctly

## Customization

### Changing Colors
Edit CSS variables in `assets/css/style.css`:

```css
:root {
    --primary-color: #007bff;  /* Change primary color */
    --bg-color: #ffffff;       /* Change background */
    /* ... more variables */
}
```

### Adding Waste Types
Edit the dropdown in `book_pickup.php`:

```php
<option value="New Type">New Waste Type</option>
```

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Verify all setup steps are completed
3. Check PHP error logs for detailed error messages
4. Ensure all file paths are correct

## License

This project is provided as-is for educational and development purposes.

## Notes

- The default admin password should be changed immediately after setup
- For production use, implement additional security measures:
  - HTTPS/SSL certificates
  - Rate limiting
  - Input sanitization beyond what's implemented
  - Regular security updates
  - Backup strategy for database

---

**Version**: 1.0  
**Last Updated**: 2024

