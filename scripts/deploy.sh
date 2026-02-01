#!/bin/bash

# Forty Miles West Theme - Deploy Script
# This script prepares the theme for deployment

set -e

# Colours for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Colour

echo -e "${GREEN}Preparing Waxdigger for deployment...${NC}"

# Navigate to theme directory
cd "$(dirname "$0")/../wp-content/themes/fmw"

# Build production CSS
echo -e "${YELLOW}Building production CSS...${NC}"
npm run css

echo -e "${GREEN}Build complete!${NC}"
echo ""
echo -e "${YELLOW}Deployment instructions:${NC}"
echo ""
echo "1. Upload the following directories to your server:"
echo "   - wp-content/themes/fmw"
echo "   - wp-content/plugins (ACF Pro, The SEO Framework)"
echo ""
echo "2. On the server:"
echo "   - Import the database"
echo "   - Update wp-config.php with production database credentials"
echo "   - Run: wp search-replace 'https://waxdigger.ddev.site' 'https://your-domain.com'"
echo "   - Sync ACF JSON fields"
echo ""
echo "3. Verify:"
echo "   - Theme is activated"
echo "   - Permalinks are set correctly"
echo "   - ACF fields are synced"
echo ""
echo -e "${GREEN}Ready for deployment!${NC}"
