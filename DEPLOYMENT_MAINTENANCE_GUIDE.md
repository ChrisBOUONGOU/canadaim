# Canadaim - Deployment & Maintenance Guide

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Deployment Options](#deployment-options)
3. [Production Deployment](#production-deployment)
4. [Staging Deployment](#staging-deployment)
5. [Maintenance Procedures](#maintenance-procedures)
6. [Troubleshooting](#troubleshooting)
7. [Rollback Procedures](#rollback-procedures)

---

## Prerequisites

### System Requirements
- PHP 8.2 or higher
- MySQL 8.0+ or MariaDB 10.5+
- Composer 2.0+
- Node.js 18+ (for frontend build)
- Apache 2.4+ with mod_rewrite and mod_headers

### Server Specifications (Recommended)
- **CPU**: 2+ cores
- **RAM**: 4GB minimum (8GB recommended)
- **Storage**: 50GB SSD minimum
- **Bandwidth**: Unlimited (or 10GB+ monthly)

### Access Requirements
- SSH access to production server
- Database admin credentials
- Git repository access
- AWS S3 access (for backups)

---

## Deployment Options

### Option 1: Traditional Server Deployment

#### 1.1 Manual Deployment
```bash
# SSH into server
ssh user@production.example.com

# Navigate to application directory
cd /var/www/canadaim

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear cache
php bin/console cache:clear --env=prod

# Run database migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Warm up cache
php bin/console cache:warmup --env=prod

# Restart web server
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

#### 1.2 Automated Deployment (GitHub Actions)
- Automatically triggered on push to `main` branch
- Runs full test suite
- Builds Docker image
- Deploys to production via SSH
- See `.github/workflows/ci-cd.yml`

### Option 2: Docker Deployment

#### 2.1 Build and Run Local
```bash
# Build Docker image
docker build -t canadaim:latest .

# Run container
docker run -d \
  --name canadaim \
  -p 80:80 \
  -p 443:443 \
  -e APP_ENV=prod \
  -e DATABASE_URL=mysql://user:pass@host/db \
  canadaim:latest
```

#### 2.2 Docker Compose (Full Stack)
```bash
# Copy environment files
cp .env.production .env

# Start all services
docker-compose up -d

# Run migrations
docker-compose exec web php bin/console doctrine:migrations:migrate

# Check status
docker-compose ps
docker-compose logs -f web
```

#### 2.3 Docker Compose Services
- **web**: Apache + PHP application
- **db**: MySQL database
- **mailhog**: Email testing (dev/staging only)
- **phpmyadmin**: Database management (staging only)

---

## Production Deployment

### Step 1: Pre-Deployment Checklist

```bash
# Run tests
php bin/phpunit

# Check code style
php bin/console lint:yaml config/
php bin/console lint:container

# Verify migrations
php bin/console doctrine:migrations:status

# Test cache warmup
php bin/console cache:warmup --env=prod
```

### Step 2: Backup Current State

```bash
# Backup database
/usr/local/bin/backup-database.sh

# Verify backup
ls -lh /backups/full/ | head -5

# Backup application
tar -czf /backups/pre-deploy-$(date +%Y%m%d_%H%M%S).tar.gz \
  --exclude='vendor' --exclude='var' --exclude='.git' \
  /var/www/canadaim
```

### Step 3: Deploy Application

```bash
# Navigate to application
cd /var/www/canadaim

# Maintenance mode (optional - for zero-downtime deployment, use blue-green)
php bin/console cache:clear --env=prod
php bin/console maintenance:enable "Deploying updates..."

# Pull latest code
git fetch origin
git reset --hard origin/main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Clear and warm cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Disable maintenance mode
php bin/console maintenance:disable
```

### Step 4: Verify Deployment

```bash
# Check application status
curl -I https://canadaim.com/

# Monitor logs for errors
tail -f var/log/prod.log

# Check database connectivity
php bin/console doctrine:query:sql "SELECT 1"

# Run health check endpoint
curl https://canadaim.com/health
```

### Step 5: Post-Deployment Verification

```bash
# Verify page rendering
curl https://canadaim.com/ | head -20

# Check all main routes
php bin/console debug:router

# Verify form functionality
# Test contact form manually or with automated tests

# Check SEO metadata
curl https://canadaim.com/ | grep -E "meta name=\"description|og:title"

# Monitor error logs for 5 minutes
sleep 300 && tail -50 var/log/prod.log
```

---

## Staging Deployment

### Staging Environment Setup

```bash
# Deploy to staging branch
git checkout develop
git pull origin develop

# Install dependencies
composer install --no-dev

# Configure environment
cp .env.staging /var/www/canadaim-staging/.env.local

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Clear cache
php bin/console cache:clear --env=staging
```

### Staging Testing

```bash
# Run full test suite
php bin/phpunit

# Generate coverage report
php bin/phpunit --coverage-html=var/coverage

# Run security checks
composer audit

# Performance testing
ab -n 1000 -c 10 https://staging.canadaim.com/

# Load testing
locust -f locustfile.py --host=https://staging.canadaim.com/
```

---

## Maintenance Procedures

### Daily Maintenance

#### Check Application Health
```bash
# Monitor error logs
tail -50 var/log/prod.log

# Check security logs
tail -20 var/log/security.log

# Verify backup completion
ls -lh /backups/full/ | head -1

# Check database performance
mysql -u root -e "SHOW PROCESSLIST;" | grep "Query"
```

#### Monitor Resources
```bash
# CPU and memory usage
top -b -n 1 | head -20

# Disk usage
df -h

# Network connections
netstat -antp | grep ESTABLISHED | wc -l

# MySQL connections
mysql -u root -e "SHOW STATUS LIKE 'Threads%';"
```

### Weekly Maintenance

#### Code and Security Updates
```bash
# Check for PHP updates
php -v

# Check for Composer package updates
composer update --dry-run

# Security audit
composer audit

# Review and install critical updates
composer update --with-dependencies
```

#### Database Maintenance
```bash
# Optimize tables
mysql -u canadaim -p canadaim -e "OPTIMIZE TABLE contact_messages, service_requests;"

# Check table status
mysql -u canadaim -p canadaim -e "CHECK TABLE contact_messages, service_requests;"

# Analyze table statistics
mysql -u canadaim -p canadaim -e "ANALYZE TABLE contact_messages, service_requests;"
```

#### Log Analysis
```bash
# Error summary
grep "ERROR" var/log/prod.log | wc -l

# Most common errors
grep "ERROR" var/log/prod.log | sed 's/.*ERROR: //' | sort | uniq -c | sort -rn | head -10

# Slow queries
grep "query took" var/log/performance.log | tail -20
```

### Monthly Maintenance

#### Comprehensive Review
```bash
# Generate monthly report
./scripts/monthly-report.sh

# Archive old logs (older than 60 days)
find var/log -name "*.log" -mtime +60 -exec gzip {} \;

# Review security incidents
grep "SECURITY" var/log/security.log | wc -l

# Database optimization
mysql -u canadaim -p canadaim -e "OPTIMIZE TABLE contact_messages, service_requests, migrations;"
```

#### Testing & Backup Recovery
```bash
# Run full test suite
php bin/phpunit --coverage-text

# Test backup recovery
./scripts/test-backup-recovery.sh

# Verify backup integrity
gzip -t /backups/full/canadaim_*.sql.gz
```

#### Performance Optimization
```bash
# Generate performance report
php bin/console debug:config

# Review cache hit rates
redis-cli INFO stats

# Check database indexes
mysql -u canadaim -p canadaim -e "SHOW INDEX FROM contact_messages;"
```

---

## Troubleshooting

### Common Issues

#### 1. Database Connection Errors

**Symptoms**: `SQLSTATE[HY000]` errors

**Solution**:
```bash
# Check database status
mysql -u root -e "STATUS;"

# Verify credentials
php bin/console doctrine:query:sql "SELECT 1"

# Restart MySQL service
sudo systemctl restart mysql

# Check log files
tail -50 var/log/database.log
```

#### 2. Cache Issues

**Symptoms**: Stale data displayed, form errors

**Solution**:
```bash
# Clear application cache
php bin/console cache:clear --env=prod

# Clear Redis cache (if used)
redis-cli FLUSHALL

# Warm up cache
php bin/console cache:warmup --env=prod

# Check cache permissions
ls -la var/cache/
```

#### 3. Permission Errors

**Symptoms**: `Cannot create directory`, write permission errors

**Solution**:
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/canadaim
sudo chmod -R 755 /var/www/canadaim
sudo chmod -R 775 /var/www/canadaim/var

# Set ACL (alternative)
setfacl -R -m u:www-data:rwx /var/www/canadaim/var
```

#### 4. High Memory Usage

**Symptoms**: Out of memory errors, slow response

**Solution**:
```bash
# Check memory usage
free -h
php -r "echo ini_get('memory_limit');"

# Identify memory leaks
php bin/console debug:memory

# Increase memory limit if needed
# Edit .env.local or php.ini
# memory_limit = 512M

# Restart PHP-FPM
sudo systemctl restart php-fpm
```

#### 5. Slow Response Times

**Symptoms**: Pages loading slowly, timeout errors

**Solution**:
```bash
# Profile application
php bin/console debug:profile

# Check database queries
mysql -u root -e "SET GLOBAL slow_query_log=1;"
mysql -u root -e "SHOW SLOW LOGS;"

# Analyze performance logs
grep "query took" var/log/performance.log | sort -k4 -rn | head -20

# Optimize indexes
mysql -u canadaim -p canadaim -e "ANALYZE TABLE contact_messages;"
```

---

## Rollback Procedures

### Quick Rollback (Git)

```bash
# Identify problematic commit
git log --oneline | head -10

# Revert to previous version
git revert HEAD
# or
git reset --hard HEAD~1

# Push to production
git push origin main -f

# Clear cache and restart
php bin/console cache:clear --env=prod
sudo systemctl restart php-fpm
```

### Database Rollback

```bash
# List available backups
ls -lh /backups/full/

# Restore from specific backup
gunzip < /backups/full/canadaim_20240206_020000.sql.gz | \
    mysql -u canadaim -p canadaim

# Verify restoration
mysql -u canadaim -p canadaim -e "SELECT COUNT(*) FROM contact_messages;"
```

### Full Rollback (Code + Database)

```bash
# Backup current state before rollback
/usr/local/bin/backup-database.sh

# Restore from point-in-time backup
BACKUP_FILE="/backups/full/canadaim_before_deploy.sql.gz"
gunzip < $BACKUP_FILE | mysql -u canadaim -p canadaim

# Revert application code
git checkout v2.0.0  # or specific version tag
git push origin main -f

# Clear cache
php bin/console cache:clear --env=prod

# Verify system
curl https://canadaim.com/health
tail -20 var/log/prod.log
```

---

## Deployment Checklist

- [ ] All tests passing
- [ ] Code reviewed and approved
- [ ] Database backup created
- [ ] Backup verified
- [ ] Staging deployment tested
- [ ] Performance tested on staging
- [ ] Maintenance window scheduled
- [ ] Team notified
- [ ] Production backup completed
- [ ] Application deployed
- [ ] Health checks passed
- [ ] All pages load correctly
- [ ] Forms submit successfully
- [ ] Database queries responsive
- [ ] Error logs monitored
- [ ] Team notified of completion
- [ ] Documentation updated

---

## Emergency Contacts

- **On-call DevOps**: devops@canadaim.com
- **Database Admin**: dba@canadaim.com
- **Security Team**: security@canadaim.com
- **Support Team**: support@canadaim.com

---

## Additional Resources

- [Testing Guide](TESTING.md)
- [Monitoring & Logging](MONITORING_AND_LOGGING.md)
- [Database Backup & Recovery](DATABASE_BACKUP_RECOVERY.md)
- [API Documentation](API.md)

---

**Last Updated**: February 2024  
**Version**: 1.0  
**Maintained By**: DevOps Team
