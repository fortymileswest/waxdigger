<?php
/**
 * Footer Template
 *
 * @package FMW
 */

$shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : '#';
?>
    </main><!-- #main -->

    <footer id="colophon" class="bg-cream">

        <!-- Footer Top -->
        <div class="flex flex-col lg:flex-row justify-between gap-[60px] px-6 md:px-[60px] pt-[60px] pb-12">

            <!-- Brand Column -->
            <div class="flex flex-col gap-4 w-full lg:w-[320px] shrink-0">
                <!-- Compass Logo -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 no-transition">
                    <svg width="24" height="24" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="18" cy="18" r="16.75" stroke="#25ddb3" stroke-width="2.5"/>
                        <circle cx="18" cy="18" r="10" stroke="#25ddb3" stroke-width="2"/>
                        <circle cx="18" cy="18" r="4.75" stroke="#25ddb3" stroke-width="1.5"/>
                    </svg>
                    <span class="font-mono text-xl font-bold text-dark tracking-[5px]">WAXDIGGER</span>
                </a>

                <!-- Tagline -->
                <p class="font-mono text-xs text-dark/50 leading-[1.7]">Curated underground vinyl.<br>Rave. Jungle. Hip Hop.<br>The sounds that matter.</p>

                <!-- Social Icons -->
                <div class="flex items-center gap-4">
                    <a href="https://instagram.com/waxdigger" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="text-dark hover:text-teal-dark transition-colors">
                        <?php fmw_icon( 'instagram', 'w-[18px] h-[18px]' ); ?>
                    </a>
                    <a href="https://twitter.com/waxdigger" target="_blank" rel="noopener noreferrer" aria-label="Twitter" class="text-dark hover:text-teal-dark transition-colors">
                        <?php fmw_icon( 'twitter', 'w-[18px] h-[18px]' ); ?>
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" aria-label="SoundCloud" class="text-dark hover:text-teal-dark transition-colors">
                        <?php fmw_icon( 'headphones', 'w-[18px] h-[18px]' ); ?>
                    </a>
                    <a href="https://youtube.com/@waxdigger" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="text-dark hover:text-teal-dark transition-colors">
                        <?php fmw_icon( 'youtube', 'w-[18px] h-[18px]' ); ?>
                    </a>
                </div>
            </div>

            <!-- Link Columns -->
            <div class="flex flex-wrap gap-20">

                <!-- Shop -->
                <div class="flex flex-col gap-4">
                    <span class="font-mono text-[10px] font-bold text-dark tracking-wider-2 uppercase">SHOP</span>
                    <a href="<?php echo esc_url( $shop_url ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">New Arrivals</a>
                    <a href="<?php echo esc_url( $shop_url ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">Pre-Orders</a>
                    <a href="<?php echo esc_url( $shop_url ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">Restocks</a>
                    <a href="<?php echo esc_url( $shop_url ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">Sale</a>
                </div>

                <!-- Genres -->
                <div class="flex flex-col gap-4">
                    <span class="font-mono text-[10px] font-bold text-dark tracking-wider-2 uppercase">GENRES</span>
                    <?php
                    $footer_genres = get_terms( array(
                        'taxonomy'   => 'genre',
                        'hide_empty' => false,
                        'orderby'    => 'name',
                        'number'     => 5,
                    ) );
                    if ( ! empty( $footer_genres ) && ! is_wp_error( $footer_genres ) ) :
                        foreach ( $footer_genres as $fg ) :
                    ?>
                        <a href="<?php echo esc_url( get_term_link( $fg ) ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors"><?php echo esc_html( $fg->name ); ?></a>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <a href="#" class="font-mono text-xs text-dark/50">Jungle / DnB</a>
                        <a href="#" class="font-mono text-xs text-dark/50">Rave / Hardcore</a>
                        <a href="#" class="font-mono text-xs text-dark/50">Hip Hop / Breaks</a>
                        <a href="#" class="font-mono text-xs text-dark/50">House / Techno</a>
                        <a href="#" class="font-mono text-xs text-dark/50">Breaks / Electro</a>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="flex flex-col gap-4">
                    <span class="font-mono text-[10px] font-bold text-dark tracking-wider-2 uppercase">INFO</span>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">About Us</a>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'shipping' ) ) ?: '#' ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">Shipping</a>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'returns' ) ) ?: '#' ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">Returns</a>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">Contact</a>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'faq' ) ) ?: '#' ); ?>" class="font-mono text-xs text-dark/50 hover:text-dark transition-colors">FAQ</a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="mx-6 md:mx-[60px] h-px bg-dark/[0.125]"></div>

        <!-- Footer Bottom -->
        <div class="flex items-center justify-between px-6 md:px-[60px] py-8">
            <span class="font-mono text-[10px] text-dark/50 tracking-[1px]">&copy; <?php echo esc_html( date( 'Y' ) ); ?> WAXDIGGER. ALL RIGHTS RESERVED.</span>
            <span class="font-mono text-[10px] text-dark/50 tracking-[1px] hidden sm:block">BUILT FOR THE UNDERGROUND</span>
        </div>

    </footer>
</div><!-- #page -->

<?php
// Login Modal (only for non-logged-in users)
if ( function_exists( 'WC' ) && ! is_user_logged_in() ) {
    fmw_component( 'login-modal' );
}

// Search Modal
if ( function_exists( 'WC' ) ) {
    fmw_component( 'search-modal' );
}

// Cart Drawer
if ( function_exists( 'WC' ) ) {
    fmw_component( 'cart-drawer' );
}

// Exit Intent Popup (non-logged-in users only)
if ( function_exists( 'WC' ) && ! is_user_logged_in() ) {
    fmw_component( 'exit-popup' );
}

// Cookie Consent
fmw_component( 'cookie-consent' );

// Back to Top
fmw_component( 'back-to-top' );
?>

<?php wp_footer(); ?>

</body>
</html>
