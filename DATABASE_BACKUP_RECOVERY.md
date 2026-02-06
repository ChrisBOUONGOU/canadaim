# Database Backup & Disaster Recovery

## Backup Strategy

### Backup Types

#### 1. Full Backups (Daily)
- Complete database snapshot
- Taken every night at 2:00 AM UTC
- Retained for 30 days
- Location: `/backups/full/`

#### 2. Incremental Backups (Hourly)
- Changes since last full backup
- Taken every hour
- Retained for 7 days
- Location: `/backups/incremental/`

#### 3. Binary Logs (Real-time)
- MySQL binary logs
- Enable point-in-time recovery
- Rotated daily
- Retained for 14 days

### Backup Scripts

#### MySQL Backup
```bash
#!/bin/bash
# /usr/local/bin/backup-database.sh

BACKUP_DIR="/backups/full"
DB_USER="canadaim"
DB_PASSWORD="your_password"
DB_NAME="canadaim"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Full backup with compression
mysqldump -u$DB_USER -p$DB_PASSWORD \
    --single-transaction \
    --quick \
    --lock-tables=false \
    $DB_NAME | gzip > $BACKUP_DIR/canadaim_$DATE.sql.gz

# Verify backup
gzip -t $BACKUP_DIR/canadaim_$DATE.sql.gz
if [ $? -eq 0 ]; then
    echo "Backup successful: canadaim_$DATE.sql.gz"
    
    # Upload to cloud storage (S3)
    aws s3 cp $BACKUP_DIR/canadaim_$DATE.sql.gz \
        s3://canadaim-backups/daily/
    
    # Clean old backups (older than 30 days)
    find $BACKUP_DIR -name "canadaim_*.sql.gz" -mtime +30 -delete
else
    echo "Backup verification failed!"
    exit 1
fi
```

#### Application Backup
```bash
#!/bin/bash
# /usr/local/bin/backup-application.sh

BACKUP_DIR="/backups/application"
APP_DIR="/var/www/canadaim"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup application files (exclude vendor, var, etc)
tar -czf $BACKUP_DIR/canadaim_app_$DATE.tar.gz \
    --exclude='vendor' \
    --exclude='var' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='.env.local' \
    -C /var/www canadaim/

# Upload to cloud storage
aws s3 cp $BACKUP_DIR/canadaim_app_$DATE.tar.gz \
    s3://canadaim-backups/application/

# Clean old backups (older than 7 days)
find $BACKUP_DIR -name "canadaim_app_*.tar.gz" -mtime +7 -delete
```

### Cron Schedule
```crontab
# Daily full backup at 2:00 AM UTC
0 2 * * * /usr/local/bin/backup-database.sh >> /var/log/backup.log 2>&1

# Hourly incremental backup
0 * * * * /usr/local/bin/backup-incremental.sh >> /var/log/backup.log 2>&1

# Application backup weekly on Sunday at 3:00 AM
0 3 * * 0 /usr/local/bin/backup-application.sh >> /var/log/backup.log 2>&1
```

## Backup Verification

### Daily Verification
```bash
#!/bin/bash
# /usr/local/bin/verify-backups.sh

BACKUP_DIR="/backups/full"

for backup in $BACKUP_DIR/*.sql.gz; do
    if ! gzip -t "$backup" 2>/dev/null; then
        echo "CRITICAL: Backup corrupted: $backup"
        # Send alert email
        mail -s "Backup Verification Failed" admin@canadaim.com <<< "Backup corrupted: $backup"
        exit 1
    fi
done

echo "All backups verified successfully"
```

## Recovery Procedures

### Scenario 1: Database Corruption

#### Step 1: Stop Application
```bash
sudo systemctl stop php-fpm
sudo systemctl stop nginx
```

#### Step 2: Restore from Latest Backup
```bash
# List available backups
ls -lh /backups/full/

# Restore from backup
gunzip < /backups/full/canadaim_20240206_020000.sql.gz | \
    mysql -u canadaim -p canadaim

# Verify data
mysql -u canadaim -p canadaim -e "SELECT COUNT(*) FROM contact_messages;"
```

#### Step 3: Restart Application
```bash
sudo systemctl start php-fpm
sudo systemctl start nginx

# Verify application
curl -I https://canadaim.com/
```

### Scenario 2: Accidental Data Delete

#### Step 1: Find Backup Before Deletion
```bash
# Determine deletion time
grep "DELETE" /var/log/mysql-general.log | tail

# Use binary logs for point-in-time recovery
mysqlbinlog --stop-datetime='2024-02-06 10:00:00' \
    /var/lib/mysql/mysql-bin.* | mysql -u canadaim -p canadaim
```

