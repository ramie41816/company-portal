#!/bin/bash

# Exit immediately if any command fails
set -e

echo ""
echo "🚀 Starting Local Development Setup Sequence"
echo ""

# 1. Environment File Check
if [ ! -f .env ]; then
    echo "📄 Creating .env file from .env.example..."
    cp .env.example .env
else
    echo "✅ .env file already exists."
fi

# 2. PHP Dependencies
echo "📦 Installing Composer dependencies (including dev)..."
composer install --no-interaction --prefer-dist

# 3. Frontend Build
if [ -f package.json ]; then
    echo "🎨 Building frontend assets..."
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
php artisan migrate

# 6. Clear Application Caches
echo "🧹 Clearing application caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 7. Set Directory Permissions
echo "🔒 Adjusting storage and cache permissions..."
chmod -R 775 storage bootstrap/cache

echo ""
echo "✨ Local development setup completed successfully!"
echo ""