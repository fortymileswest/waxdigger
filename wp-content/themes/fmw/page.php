<?php
/**
 * Page Template
 *
 * Template for displaying single pages.
 *
 * @package FMW
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    // Check for flexible content sections first
    if ( function_exists( 'have_rows' ) && have_rows( 'sections' ) ) :
        while ( have_rows( 'sections' ) ) : the_row();
            $layout = get_row_layout();
            $partial = FMW_DIR . '/partials/' . $layout . '.php';

            if ( file_exists( $partial ) ) {
                include $partial;
            }
        endwhile;
    else :
        // Default page content
        ?>
        <div class="container mx-auto px-4 py-8">
            <?php
            while ( have_posts() ) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </div>
        <?php
    endif;
    ?>
</main>

<?php
get_footer();
