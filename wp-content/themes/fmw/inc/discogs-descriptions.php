<?php
/**
 * WP-CLI Discogs Description Fetcher
 *
 * Fetches release info from Discogs and generates product descriptions.
 *
 * Usage: ddev wp fmw fetch-descriptions
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
 * Fetch descriptions from Discogs for products.
 */
class FMW_Discogs_Descriptions_Command {

	/**
	 * Discogs API token.
	 */
	private $token = 'XOiumbOTUoJCdFkUpgpUmKpLFFRtspsVkkRMvDJi';

	/**
	 * User agent for Discogs API.
	 */
	private $user_agent = 'WaxdiggerRecordShop/1.0';

	/**
	 * OpenAI API key (optional - for AI rewriting).
	 */
	private $openai_key = '';

	/**
	 * Fetch descriptions from Discogs for all products.
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Preview without updating products.
	 *
	 * [--limit=<number>]
	 * : Limit number of products to process.
	 *
	 * [--product=<id>]
	 * : Process a single product by ID.
	 *
	 * [--skip-existing]
	 * : Skip products that already have descriptions.
	 *
	 * [--openai-key=<key>]
	 * : OpenAI API key for AI-powered rewriting.
	 *
	 * ## EXAMPLES
	 *
	 *     wp fmw fetch-descriptions
	 *     wp fmw fetch-descriptions --dry-run
	 *     wp fmw fetch-descriptions --limit=5
	 *     wp fmw fetch-descriptions --product=123
	 *     wp fmw fetch-descriptions --openai-key=sk-...
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		$dry_run       = isset( $assoc_args['dry-run'] );
		$limit         = isset( $assoc_args['limit'] ) ? intval( $assoc_args['limit'] ) : -1;
		$product_id    = isset( $assoc_args['product'] ) ? intval( $assoc_args['product'] ) : null;
		$skip_existing = isset( $assoc_args['skip-existing'] );

		if ( isset( $assoc_args['openai-key'] ) ) {
			$this->openai_key = $assoc_args['openai-key'];
		}

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
		WP_CLI::log( $this->openai_key ? 'Using OpenAI for AI rewriting.' : 'Using template-based descriptions.' );
		WP_CLI::log( $dry_run ? '--- DRY RUN ---' : '--- FETCHING DESCRIPTIONS ---' );
		WP_CLI::log( '' );

		$success = 0;
		$failed  = 0;
		$skipped = 0;

		foreach ( $products as $product ) {
			$id   = $product->get_id();
			$name = $product->get_name();

			// Skip if already has description
			if ( $skip_existing && $product->get_description() ) {
				WP_CLI::log( "[{$id}] Skipping (has description): {$name}" );
				$skipped++;
				continue;
			}

			// Parse artist and title
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

			// Get release details
			$release_details = $this->get_release_details( $release['id'], $release['type'] ?? 'release' );

			if ( ! $release_details ) {
				WP_CLI::warning( "  Could not fetch release details" );
				$failed++;
				sleep( 1 );
				continue;
			}

			// Get ACF fields for context
			$label     = get_field( 'label', $id ) ?: ( $release_details['labels'][0]['name'] ?? '' );
			$year      = get_field( 'year', $id ) ?: ( $release_details['year'] ?? '' );
			$country   = get_field( 'country', $id ) ?: ( $release_details['country'] ?? '' );
			$genre     = $release_details['genres'][0] ?? '';
			$styles    = $release_details['styles'] ?? array();
			$tracklist = $release_details['tracklist'] ?? array();
			$notes     = $release_details['notes'] ?? '';

			// Generate descriptions
			$descriptions = $this->generate_descriptions( array(
				'artist'    => $artist,
				'title'     => $title,
				'label'     => $label,
				'year'      => $year,
				'country'   => $country,
				'genre'     => $genre,
				'styles'    => $styles,
				'tracklist' => $tracklist,
				'notes'     => $notes,
			) );

			if ( $dry_run ) {
				WP_CLI::log( "  [DRY RUN] Short description:" );
				WP_CLI::log( "  " . substr( $descriptions['short'], 0, 100 ) . '...' );
				WP_CLI::log( "  [DRY RUN] Long description:" );
				WP_CLI::log( "  " . substr( $descriptions['long'], 0, 200 ) . '...' );
				$success++;
				sleep( 1 );
				continue;
			}

			// Update product
			$product->set_short_description( $descriptions['short'] );
			$product->set_description( $descriptions['long'] );
			$product->save();

			WP_CLI::success( "  Updated descriptions" );
			$success++;

			WP_CLI::log( '' );

			// Rate limit
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
		$artist = preg_replace( '/\s*\(\d+\)\s*$/', '', $artist );
		$artist = preg_replace( '/\|/', ' ', $artist );

		$search_title = preg_replace( '/\s*\([^)]+\)\s*/', ' ', $title );
		$search_title = preg_replace( '/\s*E\.?P\.?\s*/i', ' ', $search_title );
		$search_title = trim( $search_title );

