<?php
/**
 * Main Template
 *
 * Fallback template for all content types.
 *
 * @package FMW
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container mx-auto px-4 py-8">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>

            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No content found.', 'fmw' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
