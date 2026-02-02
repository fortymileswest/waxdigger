# CLAUDE.md - Waxdigger

This file provides context for AI assistants working on this project.

## Project Overview

This is a WordPress site called "Waxdigger" using the Forty Miles West Theme (folder: `fmw`). Built with a clean, minimal stack optimised for AI-assisted development.

## Technology Stack

- **CMS:** WordPress (latest)
- **Fields:** ACF Pro (manual installation)
- **CSS:** Tailwind CSS v3.4 (compiled locally, output.css committed)
- **JS Framework:** Alpine.js (local vendor file)
- **Animation:** GSAP + ScrollTrigger (local vendor files)
- **SEO:** The SEO Framework plugin (manual installation)
- **Editor:** Classic Editor (Gutenberg disabled)
- **Local Dev:** DDEV

## Key Directories

```
wp-content/themes/fmw/
├── inc/           # PHP includes (setup, enqueue, acf, helpers, form-handler)
├── partials/      # Page section templates (used with ACF flexible content)
├── components/    # Reusable PHP components (button, card, icon)
├── assets/css/    # Tailwind input.css and compiled output.css
├── assets/js/     # Main app.js and vendor/ folder
├── assets/fonts/  # Local font files (no CDN)
├── assets/icons/  # Individual SVG files (no icon libraries)
├── acf-json/      # ACF field group JSON sync
```

## Commands

```bash
# Start development
ddev start

# Build CSS (production)
ddev exec npm run css

# Watch CSS changes
ddev exec npm run css:watch

# WP-CLI commands
ddev wp <command>

# Access site
https://waxdigger.ddev.site

# Auto-login
ddev wp login create admin --launch
```

## Coding Conventions

### PHP

- Use `fmw_` prefix for all functions
- Use British English in all text
- Always escape output: `esc_html()`, `esc_attr()`, `esc_url()`
- Use ACF helper functions: `fmw_get_field()`, `fmw_get_sub_field()`, `fmw_get_option()`

### Templates

- Flexible content sections go in `partials/`
- Reusable UI elements go in `components/`
- Include partials via `fmw_partial('name', $args)`
- Include components via `fmw_component('name', $args)`

### CSS

- Use Tailwind utility classes for layout and spacing
- Custom styles in `input.css` using `@layer` directives
- No decorative styling - structural only
- Compile with `npm run css`

### JavaScript

- Alpine.js for interactivity
- GSAP for animations
- Form submissions via AJAX with nonce verification

## Design Philosophy

**STRUCTURAL CODE ONLY - NO DESIGN DECISIONS**

Do NOT create:
- Gradient backgrounds
- Pre-designed layouts
- Colour application beyond the defined system
- Generic SaaS/startup template aesthetics

Do create:
- Clean semantic HTML
- Minimal Tailwind for layout (spacing, containers)
- ACF field integration with proper escaping
- Component/partial architecture ready for styling

## ACF Patterns

### Field Group Conventions

When creating ACF field groups:

- **Tabs:** Always use vertical tabs (`"placement": "left"`)
- **Images:** Max 0.5MB, formats: jpg, jpeg, png, webp only
- **Image preview:** Use smallest size (`"preview_size": "thumbnail"`)
- **Auto-sync:** Field groups auto-sync from JSON on admin load

### Flexible Content (Page Sections)

```php
if ( have_rows( 'sections' ) ) :
    while ( have_rows( 'sections' ) ) : the_row();
        $layout = get_row_layout();
        $partial = FMW_DIR . '/partials/' . $layout . '.php';
        if ( file_exists( $partial ) ) {
            include $partial;
        }
    endwhile;
endif;
```

### Options Page

Access site-wide settings:
```php
$value = fmw_get_option( 'field_name', 'default' );
```

## Helper Functions

```php
// Output SVG icon
fmw_icon( 'icon-name', 'css-class' );

// Get SVG icon as string
$svg = fmw_get_icon( 'icon-name' );

// Output responsive image
fmw_image( $image_id_or_acf_array, 'size', 'css-class' );

// Get image URL
$url = fmw_get_image_url( $image, 'large' );

// Include component
fmw_component( 'button', ['text' => 'Click', 'url' => '#'] );

// Include partial
fmw_partial( 'hero', ['heading' => 'Title'] );
```

## Form Handling

Forms use AJAX with nonce verification:

```html
<form data-ajax-form data-action="fmw_contact_form">
    <!-- Honeypot -->
    <input type="text" name="website" class="hidden">

    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <textarea name="message" required></textarea>

    <button type="submit">Send</button>
    <div data-form-message></div>
</form>
```

## Asset Rules

- All fonts downloaded locally to `assets/fonts/` (no Google Fonts CDN)
- All icons as individual SVG files in `assets/icons/` (no icon libraries)
- Alpine.js, GSAP served from `assets/js/vendor/`
- Nothing loaded from external CDNs

## Important Notes

