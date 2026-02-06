# Canadaim - Comprehensive README

![Canadaim](public/images/logo.png)

**Canadaim** is a modern, SEO-optimized web application for Canadian immigration information and services. Built with Symfony 6.1, it provides comprehensive guidance for immigration, work permits, studies, and sponsorship programs.

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer 2.0+
- Node.js 18+ (optional, for asset compilation)

### Installation

```bash
# Clone repository
git clone https://github.com/ChrisBOUONGOU/canadaim.git
cd canadaim

# Install dependencies
composer install

# Configure environment
cp .env .env.local
# Edit .env.local with your database credentials

# Create database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Load fixtures (optional)
php bin/console doctrine:fixtures:load

# Start development server
php bin/console server:run
```

Visit `http://localhost:8000/`

---

## 📋 Project Structure

```
canadaim/
├── bin/                          # Executable scripts
├── config/                       # Symfony configuration
│   ├── packages/                # Package-specific config
│   ├── bundles.php              # Bundle registration
│   └── services.yaml            # Service definitions
├── migrations/                  # Database migrations
├── public/                      # Web root (Apache/Nginx document root)
│   ├── .htaccess               # Apache configuration
│   └── index.php               # Application entry point
├── src/
│   ├── Controller/             # HTTP controllers
│   ├── Entity/                 # Doctrine entities
│   ├── Form/                   # Symfony form types
│   ├── Repository/             # Database repositories
│   ├── Service/                # Business logic services
│   └── Kernel.php              # Kernel configuration
├── templates/                  # Twig templates
│   ├── base.html.twig         # Base template
│   ├── pages/                 # Page templates
│   ├── forms/                 # Form templates
│   └── emails/                # Email templates
├── tests/                      # Test suite
│   ├── Entity/                # Entity tests
│   ├── Form/                  # Form type tests
│   ├── Controller/            # Controller tests
│   ├── Service/               # Service tests
│   └── Repository/            # Repository tests
├── translations/              # i18n translations
├── var/
│   ├── cache/                # Application cache
│   └── log/                  # Application logs
├── vendor/                   # Composer dependencies
├── .env*                     # Environment configuration
├── composer.json             # PHP dependencies
├── docker-compose.yml        # Docker Compose configuration
├── Dockerfile                # Docker image definition
└── phpunit.xml.dist          # PHPUnit configuration
```

---

## 🎯 Core Features

### 1. Immigration Information
- Comprehensive guides for immigration programs
- Eligibility requirements and documentation
- Processing timelines and fees
- Points calculator

### 2. Work Permits
- Employer-sponsored work permits
- International mobility programs
- Work permit types and requirements
- Application procedures

### 3. Study Permits
- Study program requirements
- Designated learning institutions
- Financial requirements
- Post-graduation work permits

### 4. Sponsorship Programs
- Family sponsorship
- Spousal/common-law sponsorships
- Dependent children sponsorship
- Financial obligations

### 5. SEO Optimization
- Structured data (Schema.org)
- Meta tags and Open Graph
- Sitemap generation
- XML sitemaps
- Mobile responsiveness

### 6. Contact Management
- Contact form with validation
- Email notifications
- Message storage
- Admin dashboard

### 7. Search Functionality
- Full-text search
- Filtering and sorting
- Search analytics

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php bin/phpunit

# Run specific test suite
php bin/phpunit tests/Entity/
php bin/phpunit tests/Form/
php bin/phpunit tests/Service/
php bin/phpunit tests/Repository/

# Run with coverage report
php bin/phpunit --coverage-html=var/coverage

# Run specific test class
php bin/phpunit tests/Entity/ContactMessageTest.php

