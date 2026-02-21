<?php
/**
 * Partial: Staff Picks Section
 *
 * Cream background, featured pick (large) + 5-row list from SCF options.
 *
 * @package FMW
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get featured pick from options
$pick_id    = function_exists( 'get_field' ) ? get_field( 'staff_pick_featured', 'option' ) : null;
$pick_desc  = function_exists( 'get_field' ) ? get_field( 'staff_pick_description', 'option' ) : '';
$pick       = $pick_id && function_exists( 'wc_get_product' ) ? wc_get_product( $pick_id ) : null;

// Get picks list from options repeater
$picks_list = array();
if ( function_exists( 'have_rows' ) && have_rows( 'staff_picks_list', 'option' ) ) {
    while ( have_rows( 'staff_picks_list', 'option' ) ) {
        the_row();
        $pid = get_sub_field( 'product' );
        if ( $pid ) {
            $picks_list[] = wc_get_product( $pid );
        }
    }
}

// Fallback: use latest products if no options set
if ( ! $pick && function_exists( 'wc_get_products' ) ) {
    $fallback = wc_get_products( array( 'limit' => 6, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ) );
    if ( ! empty( $fallback ) ) {
        $pick = array_shift( $fallback );
        $picks_list = array_slice( $fallback, 0, 5 );
    }
}

// Featured pick data
$pick_name   = $pick ? $pick->get_name() : '';
$pick_parts  = explode( ' - ', $pick_name, 2 );
$pick_artist = strtoupper( $pick_parts[0] ?? '' );
$pick_title  = $pick_parts[1] ?? $pick_name;
$pick_img_id = $pick ? $pick->get_image_id() : null;
$pick_price  = $pick ? strip_tags( $pick->get_price_html() ) : '';
$pick_label  = $pick ? fmw_get_product_label( $pick->get_id() ) : '';
$pick_link   = $pick ? $pick->get_permalink() : '#';
?>

<section class="bg-cream py-20 px-6 md:px-[60px]">

    <!-- Section Header -->
    <div class="flex items-end justify-between mb-12">
        <div class="flex flex-col gap-2">
            <span class="font-mono text-xs font-bold text-accent tracking-wider-2">03</span>
            <h2 class="font-display text-[32px] md:text-[40px] font-bold text-dark tracking-[-1px]">STAFF PICKS</h2>
        </div>
        <span class="font-mono text-xs text-dark/50 tracking-[1px] pb-3">Hand-selected by the crate diggers</span>
    </div>

    <!-- Divider -->
    <div class="w-full h-px bg-dark/[0.125] mb-12"></div>

    <!-- Content -->
    <div class="flex flex-col lg:flex-row gap-6">

        <!-- Featured Pick (Left) -->
        <?php if ( $pick ) : ?>
            <div class="flex flex-col w-full lg:w-[520px] shrink-0">
                <!-- Image -->
                <a href="<?php echo esc_url( $pick_link ); ?>" class="block w-full h-[520px] overflow-hidden no-transition">
                    <?php if ( $pick_img_id ) : ?>
                        <?php echo wp_get_attachment_image( $pick_img_id, 'large', false, array( 'class' => 'w-full h-full object-cover' ) ); ?>
                    <?php else : ?>
                        <div class="w-full h-full bg-card-dark"></div>
                    <?php endif; ?>
                </a>

                <!-- Info -->
                <div class="flex flex-col gap-2 pt-5">
                    <span class="inline-flex self-start bg-accent px-2.5 py-1 font-mono text-[10px] font-bold text-dark tracking-wider-2 uppercase">PICK OF THE WEEK</span>
                    <span class="font-mono text-xs font-extrabold text-teal-dark tracking-wider-2 uppercase"><?php echo esc_html( $pick_artist ); ?></span>
                    <span class="font-display text-2xl font-bold text-dark"><?php echo esc_html( $pick_title ); ?></span>
                    <span class="font-mono text-xs text-dark/50"><?php echo esc_html( $pick_label ); ?> / <?php echo esc_html( $pick_price ); ?></span>
                    <?php if ( $pick_desc ) : ?>
                        <p class="font-mono text-xs text-dark/50 leading-[1.7] max-w-[520px]"><?php echo esc_html( $pick_desc ); ?></p>
                    <?php endif; ?>
                    <?php if ( $pick->is_purchasable() && $pick->is_in_stock() ) : ?>
                        <button type="button" onclick="fmwAddToCart(<?php echo esc_attr( $pick->get_id() ); ?>, this)" class="font-mono text-[11px] font-bold text-dark tracking-wider-2 mt-1 bg-transparent border-0 cursor-pointer p-0 hover:text-teal-dark transition-colors">[ + ADD TO CART ]</button>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $pick_link ); ?>" class="font-mono text-[11px] font-bold text-dark/50 tracking-wider-2 mt-1">[ VIEW RELEASE ]</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Picks List (Right) -->
        <?php if ( ! empty( $picks_list ) ) : ?>
            <div class="flex flex-col w-full">
                <?php foreach ( $picks_list as $index => $list_pick ) :
                    if ( ! $list_pick ) continue;

                    $lp_name   = $list_pick->get_name();
                    $lp_parts  = explode( ' - ', $lp_name, 2 );
                    $lp_artist = strtoupper( $lp_parts[0] ?? '' );
                    $lp_title  = $lp_parts[1] ?? $lp_name;
                    $lp_img_id = $list_pick->get_image_id();
                    $lp_price  = strip_tags( $list_pick->get_price_html() );
                    $lp_label  = fmw_get_product_label( $list_pick->get_id() );
                    $lp_link   = $list_pick->get_permalink();
                    $lp_format = $list_pick->get_attribute( 'format' ) ?: '12"';
                    $lp_num    = str_pad( $index + 1, 2, '0', STR_PAD_LEFT );
                    $is_last   = ( $index === count( $picks_list ) - 1 );
                ?>
                    <div class="flex items-center gap-4 py-5 <?php echo ! $is_last ? 'border-b border-dark/[0.125]' : ''; ?>">
                        <!-- Number -->
                        <span class="font-mono text-[11px] font-bold text-dark/50 tracking-[1px] w-6 shrink-0"><?php echo esc_html( $lp_num ); ?></span>

                        <!-- Cover -->
                        <a href="<?php echo esc_url( $lp_link ); ?>" class="block w-16 h-16 overflow-hidden shrink-0 no-transition">
                            <?php if ( $lp_img_id ) : ?>
                                <?php echo wp_get_attachment_image( $lp_img_id, 'thumbnail', false, array( 'class' => 'w-full h-full object-cover' ) ); ?>
                            <?php else : ?>
                                <div class="w-full h-full bg-card-dark"></div>
                            <?php endif; ?>
                        </a>

                        <!-- Info -->
                        <div class="flex flex-col gap-1 flex-1 min-w-0">
                            <span class="font-mono text-[11px] font-extrabold text-teal-dark tracking-wider-2 uppercase truncate"><?php echo esc_html( $lp_artist ); ?></span>
                            <a href="<?php echo esc_url( $lp_link ); ?>" class="font-display text-base font-bold text-dark truncate no-transition"><?php echo esc_html( $lp_title ); ?></a>
                            <span class="font-mono text-[11px] text-dark/50"><?php echo esc_html( $lp_label ); ?> / <?php echo esc_html( $lp_format ); ?></span>
                        </div>

                        <!-- Price -->
                        <span class="font-mono text-sm font-bold text-dark shrink-0"><?php echo esc_html( $lp_price ); ?></span>

                        <!-- Cart -->
                        <?php if ( $list_pick->is_purchasable() && $list_pick->is_in_stock() ) : ?>
                            <button type="button" onclick="fmwAddToCart(<?php echo esc_attr( $list_pick->get_id() ); ?>, this)" class="font-mono text-[11px] font-bold text-dark tracking-wider-2 shrink-0 bg-transparent border-0 cursor-pointer p-0 hover:text-teal-dark transition-colors">[ + ]</button>
                        <?php else : ?>
                            <a href="<?php echo esc_url( $lp_link ); ?>" class="font-mono text-[11px] font-bold text-dark/50 tracking-wider-2 shrink-0">[ VIEW ]</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
