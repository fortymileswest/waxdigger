<?php
/**
 * WP-CLI Discogs Cover Art Scraper
 *
 * Fetches high-res cover art from Discogs for all products.
 * - Minimum 800x800 resolution
 * - Maximum 0.5MB file size
 *
 * Usage: ddev wp fmw fetch-covers
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

/**
 * Fetch cover art from Discogs for products.
 */
class FMW_Discogs_Scraper_Command {

    /**
     * Discogs API token.
     */
    private $token = 'XOiumbOTUoJCdFkUpgpUmKpLFFRtspsVkkRMvDJi';

    /**
     * User agent for Discogs API.
     */
    private $user_agent = 'WaxdiggerRecordShop/1.0';

    /**
     * Minimum image dimension.
     */
    private $min_dimension = 800;

    /**
     * Maximum file size in bytes (0.5MB).
     */
    private $max_file_size = 524288;

    /**
     * Fetch cover art from Discogs for all products.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Preview without downloading images.
     *
     * [--limit=<number>]
     * : Limit number of products to process.
     *
     * [--product=<id>]
     * : Process a single product by ID.
     *
     * ## EXAMPLES
     *
     *     wp fmw fetch-covers
     *     wp fmw fetch-covers --dry-run
     *     wp fmw fetch-covers --limit=5
     *     wp fmw fetch-covers --product=123
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Associative arguments.
     */
    public function __invoke( $args, $assoc_args ) {
        $dry_run    = isset( $assoc_args['dry-run'] );
        $limit      = isset( $assoc_args['limit'] ) ? intval( $assoc_args['limit'] ) : -1;
        $product_id = isset( $assoc_args['product'] ) ? intval( $assoc_args['product'] ) : null;

        if ( ! function_exists( 'wc_get_products' ) ) {
            WP_CLI::error( 'WooCommerce is not active.' );
        }

        // Get products
        $query_args = array(
            'status' => 'publish',
            'limit'  => $limit,
        );

        if ( $product_id ) {
            $products = array( wc_get_product( $product_id ) );
            if ( ! $products[0] ) {
                WP_CLI::error( "Product {$product_id} not found." );
            }
        } else {
            $products = wc_get_products( $query_args );
        }

        WP_CLI::log( sprintf( 'Processing %d products...', count( $products ) ) );
        WP_CLI::log( sprintf( 'Min resolution: %dx%d, Max size: %s', $this->min_dimension, $this->min_dimension, size_format( $this->max_file_size ) ) );
        WP_CLI::log( $dry_run ? '--- DRY RUN ---' : '--- FETCHING COVERS ---' );
        WP_CLI::log( '' );

        $success = 0;
        $failed  = 0;
        $skipped = 0;

        foreach ( $products as $product ) {
            $id    = $product->get_id();
            $name  = $product->get_name();

            // Parse artist and title from product name
            $parts = $this->parse_artist_title( $name );

            if ( ! $parts ) {
                WP_CLI::warning( "[{$id}] Could not parse: {$name}" );
                $skipped++;
                continue;
            }

            $artist = $parts['artist'];
            $title  = $parts['title'];

            WP_CLI::log( "[{$id}] Searching: {$artist} - {$title}" );

            // Search Discogs
            $release = $this->search_discogs( $artist, $title );

            if ( ! $release ) {
                WP_CLI::warning( "  No match found" );
                $failed++;
                sleep( 1 );
                continue;
            }

            WP_CLI::log( "  Found: {$release['title']} (ID: {$release['id']})" );

            // Get release details for high-res images
            $release_details = $this->get_release_details( $release['id'], $release['type'] ?? 'release' );

            if ( ! $release_details ) {
                WP_CLI::warning( "  Could not fetch release details" );
                $failed++;
                sleep( 1 );
                continue;
            }

            // Find suitable image (800x800 minimum)
            $image = $this->find_suitable_image( $release_details );

            if ( ! $image ) {
                // Fallback to search result cover
                $cover_url = $release['cover_image'] ?? null;
                if ( $cover_url && strpos( $cover_url, 'spacer.gif' ) === false ) {
                    WP_CLI::log( "  Using search result thumbnail (may be smaller)" );
                    $image = array( 'uri' => $cover_url, 'width' => 0, 'height' => 0 );
                } else {
                    WP_CLI::warning( "  No suitable image found" );
                    $failed++;
                    sleep( 1 );
                    continue;
                }
            } else {
                WP_CLI::log( sprintf( "  Found image: %dx%d", $image['width'], $image['height'] ) );
            }

            if ( $dry_run ) {
                WP_CLI::log( "  [DRY RUN] Would download: {$image['uri']}" );
                $success++;
                sleep( 1 );
                continue;
            }

            // Download, resize if needed, and attach image
            $image_id = $this->download_and_process_image( $image['uri'], $id, $name );

            if ( $image_id ) {
                // Set as product image
                $product->set_image_id( $image_id );
                $product->save();
                WP_CLI::success( "  Attached image ID: {$image_id}" );
                $success++;
            } else {
                WP_CLI::warning( "  Failed to process image" );
                $failed++;
            }

            WP_CLI::log( '' );

            // Rate limit: Discogs allows 60 requests per minute
            sleep( 1 );
        }

        WP_CLI::log( '' );
        WP_CLI::success( "Done. Success: {$success}, Failed: {$failed}, Skipped: {$skipped}" );
    }

