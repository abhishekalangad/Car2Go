# CAR2GO - Car Rental & Driver Booking Platform

A comprehensive car rental and driver booking management system built with PHP and MySQL.

## 🚗 Features

### For Users
- **Car Rental**: Browse and rent cars by location
- **Driver Booking**: Find and book experienced drivers
- **Service Booking**: Request car maintenance and repairs
- **Rating System**: Rate cars, drivers, and service centers
- **Profile Management**: Manage your account and bookings

### For Car Owners
- **List Your Car**: Add cars for rent with documents
- **Manage Bookings**: View and confirm rental requests
- **Earn Money**: Receive payments for car rentals

### For Drivers
- **Offer Services**: Register as a driver and set your rates
- **Accept Bookings**: View and confirm booking requests
- **Build Reputation**: Receive ratings from customers

### For Service Centers
- **List Services**: Offer various car services
- **Handle Requests**: Accept and manage service bookings
- **Specialized Services**: Provide custom quotes for repairs

### For Admin
- **Approve Listings**: Review and approve cars, drivers, and service centers
- **Manage Users**: Oversee all user accounts
- **Monitor Activity**: Track bookings and transactions

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for clean URLs)

## 🔧 Installation

### Step 1: Clone/Download the Project

```bash
git clone https://github.com/yourusername/car2go.git
cd car2go
```

### Step 2: Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE carservice;
```

2. Import the database schema:
```bash
mysql -u root -p carservice < database/carservice.sql
```

3. Update database credentials in `config/db_connect.php`:
```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'carservice');
```

### Step 3: Configure Upload Permissions

```bash
chmod 755 uploads/
chmod 755 uploads/cars/
chmod 755 uploads/drivers/
chmod 755 uploads/services/
chmod 755 uploads/documents/
```

### Step 4: Configure Web Server

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L,QSA]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Step 5: Set Up Admin Account

Default admin credentials:
- Email: `admin@gmail.com`
- Password: `123` (**Change this immediately!**)

To change admin password:
1. Login as admin
2. Go to Profile Settings
3. Update password

## 🔒 Security Features

### Implemented Security Measures
✅ **Password Hashing**: bcrypt with cost factor 12
✅ **SQL Injection Prevention**: Prepared statements
✅ **XSS Protection**: Input sanitization and output escaping
✅ **CSRF Protection**: Token-based validation
✅ **File Upload Validation**: MIME type and size checking
✅ **Session Security**: Secure cookies and regeneration
✅ **Brute Force Protection**: Login delay mechanism

### Security Best Practices
1. **Use HTTPS** in production
2. **Change default credentials** immediately
3. **Regular backups** of database
4. **Keep PHP updated** to latest stable version
5. **Monitor error logs** regularly

## 📁 Project Structure

```
CAR2GO/
├── config/              # Configuration files
│   ├── db_connect.php   # Database connection
│   └── constants.php    # Application constants
│
├── includes/            # Shared functions
│   ├── security.php     # Security functions
│   └── functions.php    # Helper functions
│
├── templates/           # Reusable templates
│   ├── header.php       # Header template
│   ├── footer.php       # Footer template
│   └── navbar.php       # Navigation bar
│
├── admin/              # Admin module
├── user/               # User module
├── driver/             # Driver module
├── service/            # Service center module
├── employee/           # Employee module
│
├── public/             # Public assets
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   ├── fonts/          # Web fonts
│   └── images/         # Static images
│
├── uploads/            # User uploads
│   ├── cars/           # Car photos
│   ├── drivers/        # Driver documents
│   ├── services/       # Service center files
│   └── documents/      # Other documents
│
├── database/           # Database files
│   └── carservice.sql  # Database schema
│
├── docs/               # Documentation
│
├── index.php           # Homepage
├── login.php           # Login page
├── register.php        # Registration page
│
├── .gitignore          # Git ignore file
├── .htaccess           # Apache configuration
├── README.md           # This file
└── INSTALL.md          # Installation guide
```

## 🗄️ Database Schema

### Core Tables
- `login` - User authentication
- `user_reg` - User profiles
- `driver_reg` - Driver profiles
- `service_reg` - Service center profiles
- `emp_reg` - Employee profiles
- `rent` - Car listings

### Booking Tables
- `bookcar` - Car rental bookings
- `bookdriver` - Driver bookings
- `bookservice` - Service bookings
- `bservice` - Specialized service requests

### Rating Tables
- `rating` - Car/owner ratings
- `drating` - Driver ratings
- `srating` - Service center ratings

## 🔑 API Endpoints (if implemented)

- `POST /api/login` - User login
- `POST /api/register` - User registration
- `GET /api/cars` - List available cars
- `POST /api/bookings` - Create booking
- `GET /api/bookings/{id}` - Get booking details

## 🧪 Testing

### Manual Testing Checklist
- [ ] User registration and login
- [ ] Car listing and search
- [ ] Booking creation and confirmation
- [ ] Rating and review submission
- [ ] File upload functionality
- [ ] Admin approval workflows

### Running Tests

```bash
# If you have PHPUnit set up
./vendor/bin/phpunit tests/
```

## 🐛 Troubleshooting

### Common Issues

**Issue**: Database connection error
**Solution**: Check database credentials in `config/db_connect.php`

**Issue**: File upload not working
**Solution**: Check folder permissions (755) for `uploads/` directory

**Issue**: Session not persisting
**Solution**: Ensure PHP session.save_path is writable

**Issue**: Images not displaying
**Solution**: Check file paths and image folder permissions

## 📊 Performance Optimization

### Recommended Optimizations
1. Enable OPcache in PHP
2. Use MySQL query caching
3. Implement CDN for static assets
4. Enable Gzip compression
5. Optimize database indexes

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Authors

- **Star Innovations** - Initial work
- **Contributors** - See CONTRIBUTORS.md

## 🙏 Acknowledgments

- Bootstrap for UI framework
- Font Awesome for icons
- jQuery for JavaScript functionality

## 📧 Support

For support, email support@car2go.com or open an issue in the repository.

## 🔄 Version History

### v2.0.0 (Current - Clean Version)
- ✅ Added password hashing
- ✅ Implemented prepared statements
- ✅ Added CSRF protection
- ✅ Enhanced file upload security
- ✅ Cleaned up codebase
- ✅ Improved project structure

### v1.0.0 (Original)
- ✅ Basic car rental functionality
- ✅ Driver booking system
- ✅ Service center integration
- ✅ Rating system

## 🚀 Roadmap

### Planned Features
- [ ] Email notifications
- [ ] SMS alerts
- [ ] Payment gateway integration
- [ ] Google Maps integration
- [ ] Mobile app (React Native)
- [ ] Real-time chat
- [ ] Advanced analytics
- [ ] Multi-language support

## ⚙️ Configuration

### Environment Variables (recommended)

Create a `.env` file:

```env
DB_HOST=localhost
DB_NAME=carservice
DB_USER=root
DB_PASS=your_password

APP_ENV=production
APP_DEBUG=false

UPLOAD_MAX_SIZE=5242880
SESSION_LIFETIME=3600
```

## 📱 Mobile Responsive

This application is fully responsive and works on:
- ✅ Desktop (1920px+)
- ✅ Laptop (1366px)
- ✅ Tablet (768px)
- ✅ Mobile (320px+)

## 🌐 Browser Support

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ⚠️ IE11 (limited support)

---

**Made with ❤️ by Star Innovations**