# Run specific test method
php bin/phpunit tests/Entity/ContactMessageTest.php::testContactMessageCreation
```

### Test Coverage

- **Entity Layer**: ✅ 22/22 tests (40 assertions)
- **Form Layer**: ✅ 16/16 tests (30 assertions)
- **Service Layer**: ✅ 14/14 tests (81 assertions)
- **Repository Layer**: ✅ 13/13 tests (20 assertions)
- **Total**: 65 tests, 151 assertions

See [TESTING.md](TESTING.md) for comprehensive testing guide.

---

## 🚀 Deployment

### Local Development

```bash
# Start development server
php bin/console server:run

# Or use Docker
docker-compose up -d
```

### Staging Deployment

See [DEPLOYMENT_MAINTENANCE_GUIDE.md](DEPLOYMENT_MAINTENANCE_GUIDE.md) for staging deployment procedures.

### Production Deployment

```bash
# Via GitHub Actions (automatic on push to main)
# See .github/workflows/ci-cd.yml

# Manual deployment
cd /var/www/canadaim
git pull origin main
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction
sudo systemctl restart php-fpm
```

### Docker Deployment

```bash
# Build image
docker build -t canadaim:latest .

# Run container
docker-compose up -d

# Run migrations
docker-compose exec web php bin/console doctrine:migrations:migrate
```

---

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_ENV=prod          # Environment: dev, test, prod
APP_DEBUG=0          # Debug mode (0 or 1)
APP_SECRET=xxxx      # Secret key for crypto

# Database
DATABASE_URL=mysql://user:password@host:3306/canadaim

# Mailer
MAILER_DSN=smtp://host:port

# Services
RECAPTCHA_SITE_KEY=xxx
RECAPTCHA_SECRET_KEY=xxx
```

### Database Migrations

```bash
# Create migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Rollback migration
php bin/console doctrine:migrations:execute --down [version]
```

---

## 📊 Monitoring & Logging

### Log Files
- `var/log/prod.log` - Application errors
- `var/log/security.log` - Authentication/security events
- `var/log/performance.log` - Performance metrics
- `var/log/database.log` - Database queries

### Monitoring Setup

See [MONITORING_AND_LOGGING.md](MONITORING_AND_LOGGING.md) for:
- Application monitoring
- Error tracking (Sentry)
- Performance monitoring
- Server monitoring
- Alert configuration

---

## 💾 Backup & Recovery

### Automated Backups
- Daily full backups at 2:00 AM UTC
- Hourly incremental backups
- 30-day retention policy
- S3 cloud storage

### Recovery Procedures

See [DATABASE_BACKUP_RECOVERY.md](DATABASE_BACKUP_RECOVERY.md) for:
- Backup verification
- Database recovery
- Point-in-time recovery
- Disaster recovery procedures

---

## 🔐 Security

### Best Practices
- HTTPS enforced
- CSRF protection enabled
- SQL injection prevention
- XSS protection
- Rate limiting
- Input validation
- Output encoding

### Security Headers
- `X-Frame-Options`: SAMEORIGIN
- `X-Content-Type-Options`: nosniff
- `X-XSS-Protection`: 1; mode=block
- `Content-Security-Policy`: Configured
- `Strict-Transport-Security`: max-age=31536000

### Password Security
- Bcrypt hashing
- Strong password requirements
- No password in logs
- Secure session management

---

## 📱 API Endpoints

### Contact Messages
```
POST   /api/contact/messages      - Submit contact message
GET    /api/contact/messages      - List messages (admin)
GET    /api/contact/messages/{id} - Get message (admin)
DELETE /api/contact/messages/{id} - Delete message (admin)
```

### Service Requests
```
POST   /api/services/requests     - Create service request
GET    /api/services/requests     - List requests (admin)
GET    /api/services/requests/{id} - Get request (admin)
```

---

## 🎨 Frontend Technologies

- **Twig**: Template engine
- **Bootstrap 5**: CSS framework
- **JavaScript**: Vanilla JS + HTMX
- **Font Awesome**: Icons
- **Google Analytics**: Analytics tracking
- **Responsive Design**: Mobile-first approach

---

## 📦 Dependencies

