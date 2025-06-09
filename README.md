# SecureHousing - Student Accommodation Platform

A comprehensive secure student accommodation listing system built with Laravel 12, Livewire, and Jetstream, featuring robust identity verification, fraud protection, and modern UX design.

## 🏠 Features

### Security & Verification
- **Identity Verification**: Integration with Jumio/Onfido for biometric ID verification
- **Student Verification**: University enrollment validation
- **Trust Score System**: Dynamic landlord and property trust ratings
- **Fraud Detection**: Automated scam phrase detection in messages
- **Escrow Protection**: Secure payment processing with deposit protection

### User Experience
- **Modern Design**: Tailwind CSS with custom color palette and typography
- **Responsive Interface**: Mobile-first design with desktop enhancements
- **Real-time Search**: Live property filtering and search
- **Interactive Maps**: Location-based property discovery
- **Secure Messaging**: Encrypted communication between users

### Property Management
- **Comprehensive Listings**: Detailed property information with image galleries
- **Verification Badges**: Visual trust indicators for verified properties
- **Advanced Filtering**: Search by location, price, amenities, and verification status
- **Booking System**: Complete rental workflow with contract management

## 🚀 Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- Redis (optional, for caching)

### Step 1: Clone Repository
```bash
Download the Zip folder and extract to a desired location
open the extracted folder with CMD or Terminal
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE secure_housing;"

# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=secure_housing
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### Step 5: Storage Configuration
```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Step 6: Third-Party Service Configuration

#### Jumio Verification Setup
```env
JUMIO_API_URL=https://netverify.com/api/netverify/v2
JUMIO_API_TOKEN=your_jumio_api_token
JUMIO_API_SECRET=your_jumio_api_secret
```

#### Stripe Payment Setup
```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
```

#### Pusher Real-time Setup (Optional)
```env
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=your_pusher_cluster
```

### Step 7: Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### Step 8: Start Development Server
```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server
npm run dev
```

Visit `http://localhost:8000` to access the application.

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### API Testing
The application includes comprehensive API endpoints for testing:

```bash
# Test property endpoints
curl -X GET http://localhost:8000/api/properties
curl -X GET http://localhost:8000/api/properties/1

# Test verification endpoints (requires authentication)
curl -X POST http://localhost:8000/api/verification/identity \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"country":"USA","document_type":"PASSPORT"}'
```

### Selenium UI Testing
1. Install Selenium IDE browser extension
2. Import test files from `tests/selenium/`
3. Configure base URL to `http://localhost:8000`
4. Run test suites:
   - Property Search Test
   - Verification Flow Test
   - Booking Flow Test

## 📁 Project Structure

```
secure-housing/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/              # API controllers
│   │   └── Web/              # Web controllers
│   ├── Livewire/             # Livewire components
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── Policies/             # Authorization policies
├── database/
│   ├── migrations/           # Database migrations
│   ├── factories/            # Model factories
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   ├── css/                  # Stylesheets
│   └── js/                   # JavaScript files
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
├── tests/
│   ├── Feature/              # Feature tests
│   ├── Unit/                 # Unit tests
│   └── selenium/             # Selenium UI tests
└── config/                   # Configuration files
```

## 🔧 Configuration

### User Roles
The system supports three user types:
- **Students**: Can search and book properties
- **Landlords**: Can list and manage properties
- **Admins**: Full system access

### Verification Levels
- **Unverified**: Basic account access
- **Partial**: Identity verified only
- **Verified**: Both identity and student/landlord status verified

### Trust Score Calculation
- Identity Verification: +0.5
- Student/Landlord Verification: +0.3
- Additional Verifications: +0.2

## 🔐 Security Features

### Data Protection
- All sensitive data encrypted at rest
- HTTPS enforced in production
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM

### Verification Security
- Biometric identity verification via Jumio
- Document authenticity validation
- Real-time fraud detection
- Secure file upload with validation

### Payment Security
- PCI DSS compliant payment processing
- Escrow service integration
- Automated fraud monitoring
- Secure webhook handling

## 🚀 Deployment

### Production Environment Setup
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Configure production database
# Set up SSL certificates
# Configure web server (Nginx/Apache)
# Set up process manager (Supervisor)
# Configure queue workers
```

### Performance Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

## 📊 Monitoring & Analytics

### Application Monitoring
- Laravel Telescope for debugging
- Log monitoring via Laravel Log Viewer
- Performance monitoring with Laravel Debugbar

### Business Analytics
- Property view tracking
- Conversion rate monitoring
- User verification completion rates
- Fraud attempt detection and reporting

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Update documentation for API changes
- Use conventional commit messages

## 📝 API Documentation

### Authentication
All protected endpoints require Bearer token authentication:
```bash
Authorization: Bearer YOUR_API_TOKEN
```

### Property Endpoints
- `GET /api/properties` - List properties with filtering
- `GET /api/properties/{id}` - Get property details
- `POST /api/properties` - Create property (landlords only)
- `PUT /api/properties/{id}` - Update property
- `DELETE /api/properties/{id}` - Delete property

### Verification Endpoints
- `POST /api/verification/identity` - Initiate identity verification
- `POST /api/verification/student` - Submit student verification
- `GET /api/verification/status` - Get verification status
- `POST /api/verification/callback` - Webhook for verification updates

### Booking Endpoints
- `GET /api/bookings` - List user bookings
- `POST /api/bookings` - Create booking
- `PUT /api/bookings/{id}/confirm` - Confirm booking
- `PUT /api/bookings/{id}/cancel` - Cancel booking

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Error
```bash
# Check database credentials in .env
# Ensure MySQL service is running
sudo service mysql start

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

#### File Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

#### Verification Service Errors
```bash
# Check API credentials in .env
# Verify webhook URLs are accessible
# Check logs for detailed error messages
tail -f storage/logs/laravel.log
```

## 📞 Support

For technical support or questions:
- Email: ---
- Documentation: ---
- Issues: ---
## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel Framework Team
- Livewire Team
- Tailwind CSS Team
- Jumio for verification services
- All contributors and testers

---

**Built with ❤️ for student safety and security**
