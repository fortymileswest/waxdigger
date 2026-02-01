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
            searchOpen: false,
            sticky: false,
            hidden: false,
            lastY: 0
        }"
        x-init="
            window.addEventListener('scroll', () => {
                const y = window.scrollY;
                sticky = y > 50;
                if (y > 100) {
                    hidden = y > lastY;
                } else {
                    hidden = false;
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
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'primary',
                                'menu_id'        => 'primary-menu',
                                'container'      => false,
                                'fallback_cb'    => false,
                                'depth'          => 2,
                            )
                        );
                        ?>
                    </nav>

                    <!-- Header Actions -->
                    <div class="header-actions">
                        <!-- Search Toggle (Desktop) -->
                        <button
                            type="button"
                            class="header-action hidden md:flex"
                            @click="searchOpen = !searchOpen"
                            :aria-expanded="searchOpen"
                            aria-label="<?php esc_attr_e( 'Toggle search', 'fmw' ); ?>"
                        >
                            <?php fmw_icon( 'search', 'icon' ); ?>
                        </button>

                        <!-- Account -->
                        <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                            <a
                                href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
                                class="header-action hidden md:flex"
                                aria-label="<?php esc_attr_e( 'My account', 'fmw' ); ?>"
                            >
                                <?php fmw_icon( 'user', 'icon' ); ?>
                            </a>
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

                <!-- Search Bar (Desktop) -->
                <div
                    class="search-bar hidden md:block"
                    x-show="searchOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    @click.away="searchOpen = false"
                >
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form">
                        <label class="sr-only" for="desktop-search"><?php esc_html_e( 'Search records', 'fmw' ); ?></label>
                        <input
                            type="search"
                            id="desktop-search"
                            class="search-input"
                            placeholder="<?php esc_attr_e( 'Search artists, albums, labels...', 'fmw' ); ?>"
                            name="s"
                            value="<?php echo esc_attr( get_search_query() ); ?>"
                            x-ref="searchInput"
                            @keydown.escape="searchOpen = false"
                        >
                        <?php if ( function_exists( 'WC' ) ) : ?>
                            <input type="hidden" name="post_type" value="product">
                        <?php endif; ?>
                        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'fmw' ); ?>">
                            <?php fmw_icon( 'search', 'icon' ); ?>
                        </button>
                    </form>
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
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-search-form">
                    <label class="sr-only" for="mobile-search"><?php esc_html_e( 'Search records', 'fmw' ); ?></label>
                    <input
                        type="search"
                        id="mobile-search"
                        class="search-input"
                        placeholder="<?php esc_attr_e( 'Search...', 'fmw' ); ?>"
                        name="s"
                        value="<?php echo esc_attr( get_search_query() ); ?>"
                    >
                    <?php if ( function_exists( 'WC' ) ) : ?>
                        <input type="hidden" name="post_type" value="product">
                    <?php endif; ?>
                    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'fmw' ); ?>">
                        <?php fmw_icon( 'search', 'icon' ); ?>
                    </button>
                </form>

                <!-- Mobile Navigation -->
                <nav class="mobile-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'mobile-nav-menu',
                            'container'      => false,
                            'fallback_cb'    => false,
                            'depth'          => 2,
                        )
                    );
                    ?>
                </nav>

                <!-- Mobile Account Link -->
                <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="mobile-account-link">
                        <?php fmw_icon( 'user', 'icon' ); ?>
                        <?php esc_html_e( 'My Account', 'fmw' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main id="main" class="site-main">
