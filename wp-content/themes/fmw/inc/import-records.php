<?php
/**
 * WP-CLI Import Command for VinylSnap CSV
 *
 * Usage: ddev wp fmw import-records /path/to/file.csv
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Only load if WP-CLI is active
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

/**
 * Import vinyl records from VinylSnap CSV.
 */
class FMW_Import_Records_Command {

    /**
     * Import records from CSV file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to the CSV file.
     *
     * [--dry-run]
     * : Preview import without creating products.
     *
     * ## EXAMPLES
     *
     *     wp fmw import-records /path/to/records.csv
     *     wp fmw import-records /path/to/records.csv --dry-run
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Associative arguments.
     */
    public function __invoke( $args, $assoc_args ) {
        $file    = $args[0];
        $dry_run = isset( $assoc_args['dry-run'] );

        if ( ! file_exists( $file ) ) {
            WP_CLI::error( "File not found: {$file}" );
        }

        if ( ! function_exists( 'wc_get_product' ) ) {
            WP_CLI::error( 'WooCommerce is not active.' );
        }

        // Read CSV
        $handle = fopen( $file, 'r' );
        if ( ! $handle ) {
            WP_CLI::error( 'Could not open file.' );
        }

        // Get headers
        $headers = fgetcsv( $handle );
        if ( ! $headers ) {
            WP_CLI::error( 'Could not read CSV headers.' );
        }

        // Normalize headers
        $headers = array_map( 'trim', $headers );
        $headers = array_map( 'strtolower', $headers );
        $headers = array_map( function( $h ) {
            return str_replace( ' ', '_', $h );
        }, $headers );

        WP_CLI::log( 'Headers: ' . implode( ', ', $headers ) );
        WP_CLI::log( $dry_run ? '--- DRY RUN ---' : '--- IMPORTING ---' );
        WP_CLI::log( '' );

        $count   = 0;
        $errors  = 0;

        while ( ( $row = fgetcsv( $handle ) ) !== false ) {
            // Skip empty rows
            if ( empty( array_filter( $row ) ) ) {
                continue;
            }

            // Map row to headers
            $data = array_combine( $headers, $row );

            // Parse fields
            $artist        = trim( $data['artist'] ?? '' );
            $title         = trim( $data['title'] ?? '' );
            $label         = trim( $data['label'] ?? '' );
            $country       = trim( $data['country'] ?? '' );
            $year          = trim( $data['year'] ?? '' );
            $type          = trim( $data['type'] ?? '' );
            $price         = $this->parse_price( $data['reference_price'] ?? '' );
            $media_cond    = $this->parse_condition( $data['media_condition'] ?? '' );
            $sleeve_cond   = $this->parse_condition( $data['sleeve_condition'] ?? '' );
            $image_url     = trim( $data['center_label_photo'] ?? '' );

            // Parse type into components
            $format_data = $this->parse_type( $type );

            // Build product title: "Artist - Title"
            $product_title = $artist ? "{$artist} - {$title}" : $title;

            if ( empty( $product_title ) ) {
                WP_CLI::warning( "Skipping row {$count}: No title" );
                $errors++;
                continue;
            }

            WP_CLI::log( "Processing: {$product_title}" );
            WP_CLI::log( "  Price: £{$price}" );
            WP_CLI::log( "  Format: {$format_data['size']} / {$format_data['speed']} RPM" );
            WP_CLI::log( "  Edition: " . implode( ', ', $format_data['edition'] ) );
            WP_CLI::log( "  Condition: Media {$media_cond}, Sleeve {$sleeve_cond}" );

            if ( $dry_run ) {
                WP_CLI::log( "  [DRY RUN] Would create product" );
                WP_CLI::log( '' );
                $count++;
                continue;
            }

            // Check for existing product
            $existing = get_page_by_title( $product_title, OBJECT, 'product' );
            if ( $existing ) {
                WP_CLI::warning( "  Product already exists (ID: {$existing->ID}), skipping" );
                $errors++;
                continue;
            }

            // Create product
            $product = new WC_Product_Simple();
            $product->set_name( $product_title );
            $product->set_status( 'publish' );
            $product->set_catalog_visibility( 'visible' );
            $product->set_regular_price( $price );
            $product->set_manage_stock( false );
            $product->set_stock_status( 'instock' );

            // Save to get ID
            $product_id = $product->save();

            if ( ! $product_id ) {
                WP_CLI::warning( "  Failed to create product" );
                $errors++;
                continue;
            }

            WP_CLI::log( "  Created product ID: {$product_id}" );

            // Set ACF fields
            update_field( 'artist', $artist, $product_id );
            update_field( 'label', $label, $product_id );
            update_field( 'country', $country, $product_id );
            update_field( 'year', $year, $product_id );
            update_field( 'format_size', $format_data['size'], $product_id );
            update_field( 'speed', $format_data['speed'], $product_id );
            update_field( 'edition', $format_data['edition'], $product_id );
            update_field( 'media_condition', $media_cond, $product_id );
            update_field( 'sleeve_condition', $sleeve_cond, $product_id );

            // Download and attach image
            if ( $image_url ) {
                $image_id = $this->download_image( $image_url, $product_id, $product_title );
                if ( $image_id ) {
                    $product->set_image_id( $image_id );
                    $product->save();
                    WP_CLI::log( "  Attached image ID: {$image_id}" );
                }
            }

            WP_CLI::log( '' );
            $count++;
        }

        fclose( $handle );

        WP_CLI::success( "Imported {$count} records. Errors: {$errors}" );
    }

