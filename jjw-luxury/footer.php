<?php
/**
 * footer.php — JJ WeddingZ Photography
 * Global site footer with dynamic layouts and WordPress standards.
 *
 * @package JJWeddingZ
 * @version 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Fetch Contact Options
$phone_number  = jjwz_get_option( 'jjw_primary_phone', '+91 98765 43210' );
$email_address = jjwz_get_option( 'jjw_email', 'info@jjweddingz.com' );
$copyright_text = jjwz_get_option( 'jjwz_copyright_text', '© ' . gmdate( 'Y' ) . ' JJ WeddingZ Photography. All Rights Reserved.' );

// 2. Fetch WhatsApp Configurations
$wa_number = jjwz_get_option( 'jjwz_whatsapp_number', '' );
if ( empty( $wa_number ) ) {
	$wa_number = jjwz_get_option( 'jjw_primary_whatsapp', '919876543210' );
}
$wa_mode = jjwz_get_option( 'jjwz_whatsapp_mode', 'simple' );
$wa_link = ( 'simple' === $wa_mode ) ? 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $wa_number ) : '#';

// 3. Fetch Social Media Channels
$socials_raw = jjwz_get_option( 'jjw_social_media', '[]' );
$socials     = json_decode( $socials_raw, true );
if ( ! is_array( $socials ) ) {
	$socials = [];
}

// 4. Fetch Branch Offices
$branches_raw = jjwz_get_option( 'jjw_branches', '[]' );
$branches     = json_decode( $branches_raw, true );
if ( ! is_array( $branches ) ) {
	$branches = [];
}
?>

</main><!-- /#jjwz-main-content -->

<!-- ═══════════════════════════════════════════
     PRE-FOOTER CTA BAND
     ═══════════════════════════════════════════ -->
<section class="prefooter-cta" aria-label="<?php esc_attr_e( 'Booking Call to Action', 'jjw-luxury' ); ?>">
	<div class="container prefooter-cta__inner">
		<div class="prefooter-cta__text">
			<span class="eyebrow"><?php esc_html_e( 'Begin Your Journey', 'jjw-luxury' ); ?></span>
			<h2 class="prefooter-cta__heading"><?php echo wp_kses_post( __( 'Every Love Story Deserves<br><em>an Unforgettable Frame.</em>', 'jjw-luxury' ) ); ?></h2>
		</div>
		<div class="prefooter-cta__actions">
			<a href="<?php echo esc_url( $wa_link ); ?>" class="btn btn--primary" id="footer-wa-cta" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Inquire About Your Date', 'jjw-luxury' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>" class="btn btn--outline-white" id="footer-portfolio-cta">
				<?php esc_html_e( 'View Our Portfolio', 'jjw-luxury' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════
     MAIN FOOTER
     ═══════════════════════════════════════════ -->
<footer class="jjwz-footer" role="contentinfo" aria-label="<?php esc_attr_e( 'Site Footer', 'jjw-luxury' ); ?>">
	<div class="container">
		<div class="footer__grid">
			
			<!-- Column 1: Brand Info -->
			<div class="footer__col">
				<!-- Dynamic Logo / Text Name -->
				<?php 
				$logo_url = jjwz_get_option( 'jjw_logo', '' );
				if ( ! empty( $logo_url ) ) : ?>
					<div class="footer__logo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<img src="<?php echo esc_url( $logo_url ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
						</a>
					</div>
				<?php elseif ( has_custom_logo() ) : ?>
					<div class="footer__logo">
						<?php the_custom_logo(); ?>
					</div>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer__logo-link">
						<span class="footer__logo-title"><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></span>
						<span class="footer__logo-sub"><?php esc_html_e( 'Photography', 'jjw-luxury' ); ?></span>
					</a>
				<?php endif; ?>

				<p class="footer__brand-desc">
					<?php esc_html_e( 'Capturing your most profound milestones with international elegance across Delhi NCR and Amritsar. 11+ years of professional excellence.', 'jjw-luxury' ); ?>
				</p>

				<!-- Social Media Networks -->
				<?php if ( ! empty( $socials ) ) : ?>
					<?php 
					// Sort socials by sort_order
					usort( $socials, function( $a, $b ) {
						return (int) ( $a['sort_order'] ?? 0 ) <=> (int) ( $b['sort_order'] ?? 0 );
					} );
					?>
					<div class="footer__socials" aria-label="<?php esc_attr_e( 'Social Media Profiles', 'jjw-luxury' ); ?>">
						<?php 
						foreach ( $socials as $s ) :
							if ( isset( $s['enabled'] ) && '0' === (string) $s['enabled'] ) {
								continue;
							}
							
							$name       = ! empty( $s['name'] ) ? $s['name'] : 'Link';
							$url        = ! empty( $s['url'] ) ? $s['url'] : '#';
							$icon_url   = ! empty( $s['icon_url'] ) ? $s['icon_url'] : '';
							$lower_name = strtolower( $name );
							?>
							<a href="<?php echo esc_url( $url ); ?>" class="footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $name ); ?>">
								<?php if ( ! empty( $icon_url ) ) : ?>
									<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="width:18px; height:18px; object-fit:contain; display:block;">
								<?php else : ?>
									<?php
									switch ( $lower_name ) {
										case 'instagram':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
											<?php
											break;
										case 'facebook':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
											<?php
											break;
										case 'youtube':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17z"/><path d="m10 15 5-3-5-3z"/></svg>
											<?php
											break;
										case 'pinterest':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.244 0C5.648 0 0 5.648 0 12.244c0 5.061 3.14 9.38 7.572 11.08-.101-.949-.192-2.399.04-3.432.21-.937 1.352-5.727 1.352-5.727s-.345-.69-.345-1.713c0-1.605.93-2.802 2.087-2.802 1.03 0 1.528.773 1.528 1.7 0 1.036-.66 2.585-.999 4.02-.284 1.205.603 2.188 1.792 2.188 2.152 0 3.807-2.27 3.807-5.547 0-2.9-2.083-4.93-5.064-4.93-3.45 0-5.474 2.588-5.474 5.26 0 1.042.4 2.16.9 2.76.098.12.112.227.083.345l-.34 1.393c-.055.227-.183.275-.423.165-1.579-.733-2.566-3.037-2.566-4.887 0-3.98 2.893-7.636 8.339-7.636 4.378 0 7.78 3.12 7.78 7.29 0 4.35-2.742 7.85-6.548 7.85-1.278 0-2.482-.664-2.894-1.45 0 0-.633 2.414-.787 3.01-.285 1.1-.85 2.478-1.264 3.142C10.158 23.834 11.182 24 12.244 24c6.596 0 12.244-5.648 12.244-12.244C24.488 5.648 18.84 0 12.244 0z"/></svg>
											<?php
											break;
										case 'x':
										case 'twitter':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
											<?php
											break;
										case 'linkedin':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
											<?php
											break;
										case 'threads':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.03 0C5.38 0 0 5.38 0 12.03S5.38 24.06 12.03 24.06c2.4 0 4.6-.66 6.5-1.8l-1.46-1.46c-1.4.8-3.05 1.26-5.04 1.26-5.54 0-10.03-4.49-10.03-10.03S6.49 2 12.03 2s10.03 4.49 10.03 10.03c0 2.23-.42 3.93-1.23 5.06-.72 1-1.8 1.48-3.21 1.48-1.57 0-2.47-.9-2.47-2.46v-7.14c0-.9-.72-1.63-1.63-1.63s-1.63.72-1.63 1.63v.37c-.67-.58-1.56-.93-2.53-.93-2.45 0-4.44 2-4.44 4.45s2 4.45 4.44 4.45c1.07 0 2.05-.39 2.8-1.03.4.77 1.23 1.34 2.37 1.34 2.26 0 4.09-1.04 5.25-2.73 1.18-1.72 1.78-4.22 1.78-7.39C24.06 5.38 18.68 0 12.03 0zm-2.02 14.5c-1.35 0-2.44-1.1-2.44-2.45s1.1-2.45 2.44-2.45 2.44 1.1 2.44 2.45-1.1 2.45-2.44 2.45z"/></svg>
											<?php
											break;
										case 'flickr':
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="12" r="5" fill="#0063db"/><circle cx="18" cy="12" r="5" fill="#ff0084"/></svg>
											<?php
											break;
										default:
											?>
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
											<?php
											break;
									}
									?>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Column 2: Dynamic Services List -->
			<div class="footer__col">
				<h3><?php esc_html_e( 'Services', 'jjw-luxury' ); ?></h3>
				<ul class="footer__links">
					<?php 
					$services_args = [
						'post_type'      => 'jjwz_service',
						'posts_per_page' => 5,
						'post_status'    => 'publish',
						'orderby'        => 'meta_value_num',
						'meta_key'       => 'svc_display_order',
						'order'          => 'ASC',
					];
					$services_query = new WP_Query( $services_args );
					
					if ( $services_query->have_posts() ) :
						while ( $services_query->have_posts() ) :
							$services_query->the_post();
							?>
							<li>
								<a href="<?php the_permalink(); ?>" class="footer__link"><?php the_title(); ?></a>
							</li>
							<?php
						endwhile;
						wp_reset_postdata();
					else :
						// Core fallbacks if CPT lacks entries
						$default_links = [
							'wedding-photography' => 'Wedding Photography',
							'pre-wedding'         => 'Pre-Wedding Shoots',
							'cinematography'      => 'Cinematography &amp; Films',
							'maternity-newborn'   => 'Maternity &amp; Newborn',
							'baby-shoot'          => 'Baby Shoots',
						];
						foreach ( $default_links as $slug => $label ) :
							?>
							<li>
								<a href="<?php echo esc_url( home_url( '/services/' . $slug ) ); ?>" class="footer__link"><?php echo wp_kses_post( $label ); ?></a>
							</li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			</div>

			<!-- Column 3: Studio Branches & Company Info -->
			<div class="footer__col">
				<h3><?php esc_html_e( 'Our Studios', 'jjw-luxury' ); ?></h3>
				<ul class="footer__links">
					<?php if ( ! empty( $branches ) ) : ?>
						<?php foreach ( $branches as $b ) : 
							$branch_name = ! empty( $b['name'] ) ? $b['name'] : '';
							$maps_url    = ! empty( $b['maps_url'] ) ? $b['maps_url'] : '';
							$label       = $branch_name . ' ' . __( 'Studio', 'jjw-luxury' );
							?>
							<li>
								<?php if ( ! empty( $maps_url ) ) : ?>
									<a href="<?php echo esc_url( $maps_url ); ?>" class="footer__link" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $label ); ?></a>
								<?php else : ?>
									<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="footer__link"><?php echo esc_html( $label ); ?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					<?php else : ?>
						<li><a href="<?php echo esc_url( home_url( '/delhi-ncr' ) ); ?>" class="footer__link"><?php esc_html_e( 'Delhi NCR Studio', 'jjw-luxury' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/amritsar' ) ); ?>" class="footer__link"><?php esc_html_e( 'Amritsar Studio', 'jjw-luxury' ); ?></a></li>
					<?php endif; ?>
					
					<li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="footer__link"><?php esc_html_e( 'About Us', 'jjw-luxury' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>" class="footer__link"><?php esc_html_e( 'Portfolio Gallery', 'jjw-luxury' ); ?></a></li>
				</ul>
			</div>

			<!-- Column 4: Contact details -->
			<div class="footer__col">
				<h3><?php esc_html_e( 'Get In Touch', 'jjw-luxury' ); ?></h3>
				<ul class="footer__links">
					<li>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone_number ) ); ?>" class="footer__link" id="footer-phone-link">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.07 9.8 19.79 19.79 0 0 1 .1 1.14 2 2 0 0 1 2.11 0h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L6.09 7.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 14.92z"/></svg>
							<?php echo esc_html( $phone_number ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $wa_link ); ?>" class="footer__link" id="footer-wa-link" target="_blank" rel="noopener noreferrer">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12.003 2a9.987 9.987 0 00-8.59 15.04L2 22l5.115-1.34A9.993 9.993 0 0012.003 22c5.514 0 10-4.486 10-10s-4.486-10-9.997-10zm0 18.212a8.184 8.184 0 01-4.17-1.146l-.3-.178-3.09.81.824-3.01-.195-.31A8.198 8.198 0 013.802 12c0-4.517 3.676-8.195 8.2-8.195 4.517 0 8.196 3.678 8.196 8.195 0 4.52-3.68 8.212-8.196 8.212z"/></svg>
							<?php esc_html_e( 'WhatsApp Inquiries', 'jjw-luxury' ); ?>
						</a>
					</li>
					<li>
						<a href="mailto:<?php echo esc_attr( $email_address ); ?>" class="footer__link">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
							<?php echo esc_html( $email_address ); ?>
						</a>
					</li>
					<li class="footer__note">
						<?php esc_html_e( 'Operating in Delhi NCR, Punjab, and destinations worldwide.', 'jjw-luxury' ); ?>
					</li>
				</ul>
			</div>

		</div>

		<!-- Footer Bottom Bar -->
		<div class="footer__bottom">
			<?php
			$enable_sig_footer = jjwz_get_option( 'jjwz_about_founder_enable_signature_footer', '1' );
			if ( '1' === $enable_sig_footer ) :
				$sig_img = jjwz_get_option( 'jjwz_about_founder_signature' );
				$founder_name = jjwz_get_option( 'jjwz_about_founder_name', 'Jaspreet Singh' );
				$designation = jjwz_get_option( 'jjwz_about_founder_designation', 'Founder & Lead Photographer' );
			?>
			<div class="footer__signature-block" style="text-align: center; margin-bottom: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; width: 100%;">
				<?php if ( ! empty( $sig_img ) ) : ?>
					<img src="<?php echo esc_url( $sig_img ); ?>" alt="Founder Signature" style="max-height: 60px; width: auto; filter: brightness(0) invert(1); display: block; margin: 0 auto;">
				<?php else : ?>
					<span style="font-family: var(--font-display); font-style: italic; color: var(--clr-gold); font-size: 1.6rem; letter-spacing: 0.05em;"><?php echo esc_html( $founder_name ); ?></span>
				<?php endif; ?>
				<span style="font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.08em; color: var(--clr-fog);"><?php echo esc_html( $designation ); ?></span>
			</div>
			<?php endif; ?>

			<p class="footer__copyright"><?php echo wp_kses_post( $copyright_text ); ?></p>
			<p class="footer__credit">
				<?php echo wp_kses_post( __( 'Led by <strong>Jaspreet Singh</strong> — 11 Years of Luxury Photography Excellence', 'jjw-luxury' ) ); ?>
			</p>
		</div>
	</div>
</footer>

<!-- Floating WhatsApp CTA Pill -->
<a href="<?php echo esc_url( $wa_link ); ?>" class="jjwz-wa-float" id="wa-float-btn" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'jjw-luxury' ); ?>">
	<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
		<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
		<path d="M12.003 2a9.987 9.987 0 00-8.59 15.04L2 22l5.115-1.34A9.993 9.993 0 0012.003 22c5.514 0 10-4.486 10-10s-4.486-10-9.997-10zm0 18.212a8.184 8.184 0 01-4.17-1.146l-.3-.178-3.09.81.824-3.01-.195-.31A8.198 8.198 0 013.802 12c0-4.517 3.676-8.195 8.2-8.195 4.517 0 8.196 3.678 8.196 8.195 0 4.52-3.68 8.212-8.196 8.212z"/>
	</svg>
	<span class="wa-float__label"><?php esc_html_e( 'Chat Now', 'jjw-luxury' ); ?></span>
</a>

<style>
/* Scoped styles for the floating WhatsApp pill CTA */
.jjwz-wa-float {
	position: fixed;
	bottom: 30px;
	right: 30px;
	background-color: #25d366;
	color: #ffffff !important;
	border-radius: 50px;
	padding: 10px 20px;
	display: flex;
	align-items: center;
	gap: 8px;
	box-shadow: 0 8px 24px rgba(37,211,102,0.3);
	z-index: 99999;
	transition: all 300ms cubic-bezier(0.16, 1, 0.3, 1);
	text-decoration: none;
	font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
	font-size: 14px;
	font-weight: 600;
}
.jjwz-wa-float:hover {
	background-color: #20ba5a;
	transform: translateY(-3px);
	box-shadow: 0 12px 30px rgba(37,211,102,0.45);
	color: #ffffff !important;
}
.wa-float__label {
	display: inline-block;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	font-size: 11px;
}
@media (max-width: 480px) {
	.wa-float__label {
		display: none;
	}
	.jjwz-wa-float {
		padding: 12px;
		bottom: 20px;
		right: 20px;
		border-radius: 50%;
		box-shadow: 0 6px 20px rgba(37,211,102,0.35);
	}
}
</style>

<?php wp_footer(); ?>
</body>
</html>
