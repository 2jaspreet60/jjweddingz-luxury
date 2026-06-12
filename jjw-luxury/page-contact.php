<?php
/**
 * Template Name: Contact & Inquiries
 *
 * page-contact.php — Editorial Contact Page Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

// Elementor Canvas safeguard check
if ( function_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    get_footer();
    return;
}

$wa_number  = jjwz_get_option( 'jjwz_whatsapp_number', '919876543210' );
$wa_url     = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $wa_number );
$phone      = jjwz_get_option( 'jjwz_header_phone', '+91 98765 43210' );
$phone_url  = 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
?>

<main id="primary" class="site-main">

    <!-- ═══════════════════════════════
         HERO SECTION
         ═══════════════════════════════ -->
    <section class="contact-hero section section--sm bg-cream" aria-label="Contact header">
        <div class="container text-center">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow"><?php esc_html_e( 'Connect With Us', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold"><?php esc_html_e( 'Let’s Create Art Together', 'jjweddingz' ); ?></h1>
            <p class="lead text-center" style="margin-inline: auto; margin-top: var(--sp-md);">
                <?php esc_html_e( 'Whether you are planning an editorial wedding in Delhi, a destination pre-wedding, or capturing the first days of your newborn in Amritsar, we are here to preserve your legacy.', 'jjweddingz' ); ?>
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════
         CONTACT DETAILS & FORM
         ═══════════════════════════════ -->
    <section class="section" aria-label="Contact details and form">
        <div class="container contact-grid">
            
            <!-- Left: Info -->
            <div class="contact-info" data-anim="fade-right">
                <div class="contact-info__block">
                    <h2 class="section-title text-upper" style="font-size: var(--text-2xl); margin-bottom: var(--sp-lg);">
                        <?php esc_html_e( 'Our Studios', 'jjweddingz' ); ?>
                    </h2>
                    <p class="text-mist" style="margin-bottom: var(--sp-xl);">
                        <?php esc_html_e( 'We invite you to schedule a private consultation at either of our flagship boutique studios.', 'jjweddingz' ); ?>
                    </p>
                </div>

                <div class="grid-2">
                    <?php
                    $branches_raw = jjwz_get_option( 'jjw_branches', '[]' );
                    $branches = json_decode( $branches_raw, true ) ?: [];
                    if ( ! empty( $branches ) ) :
                        foreach ( $branches as $b ) :
                            $branch_name = ! empty( $b['name'] ) ? $b['name'] : '';
                            $branch_address = ! empty( $b['address'] ) ? $b['address'] : '';
                            $branch_phone = ! empty( $b['phone'] ) ? $b['phone'] : $phone;
                            $branch_email = ! empty( $b['email'] ) ? $b['email'] : '';
                            ?>
                            <div class="contact-branch-card">
                                <h3><?php echo esc_html( $branch_name ); ?> Studio</h3>
                                <p><strong><?php esc_html_e( 'Location:', 'jjweddingz' ); ?></strong> <?php echo esc_html( $branch_address ); ?></p>
                                <p><strong><?php esc_html_e( 'Hours:', 'jjweddingz' ); ?></strong> 10:00 AM – 8:00 PM (Mon–Sun)</p>
                                <p><strong><?php esc_html_e( 'Inquiries:', 'jjweddingz' ); ?></strong> <?php echo esc_html( $branch_phone ); ?></p>
                                <?php if ( $branch_email ) : ?>
                                    <p><strong><?php esc_html_e( 'Email:', 'jjweddingz' ); ?></strong> <a href="mailto:<?php echo esc_attr( $branch_email ); ?>"><?php echo esc_html( $branch_email ); ?></a></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <!-- Delhi Branch -->
                        <div class="contact-branch-card">
                            <h3><?php esc_html_e( 'Delhi NCR Studio', 'jjweddingz' ); ?></h3>
                            <p><strong><?php esc_html_e( 'Location:', 'jjweddingz' ); ?></strong> Block 4, DLF Phase 3, Gurugram, Delhi NCR</p>
                            <p><strong><?php esc_html_e( 'Hours:', 'jjweddingz' ); ?></strong> 10:00 AM – 8:00 PM (Mon–Sun)</p>
                            <p><strong><?php esc_html_e( 'Inquiries:', 'jjweddingz' ); ?></strong> <?php echo esc_html( $phone ); ?></p>
                        </div>

                        <!-- Amritsar Branch -->
                        <div class="contact-branch-card">
                            <h3><?php esc_html_e( 'Amritsar Studio', 'jjweddingz' ); ?></h3>
                            <p><strong><?php esc_html_e( 'Location:', 'jjweddingz' ); ?></strong> Studio 12, Ranjit Avenue, Amritsar, Punjab</p>
                            <p><strong><?php esc_html_e( 'Hours:', 'jjweddingz' ); ?></strong> 10:00 AM – 8:00 PM (Mon–Sun)</p>
                            <p><strong><?php esc_html_e( 'Inquiries:', 'jjweddingz' ); ?></strong> <?php echo esc_html( $phone ); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="contact-methods" style="margin-top: var(--sp-xl); border-top: 1px solid var(--clr-border); padding-top: var(--sp-xl);">
                    <h3 class="eyebrow" style="margin-bottom: var(--sp-sm);"><?php esc_html_e( 'Immediate Consultation', 'jjweddingz' ); ?></h3>
                    <p class="text-mist" style="margin-bottom: var(--sp-lg);">
                        <?php esc_html_e( 'Need immediate assistance or looking to book a date instantly? Tap below to connect with us directly via phone or WhatsApp.', 'jjweddingz' ); ?>
                    </p>
                    <div class="flex" style="gap: var(--sp-md); flex-wrap: wrap;">
                        <a href="<?php echo esc_url( $wa_url ); ?>" class="btn btn--primary" target="_blank" rel="noopener noreferrer">
                            💬 <?php esc_html_e( 'WhatsApp Booking', 'jjweddingz' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $phone_url ); ?>" class="btn btn--outline">
                            📞 <?php esc_html_e( 'Call Studio', 'jjweddingz' ); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="contact-form-wrap" data-anim="fade-left">
                <h2 class="section-title text-upper" style="font-size: var(--text-2xl); margin-bottom: var(--sp-lg);">
                    <?php esc_html_e( 'Share Your Story', 'jjweddingz' ); ?>
                </h2>
                
                <form id="jjwz-contact-form" class="flex" style="flex-direction: column; gap: var(--sp-lg);">
                    
                    <!-- Honeypot -->
                    <div style="display:none;">
                        <input type="text" name="jjwz_honey">
                    </div>

                    <!-- Name -->
                    <div class="form-group">
                        <label for="form-name" class="form-label"><?php esc_html_e( 'Your Full Name', 'jjweddingz' ); ?> <span class="text-gold">*</span></label>
                        <input type="text" id="form-name" name="name" class="form-control" placeholder="Jaspreet Singh" required autocomplete="name">
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="form-email" class="form-label"><?php esc_html_e( 'Email Address', 'jjweddingz' ); ?> <span class="text-gold">*</span></label>
                        <input type="email" id="form-email" name="email" class="form-control" placeholder="jaspreet@example.com" required autocomplete="email">
                    </div>

                    <div class="grid-2" style="gap: var(--sp-md);">
                        <!-- Phone -->
                        <div class="form-group">
                            <label for="form-phone" class="form-label"><?php esc_html_e( 'Phone Number', 'jjweddingz' ); ?></label>
                            <input type="tel" id="form-phone" name="phone" class="form-control" placeholder="+91 98765 43210" autocomplete="tel">
                        </div>

                        <!-- Date -->
                        <div class="form-group">
                            <label for="form-date" class="form-label"><?php esc_html_e( 'Event / Shoot Date', 'jjweddingz' ); ?></label>
                            <input type="text" id="form-date" name="event_date" class="form-control" placeholder="DD/MM/YYYY">
                        </div>
                    </div>

                    <!-- Service Dropdown -->
                    <div class="form-group">
                        <label for="form-service" class="form-label"><?php esc_html_e( 'What are we capturing?', 'jjweddingz' ); ?></label>
                        <select id="form-service" name="service" class="form-control" style="appearance: auto; background: #fff;">
                            <option value="Wedding Photography"><?php esc_html_e( 'Wedding Photography', 'jjweddingz' ); ?></option>
                            <option value="Pre-Wedding Shoot"><?php esc_html_e( 'Pre-Wedding Shoot', 'jjweddingz' ); ?></option>
                            <option value="Cinematography / Film"><?php esc_html_e( 'Cinematography / Film', 'jjweddingz' ); ?></option>
                            <option value="Maternity & Newborn"><?php esc_html_e( 'Maternity & Newborn', 'jjweddingz' ); ?></option>
                            <option value="Baby & Kids Shoot"><?php esc_html_e( 'Baby & Kids Shoot', 'jjweddingz' ); ?></option>
                            <option value="Other / Portraits"><?php esc_html_e( 'Other / Portraits', 'jjweddingz' ); ?></option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label for="form-message" class="form-label"><?php esc_html_e( 'Tell us about your plans', 'jjweddingz' ); ?></label>
                        <textarea id="form-message" name="message" class="form-control" placeholder="Share any wedding venue details, destination plans, or creative themes you have in mind..."></textarea>
                    </div>

                    <!-- Dynamic input source -->
                    <input type="hidden" name="source" value="<?php echo esc_url( get_permalink() ); ?>">

                    <button type="submit" class="btn btn--primary" style="align-self: flex-start; min-width: 180px;">
                        <?php esc_html_e( 'Send Inquiry', 'jjweddingz' ); ?>
                    </button>

                    <!-- Response Div -->
                    <div id="form-response" class="form-response-msg" hidden></div>
                </form>
            </div>

        </div>
    </section>

</main>

<?php
get_footer();
