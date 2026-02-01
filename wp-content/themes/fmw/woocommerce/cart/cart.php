<?php
/**
 * Custom Cart Page
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<div class="fmw-cart">
    <div class="cart-content">
        <h1 class="cart-page-title">Basket</h1>

        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <?php if ( WC()->cart->is_empty() ) : ?>
            <div class="cart-empty">
                <p>Your basket is empty.</p>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="cart-continue-btn">
                    Continue Shopping
                </a>
            </div>
        <?php else : ?>
            <div class="cart-items">
                <?php
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                    $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                        $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                        $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'thumbnail' ), $cart_item, $cart_item_key );
                        $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                        $product_subtotal  = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );

                        // Get stock quantity
                        $stock_qty = $_product->get_stock_quantity();
                        $manages_stock = $_product->managing_stock();

                        // ACF fields
                        $artist = get_field( 'artist', $product_id );
                        $label  = get_field( 'label', $product_id );
                        ?>
                        <div class="cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                            <!-- Product Image -->
                            <div class="cart-item-image">
                                <?php if ( $product_permalink ) : ?>
                                    <a href="<?php echo esc_url( $product_permalink ); ?>">
                                        <?php echo $thumbnail; ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo $thumbnail; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Product Details -->
                            <div class="cart-item-details">
                                <div class="cart-item-info">
                                    <?php if ( $product_permalink ) : ?>
                                        <a href="<?php echo esc_url( $product_permalink ); ?>" class="cart-item-title">
                                            <?php echo esc_html( $product_name ); ?>
                                        </a>
                                    <?php else : ?>
                                        <span class="cart-item-title"><?php echo esc_html( $product_name ); ?></span>
                                    <?php endif; ?>

                                    <?php if ( $label ) : ?>
                                        <span class="cart-item-label"><?php echo esc_html( $label ); ?></span>
                                    <?php endif; ?>

                                    <span class="cart-item-price"><?php echo $product_price; ?></span>
                                </div>

                                <!-- Quantity / Remove - Secondhand shop, each item unique, no qty controls -->
                                <div class="cart-item-actions">
                                    <input type="hidden" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>">

                                    <!-- Remove Link -->
                                    <?php
                                    echo apply_filters(
                                        'woocommerce_cart_item_remove_link',
                                        sprintf(
                                            '<a href="%s" class="cart-item-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">Remove</a>',
                                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                            esc_html__( 'Remove this item', 'woocommerce' ),
                                            esc_attr( $product_id ),
                                            esc_attr( $_product->get_sku() )
                                        ),
                                        $cart_item_key
                                    );
                                    ?>
                                </div>
                            </div>

                            <!-- Subtotal -->
                            <div class="cart-item-subtotal">
                                <?php echo $product_subtotal; ?>
                            </div>
                        </div>
                        <?php
                    endif;
                endforeach;
                ?>
            </div>

            <!-- Cart Actions -->
            <div class="cart-actions">
                <button type="submit" class="cart-update-btn" name="update_cart" value="<?php esc_attr_e( 'Update basket', 'woocommerce' ); ?>">
                    Update Basket
                </button>
                <?php do_action( 'woocommerce_cart_actions' ); ?>
                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
            </div>

        <?php endif; ?>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>
    </div>

    <?php if ( ! WC()->cart->is_empty() ) : ?>
        <!-- Cart Totals -->
        <div class="cart-totals">
            <h2 class="cart-totals-title">Order Summary</h2>

            <div class="cart-totals-rows">
                <div class="cart-totals-row">
                    <span>Subtotal</span>
                    <span><?php wc_cart_totals_subtotal_html(); ?></span>
                </div>

                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                    <div class="cart-totals-row cart-discount">
                        <span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                        <span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                    <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
                    <div class="cart-totals-row">
                        <span>Shipping</span>
                        <span><?php wc_cart_totals_shipping_html(); ?></span>
                    </div>
                    <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
                <?php endif; ?>

                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                    <div class="cart-totals-row">
                        <span><?php echo esc_html( $fee->name ); ?></span>
                        <span><?php wc_cart_totals_fee_html( $fee ); ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                    <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                            <div class="cart-totals-row">
                                <span><?php echo esc_html( $tax->label ); ?></span>
                                <span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="cart-totals-row">
                            <span><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
                            <span><?php wc_cart_totals_taxes_total_html(); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

                <div class="cart-totals-row cart-total">
                    <span>Total</span>
                    <span><?php wc_cart_totals_order_total_html(); ?></span>
                </div>

                <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
            </div>

            <div class="cart-checkout">
                <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
            </div>

            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="cart-continue">
                Continue Shopping
            </a>
        </div>
    <?php endif; ?>

    <?php do_action( 'woocommerce_after_cart' ); ?>
</div>
