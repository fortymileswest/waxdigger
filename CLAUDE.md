# CLAUDE.md - Waxdigger

This file provides context for AI assistants working on this project. It is read by Claude Code, Cursor, Copilot, Windsurf, and other AI coding tools.

## Instructions for All AI Models

**Read this entire file before making changes.** Key rules:

1. **Use `fmw_` prefix** for all PHP functions
2. **British English** in all user-facing text
3. **Always escape output**: `esc_html()`, `esc_attr()`, `esc_url()`
4. **No external CDNs** — all assets self-hosted (fonts, icons, JS libraries)
5. **Tailwind utility classes** for styling — custom CSS in `input.css` only when needed
6. **Alpine.js** for interactivity (not jQuery, except WooCommerce hooks)
7. **Compile CSS** after any input.css changes: `npx tailwindcss -i ./assets/css/input.css -o ./assets/css/output.css --minify` (run from theme directory)
8. **Flush cache** after PHP changes: `ddev wp cache flush`
9. **No Gutenberg** — Classic Editor only
10. **No icon libraries** — individual SVG files in `assets/icons/`, used via `fmw_icon('name', 'classes')`
11. **Follow the design system** below — dark underground vinyl aesthetic, NOT generic templates
12. **Partials** go in `partials/`, reusable UI in `components/`
13. **`filemtime()`** for all CSS/JS version strings — never static constants
14. **Max image size**: 0.5MB, formats: jpg, jpeg, png, webp
15. **Product name convention**: "ARTIST - Title" (split on " - " for display)

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

## Design System

The homepage follows a dark underground vinyl aesthetic designed in Pencil.dev.

### Colour Palette
| Token | Hex | Usage |
|-------|-----|-------|
| `dark` | `#0d0d0d` | Primary dark background |
| `cream` | `#f0ece4` | Light backgrounds, body text on dark |
| `accent` | `#25ddb3` | Teal accent — buttons, highlights, labels |
| `teal-dark` | `#0a8c6a` | Dark teal — text accent on cream |
| `card-dark` | `#1a1a1a` | Card/input backgrounds on dark |

### Typography
- **JetBrains Mono** (`font-mono`): Labels, nav, body text, form inputs, prices — weights 400-800
- **Space Grotesk** (`font-display`): Headings, hero text — weight 700 only

### Section Pattern
Alternating `bg-dark` and `bg-cream` sections. 60px horizontal padding (`px-6 md:px-[60px]`), 80px vertical (`py-20`).

### Borders
Use `rgba(240, 236, 228, 0.125)` on dark backgrounds (cream at ~12% opacity). Use `rgba(13, 13, 13, 0.125)` on cream backgrounds.

### Component Conventions
- Section headers: numbered label (teal, `text-xs font-bold tracking-wider-2`) + heading (Space Grotesk, 32-40px) + subtitle/action
- Buttons: JetBrains Mono 10-11px, bold, uppercase, tracking-wider-2/3
- Primary CTA: `bg-accent text-dark` (teal bg, dark text)
- Secondary CTA: `border border-cream/50` on dark, `border border-dark/50` on cream
- Cards: no rounded corners, thin borders or no borders
- Icons: SVG files in `assets/icons/`, used via `fmw_icon('name', 'classes')`

### Design Philosophy
- Underground vinyl record shop aesthetic — NOT generic SaaS/startup
- Monospace typography throughout, geometric display headings
- Dark-first design with cream sections for contrast
- No rounded corners (except circular buttons/badges)
- Minimal, structural — no decorative gradients or shadows (except hero overlay)

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

- [x] **Deploy to Staging (Cloudways)** — Deployed 2 Feb 2025
- [x] **Migrate to xCloud** — Migrated 14 Feb 2026
  - Moved from Cloudways to xcloud-new (64.176.187.195)
  - Live at https://waxdigger.com
