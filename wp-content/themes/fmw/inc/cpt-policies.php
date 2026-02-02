<?php
/**
 * Policies Custom Post Type
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Policies CPT
 */
function fmw_register_policies_cpt() {
	$labels = array(
		'name'               => __( 'Policies', 'fmw' ),
		'singular_name'      => __( 'Policy', 'fmw' ),
		'menu_name'          => __( 'Policies', 'fmw' ),
		'add_new'            => __( 'Add New', 'fmw' ),
		'add_new_item'       => __( 'Add New Policy', 'fmw' ),
		'edit_item'          => __( 'Edit Policy', 'fmw' ),
		'new_item'           => __( 'New Policy', 'fmw' ),
		'view_item'          => __( 'View Policy', 'fmw' ),
		'search_items'       => __( 'Search Policies', 'fmw' ),
		'not_found'          => __( 'No policies found', 'fmw' ),
		'not_found_in_trash' => __( 'No policies found in Trash', 'fmw' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'policy', 'with_front' => false ),
		'capability_type'     => 'post',
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => 25,
		'menu_icon'           => 'dashicons-media-document',
		'supports'            => array( 'title', 'editor', 'revisions' ),
		'show_in_rest'        => false,
	);

	register_post_type( 'policy', $args );
}
add_action( 'init', 'fmw_register_policies_cpt' );

/**
 * Calculate read time for policy content
 *
 * @param string $content The content to calculate read time for.
 * @return int Read time in minutes.
 */
function fmw_get_read_time( $content = null ) {
	if ( null === $content ) {
		$content = get_the_content();
	}

	$content    = wp_strip_all_tags( $content );
	$word_count = str_word_count( $content );
	$read_time  = ceil( $word_count / 200 ); // Average reading speed: 200 words per minute

	return max( 1, $read_time );
}

/**
 * Flush rewrite rules on theme activation
 */
function fmw_policies_flush_rewrite() {
	fmw_register_policies_cpt();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'fmw_policies_flush_rewrite' );
