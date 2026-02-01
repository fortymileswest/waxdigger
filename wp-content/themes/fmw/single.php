<?php
/**
 * Single Post Template
 *
 * Template for displaying single posts.
 *
 * @package FMW
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container mx-auto px-4 py-8">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

                    <div class="entry-meta">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date() ); ?>
                        </time>
                    </div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <footer class="entry-footer">
                    <?php
                    $categories = get_the_category();
                    if ( $categories ) :
                        ?>
                        <div class="entry-categories">
                            <?php
                            foreach ( $categories as $category ) {
                                printf(
                                    '<a href="%s">%s</a>',
                                    esc_url( get_category_link( $category->term_id ) ),
                                    esc_html( $category->name )
                                );
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </footer>
            </article>

            <?php
            // Post navigation
            the_post_navigation(
                array(
                    'prev_text' => __( 'Previous: %title', 'fmw' ),
                    'next_text' => __( 'Next: %title', 'fmw' ),
                )
            );
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
