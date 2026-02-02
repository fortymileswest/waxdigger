<?php
/**
 * Single Policy Template
 *
 * @package FMW
 */

get_header();

while ( have_posts() ) :
	the_post();

	$read_time     = fmw_get_read_time();
	$last_modified = get_the_modified_date( 'j F Y' );
	?>

	<article class="policy-page">
		<header class="policy-hero">
			<div class="policy-hero-inner">
				<h1 class="policy-title"><?php the_title(); ?></h1>
				<div class="policy-meta">
					<span class="policy-meta-item">
						Last updated: <?php echo esc_html( $last_modified ); ?>
					</span>
					<span class="policy-meta-divider">·</span>
					<span class="policy-meta-item">
						<?php echo esc_html( $read_time ); ?> min read
					</span>
				</div>
			</div>
		</header>

		<div class="policy-content-wrapper">
			<div class="policy-content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>

<?php
endwhile;

get_footer();