#### Step 2: Restore Specific Table
```bash
# Export table from backup
gunzip < /backups/full/canadaim_20240205_020000.sql.gz > /tmp/backup.sql

# Extract specific table
sed -n '/^CREATE TABLE.*contact_messages/,/^CREATE TABLE/p' /tmp/backup.sql > /tmp/table.sql

# Restore to temporary database
mysql -u canadaim -p canadaim_temp < /tmp/table.sql

# Copy data back
mysql -u canadaim -p canadaim -e "INSERT INTO contact_messages SELECT * FROM canadaim_temp.contact_messages WHERE id NOT IN (SELECT id FROM canadaim.contact_messages);"
```

### Scenario 3: Complete Server Failure

#### Step 1: Provision New Server
```bash
# Create new server with same specs
# Configure MySQL, PHP, Nginx
# Create database and user
mysql -u root -e "CREATE DATABASE canadaim;"
mysql -u root -e "CREATE USER 'canadaim'@'localhost' IDENTIFIED BY 'password';"
mysql -u root -e "GRANT ALL PRIVILEGES ON canadaim.* TO 'canadaim'@'localhost';"
```

#### Step 2: Restore Database
```bash
# Download backup from S3
aws s3 cp s3://canadaim-backups/daily/canadaim_20240206_020000.sql.gz .

# Restore
gunzip < canadaim_20240206_020000.sql.gz | mysql -u canadaim -p canadaim
```

#### Step 3: Restore Application
```bash
# Download application backup
aws s3 cp s3://canadaim-backups/application/canadaim_app_20240203_030000.tar.gz .

# Extract to web root
sudo tar -xzf canadaim_app_20240203_030000.tar.gz -C /var/www

# Restore dependencies
cd /var/www/canadaim
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data /var/www/canadaim
sudo chmod -R 755 /var/www/canadaim
sudo chmod -R 775 /var/www/canadaim/var
```

#### Step 4: Configure and Restart
```bash
# Update environment variables
sudo cp .env.production /var/www/canadaim/.env.local

# Clear cache
php bin/console cache:clear --env=prod

# Run migrations
php bin/console doctrine:migrations:migrate

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php-fpm
```

## Backup Monitoring

### Backup Success Verification
```bash
#!/bin/bash
# Check if today's backup exists and is not empty

BACKUP_FILE="/backups/full/canadaim_$(date +%Y%m%d)*.sql.gz"

if ls $BACKUP_FILE 1> /dev/null 2>&1; then
    SIZE=$(du -h $(ls -t $BACKUP_FILE | head -1) | cut -f1)
    echo "Backup found: $SIZE"
    
    # Alert if backup size is unusual
    if [[ $(stat -f%z $(ls -t $BACKUP_FILE | head -1) 2>/dev/null || stat -c%s $(ls -t $BACKUP_FILE | head -1)) -lt 1000000 ]]; then
        echo "WARNING: Backup size is suspiciously small"
    fi
else
    echo "ERROR: No backup found for today!"
fi
```

### S3 Backup Verification
```bash
# List all backups in S3
aws s3 ls s3://canadaim-backups/ --recursive --human-readable --summarize

# Check latest backup age
LATEST=$(aws s3 ls s3://canadaim-backups/daily/ --recursive | sort | tail -1 | awk '{print $1, $2}')
echo "Latest backup: $LATEST"
```

## Testing Backup Recovery

### Monthly Recovery Test
```bash
#!/bin/bash
# Monthly backup recovery test

# Create temporary restoration database
mysql -u root -e "CREATE DATABASE canadaim_test;"

# Restore latest backup
LATEST_BACKUP=$(ls -t /backups/full/*.sql.gz | head -1)
gunzip < $LATEST_BACKUP | mysql -u canadaim -p canadaim_test

# Run data validation
mysql -u canadaim -p canadaim_test -e "
    SELECT 'contact_messages' as table_name, COUNT(*) as row_count FROM contact_messages UNION
    SELECT 'service_requests', COUNT(*) FROM service_requests;
"

# Clean up
mysql -u root -e "DROP DATABASE canadaim_test;"

echo "Recovery test completed successfully"
```

## Compliance & Retention

- **Retention Policy**: 30-day minimum for full backups
- **Audit Trail**: All backup operations logged
- **Encryption**: Backups encrypted at rest (S3 server-side encryption)
- **Access Control**: Limited to DBA and DevOps teams
- **Testing**: Monthly recovery test mandatory

## Disaster Recovery Plan Summary

| Scenario | RTO | RPO | Process |
|----------|-----|-----|---------|
| Single table corruption | 30 min | 1 hour | Restore from hourly backup |
| Complete database failure | 1 hour | 24 hours | Restore from daily backup |
| Server hardware failure | 2 hours | 1 day | Provision new server and restore |
| Data center outage | 4 hours | 1 day | Failover to secondary region |

**RTO**: Recovery Time Objective  
**RPO**: Recovery Point Objective
