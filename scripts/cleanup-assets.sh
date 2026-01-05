#!/bin/bash
# Asset Cleanup Script for Kesbangpol Kaltara
# This script removes unused demo files and archives to reduce deployment size
# Run with: bash scripts/cleanup-assets.sh

set -e

echo "Starting asset cleanup..."

# Remove demo CSS files (56 files, ~400KB)
if [ -d "public/assets/css/demos" ]; then
    echo "Removing demo CSS files..."
    rm -rf public/assets/css/demos
    echo "  - Removed public/assets/css/demos/"
fi

# Remove css.zip archive
if [ -f "public/assets/css/css.zip" ]; then
    echo "Removing css.zip archive..."
    rm -f public/assets/css/css.zip
    echo "  - Removed public/assets/css/css.zip"
fi

# Remove Revolution Slider if not used (19MB)
# Uncomment the lines below if Revolution Slider is not being used
# if [ -d "public/assets/vendor/rs-plugin" ]; then
#     echo "Removing Revolution Slider plugin..."
#     rm -rf public/assets/vendor/rs-plugin
#     echo "  - Removed public/assets/vendor/rs-plugin/ (19MB)"
# fi

# Remove duplicate icon sets - keep only one
# Uncomment if you want to keep only Bootstrap Icons
# if [ -d "public/assets/vendor/linea-icons" ]; then
#     echo "Removing Linea Icons (keeping Bootstrap Icons)..."
#     rm -rf public/assets/vendor/linea-icons
#     echo "  - Removed public/assets/vendor/linea-icons/ (3.2MB)"
# fi

# if [ -d "public/assets/vendor/simple-line-icons" ]; then
#     echo "Removing Simple Line Icons (keeping Bootstrap Icons)..."
#     rm -rf public/assets/vendor/simple-line-icons
#     echo "  - Removed public/assets/vendor/simple-line-icons/ (3MB)"
# fi

echo ""
echo "Asset cleanup complete!"
echo ""
echo "Recommended manual actions:"
echo "  1. Review and remove unused vendor libraries in public/assets/vendor/"
echo "  2. Compress PNG images using TinyPNG or similar tools"
echo "  3. Run 'npm install && npm run build:prod' to rebuild optimized assets"
echo "  4. Run 'php artisan optimize' to cache Laravel configuration"