- [x] **Sticky Header Hide/Show** — Fixed
- [x] **YouTube Scraper** — Done
- [x] **Single Product Page** — Done
- [x] **Homepage Redesign** — Implemented 21 Feb 2026 (see below)
- [ ] **SCF Options Page** — Register "Homepage Settings" with Featured Release + Staff Picks product pickers
- [ ] **Responsive testing** — Mobile/tablet breakpoints for all homepage sections
- [ ] **Single product page** — Restyle to match dark brand theme
- [ ] **DJ Mix Section** — New page/section for 1-hour DJ mixes with tracklists. Use **Mixcloud embeds** (free, blanket PRS/PPL licensing included). Style embed wrappers to match dark brand. Each mix: Mixcloud iframe + tracklist displayed alongside. No self-hosting, no wavesurfer.js, no licensing costs. Product pages keep YouTube embeds for 30-second clips (already done).

## Homepage Redesign (21 Feb 2026)

Pixel-perfect implementation from Pencil.dev design file. All 10 sections built and functional.

### Implementation Status
| Section | File | Status |
|---------|------|--------|
| Header | `header.php` | Done — dark bg, compass logo, nav, cart/search/account icons |
| Hero | `partials/hero.php` | Done — featured release slider (Alpine.js fade), background image, CTAs |
| Ticker Strip | `partials/ticker-strip.php` | Done — CSS marquee animation, teal bg |
| New Arrivals | `partials/new-arrivals.php` | Done — 12-product horizontal slider, snap scroll, touch swipe |
| Genre Section | `partials/genre-section.php` | Done — all genres from taxonomy, horizontal slider, AI images |
| Staff Picks | `partials/staff-picks.php` | Done — featured pick + 5-row list, falls back to latest products |
| About | `partials/about-section.php` | Done — hardcoded, stats grid, quote |
| We Buy Records | `partials/we-buy-records.php` | Done — hardcoded, USP cards, testimonial |
| Newsletter | `partials/newsletter.php` | Done — email form, decorative text |
| Footer | `footer.php` | Done — cream bg, compass logo, 3 link columns, socials |

### Components
| Component | File | Status |
|-----------|------|--------|
| Cart Drawer | `components/cart-drawer.php` | Done — slide-out, AJAX, dark theme |
| Login Modal | `components/login-modal.php` | Done — dark theme, Alpine.js |
| Search Modal | `components/search-modal.php` | Done — dark theme, advanced filters |
| Exit Popup | `components/exit-popup.php` | Done — dark theme, discount code |
| Cookie Consent | `components/cookie-consent.php` | Done — dark theme, tracking script control |
| Back to Top | `components/back-to-top.php` | Done — appears after 500px scroll |

### Key Features
- **AJAX Add to Cart**: Global `fmwAddToCart(productId, buttonEl)` function — AJAX add, opens cart drawer, shows toast on duplicate
- **Cart Drawer**: Slides in from right, shows items/total, view basket + checkout links
- **Sliders**: Hero (fade transition), New Arrivals (snap scroll), Genre (snap scroll) — all touch-swipeable
- **Genre Images**: AI-generated (Flux Schnell) WebP images in `assets/images/genre-*.webp`, with taxonomy thumbnail fallback
- **Hero Background**: AI-generated `assets/images/hero-records-in-store.webp`
- **Cookie Consent**: Blocks tracking scripts until accepted, dispatches `cookies-accepted` event

### Data Flow
- **Dynamic**: Products from `wc_get_products()`, genres from `genre` taxonomy, labels from `record_label` taxonomy
- **SCF Options** (when configured): `featured_product` (hero slider), `staff_pick_featured` + `staff_picks_list` (staff picks)
- **Fallbacks**: Hero uses latest 5 products, Staff Picks uses latest 6 products
- **Hardcoded**: About, We Buy Records, Newsletter, Ticker Strip content

### Files Created
- `partials/`: ticker-strip, new-arrivals, genre-section, staff-picks, about-section, we-buy-records, newsletter
- `components/`: cookie-consent, back-to-top
- `assets/fonts/`: JetBrains Mono (5 weights) + Space Grotesk (bold) woff2
- `assets/images/`: hero-records-in-store.webp, 12 genre images (balearic, breaks, deep-house, drum-bass, electro, happy-hardcore, hip-hop, house, jungle, rave-hardcore, soul-funk, techno)
- `assets/icons/`: arrow-right, chevron-up, pound, banknote, truck, disc, shield-check, headphones, youtube, twitter

