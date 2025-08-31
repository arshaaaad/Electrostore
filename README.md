# ElectroStore - PHP E-commerce Website

A modern, full-stack e-commerce website built with PHP, MySQL, and Tailwind CSS for selling electronic items.

## Features

- **User Authentication**: Secure login/registration system
- **Product Management**: Browse, search, and filter products by category
- **Shopping Cart**: Add/remove items, update quantities
- **Order Management**: Complete checkout process and order history
- **Admin Panel**: Manage products, orders, and users
- **Responsive Design**: Modern UI built with Tailwind CSS
- **Search & Filtering**: Advanced product search and category filtering
- **Secure**: SQL injection protection, password hashing, session management

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Tailwind CSS
- **Icons**: Font Awesome
- **Framework**: Alpine.js for interactive components

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PHP extensions: PDO, PDO_MySQL, JSON

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd ElectroStore
```

### 2. Database Setup
1. Create a new MySQL database:
```sql
CREATE DATABASE electrostore;
```

2. Update database configuration in `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'electrostore');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Web Server Configuration
1. Point your web server's document root to the project directory
2. Ensure the `actions/` directory is accessible
3. Set proper file permissions

### 4. Access the Application
Open your browser and navigate to:
```
http://localhost/ElectroStore/
```

## Project Structure

```
ElectroStore/
├── actions/                 # Form action handlers
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   └── add-to-cart.php
├── config/                  # Configuration files
│   └── database.php
├── includes/                # Shared components
│   ├── header.php
│   ├── footer.php
│   ├── auth.php
│   └── functions.php
├── pages/                   # Page templates
│   ├── landing.php
│   ├── home.php
│   ├── login.php
│   ├── register.php
│   ├── products.php
│   └── ...
├── index.php               # Main entry point
└── README.md
```

## Usage

### For Customers
1. **Browse Products**: Visit the products page to see all available items
2. **Search & Filter**: Use search bar and category filters to find specific products
3. **Add to Cart**: Click "Add to Cart" on any product
4. **Checkout**: Review cart and proceed to checkout
5. **Track Orders**: View order history in your profile

### For Administrators
1. **Login**: Use admin credentials to access admin panel
2. **Manage Products**: Add, edit, or remove products
3. **View Orders**: Monitor customer orders and update status
4. **User Management**: Manage customer accounts

## Database Schema

The application automatically creates the following tables:
- `users` - User accounts and authentication
- `categories` - Product categories
- `products` - Product information and inventory
- `orders` - Customer orders
- `order_items` - Individual items in orders
- `cart_items` - Shopping cart contents
- `reviews` - Product reviews and ratings

## Security Features

- **Password Hashing**: Uses PHP's built-in `password_hash()` function
- **SQL Injection Protection**: Prepared statements with PDO
- **Session Security**: Secure session management
- **Input Validation**: Server-side validation for all user inputs
- **XSS Protection**: HTML escaping for user-generated content

## Customization

### Styling
- Modify `includes/header.php` and `includes/footer.php` for layout changes
- Update Tailwind CSS classes for styling modifications
- Customize color scheme by modifying Tailwind color classes

### Functionality
- Add new features in `includes/functions.php`
- Extend authentication in `includes/auth.php`
- Create new pages in the `pages/` directory

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Check database permissions

2. **Page Not Found**
   - Verify web server configuration
   - Check file permissions
   - Ensure `.htaccess` is properly configured (if using Apache)

3. **Session Issues**
   - Check PHP session configuration
   - Verify session directory permissions
   - Clear browser cookies if needed

### Debug Mode
Enable error reporting by adding this to the top of `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions:
- Create an issue in the repository
- Check the troubleshooting section
- Review the code comments for implementation details

## Changelog

### Version 1.0.0
- Initial release
- User authentication system
- Product catalog with search and filtering
- Shopping cart functionality
- Order management
- Admin panel
- Responsive design with Tailwind CSS

---

**Note**: This is a demonstration project. For production use, ensure proper security measures, SSL certificates, and regular security updates. 