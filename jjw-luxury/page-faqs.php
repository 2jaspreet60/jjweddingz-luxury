<?php
/**
 * Template Name: FAQs & Guidance
 *
 * page-faqs.php — Tabbed FAQ Page Template
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
?>

<main id="primary" class="site-main">

    <!-- ═══════════════════════════════
         HERO SECTION
         ═══════════════════════════════ -->
    <section class="section section--sm bg-cream" aria-label="FAQ page header">
        <div class="container text-center">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow"><?php esc_html_e( 'Help Center', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold"><?php esc_html_e( 'Frequently Asked Questions', 'jjweddingz' ); ?></h1>
            <p class="lead text-center" style="margin-inline: auto; margin-top: var(--sp-md);">
                <?php esc_html_e( 'Find answers to common questions about booking timelines, locations, baby studio sanitization, props, post-production, and backups.', 'jjweddingz' ); ?>
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════
         TABS & ACCORDIONS
         ═══════════════════════════════ -->
    <section class="section" aria-label="FAQs accordions by category">
        <div class="container">
            
            <div class="faq-tabs-wrap">
                <!-- Tab Controls -->
                <div class="faq-tabs" role="tablist" aria-label="FAQ Categories">
                    <button class="faq-tab-btn is-active" data-tab="all" role="tab" aria-selected="true" aria-controls="faq-panel-all">
                        <?php esc_html_e( 'All Questions', 'jjweddingz' ); ?>
                    </button>
                    <button class="faq-tab-btn" data-tab="wedding" role="tab" aria-selected="false" aria-controls="faq-panel-wedding">
                        <?php esc_html_e( 'Weddings', 'jjweddingz' ); ?>
                    </button>
                    <button class="faq-tab-btn" data-tab="maternity" role="tab" aria-selected="false" aria-controls="faq-panel-maternity">
                        <?php esc_html_e( 'Maternity', 'jjweddingz' ); ?>
                    </button>
                    <button class="faq-tab-btn" data-tab="newborn" role="tab" aria-selected="false" aria-controls="faq-panel-newborn">
                        <?php esc_html_e( 'Newborn', 'jjweddingz' ); ?>
                    </button>
                    <button class="faq-tab-btn" data-tab="baby-shoot" role="tab" aria-selected="false" aria-controls="faq-panel-baby-shoot">
                        <?php esc_html_e( 'Baby Shoot', 'jjweddingz' ); ?>
                    </button>
                </div>

                <!-- Tab Panels -->
                <!-- Panel 1: All -->
                <div id="faq-panel-all" class="faq-panel is-active" role="tabpanel" aria-label="All FAQs">
                    <?php echo jjwz_render_faq_accordion( '', -1 ); ?>
                </div>

                <!-- Panel 2: Wedding -->
                <div id="faq-panel-wedding" class="faq-panel" role="tabpanel" aria-label="Wedding FAQs" hidden>
                    <?php echo jjwz_render_faq_accordion( 'wedding', -1 ); ?>
                </div>

                <!-- Panel 3: Maternity -->
                <div id="faq-panel-maternity" class="faq-panel" role="tabpanel" aria-label="Maternity FAQs" hidden>
                    <?php echo jjwz_render_faq_accordion( 'maternity', -1 ); ?>
                </div>

                <!-- Panel 4: Newborn -->
                <div id="faq-panel-newborn" class="faq-panel" role="tabpanel" aria-label="Newborn FAQs" hidden>
                    <?php echo jjwz_render_faq_accordion( 'newborn', -1 ); ?>
                </div>

                <!-- Panel 5: Baby Shoot -->
                <div id="faq-panel-baby-shoot" class="faq-panel" role="tabpanel" aria-label="Baby Shoot FAQs" hidden>
                    <?php echo jjwz_render_faq_accordion( 'baby-shoot', -1 ); ?>
                </div>
            </div>

            <!-- Footer CTA Section -->
            <div class="text-center" style="margin-top: var(--sp-4xl);" data-anim="fade-up">
                <h3 class="section-title" style="margin-bottom: var(--sp-md);"><?php esc_html_e( 'Still Have Questions?', 'jjweddingz' ); ?></h3>
                <p class="text-mist" style="margin-bottom: var(--sp-xl); max-width: 600px; margin-inline: auto;">
                    <?php esc_html_e( 'Our client experience managers are here to assist. Connect with us via WhatsApp or drop a message through our inquiry form.', 'jjweddingz' ); ?>
                </p>
                <div class="flex flex-center" style="gap: var(--sp-md);">
                    <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--primary">
                        <?php esc_html_e( 'Contact Form', 'jjweddingz' ); ?>
                    </a>
                    <?php echo jjwz_wa_link( esc_html__( 'WhatsApp Chat', 'jjweddingz' ), 'btn btn--outline' ); ?>
                </div>
            </div>

        </div>
    </section>

</main>

<?php
get_footer();
