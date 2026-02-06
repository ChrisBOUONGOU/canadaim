# Performance Monitoring & Logging Guide

## Logging Architecture

### Log Files (Production)
- **main.log** - General application errors and warnings
- **security.log** - Authentication, authorization, security events
- **performance.log** - Performance metrics and timings
- **database.log** - Database queries and Doctrine events

### Log Rotation
- Files rotate daily with 30-day retention
- JSON format for structured logging and analysis
- Located in `var/log/` directory

## Monitoring Setup

### Application Monitoring
1. **Error Tracking with Sentry**
   ```
   SENTRY_DSN=https://your-key@sentry.io/project-id
   ```

2. **Performance Monitoring**
   - Monitor database query times
   - Track HTTP response times
   - Alert on degraded performance

3. **Health Checks**
   - `/health` endpoint for uptime monitoring
   - Database connectivity check
   - Cache availability check

### Server Monitoring
- CPU and memory usage
- Disk space (especially logs directory)
- Network bandwidth
- MySQL/Database status

### Automated Alerts
- Error rate > 1%
- Response time > 2s average
- Database query time > 1s
- Disk usage > 80%
- Memory usage > 85%

## Production Logs Access

```bash
# View recent errors
tail -f var/log/prod.log

# View security events
tail -f var/log/security.log

# View database activity
tail -f var/log/database.log

# Search for specific errors
grep "ERROR" var/log/prod.log

# Real-time log streaming
tail -f var/log/prod.log | grep "exception"
```

## Metrics to Track

1. **Application Metrics**
   - Request rate
   - Error rate
   - Response time (p50, p95, p99)
   - Successful vs failed requests

2. **Database Metrics**
   - Query execution time
   - Slow queries (> 1s)
   - Connection pool usage
   - Deadlock frequency

3. **Infrastructure Metrics**
   - Server CPU usage
   - Memory consumption
   - Disk I/O
   - Network latency

## Log Analysis

### Weekly Review
```bash
# Error summary
grep "ERROR" var/log/prod.log | wc -l

# Most common errors
grep "ERROR" var/log/prod.log | sed 's/.*ERROR: //' | sort | uniq -c | sort -rn | head -10

# Performance issues
grep -E "query took|Response time" var/log/performance.log | tail -20

# Security events
grep "SECURITY" var/log/security.log | tail -50
```

## Alerting Rules

### Critical Alerts
- Application crashes (exit code != 0)
- Database unavailable
- Out of disk space
- Disk usage > 95%

### Warning Alerts
- High error rate (> 1%)
- Slow response times (p95 > 3s)
- Database connection pool exhausted
- Memory usage > 80%

### Info Alerts
- Scheduled backup completion
- Deployment success/failure
- Security updates available

## Dashboard Setup (Grafana/DataDog)

Create dashboards showing:
1. Real-time request rate
2. Error rate trends
3. Response time distribution
4. Database performance
5. Server resource usage
6. Cache hit rates
7. User activity patterns

## Third-Party Services

### Sentry (Error Tracking)
- Automatically captures exceptions
- Groups similar errors
- Tracks error trends
- Provides stack traces

### New Relic (APM)
- Application Performance Monitoring
- Detailed transaction tracing
- Database performance analysis
- Infrastructure monitoring

### DataDog (Monitoring)
- Unified monitoring and analytics
- Custom metrics
- Real-time dashboards
- Alert management

## Best Practices

1. **Log Levels**
   - Use appropriate levels (DEBUG, INFO, WARNING, ERROR, CRITICAL)
   - Avoid logging sensitive data (passwords, tokens, PII)

2. **Performance**
   - Use error handler for critical issues only
   - Buffer messages to reduce I/O
   - Archive old logs regularly

3. **Retention**
   - Keep error logs for 30 days minimum
   - Archive important logs for compliance
   - Clean up test/debug logs weekly

4. **Security**
   - Restrict log file access (chmod 640)
   - Don't include sensitive data in logs
   - Encrypt logs in transit
   - Monitor for suspicious patterns
