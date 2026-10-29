#!/bin/bash

Exit immediately if any command fails

set -e

echo ""
echo "🚀 Starting Project Setup & Deployment Sequence"
echo ""

1. Environment File Check

if [ ! -f .env ]; then
echo "📄 Creating .env file from .env.example..."
cp .env.example .env
else
echo "✅ .env file already exists."
fi

2. PHP Dependencies

echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

3. Frontend Build (if package.json exists)

if [ -f package.json ]; then
echo "🎨 Building frontend assets..."
npm install
npm run build
fi

4. Generate Application Key (if missing)

if ! grep -q "APP_KEY=base64" .env; then
echo "🔑 Generating application key..."
php artisan key:generate
fi

5. Database Migrations

echo "🗄️ Running database migrations..."
php artisan migrate --force

6. Synchronize Custom System Permissions

echo "🛡️ Synchronizing system permissions..."
php artisan permissions:sync

7. Clear & Optimize Application Cache

echo "🧹 Clearing and optimizing application cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize

8. Set Directory Permissions

echo "🔒 Adjusting storage and cache permissions..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "✨ Project setup completed successfully!"
echo ""