#!/bin/bash

# DPDRI Award App Docker Deployment Script for AWS EC2
# Usage: ./docker-deploy.sh [branch] [environment]
# Example: ./docker-deploy.sh develop production

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default values
BRANCH=${1:-master}
ENVIRONMENT=${2:-production}
APP_DIR="/home/ubuntu/dpd-award-app"
BACKUP_DIR="/home/ubuntu/backups"

echo -e "${GREEN}🚀 Starting DPDRI Award App Docker Deployment...${NC}"
echo -e "${YELLOW}Branch: $BRANCH${NC}"
echo -e "${YELLOW}Environment: $ENVIRONMENT${NC}"
echo -e "${YELLOW}Directory: $APP_DIR${NC}"

# Function to print status
print_status() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Check if script is run from correct directory
if [ ! -f "docker-compose.yml" ]; then 
    print_error "docker-compose.yml not found. Please run this script from the application directory."
    exit 1
fi

# # Create backup directory if not exists
# mkdir -p $BACKUP_DIR

# # Navigate to application directory
# cd $APP_DIR || { print_error "Application directory not found"; exit 1; }

# print_info "Creating pre-deployment backup..."
# # Backup database before deployment
# if docker-compose ps pgsql | grep -q "Up"; then
#     BACKUP_FILE="$BACKUP_DIR/pre_deploy_$(date +%Y%m%d_%H%M%S).sql.gz"
#     docker-compose exec -T pgsql pg_dump -U dpd_user dpd_app | gzip > "$BACKUP_FILE"
#     print_status "Database backup created: $BACKUP_FILE"
# else
#     print_warning "Database container not running, skipping backup"
# fi

# Pull latest changes
print_info "Pulling latest changes from repository..."
git fetch origin
git checkout $BRANCH
git pull origin $BRANCH
print_status "Repository updated to latest $BRANCH"

# Stop containers
print_info "Stopping existing containers..."
# Stop dengan project name yang sekarang (TANPA menghapus volume untuk menjaga data database)
docker-compose -p dpd-award-app -f docker-compose.yml -f docker-compose.prod.yml down || true
# Stop tanpa project name (default) - TANPA menghapus volume
docker-compose -f docker-compose.yml -f docker-compose.prod.yml down || true

# Optional: Remove only specific volumes if needed (uncomment if you want to reset data)
# print_warning "Removing application cache volumes only (keeping database data)..."
# docker volume rm dpd-award-redis 2>/dev/null || true

# Hapus container yang mungkin masih ada dengan nama pattern tertentu
print_info "Cleaning up any remaining containers..."
docker ps -a | grep -E "(dpd-award|dpd-award-app)" | awk '{print $1}' | xargs -r docker rm -f || true

docker network prune -f || true
print_status "Containers stopped and cleaned"

# Remove old images to free space (optional)
print_info "Cleaning up old Docker images..."
docker image prune -f
print_status "Old images cleaned"

# Build and start containers
print_info "Building and starting containers..."
if [ "$ENVIRONMENT" = "production" ]; then
    if [ -f "docker-compose.prod.yml" ]; then
        docker-compose -p dpd-award-app -f docker-compose.yml -f docker-compose.prod.yml up -d --build
    else
        docker-compose -p dpd-award-app up -d --build
    fi
else
    docker-compose -p dpd-award-app up -d --build
fi

print_status "Containers built and started"

# Wait for services to be ready
print_info "Waiting for services to start..."
sleep 30

# Check if containers are running
print_info "Checking container health..."
if ! docker-compose -p dpd-award-app ps | grep -q "Up"; then
    print_error "Some containers failed to start"
    docker-compose -p dpd-award-app logs
    exit 1
fi

print_status "All containers are running"

# Wait for application container to finish initialization
print_info "Waiting for application container to complete initialization..."
sleep 10

# Check if application container started successfully
print_info "Checking application container logs..."
if docker-compose -p dpd-award-app logs app | grep -q "✅ PostgreSQL is ready"; then
    print_status "Application container initialized successfully"
else
    print_warning "Application container may still be initializing. Checking logs:"
    docker-compose -p dpd-award-app logs --tail=20 app
fi

# Run seeders if requested (only thing not handled by entrypoint.sh)
if [ "$3" = "--seed" ]; then
    print_info "Running database seeders..."
    docker-compose -p dpd-award-app exec app php artisan db:seed --force
    print_status "Database seeders completed"
fi

# Test application
print_info "Testing application..."
sleep 5
if curl -f -s --head http://localhost:8000 > /dev/null; then
    print_status "Application is responding correctly"
else
    print_warning "Application might not be responding on port 8000. Check logs:"
    docker-compose -p dpd-award-app logs app | tail -10
fi

# Display deployment summary
echo -e "${GREEN}"
echo "========================================"
echo "   DOCKER DEPLOYMENT COMPLETED! 🎉"
echo "========================================"
echo -e "${NC}"

print_info "Deployment Summary:"
echo "  - Branch: $BRANCH"
echo "  - Environment: $ENVIRONMENT"
echo "  - Backup created: $(ls -t $BACKUP_DIR/pre_deploy_*.sql.gz 2>/dev/null | head -1 || echo 'None')"
echo "  - Application URL: http://$(curl -s http://checkip.amazonaws.com || echo 'localhost'):8000"

# Show container status
echo -e "${YELLOW}Container Status:${NC}"
docker-compose -p dpd-award-app ps

# Show recent logs
echo -e "${YELLOW}Recent Application Logs:${NC}"
docker-compose -p dpd-award-app logs --tail=10 app

print_status "Deployment completed successfully!"