### Files Modified
- `tailwind.config.js` — brand colours, font families, custom utilities
- `assets/css/input.css` — @font-face, restyled all modals/drawers to dark brand theme, scrollbar-hide, ticker animation
- `header.php` — dark bg, compass logo, Alpine.js mobile menu, cart drawer trigger
- `footer.php` — cream bg, link columns, component includes (cart-drawer, login-modal, search-modal, exit-popup, cookie-consent, back-to-top)
- `front-page.php` — direct partial includes (hero, ticker-strip, new-arrivals, genre-section, staff-picks, about-section, we-buy-records, newsletter)
- `partials/hero.php` — complete rewrite with Alpine.js featured slider
- `inc/ajax-cart.php` — AJAX add-to-cart with duplicate detection

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

## Production Deploy (xCloud)

- **Server**: xcloud-new (`64.176.187.195`)
- **SSH alias**: `xcloud-new` (user `fortymileswest`, key `~/.ssh/id_ed25519_xcloud`)
- **Site owner**: `u4_waxdigger`
- **Site path**: `/var/www/waxdigger.com`
- **PHP**: 8.3 | **WP-CLI**: 2.12
- **WP Admin**: danny@leadpath.co.uk
- **WP Password**: Zg0K1YV3peVhzXU1IIHw

```bash
# 1. Build CSS
npm run css

# 2. Deploy theme via rsync (as site owner)
rsync -avz --delete \
  --exclude='.git' --exclude='node_modules' --exclude='.DS_Store' \
  -e "ssh -i ~/.ssh/id_ed25519_xcloud" \
  wp-content/themes/fmw/ \
  u4_waxdigger@64.176.187.195:/var/www/waxdigger.com/wp-content/themes/fmw/

# 3. Purge ALL caches (CRITICAL - do after every deploy)
# nginx-helper + redis-cache plugins MUST be active for this to work.
# If CSS/HTML looks stale after deploy, check: wp plugin list --status=active
ssh -i ~/.ssh/id_ed25519_xcloud u4_waxdigger@64.176.187.195 \
  "wp cache flush --path=/var/www/waxdigger.com && wp eval 'do_action(\"rt_nginx_helper_purge_all\");' --path=/var/www/waxdigger.com"

# 4. Purge Cloudflare cache (site is proxied through Cloudflare)
source ~/.config/wp-dev-env/credentials.env
ZONE_ID=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones?name=waxdigger.com" \
  -H "X-Auth-Email: $CLOUDFLARE_EMAIL" \
  -H "X-Auth-Key: $CLOUDFLARE_API_KEY" | python3 -c "import sys,json; print(json.load(sys.stdin)['result'][0]['id'])")
curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/purge_cache" \
  -H "X-Auth-Email: $CLOUDFLARE_EMAIL" \
  -H "X-Auth-Key: $CLOUDFLARE_API_KEY" \
  -H "Content-Type: application/json" \
  --data '{"purge_everything":true}'

# 5. Verify cache purge
curl -sI https://waxdigger.com | grep x-cache
# Should show: x-cache: MISS
# Also check CSS version matches file timestamp:
curl -s https://waxdigger.com | grep -o 'output\.css[^"]*'
```

### Required plugins for caching (must be active)

- **nginx-helper** — purges Nginx fastcgi cache via `rt_nginx_helper_purge_all` action
- **redis-cache** — object cache for WP transients/options

If these are inactive, the WP cache flush and Nginx purge commands silently do nothing, and stale HTML (with old CSS version strings) will keep being served. Always verify both are active after migrations or plugin updates.

### WP-CLI on production

```bash
# Run WP-CLI as site owner
ssh -i ~/.ssh/id_ed25519_xcloud u4_waxdigger@64.176.187.195 "wp <command> --path=/var/www/waxdigger.com"

# Examples
ssh -i ~/.ssh/id_ed25519_xcloud u4_waxdigger@64.176.187.195 "wp plugin list --path=/var/www/waxdigger.com"
ssh -i ~/.ssh/id_ed25519_xcloud u4_waxdigger@64.176.187.195 "wp option get siteurl --path=/var/www/waxdigger.com"
```

## New Project Setup

When starting a new client project from this starter:

1. Copy the entire directory
2. Update `.ddev/config.yaml` with new project name
3. Update `style.css` theme name
4. Run `./scripts/setup.sh`
5. Install ACF Pro and The SEO Framework
6. Begin development
