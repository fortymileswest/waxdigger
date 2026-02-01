<?php
/**
 * Front Page Template
 *
 * Template for the site's front page.
 *
 * @package FMW
 */

get_header();

// Check for ACF flexible content sections first
if ( function_exists( 'have_rows' ) && have_rows( 'sections' ) ) :
    while ( have_rows( 'sections' ) ) : the_row();
        $layout  = get_row_layout();
        $partial = FMW_DIR . '/partials/' . $layout . '.php';

        if ( file_exists( $partial ) ) {
            include $partial;
        }
    endwhile;
else :
    // Fallback: Show featured releases slider
    fmw_partial( 'featured_releases' );

    // Default page content if any
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            if ( get_the_content() ) :
                ?>
                <div class="container mx-auto px-4 py-8">
                    <?php the_content(); ?>
                </div>
                <?php
            endif;
        endwhile;
    endif;

    // Show latest products
    if ( function_exists( 'wc_get_products' ) ) :
        $latest_products = wc_get_products( array(
            'limit'   => 12,
            'status'  => 'publish',
            'orderby' => 'date',
            'order'   => 'DESC',
        ) );

        if ( ! empty( $latest_products ) ) :
        ?>
        <section class="py-8 md:py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-xl md:text-2xl font-bold text-black mb-6">Latest Arrivals</h2>
                <div class="products-grid">
                    <?php foreach ( $latest_products as $product ) :
                        $prod_id    = $product->get_id();
                        $prod_title = $product->get_name();
                        $prod_price = $product->get_price_html();
                        $prod_link  = $product->get_permalink();
                        $prod_img   = $product->get_image_id();
                        $prod_label = get_field( 'label', $prod_id );
                    ?>
                        <article class="product-card">
                            <a href="<?php echo esc_url( $prod_link ); ?>" class="product-card-link">
                                <div class="product-card-image">
                                    <?php if ( $prod_img ) : ?>
                                        <?php echo wp_get_attachment_image( $prod_img, 'medium', false, array( 'class' => 'product-card-img' ) ); ?>
                                    <?php else : ?>
                                        <div class="product-card-placeholder"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-card-info">
                                    <h3 class="product-card-title"><?php echo esc_html( $prod_title ); ?></h3>
                                    <?php if ( $prod_label ) : ?>
                                        <p class="product-card-label"><?php echo esc_html( $prod_label ); ?></p>
                                    <?php endif; ?>
                                    <p class="product-card-price"><?php echo $prod_price; ?></p>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        endif;
    endif;
endif;

get_footer();