    /**
     * Parse artist and title from product name.
     */
    private function parse_artist_title( $name ) {
        $parts = explode( ' - ', $name, 2 );

        if ( count( $parts ) < 2 ) {
            return false;
        }

        return array(
            'artist' => trim( $parts[0] ),
            'title'  => trim( $parts[1] ),
        );
    }

    /**
     * Search Discogs for a release.
     */
    private function search_discogs( $artist, $title ) {
        // Clean up artist name
        $artist = preg_replace( '/\s*\(\d+\)\s*$/', '', $artist );
        $artist = preg_replace( '/\|/', ' ', $artist ); // Handle multiple artists

        // Clean up title
        $search_title = preg_replace( '/\s*\([^)]+\)\s*/', ' ', $title );
        $search_title = preg_replace( '/\s*E\.?P\.?\s*/i', ' ', $search_title );
        $search_title = trim( $search_title );

        // Build search query
        $query = urlencode( "{$artist} {$search_title}" );

        $url = "https://api.discogs.com/database/search?q={$query}&type=release&format=Vinyl&per_page=10";

        $response = $this->api_request( $url );

        if ( ! $response || empty( $response['results'] ) ) {
            // Try without format filter
            $url = "https://api.discogs.com/database/search?q={$query}&type=release&per_page=10";
            $response = $this->api_request( $url );
        }

        if ( ! $response || empty( $response['results'] ) ) {
            return null;
        }

        // Return first result with a cover image
        foreach ( $response['results'] as $result ) {
            if ( ! empty( $result['cover_image'] ) && strpos( $result['cover_image'], 'spacer.gif' ) === false ) {
                return $result;
            }
        }

        // Fallback to first result
        return $response['results'][0];
    }

    /**
     * Get release details from Discogs.
     */
    private function get_release_details( $release_id, $type = 'release' ) {
        // Use master endpoint if it's a master release
        if ( $type === 'master' ) {
            $url = "https://api.discogs.com/masters/{$release_id}";
        } else {
            $url = "https://api.discogs.com/releases/{$release_id}";
        }

        return $this->api_request( $url );
    }

    /**
     * Find a suitable image from release details.
     */
    private function find_suitable_image( $release ) {
        if ( empty( $release['images'] ) ) {
            return null;
        }

        $best_image = null;
        $best_size  = 0;

        foreach ( $release['images'] as $image ) {
            // Skip if no URI
            if ( empty( $image['uri'] ) ) {
                continue;
            }

            // Prefer primary images
            $is_primary = ( $image['type'] ?? '' ) === 'primary';

            $width  = $image['width'] ?? 0;
            $height = $image['height'] ?? 0;
            $size   = min( $width, $height );

            // Check if meets minimum dimension
            if ( $size >= $this->min_dimension ) {
                // If primary and meets requirements, use it
                if ( $is_primary ) {
                    return $image;
                }

                // Otherwise track best option
                if ( $size > $best_size ) {
                    $best_image = $image;
                    $best_size  = $size;
                }
            }
        }

        // Return best image found, even if under minimum
        if ( ! $best_image && ! empty( $release['images'] ) ) {
            // Just get the largest available
            foreach ( $release['images'] as $image ) {
                if ( empty( $image['uri'] ) ) {
                    continue;
                }
                $size = min( $image['width'] ?? 0, $image['height'] ?? 0 );
                if ( $size > $best_size ) {
                    $best_image = $image;
                    $best_size  = $size;
                }
            }
        }

        return $best_image;
    }

