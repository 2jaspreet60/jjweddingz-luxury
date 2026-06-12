<?php
/**
 * Plugin Name:       JJW Core
 * Plugin URI:        https://jjweddingz.com
 * Description:       Core functionality for JJ WeddingZ Photography: image processing (WebP + watermark), performance optimizations, FAQ, Portfolio, Services, Films, Packages, Testimonials, Team, and Locations CPTs, global admin settings panel, CRM lead forms, client gallery auth, SEO schema, and 50-post blog seeder.
 * Version:           1.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            JJ WeddingZ Photography
 * Author URI:        https://jjweddingz.com
 * License:           Proprietary
 * Text Domain:       jjw-core
 *
 * @package JJW_Core
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ═══════════════════════════════════════════════════════════════════════════
   CONSTANTS
   ═══════════════════════════════════════════════════════════════════════════ */

define( 'JJWZ_CORE_VERSION', '1.1.0' );
define( 'JJWZ_CORE_DIR',     plugin_dir_path( __FILE__ ) );
define( 'JJWZ_CORE_URL',     plugin_dir_url( __FILE__ ) );
define( 'JJWZ_CORE_FILE',    __FILE__ );

/* ═══════════════════════════════════════════════════════════════════════════
   LOAD ALL MODULES
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'plugins_loaded', function() {
    require_once JJWZ_CORE_DIR . 'includes/class-image-processor.php';
    require_once JJWZ_CORE_DIR . 'includes/class-performance.php';
    require_once JJWZ_CORE_DIR . 'includes/class-video-block.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-faq.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-portfolio.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-services.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-films.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-packages.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-testimonials.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-team.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-locations.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-blog.php';
    require_once JJWZ_CORE_DIR . 'includes/class-options-panel.php';
    require_once JJWZ_CORE_DIR . 'includes/class-crm-forms.php';
    require_once JJWZ_CORE_DIR . 'includes/class-gallery-access.php';
    require_once JJWZ_CORE_DIR . 'includes/class-seo-schema.php';

    // Future placeholders
    require_once JJWZ_CORE_DIR . 'includes/crm/class-crm-placeholder.php';
    require_once JJWZ_CORE_DIR . 'includes/finance/class-finance-placeholder.php';
    require_once JJWZ_CORE_DIR . 'includes/gallery/class-gallery-placeholder.php';
    require_once JJWZ_CORE_DIR . 'includes/automation/class-automation-placeholder.php';
    require_once JJWZ_CORE_DIR . 'includes/client-portal/class-client-portal-placeholder.php';

    // Instantiate all modules
    new JJWZ_Image_Processor();
    new JJWZ_Performance();
    new JJWZ_Video_Block();
    new JJWZ_CPT_FAQ();
    new JJWZ_CPT_Portfolio();
    new JJWZ_CPT_Services();
    new JJWZ_CPT_Films();
    new JJWZ_CPT_Packages();
    new JJWZ_CPT_Testimonials();
    new JJWZ_CPT_Team();
    new JJWZ_CPT_Locations();
    new JJWZ_CPT_Blog();
    new JJWZ_Options_Panel();
    new JJWZ_CRM_Forms();
    new JJWZ_Gallery_Access();
    new JJWZ_SEO_Schema();

    // Future placeholders instantiation
    new JJWZ_CRM_Placeholder();
    new JJWZ_Finance_Placeholder();
    new JJWZ_Gallery_Placeholder();
    new JJWZ_Automation_Placeholder();
    new JJWZ_Client_Portal_Placeholder();
} );

/* ═══════════════════════════════════════════════════════════════════════════
   ACTIVATION HOOK — Run setup tasks
   ═══════════════════════════════════════════════════════════════════════════ */

register_activation_hook( JJWZ_CORE_FILE, 'jjwz_core_activate' );

function jjwz_core_activate() {
    // Ensure CPTs flush rewrite rules
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-faq.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-portfolio.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-services.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-films.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-packages.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-testimonials.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-team.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-locations.php';
    require_once JJWZ_CORE_DIR . 'includes/class-cpt-blog.php';

    $faq = new JJWZ_CPT_FAQ();
    $faq->register_cpt();
    $faq->seed_faqs();

    $portfolio = new JJWZ_CPT_Portfolio();
    $portfolio->register_cpt();
    $portfolio->register_taxonomies();
    $portfolio->seed_terms();

    $services = new JJWZ_CPT_Services();
    $services->register_cpt();

    $films = new JJWZ_CPT_Films();
    $films->register_cpt();

    $packages = new JJWZ_CPT_Packages();
    $packages->register_cpt();

    $testimonials = new JJWZ_CPT_Testimonials();
    $testimonials->register_cpt();

    $team = new JJWZ_CPT_Team();
    $team->register_cpt();

    $locations = new JJWZ_CPT_Locations();
    $locations->register_cpt();

    $blog = new JJWZ_CPT_Blog();
    $blog->register_categories();

    // Create leads table
    require_once JJWZ_CORE_DIR . 'includes/class-crm-forms.php';
    JJWZ_CRM_Forms::create_leads_table();

    flush_rewrite_rules();

    // Mark plugin as freshly activated — seeder will run once on next admin page load
    update_option( 'jjwz_core_activated', true );
    update_option( 'jjwz_blog_seeded',    false );
}

/* ═══════════════════════════════════════════════════════════════════════════
   DEACTIVATION HOOK
   ═══════════════════════════════════════════════════════════════════════════ */

register_deactivation_hook( JJWZ_CORE_FILE, function() {
    flush_rewrite_rules();
} );

/* ═══════════════════════════════════════════════════════════════════════════
   ONE-TIME BLOG SEED on first admin load after activation
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'admin_init', function() {
    if ( get_option( 'jjwz_core_activated' ) && ! get_option( 'jjwz_blog_seeded' ) ) {
        if ( file_exists( JJWZ_CORE_DIR . 'includes/class-cpt-blog.php' ) ) {
            require_once JJWZ_CORE_DIR . 'includes/class-cpt-blog.php';
            $blog = new JJWZ_CPT_Blog();
            $blog->seed_posts();
        }
        delete_option( 'jjwz_core_activated' );
        update_option( 'jjwz_blog_seeded', true );
    }
} );
