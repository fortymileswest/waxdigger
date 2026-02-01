#!/bin/bash

# Forty Miles West Theme - Setup Script
# This script sets up a fresh WordPress installation with DDEV

set -e

# Colours for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Colour

echo -e "${GREEN}Starting Waxdigger Setup...${NC}"

# Navigate to project root
cd "$(dirname "$0")/.."

# Start DDEV
echo -e "${YELLOW}Starting DDEV...${NC}"
ddev start

# Download WordPress
echo -e "${YELLOW}Downloading WordPress...${NC}"
ddev wp core download --locale=en_GB --force

# Create wp-config.php
echo -e "${YELLOW}Creating WordPress configuration...${NC}"
ddev wp config create --dbname=db --dbuser=db --dbpass=db --dbhost=db --force

# Install WordPress
echo -e "${YELLOW}Installing WordPress...${NC}"
ddev wp core install \
    --url="https://waxdigger.ddev.site" \
    --title="Waxdigger" \
    --admin_user="admin" \
    --admin_password="admin" \
    --admin_email="danny@leadpath.co.uk" \
    --skip-email

# Set site language and timezone
echo -e "${YELLOW}Configuring WordPress settings...${NC}"
ddev wp language core install en_GB
ddev wp site switch-language en_GB
ddev wp option update timezone_string "Europe/London"

# Disable comments
ddev wp option update default_comment_status "closed"
ddev wp option update default_ping_status "closed"

# Disable media organisation by year/month
ddev wp option update uploads_use_yearmonth_folders 0

# Set image sizes
ddev wp option update thumbnail_size_w 250
ddev wp option update thumbnail_size_h 250
ddev wp option update thumbnail_crop 1
ddev wp option update medium_size_w 950
ddev wp option update medium_size_h 950
ddev wp option update large_size_w 1920
ddev wp option update large_size_h 1080

# Disable Gutenberg (use Classic Editor)
ddev wp plugin install classic-editor --activate

# Set permalink structure
ddev wp rewrite structure '/%postname%/'

# Activate the theme
echo -e "${YELLOW}Activating FMW theme...${NC}"
ddev wp theme activate fmw

# Delete default themes
echo -e "${YELLOW}Cleaning up default themes...${NC}"
ddev wp theme delete twentytwentyfour twentytwentythree twentytwentytwo twentytwentyone twentytwenty || true

# Delete default plugins
echo -e "${YELLOW}Cleaning up default plugins...${NC}"
ddev wp plugin delete akismet hello || true

# Delete sample content
echo -e "${YELLOW}Removing sample content...${NC}"
ddev wp post delete 1 --force || true
ddev wp post delete 2 --force || true
ddev wp post delete 3 --force || true

# Create a home page
echo -e "${YELLOW}Creating home page...${NC}"
ddev wp post create --post_type=page --post_title="Home" --post_status=publish
ddev wp option update show_on_front "page"
ddev wp option update page_on_front $(ddev wp post list --post_type=page --name=home --field=ID)

# Install npm dependencies and build CSS
echo -e "${YELLOW}Installing npm dependencies...${NC}"
cd wp-content/themes/fmw
ddev exec npm install

echo -e "${YELLOW}Building CSS...${NC}"
ddev exec npm run css

cd ../../..

# Generate login URL
echo -e "${GREEN}Setup complete!${NC}"
echo ""
echo -e "${YELLOW}Site URL:${NC} https://waxdigger.ddev.site"
echo -e "${YELLOW}Admin URL:${NC} https://waxdigger.ddev.site/wp-admin"
echo -e "${YELLOW}Username:${NC} admin"
echo -e "${YELLOW}Password:${NC} admin"
echo ""
echo -e "${YELLOW}Auto-login URL:${NC}"
ddev wp login create admin --launch

echo ""
echo -e "${GREEN}Next steps:${NC}"
echo "1. Install ACF Pro manually"
echo "2. Install The SEO Framework plugin"
echo "3. Start developing!"