		$query = urlencode( "{$artist} {$search_title}" );

		$url      = "https://api.discogs.com/database/search?q={$query}&type=release&format=Vinyl&per_page=10";
		$response = $this->api_request( $url );

		if ( ! $response || empty( $response['results'] ) ) {
			$url      = "https://api.discogs.com/database/search?q={$query}&type=release&per_page=10";
			$response = $this->api_request( $url );
		}

		if ( ! $response || empty( $response['results'] ) ) {
			return null;
		}

		return $response['results'][0];
	}

	/**
	 * Get release details from Discogs.
	 */
	private function get_release_details( $release_id, $type = 'release' ) {
		if ( $type === 'master' ) {
			$url = "https://api.discogs.com/masters/{$release_id}";
		} else {
			$url = "https://api.discogs.com/releases/{$release_id}";
		}

		return $this->api_request( $url );
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
	 * Generate descriptions from release data.
	 */
	private function generate_descriptions( $data ) {
		// If OpenAI key is available, use AI
		if ( $this->openai_key ) {
			return $this->generate_ai_descriptions( $data );
		}

		// Otherwise use template-based approach
		return $this->generate_template_descriptions( $data );
	}

	/**
	 * Generate AI-powered descriptions using OpenAI.
	 */
	private function generate_ai_descriptions( $data ) {
		$prompt = $this->build_ai_prompt( $data );

		$response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->openai_key,
				'Content-Type'  => 'application/json',
			),
			'body'    => json_encode( array(
				'model'    => 'gpt-4o-mini',
				'messages' => array(
					array(
						'role'    => 'system',
						'content' => 'You are a professional music journalist and vinyl record expert. Write engaging, knowledgeable descriptions that appeal to serious record collectors. Be concise but evocative. Avoid clichés. Focus on what makes each release special - the sound, the context, the significance.',
					),
					array(
						'role'    => 'user',
						'content' => $prompt,
					),
				),
				'temperature' => 0.7,
				'max_tokens'  => 500,
			) ),
			'timeout' => 60,
		) );

		if ( is_wp_error( $response ) ) {
			WP_CLI::warning( '  OpenAI API error: ' . $response->get_error_message() );
			return $this->generate_template_descriptions( $data );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['choices'][0]['message']['content'] ) ) {
			WP_CLI::warning( '  OpenAI returned empty response' );
			return $this->generate_template_descriptions( $data );
		}

		$content = $body['choices'][0]['message']['content'];

		// Parse the response (expecting SHORT: and LONG: sections)
		$short = '';
		$long  = '';

		if ( preg_match( '/SHORT:\s*(.+?)(?=LONG:|$)/s', $content, $matches ) ) {
			$short = trim( $matches[1] );
		}

		if ( preg_match( '/LONG:\s*(.+?)$/s', $content, $matches ) ) {
			$long = trim( $matches[1] );
		}

		// Fallback if parsing fails
		if ( ! $short || ! $long ) {
			$paragraphs = explode( "\n\n", $content );
			$short      = $paragraphs[0] ?? $content;
			$long       = $content;
		}

		return array(
			'short' => $short,
			'long'  => $long,
		);
	}

	/**
	 * Build AI prompt from release data.
	 */
	private function build_ai_prompt( $data ) {
		$prompt = "Write two descriptions for this vinyl record:\n\n";
		$prompt .= "Artist: {$data['artist']}\n";
		$prompt .= "Title: {$data['title']}\n";

		if ( $data['label'] ) {
			$prompt .= "Label: {$data['label']}\n";
		}
		if ( $data['year'] ) {
			$prompt .= "Year: {$data['year']}\n";
		}
		if ( $data['country'] ) {
			$prompt .= "Country: {$data['country']}\n";
		}
		if ( $data['genre'] ) {
			$prompt .= "Genre: {$data['genre']}\n";
		}
		if ( ! empty( $data['styles'] ) ) {
			$prompt .= "Styles: " . implode( ', ', $data['styles'] ) . "\n";
		}

		if ( ! empty( $data['tracklist'] ) ) {
			$tracks = array();
			foreach ( array_slice( $data['tracklist'], 0, 6 ) as $track ) {
				if ( ! empty( $track['title'] ) ) {
					$tracks[] = $track['title'];
				}
			}
			if ( $tracks ) {
				$prompt .= "Tracks: " . implode( ', ', $tracks ) . "\n";
			}
		}

		if ( $data['notes'] ) {
			$prompt .= "Original notes: " . substr( $data['notes'], 0, 500 ) . "\n";
		}

		$prompt .= "\nWrite:\n";
		$prompt .= "SHORT: A punchy 1-2 sentence hook (under 160 characters) that captures the essence.\n";
		$prompt .= "LONG: A 2-3 paragraph description (150-250 words) covering the sound, significance, and why a collector should want this record.\n";

		return $prompt;
	}

	/**
	 * Generate template-based descriptions.
	 */
	private function generate_template_descriptions( $data ) {
		$short = '';
		$long  = '';

		// Build short description
		$short_parts = array();

		if ( $data['genre'] ) {
			$short_parts[] = $data['genre'];
		}
		if ( ! empty( $data['styles'] ) ) {
			$short_parts[] = implode( '/', array_slice( $data['styles'], 0, 2 ) );
		}

		$style_text = implode( ' ', $short_parts );

		if ( $data['year'] && $data['label'] ) {
			$short = sprintf(
				'%s release from %s on %s.',
				$style_text ?: 'Classic',
				$data['year'],
				$data['label']
			);
		} elseif ( $data['label'] ) {
			$short = sprintf(
				'%s release on %s.',
				$style_text ?: 'Essential',
				$data['label']
			);
		} else {
			$short = sprintf(
				'%s release by %s.',
				$style_text ?: 'Quality',
				$data['artist']
			);
		}

		// Build long description
		$long_parts = array();

		// Opening paragraph
		$opening = sprintf( '"%s" by %s', $data['title'], $data['artist'] );
		if ( $data['year'] && $data['label'] ) {
			$opening .= sprintf( ', released in %s on %s', $data['year'], $data['label'] );
		} elseif ( $data['label'] ) {
			$opening .= sprintf( ', released on %s', $data['label'] );
		}
		$opening .= '.';
		$long_parts[] = $opening;

		// Genre/style info
		if ( $data['genre'] || ! empty( $data['styles'] ) ) {
			$genre_text = '';
			if ( $data['genre'] && ! empty( $data['styles'] ) ) {
				$genre_text = sprintf(
					'A %s record drawing from %s traditions.',
					$data['genre'],
					implode( ' and ', array_slice( $data['styles'], 0, 3 ) )
				);
			} elseif ( $data['genre'] ) {
				$genre_text = sprintf( 'A quality %s record.', $data['genre'] );
			}
			if ( $genre_text ) {
				$long_parts[] = $genre_text;
			}
		}

		// Tracklist
		if ( ! empty( $data['tracklist'] ) ) {
			$tracks = array();
			foreach ( array_slice( $data['tracklist'], 0, 4 ) as $track ) {
				if ( ! empty( $track['title'] ) ) {
					$tracks[] = '"' . $track['title'] . '"';
				}
			}
			if ( $tracks ) {
				$long_parts[] = 'Featuring tracks including ' . implode( ', ', $tracks ) . '.';
			}
		}

		// Original notes (cleaned up)
		if ( $data['notes'] ) {
			$notes = strip_tags( $data['notes'] );
			$notes = preg_replace( '/\s+/', ' ', $notes );
			$notes = trim( $notes );

			if ( strlen( $notes ) > 50 && strlen( $notes ) < 500 ) {
				$long_parts[] = $notes;
			}
		}

		$long = implode( "\n\n", $long_parts );

		return array(
			'short' => $short,
			'long'  => $long,
		);
	}
}

WP_CLI::add_command( 'fmw fetch-descriptions', 'FMW_Discogs_Descriptions_Command' );