    /**
     * Parse price string to float.
     */
    private function parse_price( $price_str ) {
        // Remove currency symbol and whitespace
        $price = preg_replace( '/[^0-9.]/', '', $price_str );
        return floatval( $price );
    }

    /**
     * Parse condition string to code.
     */
    private function parse_condition( $condition_str ) {
        $condition_str = strtolower( trim( $condition_str ) );

        if ( strpos( $condition_str, 'mint' ) !== false && strpos( $condition_str, 'near' ) === false ) {
            return 'M';
        }
        if ( strpos( $condition_str, 'near mint' ) !== false || strpos( $condition_str, 'nm' ) !== false ) {
            return 'NM';
        }
        if ( strpos( $condition_str, 'very good plus' ) !== false || strpos( $condition_str, 'vg+' ) !== false ) {
            return 'VG+';
        }
        if ( strpos( $condition_str, 'very good' ) !== false || strpos( $condition_str, 'vg' ) !== false ) {
            return 'VG';
        }
        if ( strpos( $condition_str, 'good plus' ) !== false || strpos( $condition_str, 'g+' ) !== false ) {
            return 'G+';
        }
        if ( strpos( $condition_str, 'good' ) !== false ) {
            return 'G';
        }
        if ( strpos( $condition_str, 'fair' ) !== false || strpos( $condition_str, 'poor' ) !== false ) {
            return 'F';
        }

        return '';
    }

    /**
     * Parse type string into format components.
     */
    private function parse_type( $type_str ) {
        $result = array(
            'size'    => '',
            'speed'   => '',
            'edition' => array(),
        );

        $type_str = strtolower( $type_str );

        // Size
        if ( strpos( $type_str, '12"' ) !== false || strpos( $type_str, '12"' ) !== false ) {
            $result['size'] = '12"';
        } elseif ( strpos( $type_str, '10"' ) !== false || strpos( $type_str, '10"' ) !== false ) {
            $result['size'] = '10"';
        } elseif ( strpos( $type_str, '7"' ) !== false || strpos( $type_str, '7"' ) !== false ) {
            $result['size'] = '7"';
        } elseif ( strpos( $type_str, 'lp' ) !== false ) {
            $result['size'] = 'LP';
        }

        // Speed
        if ( strpos( $type_str, '45 rpm' ) !== false || strpos( $type_str, '45rpm' ) !== false ) {
            $result['speed'] = '45';
        } elseif ( strpos( $type_str, '33' ) !== false ) {
            $result['speed'] = '33';
        } elseif ( strpos( $type_str, '78 rpm' ) !== false ) {
            $result['speed'] = '78';
        }

        // Edition flags
        if ( strpos( $type_str, 'white label' ) !== false ) {
            $result['edition'][] = 'white_label';
        }
        if ( strpos( $type_str, 'promo' ) !== false ) {
            $result['edition'][] = 'promo';
        }
        if ( strpos( $type_str, 'limited' ) !== false ) {
            $result['edition'][] = 'limited_edition';
        }
        if ( preg_match( '/\bep\b/', $type_str ) ) {
            $result['edition'][] = 'ep';
        }
        if ( strpos( $type_str, 'single' ) !== false ) {
            $result['edition'][] = 'single';
        }
        if ( strpos( $type_str, 'stereo' ) !== false ) {
            $result['edition'][] = 'stereo';
        }
        if ( strpos( $type_str, 'mono' ) !== false ) {
            $result['edition'][] = 'mono';
        }

        return $result;
    }

    /**
     * Download image from URL and attach to product.
     */
    private function download_image( $url, $product_id, $title ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Download to temp file
        $tmp = download_url( $url, 30 );

        if ( is_wp_error( $tmp ) ) {
            WP_CLI::warning( "  Failed to download image: " . $tmp->get_error_message() );
            return false;
        }

        // Get file extension from URL
        $ext = pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION );
        if ( ! $ext || $ext === 'webp' ) {
            $ext = 'jpg'; // Convert webp reference to jpg for compatibility
        }

        // Prepare file array
        $file_array = array(
            'name'     => sanitize_file_name( $title ) . '.' . $ext,
            'tmp_name' => $tmp,
        );

        // Upload and attach
        $attachment_id = media_handle_sideload( $file_array, $product_id, $title );

        // Clean up temp file
        if ( file_exists( $tmp ) ) {
            @unlink( $tmp );
        }

        if ( is_wp_error( $attachment_id ) ) {
            WP_CLI::warning( "  Failed to attach image: " . $attachment_id->get_error_message() );
            return false;
        }

        return $attachment_id;
    }
}

WP_CLI::add_command( 'fmw import-records', 'FMW_Import_Records_Command' );