1. **Gutenberg is disabled** - All content uses Classic Editor
2. **Comments are disabled** site-wide
3. **Media not organised** by year/month folders
4. **ACF JSON sync** - Field groups saved to `acf-json/`
5. **Tailwind output.css is committed** - No server-side build step
6. **Always flush cache after changes** - Run `ddev wp cache flush` after any PHP/template changes

## TODO

- [x] **Deploy to Staging** — ✓ Deployed 2 Feb 2025
  - Credentials stored in `.env` (gitignored)
  - Theme deployed via rsync, DB synced with search-replace
  - `record_label` taxonomy (56 terms) synced
  - Live at https://waxdigger.com
  - **Post-deploy checklist:**
    - Fix file permissions: `chmod 644` for files (especially `output.css`)
    - Clear Breeze cache: `rm -rf wp-content/cache/breeze/*`
    - Purge Varnish via API (see Staging Deploy section below)
    - Purge Cloudflare cache from dashboard

- [ ] **Sticky Header Hide/Show** — Fix scroll behaviour
  - Header should hide on scroll down (only after scrolling 300px+)
  - Header should show on scroll up with fade animation
  - Currently has jitter issue on initial scroll/near top of page
  - CSS transitions are in place (input.css), logic is in header.php Alpine x-init
  - Needs debugging to prevent flicker when first scrolling down

- [ ] **YouTube Scraper** — Build WP-CLI command to auto-fetch YouTube videos for products
  - User needs YouTube Data API key from https://console.cloud.google.com/
  - Enable "YouTube Data API v3", create API key under Credentials
  - ACF repeater field `youtube_videos` already exists on products (url + title)
  - Scraper should search "Artist - Title" and save top results

- [ ] **Single Product Page** — Custom template with video display

## WP-CLI Commands

```bash
# Import records from CSV
ddev wp fmw import-records /path/to/file.csv

# Fetch cover art from Discogs
ddev wp fmw fetch-covers
ddev wp fmw fetch-covers --dry-run
ddev wp fmw fetch-covers --limit=5
ddev wp fmw fetch-covers --product=123

# Migrate ACF labels to taxonomy (already run - for reference)
ddev wp fmw migrate-labels --dry-run
ddev wp fmw migrate-labels --delete-acf
```

## Custom Taxonomies

### Record Labels

Products use a `record_label` taxonomy (not ACF field). Helper functions:

```php
// Get label name
$label = fmw_get_product_label( $product_id );

// Get label term object (for URLs, IDs)
$term = fmw_get_product_label_term( $product_id );
if ( $term ) {
    $url = get_term_link( $term );
}
```

Archive pages available at `/label/{slug}/`

## API Keys

- **Discogs Token:** Stored in `inc/discogs-scraper.php`
- **Cloudways:** Stored in `.env` (gitignored)

## Staging Deploy

Credentials in `.env`. Server: `206.189.31.119`, App ID: `6182241`, Server ID: `1583104`

```bash
# 1. Build CSS
npm run css

# 2. Deploy theme via rsync
rsync -avz --delete \
  --exclude='.git' --exclude='node_modules' --exclude='.DS_Store' \
  -e "sshpass -p '\$CLOUDWAYS_SSH_PASS' ssh -o StrictHostKeyChecking=no" \
  wp-content/themes/fmw/ \
  wax101@206.189.31.119:public_html/wp-content/themes/fmw/

# 3. Fix permissions
ssh wax101@206.189.31.119 'find public_html/wp-content/themes/fmw -type f -exec chmod 644 {} \;'

# 4. Export local DB and import to staging
ddev export-db --file=export.sql
scp export.sql wax101@206.189.31.119:tmp/
ssh wax101@206.189.31.119 'cd public_html && gunzip -c ~/tmp/export.sql > ~/tmp/import.sql && wp db import ~/tmp/import.sql'

# 5. Search-replace URLs
ssh wax101@206.189.31.119 'cd public_html && wp search-replace "https://waxdigger.ddev.site" "https://waxdigger.com" --all-tables'

# 6. Clear all caches
ssh wax101@206.189.31.119 'cd public_html && wp cache flush && rm -rf wp-content/cache/breeze/*'

# 7. Purge Varnish via API
TOKEN=$(curl -s -X POST "https://api.cloudways.com/api/v1/oauth/access_token" \
  --data-urlencode "email=$CLOUDWAYS_EMAIL" \
  --data-urlencode "api_key=$CLOUDWAYS_API_KEY" | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)
curl -s -X POST "https://api.cloudways.com/api/v1/service/varnish" \
  -H "Authorization: Bearer $TOKEN" -d "server_id=1583104&action=purge"

# 8. Purge Cloudflare from dashboard
```

## New Project Setup

When starting a new client project from this starter:

1. Copy the entire directory
2. Update `.ddev/config.yaml` with new project name
3. Update `style.css` theme name
4. Run `./scripts/setup.sh`
5. Install ACF Pro and The SEO Framework
6. Begin development
