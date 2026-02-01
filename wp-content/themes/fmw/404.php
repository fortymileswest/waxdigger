<?php
/**
 * 404 Template
 *
 * Template for displaying 404 (not found) pages.
 *
 * @package FMW
 */

get_header();
?>

<div class="container mx-auto px-4 py-8">
    <article class="error-404">
        <header class="entry-header">
            <h1 class="entry-title"><?php esc_html_e( 'Page Not Found', 'fmw' ); ?></h1>
        </header>

        <div class="entry-content">
            <p><?php esc_html_e( 'The page you are looking for could not be found. Please check the URL or navigate back to the homepage.', 'fmw' ); ?></p>

            <p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <?php esc_html_e( 'Return to Homepage', 'fmw' ); ?>
                </a>
            </p>
        </div>
    </article>
</div>

<?php
get_footer();
