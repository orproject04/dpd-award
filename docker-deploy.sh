#!/bin/bash

# DPDRI Award App Docker Deployment Script for AWS EC2 (BLUE-GREEN DEPLOYMENT)
# Usage: ./docker-deploy.sh [branch] [environment]
# Example: ./docker-deploy.sh master local

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
APP_DIR="/www/wwwroot/awards.dpd.go.id"
BACKUP_DIR="/home/ubuntu/backups"

echo -e "${GREEN}🚀 Starting DPDRI Award App Blue-Green Deployment...${NC}"
echo -e "${YELLOW}Branch: $BRANCH${NC}"
echo -e "${YELLOW}Environment: $ENVIRONMENT${NC}"

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

# Pull latest changes
print_info "Pulling latest changes from repository..."
git fetch origin
git checkout $BRANCH
git pull origin $BRANCH
print_status "Repository updated to latest $BRANCH"

# Remove old images to free space
print_info "Cleaning up old Docker images..."
docker image prune -f || true

# Determine Compose Files
COMPOSE_ARGS="-p dpd-award-app -f docker-compose.yml"
if [ "$ENVIRONMENT" = "production" ] && [ -f "docker-compose.prod.yml" ]; then
    COMPOSE_ARGS="$COMPOSE_ARGS -f docker-compose.prod.yml"
fi

# ==========================================
# BLUE-GREEN DEPLOYMENT LOGIC
# ==========================================

# Make sure proxy is running
print_info "Ensuring Nginx proxy is running..."
docker-compose $COMPOSE_ARGS up -d proxy

# Detect Active Container
ACTIVE_COLOR="blue"
if docker-compose $COMPOSE_ARGS ps --status=running | grep -q "app-green"; then
    ACTIVE_COLOR="green"
fi

if [ "$ACTIVE_COLOR" = "blue" ]; then
    IDLE_COLOR="green"
else
    IDLE_COLOR="blue"
fi

print_info "Current active environment: $ACTIVE_COLOR"
print_info "Deploying new version to: $IDLE_COLOR"

# 1. Build and start idle container
print_info "Building new image (using cache for speed)..."
docker-compose $COMPOSE_ARGS build app-$IDLE_COLOR
docker-compose $COMPOSE_ARGS up -d app-$IDLE_COLOR
print_status "Started app-$IDLE_COLOR"

# 2. Wait for idle container to become healthy
print_info "Waiting for app-$IDLE_COLOR to initialize (entrypoint.sh)..."
MAX_ATTEMPTS=20
ATTEMPT=1
HEALTHY=false

while [ $ATTEMPT -le $MAX_ATTEMPTS ]; do
    # Check if container is physically up
    if ! docker-compose $COMPOSE_ARGS ps | grep -q "app-$IDLE_COLOR"; then
        print_error "Container app-$IDLE_COLOR crashed during startup!"
        exit 1
    fi
    
    # We check if Nginx inside the app container has started responding
    STATUS_CODE=$(docker-compose $COMPOSE_ARGS exec -T app-$IDLE_COLOR curl -s -o /dev/null -w "%{http_code}" http://localhost || echo "000")
    if [ "$STATUS_CODE" = "200" ] || [ "$STATUS_CODE" = "302" ]; then
        HEALTHY=true
        print_status "app-$IDLE_COLOR is healthy and ready!"
        break
    fi
    
    echo "Wait... (Attempt $ATTEMPT/$MAX_ATTEMPTS)"
    sleep 5
    ATTEMPT=$((ATTEMPT+1))
done

if [ "$HEALTHY" = false ]; then
    print_error "app-$IDLE_COLOR failed to become healthy. Aborting deployment."
    docker-compose $COMPOSE_ARGS logs --tail=50 app-$IDLE_COLOR
    print_warning "Rolling back (keeping $ACTIVE_COLOR active)."
    docker-compose $COMPOSE_ARGS stop app-$IDLE_COLOR
    exit 1
fi

# 3. Swap Proxy
print_info "Swapping proxy traffic to $IDLE_COLOR..."
sed -i "s/app-$ACTIVE_COLOR/app-$IDLE_COLOR/g" docker/proxy/default.conf
docker-compose $COMPOSE_ARGS exec -T proxy nginx -s reload || docker-compose $COMPOSE_ARGS restart proxy
print_status "Traffic successfully routed to app-$IDLE_COLOR! (ZERO DOWNTIME)"

# 4. Stop and remove old container
print_info "Shutting down old environment ($ACTIVE_COLOR)..."
docker-compose $COMPOSE_ARGS stop app-$ACTIVE_COLOR || true
docker-compose $COMPOSE_ARGS rm -f app-$ACTIVE_COLOR || true

# 5. Clear cache on the new container again (to prevent old container from poisoning shared cache)
print_info "Clearing cache on new environment ($IDLE_COLOR) to ensure fresh views..."
docker-compose $COMPOSE_ARGS exec -T app-$IDLE_COLOR php artisan view:clear || true
docker-compose $COMPOSE_ARGS exec -T app-$IDLE_COLOR php artisan responsecache:clear || true

# Display deployment summary
echo -e "${GREEN}"
echo "========================================"
echo "   BLUE-GREEN DEPLOYMENT COMPLETED! 🎉"
echo "========================================"
echo -e "${NC}"

print_info "Deployment Summary:"
echo "  - Branch: $BRANCH"
echo "  - Environment: $ENVIRONMENT"
echo "  - Now Active: app-$IDLE_COLOR"
echo "  - Application URL: http://$(curl -s http://checkip.amazonaws.com || echo 'localhost'):8005"

# Show container status
echo -e "${YELLOW}Container Status:${NC}"
docker-compose $COMPOSE_ARGS ps
