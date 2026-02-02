<?php
/**
 * WP-CLI Command to Migrate ACF Labels to Taxonomy
 *
 * Usage: ddev wp fmw migrate-labels
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
 * Migrate ACF label field to record_label taxonomy.
 */
class FMW_Migrate_Labels_Command {

	/**
	 * Migrate ACF label field data to record_label taxonomy.
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Preview migration without making changes.
	 *
	 * [--delete-acf]
	 * : Delete ACF field data after successful migration.
	 *
	 * ## EXAMPLES
	 *
	 *     wp fmw migrate-labels
	 *     wp fmw migrate-labels --dry-run
	 *     wp fmw migrate-labels --delete-acf
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		$dry_run    = isset( $assoc_args['dry-run'] );
		$delete_acf = isset( $assoc_args['delete-acf'] );

		if ( ! function_exists( 'wc_get_product' ) ) {
			WP_CLI::error( 'WooCommerce is not active.' );
		}

		if ( ! taxonomy_exists( 'record_label' ) ) {
			WP_CLI::error( 'record_label taxonomy does not exist. Make sure the theme is loaded.' );
		}

		if ( $dry_run ) {
			WP_CLI::log( '🔍 DRY RUN - No changes will be made.' );
			WP_CLI::log( '' );
		}

		// Get all products with ACF label field
		$products = get_posts( array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		) );

		if ( empty( $products ) ) {
			WP_CLI::warning( 'No products found.' );
			return;
		}

		WP_CLI::log( sprintf( 'Found %d products to process.', count( $products ) ) );
		WP_CLI::log( '' );

		$migrated     = 0;
		$skipped      = 0;
		$labels_found = array();

		foreach ( $products as $product_id ) {
			$label = get_field( 'label', $product_id );
			$title = get_the_title( $product_id );

			if ( empty( $label ) ) {
				$skipped++;
				continue;
			}

			// Trim and normalise label
			$label = trim( $label );

			// Track unique labels
			if ( ! isset( $labels_found[ $label ] ) ) {
				$labels_found[ $label ] = 0;
			}
			$labels_found[ $label ]++;

			if ( $dry_run ) {
				WP_CLI::log( sprintf( '  [%d] %s → "%s"', $product_id, $title, $label ) );
			} else {
				// Check if term exists, create if not
				$term = get_term_by( 'name', $label, 'record_label' );

				if ( ! $term ) {
					$result = wp_insert_term( $label, 'record_label' );
					if ( is_wp_error( $result ) ) {
						WP_CLI::warning( sprintf( 'Failed to create term "%s": %s', $label, $result->get_error_message() ) );
						continue;
					}
					$term_id = $result['term_id'];
				} else {
					$term_id = $term->term_id;
				}

				// Assign product to taxonomy term
				$result = wp_set_object_terms( $product_id, $term_id, 'record_label' );

				if ( is_wp_error( $result ) ) {
					WP_CLI::warning( sprintf( 'Failed to assign term to product %d: %s', $product_id, $result->get_error_message() ) );
					continue;
				}

				// Delete ACF field if requested
				if ( $delete_acf ) {
					delete_field( 'label', $product_id );
				}

				WP_CLI::log( sprintf( '  ✓ [%d] %s → "%s"', $product_id, $title, $label ) );
			}

			$migrated++;
		}

		WP_CLI::log( '' );
		WP_CLI::log( '─────────────────────────────────────' );
		WP_CLI::log( sprintf( 'Unique labels found: %d', count( $labels_found ) ) );
		WP_CLI::log( sprintf( 'Products migrated: %d', $migrated ) );
		WP_CLI::log( sprintf( 'Products skipped (no label): %d', $skipped ) );
		WP_CLI::log( '' );

		if ( ! empty( $labels_found ) ) {
			WP_CLI::log( 'Labels breakdown:' );
			arsort( $labels_found );
			foreach ( $labels_found as $label => $count ) {
				WP_CLI::log( sprintf( '  %s (%d)', $label, $count ) );
			}
		}

		WP_CLI::log( '' );

		if ( $dry_run ) {
			WP_CLI::success( 'Dry run complete. Run without --dry-run to apply changes.' );
		} else {
			WP_CLI::success( 'Migration complete!' );

			if ( ! $delete_acf ) {
				WP_CLI::log( '' );
				WP_CLI::log( 'Note: ACF field data has been preserved. Run with --delete-acf to remove it.' );
			}
		}
	}
}

WP_CLI::add_command( 'fmw migrate-labels', 'FMW_Migrate_Labels_Command' );
