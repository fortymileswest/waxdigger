<?php
/**
 * Advanced Search Handler
 *
 * Filters WooCommerce products based on ACF fields and meta
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

/**
 * Modify product query for advanced search
 */
function fmw_advanced_product_search( $query ) {
	// Only modify main query on frontend
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// Only for product searches
	if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'product' ) {
		return;
	}

	$meta_query = $query->get( 'meta_query' ) ?: array();

	// Artist filter
	if ( ! empty( $_GET['artist'] ) ) {
		$meta_query[] = array(
			'key'     => 'artist',
			'value'   => sanitize_text_field( $_GET['artist'] ),
			'compare' => 'LIKE',
		);
	}

	// Label filter
	if ( ! empty( $_GET['label'] ) ) {
		$meta_query[] = array(
			'key'     => 'label',
			'value'   => sanitize_text_field( $_GET['label'] ),
			'compare' => 'LIKE',
		);
	}

	// Format filter
	if ( ! empty( $_GET['format_size'] ) ) {
		$meta_query[] = array(
			'key'     => 'format_size',
			'value'   => sanitize_text_field( $_GET['format_size'] ),
			'compare' => '=',
		);
	}

	// Condition filter (media condition)
	if ( ! empty( $_GET['condition'] ) ) {
		$meta_query[] = array(
			'key'     => 'media_condition',
			'value'   => sanitize_text_field( $_GET['condition'] ),
			'compare' => '=',
		);
	}

	// Price range filter
	if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
		$min_price = ! empty( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : 0;
		$max_price = ! empty( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : 999999;

		$meta_query[] = array(
			'key'     => '_price',
			'value'   => array( $min_price, $max_price ),
			'compare' => 'BETWEEN',
			'type'    => 'NUMERIC',
		);
	}

	// Apply meta query if we have filters
	if ( ! empty( $meta_query ) ) {
		$meta_query['relation'] = 'AND';
		$query->set( 'meta_query', $meta_query );
	}
}
add_action( 'pre_get_posts', 'fmw_advanced_product_search' );

/**
 * Add decade filter via SQL (handles mixed date formats)
 */
function fmw_decade_filter_sql( $where, $query ) {
	global $wpdb;

	if ( is_admin() || ! $query->is_main_query() ) {
		return $where;
	}

	if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'product' ) {
		return $where;
	}

	if ( empty( $_GET['decade'] ) ) {
		return $where;
	}

	$decade = sanitize_text_field( $_GET['decade'] );
	$years  = explode( '-', $decade );

	if ( count( $years ) !== 2 ) {
		return $where;
	}

	$start_year = intval( $years[0] );
	$end_year   = intval( $years[1] );

	// Filter by year - extracts first 4 characters and compares as number
	$where .= $wpdb->prepare(
		" AND {$wpdb->posts}.ID IN (
			SELECT post_id FROM {$wpdb->postmeta}
			WHERE meta_key = 'year'
			AND CAST(LEFT(meta_value, 4) AS UNSIGNED) BETWEEN %d AND %d
		)",
		$start_year,
		$end_year
	);

	return $where;
}
add_filter( 'posts_where', 'fmw_decade_filter_sql', 10, 2 );

/**
 * Extend WooCommerce search to include ACF fields
 */
function fmw_extend_product_search( $where, $query ) {
	global $wpdb;

	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return $where;
	}

	if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'product' ) {
		return $where;
	}

	$search_term = $query->get( 's' );

	if ( empty( $search_term ) ) {
		return $where;
	}

	$like = '%' . $wpdb->esc_like( $search_term ) . '%';

	// Add search in ACF fields: artist, label
	$where .= $wpdb->prepare(
		" OR {$wpdb->posts}.ID IN (
			SELECT post_id FROM {$wpdb->postmeta}
			WHERE (meta_key = 'artist' AND meta_value LIKE %s)
			   OR (meta_key = 'label' AND meta_value LIKE %s)
		)",
		$like,
		$like
	);

	return $where;
}
add_filter( 'posts_where', 'fmw_extend_product_search', 10, 2 );

/**
 * Display active filters on search results
 */
function fmw_display_active_search_filters() {
	if ( ! is_search() || ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'product' ) {
		return;
	}

	$filters = array();

	if ( ! empty( $_GET['artist'] ) ) {
		$filters[] = 'Artist: ' . esc_html( $_GET['artist'] );
	}
	if ( ! empty( $_GET['label'] ) ) {
		$filters[] = 'Label: ' . esc_html( $_GET['label'] );
	}
	if ( ! empty( $_GET['decade'] ) ) {
		$years     = explode( '-', $_GET['decade'] );
		$filters[] = 'Decade: ' . esc_html( $years[0] ) . 's';
	}
	if ( ! empty( $_GET['format_size'] ) ) {
		$filters[] = 'Format: ' . esc_html( $_GET['format_size'] );
	}
	if ( ! empty( $_GET['condition'] ) ) {
		$conditions = array(
			'M'   => 'Mint',
			'NM'  => 'Near Mint',
			'VG+' => 'Very Good Plus',
			'VG'  => 'Very Good',
			'G+'  => 'Good Plus',
			'G'   => 'Good',
		);
		$cond       = $_GET['condition'];
		$filters[]  = 'Condition: ' . esc_html( $conditions[ $cond ] ?? $cond );
	}
	if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
		$min       = ! empty( $_GET['min_price'] ) ? '£' . number_format( floatval( $_GET['min_price'] ), 2 ) : '';
		$max       = ! empty( $_GET['max_price'] ) ? '£' . number_format( floatval( $_GET['max_price'] ), 2 ) : '';
		$filters[] = 'Price: ' . ( $min ?: 'Any' ) . ' - ' . ( $max ?: 'Any' );
	}

	if ( ! empty( $filters ) ) {
		echo '<div class="active-search-filters">';
		echo '<span class="filters-label">Filters:</span> ';
		echo esc_html( implode( ' | ', $filters ) );
		echo ' <a href="' . esc_url( remove_query_arg( array( 'artist', 'label', 'decade', 'format_size', 'condition', 'min_price', 'max_price' ) ) ) . '" class="clear-filters">Clear all</a>';
		echo '</div>';
	}
}
