<?php
/**
 * Product Category Archive Template
 *
 * Custom template for displaying products by genre/category.
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

get_header();

$current_term = get_queried_object();
$term_name    = $current_term->name;
$term_desc    = $current_term->description;

// Get products in this category
$args = array(
	'post_type'      => 'product',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
	'tax_query'      => array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $current_term->term_id,
		),
	),
);

$products = new WP_Query( $args );

// Get all genre categories for filter
$genres = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
) );
?>

<div class="genre-archive">
	<div class="container mx-auto px-4">

		<!-- Page Header -->
		<header class="genre-header">
			<h1 class="genre-title"><?php echo esc_html( $term_name ); ?></h1>
			<?php if ( $term_desc ) : ?>
				<p class="genre-description"><?php echo esc_html( $term_desc ); ?></p>
			<?php endif; ?>
			<p class="genre-count">
				<?php
				printf(
					_n( '%s record', '%s records', $products->found_posts, 'fmw' ),
					number_format_i18n( $products->found_posts )
				);
				?>
			</p>
		</header>

		<!-- Genre Filter Pills -->
		<?php if ( ! empty( $genres ) && ! is_wp_error( $genres ) ) : ?>
			<div class="genre-filters">
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="genre-pill">
					All
				</a>
				<?php foreach ( $genres as $genre ) : ?>
					<a
						href="<?php echo esc_url( get_term_link( $genre ) ); ?>"
						class="genre-pill <?php echo $genre->term_id === $current_term->term_id ? 'is-active' : ''; ?>"
					>
						<?php echo esc_html( $genre->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<!-- Products Grid -->
		<?php if ( $products->have_posts() ) : ?>
			<div class="products-grid">
				<?php while ( $products->have_posts() ) : $products->the_post();
					global $product;

					$product_id  = $product->get_id();
					$title       = $product->get_name();
					$price       = $product->get_price_html();
					$permalink   = $product->get_permalink();
					$image_id    = $product->get_image_id();
					$label       = get_field( 'label', $product_id );
				?>
					<article class="product-card">
						<a href="<?php echo esc_url( $permalink ); ?>" class="product-card-link">
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
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div class="genre-no-results">
				<p>No records found in this genre.</p>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn">
					Browse All Records
				</a>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php
get_footer();
