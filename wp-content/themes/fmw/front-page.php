<?php
/**
 * Front Page Template
 *
 * Homepage with all sections in fixed order.
 *
 * @package FMW
 */

get_header();

// Homepage sections in design order
fmw_partial( 'hero' );
fmw_partial( 'ticker-strip' );
fmw_partial( 'new-arrivals' );
fmw_partial( 'genre-section' );
fmw_partial( 'staff-picks' );
fmw_partial( 'about-section' );
fmw_partial( 'we-buy-records' );
fmw_partial( 'newsletter' );

get_footer();
