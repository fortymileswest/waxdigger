<?php
/**
 * Footer Template
 *
 * @package FMW
 */
?>
    </main><!-- #main -->

    <footer id="colophon" class="site-footer">
        <div class="container mx-auto px-4">
            <!-- Footer Main -->
            <div class="footer-main">
                <!-- Brand Column -->
                <div class="footer-brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                    <p class="footer-tagline">Curated secondhand vinyl records.</p>
                </div>

                <!-- Shop Links -->
                <div class="footer-column">
                    <h4 class="footer-heading">Shop</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">All Records</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/?orderby=date' ) ); ?>">New Arrivals</a></li>
                    </ul>
                </div>

                <!-- Info Links -->
                <div class="footer-column">
                    <h4 class="footer-heading">Info</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>">About</a></li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>">Contact</a></li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'refund-and-returns-policy' ) ) ); ?>">Returns</a></li>
                    </ul>
                </div>

                <!-- Connect -->
                <div class="footer-column">
                    <h4 class="footer-heading">Connect</h4>
                    <div class="footer-social">
                        <a href="https://instagram.com/waxdigger" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="footer-social-link">
                            <?php fmw_icon( 'instagram', 'w-5 h-5' ); ?>
                        </a>
                        <a href="https://discogs.com/seller/waxdigger" target="_blank" rel="noopener noreferrer" aria-label="Discogs" class="footer-social-link">
                            <?php fmw_icon( 'discogs', 'w-5 h-5' ); ?>
                        </a>
                        <a href="mailto:hello@waxdigger.com" aria-label="Email" class="footer-social-link">
                            <?php fmw_icon( 'mail', 'w-5 h-5' ); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p class="footer-copyright">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
                <p class="footer-location">Made in the United Kingdom</p>
            </div>
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
?>

<?php wp_footer(); ?>

</body>
</html>
