<?php
/**
 * Search Modal Component
 *
 * Advanced search with dark theme
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

// Get unique values for dropdowns
$formats = array( '7"', '10"', '12"', 'LP', '2xLP' );
$decades = array(
	'2020s' => '2020-2029',
	'2010s' => '2010-2019',
	'2000s' => '2000-2009',
	'1990s' => '1990-1999',
	'1980s' => '1980-1989',
	'1970s' => '1970-1979',
	'1960s' => '1960-1969',
);
$conditions = array(
	'M'   => 'Mint',
	'NM'  => 'Near Mint',
	'VG+' => 'Very Good Plus',
	'VG'  => 'Very Good',
	'G+'  => 'Good Plus',
	'G'   => 'Good',
);

// Get genres (product categories)
$genres = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
) );
?>

<div
	id="search-modal"
	class="search-modal"
	x-data="{ open: false, showAdvanced: false }"
	x-show="open"
	x-cloak
	@keydown.escape.window="open = false"
	@search-modal.window="open = true; $nextTick(() => $refs.searchInput.focus())"
>
	<!-- Backdrop -->
	<div
		class="search-modal-backdrop"
		@click="open = false"
		x-show="open"
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
	></div>

	<!-- Modal Panel -->
	<div
		class="search-modal-panel"
		x-show="open"
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="opacity-0 -translate-y-4"
		x-transition:enter-end="opacity-100 translate-y-0"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="opacity-100 translate-y-0"
		x-transition:leave-end="opacity-0 -translate-y-4"
		@click.away="open = false"
	>
		<!-- Close Button -->
		<button
			type="button"
			class="search-modal-close"
			@click="open = false"
			aria-label="<?php esc_attr_e( 'Close', 'fmw' ); ?>"
		>
			<?php fmw_icon( 'close', 'icon' ); ?>
		</button>

		<div class="search-modal-content">
			<h2 class="search-modal-title">Search Store</h2>

			<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="search-modal-form" autocomplete="off">
				<input type="hidden" name="post_type" value="product">

				<!-- Main Search Field -->
				<div class="search-main-field">
					<input
						type="search"
						name="s"
						x-ref="searchInput"
						placeholder="Search artists, albums, labels..."
						class="search-main-input"
						value="<?php echo esc_attr( get_search_query() ); ?>"
						autocomplete="off"
					>
					<button type="submit" class="search-main-submit">
						<?php fmw_icon( 'search', 'icon' ); ?>
					</button>
				</div>

				<!-- Toggle Advanced -->
				<button
					type="button"
					class="search-advanced-toggle"
					@click="showAdvanced = !showAdvanced"
				>
					<span x-text="showAdvanced ? 'Hide filters' : 'Show filters'"></span>
					<svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
					</svg>
				</button>

				<!-- Advanced Filters -->
				<div
					class="search-advanced"
					x-show="showAdvanced"
					x-transition:enter="transition ease-out duration-200"
					x-transition:enter-start="opacity-0 -translate-y-2"
					x-transition:enter-end="opacity-100 translate-y-0"
				>
					<div class="search-filters-grid">
						<!-- Artist -->
						<div class="search-field">
							<label for="search-artist">Artist</label>
							<input type="text" id="search-artist" name="artist" placeholder="e.g. Radiohead" autocomplete="off">
						</div>

						<!-- Label -->
						<div class="search-field">
							<label for="search-label">Label</label>
							<input type="text" id="search-label" name="label" placeholder="e.g. Warp Records" autocomplete="off">
						</div>

						<!-- Genre -->
						<?php if ( ! empty( $genres ) && ! is_wp_error( $genres ) ) : ?>
							<div class="search-field">
								<label for="search-genre">Genre</label>
								<select id="search-genre" name="product_cat">
									<option value="">All Genres</option>
									<?php foreach ( $genres as $genre ) : ?>
										<option value="<?php echo esc_attr( $genre->slug ); ?>">
											<?php echo esc_html( $genre->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>

						<!-- Decade -->
						<div class="search-field">
							<label for="search-decade">Decade</label>
							<select id="search-decade" name="decade">
								<option value="">All Years</option>
								<?php foreach ( $decades as $label => $value ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>">
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Format -->
						<div class="search-field">
							<label for="search-format">Format</label>
							<select id="search-format" name="format_size">
								<option value="">All Formats</option>
								<?php foreach ( $formats as $format ) : ?>
									<option value="<?php echo esc_attr( $format ); ?>">
										<?php echo esc_html( $format ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Condition -->
						<div class="search-field">
							<label for="search-condition">Condition</label>
							<select id="search-condition" name="condition">
								<option value="">All Conditions</option>
								<?php foreach ( $conditions as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>">
										<?php echo esc_html( $label ); ?> (<?php echo esc_html( $key ); ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<!-- Price Range -->
					<div class="search-price-range">
						<label>Price Range</label>
						<div class="search-price-inputs">
							<input type="number" name="min_price" placeholder="Min" min="0" step="0.01" autocomplete="off">
							<span class="search-price-separator">to</span>
							<input type="number" name="max_price" placeholder="Max" min="0" step="0.01" autocomplete="off">
						</div>
					</div>
				</div>

				<!-- Submit -->
				<button type="submit" class="search-submit-btn">
					Search Store
				</button>
			</form>

			<!-- Quick Links -->
			<div class="search-quick-links">
				<span class="search-quick-label">Quick:</span>
				<a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', wc_get_page_permalink( 'shop' ) ) ); ?>">New Arrivals</a>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">All Records</a>
			</div>
		</div>
	</div>
</div>
