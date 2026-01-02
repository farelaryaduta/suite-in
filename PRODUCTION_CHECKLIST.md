# Production Deployment Checklist for suite.in

## ⚠️ CRITICAL: Environment Configuration

### 1. Update .env for Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Generate new key for production
# php artisan key:generate

# Session settings
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# Database
DB_CONNECTION=mysql
DB_HOST=your-production-host
DB_DATABASE=your-production-db
DB_USERNAME=your-production-user
DB_PASSWORD=your-secure-password

# Midtrans Production
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_CLIENT_KEY="your-production-client-key"
MIDTRANS_SERVER_KEY="your-production-server-key"
```

### 2. Midtrans Webhook URL
Configure in Midtrans Dashboard:
- **Notification URL**: `https://yourdomain.com/payment/notification`
- **Finish URL**: `https://yourdomain.com/bookings/{booking_id}`

## 🔒 Security Checklist

- [x] Mass assignment protection (User role field removed from fillable)
- [x] Rate limiting on all login routes (5 attempts/minute)
- [x] Rate limiting on registration (3 attempts/minute)
- [x] CSRF protection on all forms
- [x] Password hashing with bcrypt
- [x] Current password required for password changes
- [x] Secure payment number generation (Str::random instead of rand)
- [x] Session regeneration on login
- [x] Proper role-based access control
- [ ] HTTPS enforcement (configure in production server)
- [ ] Set secure cookie flags in production

## 📋 Pre-Deployment Commands

```bash
# 1. Install production dependencies
composer install --optimize-autoloader --no-dev

# 2. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 3. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run migrations
php artisan migrate --force

# 5. Create storage link
php artisan storage:link

# 6. Build frontend assets
npm run build
```

## 📁 Files to Exclude from Production

- `.env` (keep template as `.env.example`)
- `tests/`
- `phpunit.xml`
- `.git/`
- `node_modules/` (only needed for build)

## 🧪 Features Tested

### Authentication
- [x] Customer login/register
- [x] Partner login/register
- [x] Admin login
- [x] Role-based redirects
- [x] Auto-logout on portal switch
- [x] Rate limiting

### Booking Flow
- [x] Hotel search
- [x] Room availability check
- [x] Booking creation
- [x] Payment with Midtrans
- [x] Payment status sync
- [x] Booking confirmation

### Dashboard
- [x] Admin dashboard with tax revenue (10%)
- [x] Partner dashboard with partner revenue (subtotal + 5% service)
- [x] Recent bookings display

### Security
- [x] CSRF protection
- [x] XSS prevention (all outputs escaped)
- [x] SQL injection prevention
- [x] Authorization checks

## 🐛 Known Limitations

1. **Midtrans Webhook**: May not work on localhost. Use `payments:sync-status` command to sync pending payments.
2. **File Uploads**: Images stored in local storage. Consider S3 for production.
3. **Email**: Currently using log driver. Configure SMTP for production.

## 📞 Support Commands

```bash
# Sync pending payments with Midtrans
php artisan payments:sync-status

# Check route list
php artisan route:list

# View logs
tail -f storage/logs/laravel.log
```
