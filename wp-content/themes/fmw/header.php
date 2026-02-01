<?php
/**
 * Header Template
 *
 * @package FMW
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main">
        <?php esc_html_e( 'Skip to content', 'fmw' ); ?>
    </a>

    <header
        id="masthead"
        class="site-header"
        x-data="{
            mobileMenuOpen: false,
            sticky: false,
            hidden: false,
            lastY: 0,
            hasScrolled: false
        }"
        x-init="
            lastY = window.scrollY;
            window.addEventListener('scroll', () => {
                const y = window.scrollY;

                // Become sticky after 50px
                sticky = y > 50;

                // Only start tracking after user has scrolled past 300px at least once
                if (y > 300) hasScrolled = true;

                // Hide when scrolling down, but only after scrolling past 300px
                if (hasScrolled && y > 300 && y > lastY + 5) {
                    hidden = true;
                }

                // Show when scrolling up
                if (y < lastY - 5) {
                    hidden = false;
                }

                // Always show near top
                if (y < 150) {
                    hidden = false;
                    hasScrolled = false;
                }

                lastY = y;
            }, { passive: true });
        "
        :class="{
            'is-sticky': sticky,
            'is-hidden': hidden
        }"
    >
        <div class="header-inner">
            <div class="container mx-auto px-4">
                <div class="header-row">
                    <!-- Logo -->
                    <div class="site-branding">
                        <?php if ( has_custom_logo() ) : ?>
                            <?php the_custom_logo(); ?>
                        <?php else : ?>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title">
                                <?php bloginfo( 'name' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav id="site-navigation" class="main-navigation hidden lg:block">
                        <ul id="primary-menu" class="menu">
                            <li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Shop</a></li>
                            <li
                                class="menu-item-has-children has-mega-menu"
                                x-data="{ open: false }"
                                @mouseenter="open = true"
                                @mouseleave="open = false"
                            >
                                <a href="#" @click.prevent>Genres</a>
                                <div
                                    class="mega-menu"
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2"
                                >
                                    <div class="mega-menu-inner">
                                        <p class="mega-menu-title">Browse by Genre</p>
                                        <?php
                                        $genres = get_terms( array(
                                            'taxonomy'   => 'product_cat',
                                            'hide_empty' => false,
                                            'exclude'    => array( get_option( 'default_product_cat' ) ),
                                            'orderby'    => 'name',
                                            'order'      => 'ASC',
                                        ) );

                                        if ( ! empty( $genres ) && ! is_wp_error( $genres ) ) :
                                        ?>
                                            <div class="mega-menu-grid">
                                                <?php foreach ( $genres as $genre ) : ?>
                                                    <a href="<?php echo esc_url( get_term_link( $genre ) ); ?>" class="mega-menu-item">
                                                        <span><?php echo esc_html( $genre->name ); ?></span>
                                                        <?php if ( $genre->count > 0 ) : ?>
                                                            <span class="mega-menu-count"><?php echo esc_html( $genre->count ); ?></span>
                                                        <?php endif; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>">About</a></li>
                            <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>">Contact</a></li>
                        </ul>
                    </nav>

                    <!-- Header Actions -->
                    <div class="header-actions">
                        <!-- Search Toggle (Desktop) -->
                        <button
                            type="button"
                            class="header-action hidden md:flex"
                            @click="$dispatch('search-modal')"
                            aria-label="<?php esc_attr_e( 'Search records', 'fmw' ); ?>"
                        >
                            <?php fmw_icon( 'search', 'icon' ); ?>
                        </button>

                        <!-- Account -->
                        <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                            <?php if ( is_user_logged_in() ) : ?>
                                <a
                                    href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
                                    class="header-action hidden md:flex"
                                    aria-label="<?php esc_attr_e( 'My account', 'fmw' ); ?>"
                                >
                                    <?php fmw_icon( 'user', 'icon' ); ?>
                                </a>
                            <?php else : ?>
                                <button
                                    type="button"
                                    class="header-action hidden md:flex"
                                    @click="$dispatch('login-modal')"
                                    aria-label="<?php esc_attr_e( 'Sign in', 'fmw' ); ?>"
                                >
                                    <?php fmw_icon( 'user', 'icon' ); ?>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Cart -->
                        <?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
                            <a
                                href="<?php echo esc_url( wc_get_cart_url() ); ?>"
                                class="header-action header-cart"
                                aria-label="<?php esc_attr_e( 'View cart', 'fmw' ); ?>"
                            >
                                <?php fmw_icon( 'cart', 'icon' ); ?>
                                <?php if ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                                    <span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <!-- Mobile Menu Toggle -->
                        <button
                            type="button"
                            class="header-action mobile-menu-toggle lg:hidden"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            :aria-expanded="mobileMenuOpen"
                            aria-controls="mobile-menu"
                            aria-label="<?php esc_attr_e( 'Toggle menu', 'fmw' ); ?>"
                        >
                            <span x-show="!mobileMenuOpen"><?php fmw_icon( 'menu', 'icon' ); ?></span>
                            <span x-show="mobileMenuOpen" x-cloak><?php fmw_icon( 'close', 'icon' ); ?></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Mobile Menu -->
        <div
            id="mobile-menu"
            class="mobile-menu lg:hidden"
            x-show="mobileMenuOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="container mx-auto px-4">
                <!-- Mobile Search -->
                <button
                    type="button"
                    class="mobile-search-btn"
                    @click="$dispatch('search-modal'); mobileMenuOpen = false"
                >
                    <?php fmw_icon( 'search', 'icon' ); ?>
                    <span>Search Store</span>
                </button>

                <!-- Mobile Navigation -->
                <nav class="mobile-navigation">
                    <ul class="mobile-menu-list">
                        <li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Shop</a></li>
                        <li x-data="{ open: false }">
                            <button type="button" class="mobile-menu-toggle" @click="open = !open">
                                <span>Genres</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <ul
                                class="mobile-submenu"
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                            >
                                <?php
                                $mobile_genres = get_terms( array(
                                    'taxonomy'   => 'product_cat',
                                    'hide_empty' => false,
                                    'exclude'    => array( get_option( 'default_product_cat' ) ),
                                ) );
                                if ( ! empty( $mobile_genres ) && ! is_wp_error( $mobile_genres ) ) :
                                    foreach ( $mobile_genres as $genre ) :
                                ?>
                                    <li>
                                        <a href="<?php echo esc_url( get_term_link( $genre ) ); ?>">
                                            <?php echo esc_html( $genre->name ); ?>
                                        </a>
                                    </li>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </ul>
                        </li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>">About</a></li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>">Contact</a></li>
                    </ul>
                </nav>

                <!-- Mobile Account Link -->
                <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                    <?php if ( is_user_logged_in() ) : ?>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="mobile-account-link">
                            <?php fmw_icon( 'user', 'icon' ); ?>
                            <?php esc_html_e( 'My Account', 'fmw' ); ?>
                        </a>
                    <?php else : ?>
                        <button type="button" class="mobile-account-link" @click="$dispatch('login-modal'); mobileMenuOpen = false">
                            <?php fmw_icon( 'user', 'icon' ); ?>
                            <?php esc_html_e( 'Sign In', 'fmw' ); ?>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main id="main" class="site-main page-transition">
