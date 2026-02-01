<?php
/**
 * Single Product Page
 *
 * Custom template for displaying a single product.
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    global $product;

    $product_id = $product->get_id();
    $title      = $product->get_name();
    $price      = $product->get_price_html();
    $permalink  = $product->get_permalink();
    $image_id   = $product->get_image_id();
    $gallery    = $product->get_gallery_image_ids();

    // ACF fields
    $artist          = get_field( 'artist', $product_id );
    $label           = get_field( 'label', $product_id );
    $year            = get_field( 'year', $product_id );
    $country         = get_field( 'country', $product_id );
    $format_size     = get_field( 'format_size', $product_id );
    $speed           = get_field( 'speed', $product_id );
    $edition         = get_field( 'edition', $product_id );
    $media_condition = get_field( 'media_condition', $product_id );
    $sleeve_condition = get_field( 'sleeve_condition', $product_id );
    $youtube_videos  = get_field( 'youtube_videos', $product_id );

    // Condition labels
    $condition_labels = array(
        'M'   => 'Mint',
        'NM'  => 'Near Mint',
        'VG+' => 'Very Good Plus',
        'VG'  => 'Very Good',
        'G+'  => 'Good Plus',
        'G'   => 'Good',
        'F'   => 'Fair',
        'P'   => 'Poor',
    );

    // Edition labels
    $edition_labels = array(
        'white_label'     => 'White Label',
        'promo'           => 'Promo',
        'limited_edition' => 'Limited Edition',
        'ep'              => 'EP',
        'single'          => 'Single',
        'stereo'          => 'Stereo',
        'mono'            => 'Mono',
    );
?>

<div class="single-product">
        <div class="container mx-auto px-4">

            <div class="product-layout">
                <!-- Left: Images -->
                <div class="product-images">
                    <div class="product-image-main">
                        <?php if ( $image_id ) : ?>
                            <?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'product-main-img' ) ); ?>
                        <?php else : ?>
                            <div class="product-image-placeholder"></div>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $gallery ) ) : ?>
                        <div class="product-gallery">
                            <?php foreach ( $gallery as $gallery_image_id ) : ?>
                                <div class="product-gallery-item">
                                    <?php echo wp_get_attachment_image( $gallery_image_id, 'thumbnail' ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Details -->
                <div class="product-details">
                    <?php if ( $artist ) : ?>
                        <h1 class="product-artist"><?php echo esc_html( $artist ); ?></h1>
                        <h2 class="product-title"><?php echo esc_html( str_replace( $artist . ' - ', '', $title ) ); ?></h2>
                    <?php else : ?>
                        <h1 class="product-title"><?php echo esc_html( $title ); ?></h1>
                    <?php endif; ?>

                    <div class="product-meta">
                        <?php if ( $label ) : ?>
                            <span class="product-label"><?php echo esc_html( $label ); ?></span>
                        <?php endif; ?>
                        <?php if ( $year ) : ?>
                            <span class="product-year"><?php echo esc_html( $year ); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Format Info -->
                    <div class="product-format">
                        <h3 class="product-format-title">Vinyl</h3>
                        <div class="product-format-details">
                            <?php if ( $format_size ) : ?>
                                <span><?php echo esc_html( $format_size ); ?></span>
                            <?php endif; ?>
                            <?php if ( $speed ) : ?>
                                <span><?php echo esc_html( $speed ); ?> RPM</span>
                            <?php endif; ?>
                        </div>

                        <?php if ( $media_condition || $sleeve_condition ) : ?>
                            <div class="product-condition">
                                <?php if ( $media_condition ) : ?>
                                    <span>Media: <?php echo esc_html( $condition_labels[ $media_condition ] ?? $media_condition ); ?></span>
                                <?php endif; ?>
                                <?php if ( $sleeve_condition ) : ?>
                                    <span>Sleeve: <?php echo esc_html( $condition_labels[ $sleeve_condition ] ?? $sleeve_condition ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $edition ) ) : ?>
                            <div class="product-edition">
                                <?php foreach ( (array) $edition as $ed ) : ?>
                                    <span class="edition-badge"><?php echo esc_html( $edition_labels[ $ed ] ?? $ed ); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Price & Add to Cart -->
                    <div class="product-purchase">
                        <span class="product-price"><?php echo $price; ?></span>

                        <?php if ( $product->is_in_stock() ) : ?>
                            <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $permalink ) ); ?>" method="post" enctype="multipart/form-data">
                                <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
                                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" class="product-add-to-cart">
                                    Add to Basket
                                </button>
                                <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                            </form>
                        <?php else : ?>
                            <span class="product-sold-out">Sold</span>
                        <?php endif; ?>
                    </div>

                    <!-- Share -->
                    <div class="product-share">
                        <button class="share-button" onclick="navigator.share ? navigator.share({title: '<?php echo esc_js( $title ); ?>', url: '<?php echo esc_js( $permalink ); ?>'}) : null">
                            Share
                        </button>
                    </div>

                    <!-- Description -->
                    <?php if ( $product->get_description() ) : ?>
                        <div class="product-description">
                            <?php echo wp_kses_post( $product->get_description() ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- YouTube Videos -->
            <?php if ( ! empty( $youtube_videos ) ) : ?>
                <div class="product-videos">
                    <h3 class="product-section-title">Videos</h3>
                    <div class="product-videos-grid">
                        <?php foreach ( $youtube_videos as $video ) :
                            $video_url = $video['url'] ?? '';
                            $video_title = $video['title'] ?? '';

                            // Extract YouTube ID
                            preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches );
                            $video_id = $matches[1] ?? '';

                            if ( ! $video_id ) continue;
                        ?>
                            <div class="product-video">
                                <div class="product-video-embed">
                                    <iframe
                                        src="https://www.youtube.com/embed/<?php echo esc_attr( $video_id ); ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                                <?php if ( $video_title ) : ?>
                                    <p class="product-video-title"><?php echo esc_html( $video_title ); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Products -->
            <?php
            $related_args = array(
                'post_type'      => 'product',
                'posts_per_page' => 6,
                'post__not_in'   => array( $product_id ),
                'orderby'        => 'rand',
            );

            // Try to get products from same label
            if ( $label ) {
                $related_args['meta_query'] = array(
                    array(
                        'key'     => 'label',
                        'value'   => $label,
                        'compare' => '=',
                    ),
                );
            }

            $related_products = new WP_Query( $related_args );

            if ( $related_products->have_posts() ) :
            ?>
                <div class="product-related">
                    <h3 class="product-section-title">
                        <?php echo $label ? 'More from ' . esc_html( $label ) : 'You may also like'; ?>
                    </h3>
                    <div class="products-grid">
                        <?php while ( $related_products->have_posts() ) : $related_products->the_post();
                            global $product;
                            $rel_id    = $product->get_id();
                            $rel_title = $product->get_name();
                            $rel_price = $product->get_price_html();
                            $rel_link  = $product->get_permalink();
                            $rel_img   = $product->get_image_id();
                            $rel_label = get_field( 'label', $rel_id );
                        ?>
                            <article class="product-card">
                                <a href="<?php echo esc_url( $rel_link ); ?>" class="product-card-link">
                                    <div class="product-card-image">
                                        <?php if ( $rel_img ) : ?>
                                            <?php echo wp_get_attachment_image( $rel_img, 'medium', false, array( 'class' => 'product-card-img' ) ); ?>
                                        <?php else : ?>
                                            <div class="product-card-placeholder"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-card-info">
                                        <h4 class="product-card-title"><?php echo esc_html( $rel_title ); ?></h4>
                                        <?php if ( $rel_label ) : ?>
                                            <p class="product-card-label"><?php echo esc_html( $rel_label ); ?></p>
                                        <?php endif; ?>
                                        <p class="product-card-price"><?php echo $rel_price; ?></p>
                                    </div>
                                </a>
                            </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

<?php
endwhile;

get_footer();
