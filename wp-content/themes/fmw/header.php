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

<body <?php body_class( 'bg-dark text-cream font-mono' ); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main">
        <?php esc_html_e( 'Skip to content', 'fmw' ); ?>
    </a>

    <header
        id="masthead"
        class="site-header fixed top-0 left-0 w-full z-50 bg-dark"
        x-data="{ mobileMenuOpen: false }"
    >
        <div class="flex items-center justify-between px-6 md:px-[60px] py-5 border-b border-cream/[0.125]">

            <!-- Logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 no-transition">
                <!-- Compass rings -->
                <svg width="24" height="24" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16.75" stroke="#25ddb3" stroke-width="2.5"/>
                    <circle cx="18" cy="18" r="10" stroke="#25ddb3" stroke-width="2"/>
                    <circle cx="18" cy="18" r="4.75" stroke="#25ddb3" stroke-width="1.5"/>
                </svg>
                <span class="text-cream font-mono text-xl font-bold tracking-[5px]">WAXDIGGER</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-8">
                <?php
                $shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : '#';
                ?>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="text-accent font-mono text-[11px] font-semibold uppercase tracking-wider-2 hover:text-accent transition-colors">NEW ARRIVALS</a>
                <a href="<?php echo esc_url( $shop_url ); ?>?browse=genres" class="text-cream font-mono text-[11px] font-medium uppercase tracking-wider-2 hover:text-accent transition-colors">GENRES</a>
                <a href="<?php echo esc_url( $shop_url ); ?>?browse=labels" class="text-cream font-mono text-[11px] font-medium uppercase tracking-wider-2 hover:text-accent transition-colors">LABELS</a>
                <a href="<?php echo esc_url( $shop_url ); ?>?browse=artists" class="text-cream font-mono text-[11px] font-medium uppercase tracking-wider-2 hover:text-accent transition-colors">ARTISTS</a>
                <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>" class="text-cream font-mono text-[11px] font-medium uppercase tracking-wider-2 hover:text-accent transition-colors">ABOUT</a>
            </nav>

            <!-- Header Actions -->
            <div class="flex items-center gap-6">
                <!-- Search -->
                <button
                    type="button"
                    class="hidden md:flex text-cream hover:text-accent transition-colors"
                    @click="$dispatch('search-modal')"
                    aria-label="<?php esc_attr_e( 'Search records', 'fmw' ); ?>"
                >
                    <?php fmw_icon( 'search', 'w-[18px] h-[18px]' ); ?>
                </button>

                <!-- Cart -->
                <?php if ( function_exists( 'WC' ) ) : ?>
                    <button
                        type="button"
                        class="hidden md:flex text-cream hover:text-accent transition-colors relative header-cart bg-transparent border-0 cursor-pointer p-0"
                        @click="$dispatch('open-cart')"
                        aria-label="<?php esc_attr_e( 'View cart', 'fmw' ); ?>"
                    >
                        <?php fmw_icon( 'shopping-bag', 'w-[18px] h-[18px]' ); ?>
                        <?php if ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                            <span class="cart-count absolute -top-1.5 -right-1.5 bg-accent text-dark text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
                        <?php endif; ?>
                    </button>
                <?php endif; ?>

                <!-- Account -->
                <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                    <?php if ( is_user_logged_in() ) : ?>
                        <a
                            href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
                            class="hidden md:flex text-cream hover:text-accent transition-colors"
                            aria-label="<?php esc_attr_e( 'My account', 'fmw' ); ?>"
                        >
                            <?php fmw_icon( 'user', 'w-[18px] h-[18px]' ); ?>
                        </a>
                    <?php else : ?>
                        <button
                            type="button"
                            class="hidden md:flex text-cream hover:text-accent transition-colors"
                            @click="$dispatch('login-modal')"
                            aria-label="<?php esc_attr_e( 'Sign in', 'fmw' ); ?>"
                        >
                            <?php fmw_icon( 'user', 'w-[18px] h-[18px]' ); ?>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button
                    type="button"
                    class="lg:hidden text-cream hover:text-accent transition-colors"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen"
                    aria-controls="mobile-menu"
                    aria-label="<?php esc_attr_e( 'Toggle menu', 'fmw' ); ?>"
                >
                    <span x-show="!mobileMenuOpen"><?php fmw_icon( 'menu', 'w-6 h-6' ); ?></span>
                    <span x-show="mobileMenuOpen" x-cloak><?php fmw_icon( 'close', 'w-6 h-6' ); ?></span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div
            id="mobile-menu"
            class="lg:hidden bg-dark border-b border-cream/[0.125]"
            x-show="mobileMenuOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="px-6 py-6 flex flex-col gap-4">
                <a href="<?php echo esc_url( $shop_url ); ?>" class="text-cream font-mono text-sm uppercase tracking-wider-2 hover:text-accent">New Arrivals</a>
                <a href="<?php echo esc_url( $shop_url ); ?>?browse=genres" class="text-cream font-mono text-sm uppercase tracking-wider-2 hover:text-accent">Genres</a>
                <a href="<?php echo esc_url( $shop_url ); ?>?browse=labels" class="text-cream font-mono text-sm uppercase tracking-wider-2 hover:text-accent">Labels</a>
                <a href="<?php echo esc_url( $shop_url ); ?>?browse=artists" class="text-cream font-mono text-sm uppercase tracking-wider-2 hover:text-accent">Artists</a>
                <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>" class="text-cream font-mono text-sm uppercase tracking-wider-2 hover:text-accent">About</a>

                <div class="flex items-center gap-4 pt-4 border-t border-cream/[0.125]">
                    <button type="button" class="text-cream hover:text-accent" @click="$dispatch('search-modal'); mobileMenuOpen = false">
                        <?php fmw_icon( 'search', 'w-5 h-5' ); ?>
                    </button>
                    <?php if ( function_exists( 'WC' ) ) : ?>
                        <button type="button" class="text-cream hover:text-accent bg-transparent border-0 cursor-pointer p-0" @click="$dispatch('open-cart'); mobileMenuOpen = false">
                            <?php fmw_icon( 'shopping-bag', 'w-5 h-5' ); ?>
                        </button>
                    <?php endif; ?>
                    <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="text-cream hover:text-accent">
                                <?php fmw_icon( 'user', 'w-5 h-5' ); ?>
                            </a>
                        <?php else : ?>
                            <button type="button" class="text-cream hover:text-accent" @click="$dispatch('login-modal'); mobileMenuOpen = false">
                                <?php fmw_icon( 'user', 'w-5 h-5' ); ?>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main id="main" class="site-main">
