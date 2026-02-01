<?php
/**
 * Custom Search Results Template
 *
 * @package FMW
 */

get_header();

$search_query      = get_search_query();
$is_product_search = isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product';
?>

<div class="search-results-page">
	<div class="container mx-auto px-4">

		<!-- Search Header -->
		<header class="search-header">
			<h1 class="search-title">
				<?php if ( $search_query ) : ?>
					Search results for "<?php echo esc_html( $search_query ); ?>"
				<?php else : ?>
					Search Results
				<?php endif; ?>
			</h1>

			<?php if ( have_posts() ) : ?>
				<p class="search-count">
					<?php
					global $wp_query;
					printf(
						_n( '%s result found', '%s results found', $wp_query->found_posts, 'fmw' ),
						number_format_i18n( $wp_query->found_posts )
					);
					?>
				</p>
			<?php endif; ?>
		</header>

		<!-- Active Filters -->
		<?php fmw_display_active_search_filters(); ?>

		<!-- Search Again -->
		<div class="search-again">
			<button
				type="button"
				class="search-again-btn"
				onclick="window.dispatchEvent(new CustomEvent('search-modal'))"
			>
				<?php fmw_icon( 'search', 'icon' ); ?>
				<span>Refine Search</span>
			</button>
		</div>

		<?php if ( have_posts() ) : ?>

			<?php if ( $is_product_search ) : ?>
				<!-- Product Results Grid -->
				<div class="products-grid">
					<?php
					while ( have_posts() ) :
						the_post();

						$product = wc_get_product( get_the_ID() );

						if ( ! $product ) {
							continue;
						}

						$product_id = $product->get_id();
						$title      = $product->get_name();
						$price      = $product->get_price_html();
						$link       = $product->get_permalink();
						$image_id   = $product->get_image_id();

						// ACF fields
						$artist = get_field( 'artist', $product_id );
						$label  = get_field( 'label', $product_id );
						?>
						<article class="product-card">
							<a href="<?php echo esc_url( $link ); ?>" class="product-card-link">
								<div class="product-card-image">
									<?php if ( $image_id ) : ?>
										<?php echo wp_get_attachment_image( $image_id, 'medium', false, array( 'class' => 'product-card-img' ) ); ?>
									<?php else : ?>
										<div class="product-card-placeholder"></div>
									<?php endif; ?>

									<?php if ( ! $product->is_in_stock() ) : ?>
										<span class="product-card-badge">Sold</span>
									<?php endif; ?>
								</div>
								<div class="product-card-info">
									<h3 class="product-card-title"><?php echo esc_html( $title ); ?></h3>
									<?php if ( $label ) : ?>
										<p class="product-card-label"><?php echo esc_html( $label ); ?></p>
									<?php endif; ?>
									<p class="product-card-price"><?php echo $price; ?></p>
								</div>
							</a>
						</article>
						<?php
					endwhile;
					?>
				</div>

				<!-- Pagination -->
				<?php if ( $wp_query->max_num_pages > 1 ) : ?>
					<nav class="search-pagination">
						<?php
						echo paginate_links( array(
							'total'     => $wp_query->max_num_pages,
							'current'   => max( 1, get_query_var( 'paged' ) ),
							'prev_text' => '&larr; Previous',
							'next_text' => 'Next &rarr;',
						) );
						?>
					</nav>
				<?php endif; ?>

			<?php else : ?>
				<!-- Non-product Results -->
				<div class="search-results-list">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article class="search-result-item">
							<h2 class="search-result-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="search-result-excerpt">
								<?php the_excerpt(); ?>
							</div>
						</article>
						<?php
					endwhile;
					?>
				</div>
			<?php endif; ?>

		<?php else : ?>

			<!-- No Results -->
			<div class="search-no-results">
				<h2>No results found</h2>
				<p>Sorry, we couldn't find any records matching your search.</p>

				<div class="search-suggestions">
					<h3>Suggestions:</h3>
					<ul>
						<li>Check your spelling</li>
						<li>Try different keywords</li>
						<li>Try broader search terms</li>
						<li>Remove some filters</li>
					</ul>
				</div>

				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn">
					Browse All Records
				</a>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();
