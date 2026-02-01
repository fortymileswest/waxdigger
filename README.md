# Waxdigger

WordPress site built with the Forty Miles West starter theme.

## Stack

- WordPress (latest)
- ACF Pro (manual installation required)
- Tailwind CSS (compiled locally)
- Alpine.js (local file)
- GSAP + ScrollTrigger (local files)
- The SEO Framework (manual installation required)
- Classic Editor (Gutenberg disabled)

## Requirements

- [DDEV](https://ddev.readthedocs.io/en/stable/)
- Node.js 20+
- ACF Pro license

## Quick Start

```bash
# Clone the repository
git clone <repo-url> project-name
cd project-name

# Run setup
./scripts/setup.sh
```

The setup script will:
1. Start DDEV
2. Download and install WordPress
3. Configure all settings
4. Activate the theme
5. Build CSS
6. Open auto-login URL

## Development

### CSS

```bash
# Navigate to theme directory
cd wp-content/themes/fmw

# Watch for changes
ddev exec npm run css:watch

# Build for development
ddev exec npm run css:dev

# Build for production (minified)
ddev exec npm run css
```

### Theme Structure

```
wp-content/themes/fmw/
├── style.css              # Theme metadata
├── functions.php          # Main functions file
├── index.php              # Fallback template
├── front-page.php         # Home page template
├── page.php               # Page template
├── single.php             # Single post template
├── archive.php            # Archive template
├── 404.php                # 404 template
├── header.php             # Header template
├── footer.php             # Footer template
├── inc/
│   ├── setup.php          # Theme setup
│   ├── enqueue.php        # Asset enqueueing
│   ├── acf.php            # ACF configuration
│   ├── helpers.php        # Helper functions
│   └── form-handler.php   # AJAX form handling
├── partials/              # Page section partials
├── components/            # Reusable components
├── assets/
│   ├── css/
│   │   ├── input.css      # Tailwind input
│   │   └── output.css     # Compiled CSS
│   ├── js/
│   │   ├── app.js         # Main JavaScript
│   │   └── vendor/        # Third-party JS
│   ├── fonts/             # Local fonts
│   ├── icons/             # SVG icons
│   └── images/            # Theme images
├── acf-json/              # ACF field group JSON
├── tailwind.config.js
└── package.json
```

## Deployment

```bash
./scripts/deploy.sh
```

## Manual Plugin Installation

After setup, install manually:
1. ACF Pro - Upload via Plugins > Add New > Upload
2. The SEO Framework - Install from plugin repository

## Credentials

- **URL:** https://waxdigger.ddev.site
- **Admin:** https://waxdigger.ddev.site/wp-admin
- **Username:** admin
- **Password:** admin

## Author

Danny Wincott
[Forty Miles West](https://fortymileswest.co.uk)