### Core Dependencies
- `symfony/symfony`: Web framework
- `doctrine/orm`: ORM
- `doctrine/doctrine-bundle`: Database bundle
- `symfony/twig-bundle`: Template engine
- `symfony/form`: Form builder
- `symfony/validator`: Validation
- `symfony/security-bundle`: Security
- `symfony/mailer`: Email sending

### Development Dependencies
- `phpunit/phpunit`: Testing framework
- `symfony/debug-bundle`: Debug toolbar
- `doctrine/doctrine-fixtures-bundle`: Test fixtures

---

## 🚦 CI/CD Pipeline

### Automated Testing
On every push to `main` or `develop`:
1. Run all tests (Entity, Form, Service, Repository, Controller)
2. Generate code coverage report
3. Lint YAML configuration
4. Build Docker image
5. Deploy to staging (if develop branch)
6. Deploy to production (if main branch)

See `.github/workflows/ci-cd.yml` for details.

---

## 📚 Documentation

- [Testing Guide](TESTING.md) - Comprehensive testing documentation
- [Deployment & Maintenance](DEPLOYMENT_MAINTENANCE_GUIDE.md) - Deployment procedures
- [Monitoring & Logging](MONITORING_AND_LOGGING.md) - Monitoring setup
- [Database Backup & Recovery](DATABASE_BACKUP_RECOVERY.md) - Backup procedures
- [Section 6 Summary](SECTION_6_SUMMARY.md) - Tests overview

---

## 🐛 Troubleshooting

### Common Issues

#### 404 Not Found
- Check `.htaccess` is enabled
- Verify `mod_rewrite` is loaded
- Check route configuration in `config/routes.yaml`

#### Database Connection Error
```bash
php bin/console doctrine:query:sql "SELECT 1"
```

#### Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/canadaim
sudo chmod -R 755 /var/www/canadaim
sudo chmod -R 775 /var/www/canadaim/var
```

#### Cache Issues
```bash
php bin/console cache:clear --env=prod
```

For more troubleshooting, see [DEPLOYMENT_MAINTENANCE_GUIDE.md](DEPLOYMENT_MAINTENANCE_GUIDE.md#troubleshooting).

---

## 📞 Support

- **Documentation**: See `/docs` folder
- **Issues**: GitHub Issues
- **Email**: support@canadaim.com
- **Slack**: #canadaim-support

---

## 📄 License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file.

---

## 👥 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Development Workflow
1. Create branch from `develop`
2. Make changes with tests
3. Push branch
4. Create Pull Request
5. Code review
6. Merge to develop
7. Deploy to staging
8. Test on staging
9. Merge develop to main
10. Deploy to production

---

## 🎓 Architecture Overview

### MVC Pattern
- **Model**: Doctrine entities in `src/Entity/`
- **View**: Twig templates in `templates/`
- **Controller**: Controllers in `src/Controller/`

### Service Layer
Business logic in `src/Service/`:
- `SeoMetadataService` - SEO metadata management
- Additional services for domain logic

### Database
- MySQL/MariaDB
- Migrations: `migrations/`
- Entities: `src/Entity/`
- Repositories: `src/Repository/`

### Forms
- Type classes in `src/Form/`
- Form templates in `templates/forms/`
- Built-in validation

---

## 📊 Performance Metrics

- **Page Load Time**: < 2s (target)
- **API Response Time**: < 500ms
- **Database Query Time**: < 100ms (p95)
- **Cache Hit Rate**: > 85%

---

## 🔄 Version History

- **v2.0** (Current) - Production release
- **v1.0** - Initial release

---

## ✨ Key Achievements

✅ 6 completed sections  
✅ 65 passing tests (151 assertions)  
✅ 80%+ code coverage  
✅ Production-ready infrastructure  
✅ CI/CD automation  
✅ Comprehensive documentation  

---

**Last Updated**: February 2024  
**Maintained By**: Development Team  
**Status**: Active Development ✨
