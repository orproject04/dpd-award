#!/bin/sh
set -e

cd /var/www/html

# Configure PHP security settings to hide version information
echo "👉 Configuring PHP security settings..."
if [ ! -f /usr/local/etc/php/conf.d/security-headers.ini ]; then
    cat > /usr/local/etc/php/conf.d/security-headers.ini << EOF
; Hide PHP version information
expose_php = Off
; Hide PHP from Server header
; Disable detailed error messages in production
display_errors = Off
log_errors = On
error_log = /var/log/php-errors.log
EOF
    echo "✅ PHP security settings configured!"
else
    echo "✅ PHP security settings already configured!"
fi

# Configure upload limits so Laravel receives larger multipart requests.
if [ ! -f /usr/local/etc/php/conf.d/upload-limits.ini ]; then
    cat > /usr/local/etc/php/conf.d/upload-limits.ini << EOF
upload_max_filesize = 500M
post_max_size = 500M
EOF
    echo "✅ PHP upload limits configured!"
else
    echo "✅ PHP upload limits already configured!"
fi

# Fix permissions untuk mounted volumes (karena volume mount override Dockerfile permissions)
echo "👉 Fixing storage and bootstrap/cache permissions..."
mkdir -p storage/logs storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo "✅ Permissions fixed!"

# Function to wait for PostgreSQL to be ready
wait_for_postgres() {
    echo "👉 Waiting for PostgreSQL to be ready..."
    
    # Get database connection details from environment (set by .env-docker)
    DB_HOST=${DB_HOST:-pgsql}
    DB_PORT=${DB_PORT:-5432}
    DB_DATABASE=${DB_DATABASE:-dpdaward}
    DB_USERNAME=${DB_USERNAME:-postgres}
    
    # Wait for PostgreSQL to accept connections
    until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" > /dev/null 2>&1; do
        echo "PostgreSQL ($DB_HOST:$DB_PORT/$DB_DATABASE) is unavailable - sleeping for 2 seconds..."
        sleep 2
    done
    
    echo "✅ PostgreSQL is ready at $DB_HOST:$DB_PORT/$DB_DATABASE!"
}

# Wait for database before running migrations
wait_for_postgres

# Patch BaseFilter.php - menambahkan method label() jika belum ada
echo "👉 Patching BaseFilter.php..."
BASEFILTER_FILE="/var/www/html/vendor/laravolt/laravolt/src/Ui/Filters/BaseFilter.php"
if [ -f "$BASEFILTER_FILE" ]; then
    # Cek apakah method label() sudah ada
    if ! grep -q "protected function label()" "$BASEFILTER_FILE"; then
        echo "Adding label() method to BaseFilter.php..."
        # Backup original file
        cp "$BASEFILTER_FILE" "$BASEFILTER_FILE.backup"
        
        # Add the method before the closing brace
        sed -i '/^}$/i\\n    protected function label(): string\n    {\n        return $this->label;\n    }' "$BASEFILTER_FILE"
        echo "✅ BaseFilter.php patched successfully!"
    else
        echo "✅ BaseFilter.php already has label() method, skipping patch..."
    fi
else
    echo "⚠️  BaseFilter.php not found, skipping patch..."
fi

# Patch Avatar.php - mengubah align('center', 'middle') menjadi align('center', 'center')
echo "👉 Patching Avatar.php..."
AVATAR_FILE="/var/www/html/vendor/laravolt/avatar/src/Avatar.php"

if [ -f "$AVATAR_FILE" ]; then
    if grep -q "\$font->align('center', 'middle');" "$AVATAR_FILE"; then
        echo "Updating font alignment in Avatar.php..."

        # Backup original file (hanya sekali)
        [ ! -f "$AVATAR_FILE.backup" ] && cp "$AVATAR_FILE" "$AVATAR_FILE.backup"

        sed -i "s/\$font->align('center', 'middle');/\$font->align('center', 'center');/g" "$AVATAR_FILE"

        echo "✅ Avatar.php patched successfully!"
    else
        echo "✅ Avatar.php already patched, skipping..."
    fi
else
    echo "⚠️  Avatar.php not found, skipping patch..."
fi

# Generate APP_KEY kalau belum ada
if ! grep -q "^APP_KEY=" .env || grep -q "^APP_KEY=$" .env; then
    echo "👉 APP_KEY belum ada, generate baru..."
    php artisan key:generate --force
fi

# Pastikan storage:link ada (skip jika sudah exists)
if [ ! -L /var/www/html/public/storage ] && [ ! -d /var/www/html/public/storage ]; then
    echo "👉 Running: php artisan storage:link"
    php artisan storage:link || true
else
    echo "👉 Storage link already exists, skipping..."
fi

# Jalankan laravolt:link (skip jika sudah exists)
if [ ! -L /var/www/html/public/laravolt ] && [ ! -d /var/www/html/public/laravolt ]; then
    echo "👉 Running: php artisan laravolt:link"
    php artisan laravolt:link || true
else
    echo "👉 Laravolt link already exists, skipping..."
fi

echo "👉 Running: php artisan migrate"
php artisan migrate --force

# Default value jika environment variable tidak ada
LARAVOLT_ADMIN_NAME=${LARAVOLT_ADMIN_NAME:-Administrator}
LARAVOLT_ADMIN_EMAIL=${LARAVOLT_ADMIN_EMAIL:-admin@dpd.go.id}
LARAVOLT_ADMIN_PASSWORD=${LARAVOLT_ADMIN_PASSWORD:-secret}

echo "👉 Checking default Laravolt admin..."

if ! php artisan tinker --execute="echo \App\Models\User::where('email', '${LARAVOLT_ADMIN_EMAIL}')->exists() ? '1' : '0';" | grep -q "1"; then
    echo "👉 Creating default Laravolt admin..."
    php artisan laravolt:admin \
        "$LARAVOLT_ADMIN_NAME" \
        "$LARAVOLT_ADMIN_EMAIL" \
        "$LARAVOLT_ADMIN_PASSWORD"
else
    echo "✅ Default Laravolt admin already exists, skipping..."
fi

# Clear cache setiap kali start container
echo "👉 Clearing Laravel cache"
php artisan optimize:clear

echo "👉 Optimizing Laravel caches"
php artisan optimize

exec "$@"