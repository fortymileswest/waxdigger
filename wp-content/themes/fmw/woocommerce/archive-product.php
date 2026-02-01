<?php
/**
 * WooCommerce Shop/Archive Page
 *
 * Custom template for displaying products.
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Get all products
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
);

$products = new WP_Query( $args );
?>

<div class="shop-page">
        <div class="container mx-auto px-4">

            <!-- Page Header -->
            <header class="shop-header">
                <h1 class="shop-title"><?php woocommerce_page_title(); ?></h1>
            </header>

            <!-- Products Grid -->
            <?php if ( $products->have_posts() ) : ?>
                <div class="products-grid">
                    <?php while ( $products->have_posts() ) : $products->the_post();
                        global $product;

                        // Get product data
                        $product_id  = $product->get_id();
                        $title       = $product->get_name();
                        $price       = $product->get_price_html();
                        $permalink   = $product->get_permalink();
                        $image_id    = $product->get_image_id();

                        // ACF fields
                        $artist          = get_field( 'artist', $product_id );
                        $label           = get_field( 'label', $product_id );
                        $format_size     = get_field( 'format_size', $product_id );
                        $media_condition = get_field( 'media_condition', $product_id );
                    ?>
                        <article class="product-card">
                            <a href="<?php echo esc_url( $permalink ); ?>" class="product-card-link">
                                <div class="product-card-image">
                                    <?php if ( $image_id ) : ?>
                                        <?php echo wp_get_attachment_image( $image_id, 'medium', false, array( 'class' => 'product-card-img' ) ); ?>
                                    <?php else : ?>
                                        <div class="product-card-placeholder"></div>
                                    <?php endif; ?>
                                </div>

                                <div class="product-card-info">
                                    <h2 class="product-card-title"><?php echo esc_html( $title ); ?></h2>

                                    <?php if ( $label ) : ?>
                                        <p class="product-card-label"><?php echo esc_html( $label ); ?></p>
                                    <?php endif; ?>

                                    <div class="product-card-meta">
                                        <?php if ( $format_size ) : ?>
                                            <span class="product-card-format"><?php echo esc_html( $format_size ); ?></span>
                                        <?php endif; ?>
                                        <?php if ( $media_condition ) : ?>
                                            <span class="product-card-condition"><?php echo esc_html( $media_condition ); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="product-card-price"><?php echo $price; ?></p>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php wp_reset_postdata(); ?>

            <?php else : ?>
                <p class="no-products">No products found.</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
get_footer();
