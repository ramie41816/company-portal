#!/bin/bash

# Exit immediately if any command fails
set -e

echo ""
echo "🚀 Starting Production Deployment Sequence"
echo ""

# 1. Environment File Check
if [ ! -f .env ]; then
    echo "📄 Error: .env file missing in production!"
    exit 1
else
    echo "✅ .env file exists."
fi

# 2. PHP Dependencies
echo "📦 Installing production Composer dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3. Frontend Build
if [ -f package.json ]; then
    echo "🎨 Building frontend assets for production..."
    npm install
    npm run build
fi

# 4. Generate Application Key
if ! grep -q "APP_KEY=base64" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# 5. Database Migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 6. Clear & Aggressively Optimize Application Cache for Production
echo "🧹 Optimizing application cache and compiling configurations..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize

# 7. Set Directory Permissions
echo "🔒 Adjusting storage and cache permissions..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "✨ Production deployment completed successfully!"
echo ""