    /**
     * Make API request to Discogs.
     */
    private function api_request( $url ) {
        $args = array(
            'headers' => array(
                'Authorization' => 'Discogs token=' . $this->token,
                'User-Agent'    => $this->user_agent,
            ),
            'timeout' => 30,
        );

        $response = wp_remote_get( $url, $args );

        if ( is_wp_error( $response ) ) {
            return null;
        }

        $code = wp_remote_retrieve_response_code( $response );

        if ( $code !== 200 ) {
            return null;
        }

        $body = wp_remote_retrieve_body( $response );

        return json_decode( $body, true );
    }

    /**
     * Download, process (resize/compress), and attach image.
     */
    private function download_and_process_image( $url, $product_id, $title ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Download image
        $tmp = download_url( $url, 60 );

        if ( is_wp_error( $tmp ) ) {
            WP_CLI::warning( '  Download failed: ' . $tmp->get_error_message() );
            return false;
        }

        // Check file size and resize if needed
        $file_size = filesize( $tmp );
        WP_CLI::log( sprintf( '  Downloaded: %s', size_format( $file_size ) ) );

        // Get image info
        $image_info = getimagesize( $tmp );
        if ( ! $image_info ) {
            @unlink( $tmp );
            return false;
        }

        $width     = $image_info[0];
        $height    = $image_info[1];
        $mime_type = $image_info['mime'];

        WP_CLI::log( sprintf( '  Dimensions: %dx%d', $width, $height ) );

        // Resize/compress if over 0.5MB
        if ( $file_size > $this->max_file_size ) {
            WP_CLI::log( '  Compressing image...' );
            $tmp = $this->compress_image( $tmp, $mime_type, $width, $height );

            if ( ! $tmp ) {
                return false;
            }

            $new_size = filesize( $tmp );
            WP_CLI::log( sprintf( '  Compressed to: %s', size_format( $new_size ) ) );
        }

        // Get extension
        $ext = 'jpg';
        if ( $mime_type === 'image/png' ) {
            $ext = 'png';
        } elseif ( $mime_type === 'image/gif' ) {
            $ext = 'gif';
        } elseif ( $mime_type === 'image/webp' ) {
            $ext = 'webp';
        }

        $file_array = array(
            'name'     => sanitize_file_name( $title ) . '-cover.' . $ext,
            'tmp_name' => $tmp,
        );

        $attachment_id = media_handle_sideload( $file_array, $product_id, $title . ' - Cover' );

        if ( file_exists( $tmp ) ) {
            @unlink( $tmp );
        }

        if ( is_wp_error( $attachment_id ) ) {
            WP_CLI::warning( '  Sideload failed: ' . $attachment_id->get_error_message() );
            return false;
        }

        return $attachment_id;
    }

    /**
     * Compress image to fit under max file size.
     */
    private function compress_image( $file, $mime_type, $width, $height ) {
        // Load image based on type
        switch ( $mime_type ) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg( $file );
                break;
            case 'image/png':
                $image = imagecreatefrompng( $file );
                break;
            case 'image/gif':
                $image = imagecreatefromgif( $file );
                break;
            case 'image/webp':
                $image = imagecreatefromwebp( $file );
                break;
            default:
                return $file;
        }

        if ( ! $image ) {
            return $file;
        }

        // Calculate new dimensions (max 800px, maintain aspect ratio)
        $max_dim = 800;
        if ( $width > $max_dim || $height > $max_dim ) {
            if ( $width > $height ) {
                $new_width  = $max_dim;
                $new_height = intval( $height * ( $max_dim / $width ) );
            } else {
                $new_height = $max_dim;
                $new_width  = intval( $width * ( $max_dim / $height ) );
            }

            // Resize
            $resized = imagecreatetruecolor( $new_width, $new_height );

            // Preserve transparency for PNG
            if ( $mime_type === 'image/png' ) {
                imagealphablending( $resized, false );
                imagesavealpha( $resized, true );
            }

            imagecopyresampled( $resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
            imagedestroy( $image );
            $image = $resized;
        }

        // Save as JPEG with quality adjustment
        $new_file = $file . '_compressed.jpg';
        $quality  = 85;

        // Try progressively lower quality until under max size
        while ( $quality >= 60 ) {
            imagejpeg( $image, $new_file, $quality );

            if ( filesize( $new_file ) <= $this->max_file_size ) {
                break;
            }

            $quality -= 5;
        }

        imagedestroy( $image );

        // Clean up original
        @unlink( $file );

        return $new_file;
    }
}

WP_CLI::add_command( 'fmw fetch-covers', 'FMW_Discogs_Scraper_Command' );
