<?php
/**
 * Template Name: About Page
 *
 * @package FMW
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Get all fields
$hero_headline = get_field( 'hero_headline' );
$hero_subline  = get_field( 'hero_subline' );
$hero_image    = get_field( 'hero_image' );

$intro_lead          = get_field( 'intro_lead' );
$intro_body          = get_field( 'intro_body' );
$intro_stat_1_number = get_field( 'intro_stat_1_number' );
$intro_stat_1_label  = get_field( 'intro_stat_1_label' );
$intro_stat_2_number = get_field( 'intro_stat_2_number' );
$intro_stat_2_label  = get_field( 'intro_stat_2_label' );

$history_title      = get_field( 'history_title' );
$history_milestones = get_field( 'history_milestones' );

$values_title = get_field( 'values_title' );
$values_intro = get_field( 'values_intro' );
$values_items = get_field( 'values_items' );

$faq_title = get_field( 'faq_title' );
$faq_items = get_field( 'faq_items' );
?>

<article class="about-page">

	<?php // Hero Section ?>
	<section class="about-hero">
		<div class="about-hero-content">
			<?php if ( $hero_headline ) : ?>
				<h1 class="about-hero-headline"><?php echo esc_html( $hero_headline ); ?></h1>
			<?php endif; ?>
			<?php if ( $hero_subline ) : ?>
				<p class="about-hero-subline"><?php echo esc_html( $hero_subline ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $hero_image ) : ?>
			<div class="about-hero-image">
				<?php echo wp_get_attachment_image( $hero_image, 'large', false, array( 'class' => 'about-hero-img' ) ); ?>
			</div>
		<?php endif; ?>
	</section>

	<?php // Intro Section ?>
	<?php if ( $intro_lead || $intro_body ) : ?>
	<section class="about-intro">
		<div class="container mx-auto px-4">
			<div class="about-intro-grid">
				<div class="about-intro-lead-wrap">
					<?php if ( $intro_lead ) : ?>
						<p class="about-intro-lead"><?php echo esc_html( $intro_lead ); ?></p>
					<?php endif; ?>
					<?php if ( $intro_stat_1_number || $intro_stat_2_number ) : ?>
						<div class="about-intro-stats">
							<?php if ( $intro_stat_1_number ) : ?>
								<div class="about-stat">
									<span class="about-stat-number"><?php echo esc_html( $intro_stat_1_number ); ?></span>
									<?php if ( $intro_stat_1_label ) : ?>
										<span class="about-stat-label"><?php echo esc_html( $intro_stat_1_label ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( $intro_stat_2_number ) : ?>
								<div class="about-stat">
									<span class="about-stat-number"><?php echo esc_html( $intro_stat_2_number ); ?></span>
									<?php if ( $intro_stat_2_label ) : ?>
										<span class="about-stat-label"><?php echo esc_html( $intro_stat_2_label ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $intro_body ) : ?>
					<div class="about-intro-body">
						<?php echo wp_kses_post( $intro_body ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // History Section ?>
	<?php if ( $history_milestones && is_array( $history_milestones ) ) : ?>
	<section class="about-history">
		<div class="container mx-auto px-4">
			<?php if ( $history_title ) : ?>
				<h2 class="about-section-title"><?php echo esc_html( $history_title ); ?></h2>
			<?php endif; ?>
			<div class="about-timeline">
				<?php foreach ( $history_milestones as $index => $milestone ) : ?>
					<div class="about-milestone <?php echo $index % 2 === 0 ? 'is-left' : 'is-right'; ?>">
						<div class="about-milestone-year"><?php echo esc_html( $milestone['year'] ); ?></div>
						<div class="about-milestone-content">
							<h3 class="about-milestone-title"><?php echo esc_html( $milestone['title'] ); ?></h3>
							<?php if ( ! empty( $milestone['description'] ) ) : ?>
								<p class="about-milestone-desc"><?php echo esc_html( $milestone['description'] ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $milestone['image'] ) ) : ?>
							<div class="about-milestone-image">
								<?php echo wp_get_attachment_image( $milestone['image'], 'medium', false, array( 'class' => 'about-milestone-img' ) ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // Values Section ?>
	<?php if ( $values_items && is_array( $values_items ) ) : ?>
	<section class="about-values">
		<div class="container mx-auto px-4">
			<div class="about-values-header">
				<?php if ( $values_title ) : ?>
					<h2 class="about-section-title"><?php echo esc_html( $values_title ); ?></h2>
				<?php endif; ?>
				<?php if ( $values_intro ) : ?>
					<p class="about-values-intro"><?php echo esc_html( $values_intro ); ?></p>
				<?php endif; ?>
			</div>
			<div class="about-values-grid">
				<?php foreach ( $values_items as $value ) : ?>
					<div class="about-value">
						<?php if ( ! empty( $value['number'] ) ) : ?>
							<span class="about-value-number"><?php echo esc_html( $value['number'] ); ?></span>
						<?php endif; ?>
						<h3 class="about-value-title"><?php echo esc_html( $value['title'] ); ?></h3>
						<?php if ( ! empty( $value['description'] ) ) : ?>
							<p class="about-value-desc"><?php echo esc_html( $value['description'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // FAQ Section ?>
	<?php if ( $faq_items && is_array( $faq_items ) ) : ?>
	<section class="about-faq">
		<div class="container mx-auto px-4">
			<?php if ( $faq_title ) : ?>
				<h2 class="about-section-title"><?php echo esc_html( $faq_title ); ?></h2>
			<?php endif; ?>
			<div class="about-faq-list" x-data="{ active: null }">
				<?php foreach ( $faq_items as $index => $faq ) : ?>
					<div class="about-faq-item">
						<button
							type="button"
							class="about-faq-trigger"
							@click="active = active === <?php echo $index; ?> ? null : <?php echo $index; ?>"
							:class="{ 'is-open': active === <?php echo $index; ?> }"
						>
							<span class="about-faq-index"><?php echo str_pad( $index + 1, 2, '0', STR_PAD_LEFT ); ?></span>
							<span class="about-faq-question"><?php echo esc_html( $faq['question'] ); ?></span>
							<span class="about-faq-icon" :class="{ 'rotate-45': active === <?php echo $index; ?> }">+</span>
						</button>
						<div
							class="about-faq-answer"
							x-show="active === <?php echo $index; ?>"
							x-cloak
							x-collapse
						>
							<div class="about-faq-answer-inner">
								<?php echo wp_kses_post( $faq['answer'] ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // Collection CTA ?>
	<section class="collection-cta">
		<div class="container mx-auto px-4">
			<div class="collection-cta-inner">
				<div class="collection-cta-icons">
					<span class="collection-cta-icon collection-cta-icon-van">
						<?php fmw_icon( 'van', 'w-10 h-10' ); ?>
					</span>
					<span class="collection-cta-icon collection-cta-icon-cash">
						<?php fmw_icon( 'cash', 'w-8 h-8' ); ?>
					</span>
				</div>
				<div class="collection-cta-content">
					<h2 class="collection-cta-title">We Buy Record Collections</h2>
					<p class="collection-cta-text">Got vinyl to sell? We pay top prices for quality collections and can <strong>collect anywhere in the UK</strong>. Cash paid on the spot.</p>
				</div>
				<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="collection-cta-btn">
					Get a Quote
				</a>
			</div>
		</div>
	</section>

</article>

<?php
get_footer();
