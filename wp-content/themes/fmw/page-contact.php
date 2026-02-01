<?php
/**
 * Template Name: Contact Page
 *
 * @package FMW
 */

get_header();
?>

<div class="contact-page" x-data="{ activeTab: 'customers' }">
	<!-- Hero Section -->
	<section class="contact-hero">
		<div class="container mx-auto px-4">
			<h1 class="contact-hero-title">Get in Touch</h1>
			<p class="contact-hero-subtitle">Questions about an order, looking to sell your collection, or just want to chat vinyl? We're here to help.</p>
		</div>
	</section>

	<!-- Collection CTA -->
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
				<button
					type="button"
					class="collection-cta-btn"
					@click="activeTab = 'suppliers'; $nextTick(() => document.querySelector('.contact-tabs').scrollIntoView({ behavior: 'smooth', block: 'start' }))"
				>
					Get a Quote
				</button>
			</div>
		</div>
	</section>

	<!-- Tabs Section -->
	<section class="contact-main">
		<div class="container mx-auto px-4">

			<!-- Tab Navigation -->
			<div class="contact-tabs">
				<button
					type="button"
					class="contact-tab"
					:class="{ 'is-active': activeTab === 'customers' }"
					@click="activeTab = 'customers'"
				>
					Customers
				</button>
				<button
					type="button"
					class="contact-tab"
					:class="{ 'is-active': activeTab === 'suppliers' }"
					@click="activeTab = 'suppliers'"
				>
					Suppliers
				</button>
			</div>

			<!-- Customers Tab -->
			<div
				class="contact-tab-content"
				x-show="activeTab === 'customers'"
				x-transition:enter="transition ease-out duration-300"
				x-transition:enter-start="opacity-0"
				x-transition:enter-end="opacity-100"
			>
				<div class="contact-grid">
					<!-- Contact Form -->
					<div class="contact-form-column">
						<h2 class="contact-section-title">Send us a Message</h2>
						<form
							class="contact-form"
							x-data="contactForm()"
							@submit.prevent="submit"
						>
							<input type="hidden" name="form_type" value="customer">

							<div class="form-group">
								<label for="customer-name">Name</label>
								<input
									type="text"
									id="customer-name"
									name="name"
									x-model="formData.name"
									required
									autocomplete="off"
								>
							</div>

							<div class="form-group">
								<label for="customer-email">Email</label>
								<input
									type="email"
									id="customer-email"
									name="email"
									x-model="formData.email"
									required
									autocomplete="off"
								>
							</div>

							<div class="form-group">
								<label for="customer-subject">Subject</label>
								<select id="customer-subject" name="subject" x-model="formData.subject" required>
									<option value="">Select a topic</option>
									<option value="order">Order Enquiry</option>
									<option value="shipping">Shipping Question</option>
									<option value="returns">Returns & Refunds</option>
									<option value="product">Product Question</option>
									<option value="other">Other</option>
								</select>
							</div>

							<div class="form-group">
								<label for="customer-message">Message</label>
								<textarea
									id="customer-message"
									name="message"
									rows="5"
									x-model="formData.message"
									required
								></textarea>
							</div>

							<button
								type="submit"
								class="contact-submit"
								:disabled="loading"
								x-text="loading ? 'Sending...' : 'Send Message'"
							></button>

							<div
								class="form-message"
								x-show="message"
								x-text="message"
								:class="{ 'is-success': success, 'is-error': !success }"
								x-transition
							></div>
						</form>
					</div>

					<!-- Contact Info & FAQ -->
					<div class="contact-info-column">
						<!-- Contact Details -->
						<div class="contact-details">
							<h2 class="contact-section-title">Contact Details</h2>

							<div class="contact-detail-item">
								<h3>Email</h3>
								<p><a href="mailto:hello@waxdigger.com">hello@waxdigger.com</a></p>
							</div>

							<div class="contact-detail-item">
								<h3>Response Time</h3>
								<p>We typically respond within 24-48 hours</p>
							</div>

							<div class="contact-detail-item">
								<h3>Social</h3>
								<div class="contact-social">
									<a href="https://instagram.com/waxdigger" target="_blank" rel="noopener noreferrer">
										<?php fmw_icon( 'instagram', 'w-5 h-5' ); ?>
									</a>
									<a href="https://discogs.com/seller/waxdigger" target="_blank" rel="noopener noreferrer">
										<?php fmw_icon( 'discogs', 'w-5 h-5' ); ?>
									</a>
								</div>
							</div>
						</div>

						<!-- FAQ -->
						<div class="contact-faq" x-data="{ openFaq: null }">
							<h2 class="contact-section-title">FAQ</h2>

							<div class="faq-list">
								<div class="faq-item">
									<button
										type="button"
										class="faq-question"
										@click="openFaq = openFaq === 1 ? null : 1"
										:class="{ 'is-open': openFaq === 1 }"
									>
										<span>How long does shipping take?</span>
										<svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
										</svg>
									</button>
									<div
										class="faq-answer"
										x-show="openFaq === 1"
										x-cloak
										x-transition:enter="transition ease-out duration-200"
										x-transition:enter-start="opacity-0"
										x-transition:enter-end="opacity-100"
									>
										<p>UK orders are dispatched within 1-2 working days and typically arrive within 3-5 days. International shipping takes 5-14 days depending on destination.</p>
									</div>
								</div>

								<div class="faq-item">
									<button
										type="button"
										class="faq-question"
										@click="openFaq = openFaq === 2 ? null : 2"
										:class="{ 'is-open': openFaq === 2 }"
									>
										<span>What's your returns policy?</span>
										<svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
										</svg>
									</button>
									<div
										class="faq-answer"
										x-show="openFaq === 2"
										x-cloak
										x-transition:enter="transition ease-out duration-200"
										x-transition:enter-start="opacity-0"
										x-transition:enter-end="opacity-100"
									>
										<p>If your record arrives damaged or isn't as described, we'll happily refund or replace it. Just get in touch within 14 days of delivery.</p>
									</div>
								</div>

								<div class="faq-item">
									<button
										type="button"
										class="faq-question"
										@click="openFaq = openFaq === 3 ? null : 3"
										:class="{ 'is-open': openFaq === 3 }"
									>
										<span>How do you grade records?</span>
										<svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
										</svg>
									</button>
									<div
										class="faq-answer"
										x-show="openFaq === 3"
										x-cloak
										x-transition:enter="transition ease-out duration-200"
										x-transition:enter-start="opacity-0"
										x-transition:enter-end="opacity-100"
									>
										<p>We use the standard Goldmine grading system. Each listing shows the condition of both the vinyl and sleeve. We're always conservative with our grades.</p>
									</div>
								</div>

								<div class="faq-item">
									<button
										type="button"
										class="faq-question"
										@click="openFaq = openFaq === 4 ? null : 4"
										:class="{ 'is-open': openFaq === 4 }"
									>
										<span>Do you ship internationally?</span>
										<svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
										</svg>
									</button>
									<div
										class="faq-answer"
										x-show="openFaq === 4"
										x-cloak
										x-transition:enter="transition ease-out duration-200"
										x-transition:enter-start="opacity-0"
										x-transition:enter-end="opacity-100"
									>
										<p>Yes! We ship worldwide. Shipping costs are calculated at checkout based on your location and order weight.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Suppliers Tab -->
			<div
				class="contact-tab-content"
				x-show="activeTab === 'suppliers'"
				x-cloak
				x-transition:enter="transition ease-out duration-300"
				x-transition:enter-start="opacity-0"
				x-transition:enter-end="opacity-100"
			>
				<div class="contact-grid">
					<!-- Supplier Form -->
					<div class="contact-form-column">
						<h2 class="contact-section-title">Sell Your Records</h2>
						<p class="contact-form-intro">Got a collection you're looking to sell? We're always interested in quality vinyl. Tell us what you have and we'll get back to you.</p>

						<form
							class="contact-form"
							x-data="contactForm()"
							@submit.prevent="submit"
						>
							<input type="hidden" name="form_type" value="supplier">

							<div class="form-group">
								<label for="supplier-name">Name</label>
								<input
									type="text"
									id="supplier-name"
									name="name"
									x-model="formData.name"
									required
									autocomplete="off"
								>
							</div>

							<div class="form-group">
								<label for="supplier-email">Email</label>
								<input
									type="email"
									id="supplier-email"
									name="email"
									x-model="formData.email"
									required
									autocomplete="off"
								>
							</div>

							<div class="form-group">
								<label for="supplier-phone">Phone (optional)</label>
								<input
									type="tel"
									id="supplier-phone"
									name="phone"
									x-model="formData.phone"
									autocomplete="off"
								>
							</div>

							<div class="form-group">
								<label for="supplier-collection">Tell us about your collection</label>
								<textarea
									id="supplier-collection"
									name="message"
									rows="6"
									x-model="formData.message"
									required
									placeholder="Genres, approx. number of records, condition, any notable items..."
								></textarea>
							</div>

							<div class="form-group">
								<label for="supplier-location">Location</label>
								<input
									type="text"
									id="supplier-location"
									name="location"
									x-model="formData.location"
									placeholder="City, Country"
									autocomplete="off"
								>
							</div>

							<button
								type="submit"
								class="contact-submit"
								:disabled="loading"
								x-text="loading ? 'Sending...' : 'Submit'"
							></button>

							<div
								class="form-message"
								x-show="message"
								x-text="message"
								:class="{ 'is-success': success, 'is-error': !success }"
								x-transition
							></div>
						</form>
					</div>

					<!-- Supplier Info -->
					<div class="contact-info-column">
						<div class="contact-details">
							<h2 class="contact-section-title">What We're Looking For</h2>

							<div class="supplier-criteria">
								<div class="criteria-item">
									<h3>Genres</h3>
									<p>House, Techno, Drum & Bass, Jungle, Rave, Breaks, Electro, Soul, Funk, Hip Hop and related electronic music.</p>
								</div>

								<div class="criteria-item">
									<h3>Condition</h3>
									<p>We prefer records in VG+ condition or better. We'll consider lower grades for rare items.</p>
								</div>

								<div class="criteria-item">
									<h3>Collection Size</h3>
									<p>Happy to look at anything from a handful of records to entire collections. No minimum.</p>
								</div>

								<div class="criteria-item">
									<h3>Process</h3>
									<p>Send us details and we'll arrange to view the collection, either in person or via photos. We offer fair prices and can collect for larger lots.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
</div>

<script>
document.addEventListener('alpine:init', () => {
	Alpine.data('contactForm', () => ({
		formData: {
			name: '',
			email: '',
			subject: '',
			message: '',
			phone: '',
			location: ''
		},
		loading: false,
		message: '',
		success: false,

		async submit() {
			this.loading = true;
			this.message = '';

			const form = this.$el;
			const formType = form.querySelector('[name="form_type"]').value;

			try {
				const response = await fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: new URLSearchParams({
						action: 'fmw_contact_form',
						nonce: '<?php echo wp_create_nonce( 'fmw_contact_nonce' ); ?>',
						form_type: formType,
						...this.formData
					})
				});

				const data = await response.json();

				if (data.success) {
					this.success = true;
					this.message = data.data.message || 'Thanks! We\'ll be in touch soon.';
					this.formData = { name: '', email: '', subject: '', message: '', phone: '', location: '' };
				} else {
					this.success = false;
					this.message = data.data?.message || 'Something went wrong. Please try again.';
				}
			} catch (error) {
				this.success = false;
				this.message = 'Something went wrong. Please try again.';
			}

			this.loading = false;
		}
	}));
});
</script>

<?php
get_footer();
