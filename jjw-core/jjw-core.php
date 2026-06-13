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
    require_once JJWZ_CORE_DIR . 'includes/class-service-importer-exporter.php';
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

    // Seed default timeline repeater if not set
    if ( ! get_option( 'jjw_timeline' ) ) {
        $default_timeline = [
            [ 'year' => '2013', 'title' => 'Founded in Amritsar', 'desc' => 'JJ WeddingZ Photography established by Jaspreet Singh with a focus on authentic, emotion-driven wedding documentation.' ],
            [ 'year' => '2016', 'title' => 'Delhi NCR Branch Launch', 'desc' => 'Rapid client demand from Delhi and NCR necessitated the launch of a dedicated metropolitan branch, bringing our services to India\'s capital region.' ],
            [ 'year' => '2018', 'title' => 'Cinematic Department Established', 'desc' => 'Full cinema-grade videography services added using Sony FX3 systems, expanding our offer to include sweeping wedding films.' ],
            [ 'year' => '2020', 'title' => 'Maternity & Newborn Studio Launch', 'desc' => 'A dedicated, sanitized maternity and newborn photography studio established with medical-grade safety protocols.' ],
            [ 'year' => '2022', 'title' => 'International Destination Commissions', 'desc' => 'First international destination wedding assignments accepted. JJ WeddingZ now travels globally.' ],
            [ 'year' => '2024', 'title' => '500+ Weddings Milestone', 'desc' => 'Crossing the 500 premium weddings threshold, JJ WeddingZ cements its status as Northern India\'s most trusted luxury photography house.' ]
        ];
        update_option( 'jjw_timeline', wp_json_encode( $default_timeline ) );
    }

    // Seed default placeholders if not set
    $placeholder_base = content_url( '/themes/jjw-luxury/assets/images/' );
    if ( ! get_option( 'jjw_default_placeholder_founder' ) ) {
        update_option( 'jjw_default_placeholder_founder', $placeholder_base . 'placeholder-founder.png' );
    }
    if ( ! get_option( 'jjw_default_placeholder_service' ) ) {
        update_option( 'jjw_default_placeholder_service', $placeholder_base . 'placeholder-category-default.png' );
    }
    if ( ! get_option( 'jjw_default_placeholder_portfolio' ) ) {
        update_option( 'jjw_default_placeholder_portfolio', $placeholder_base . 'placeholder-category-default.png' );
    }
    if ( ! get_option( 'jjw_default_placeholder_testimonial' ) ) {
        update_option( 'jjw_default_placeholder_testimonial', $placeholder_base . 'placeholder-testimonial.png' );
    }
    if ( ! get_option( 'jjw_default_placeholder_blog' ) ) {
        update_option( 'jjw_default_placeholder_blog', $placeholder_base . 'placeholder-blog.png' );
    }

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

/* ═══════════════════════════════════════════════════════════════════════════
   CUSTOM REWRITE RULES & TEMPLATE REDIRECTS (Sprint 3)
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'init', 'jjwz_add_rewrite_rules' );
function jjwz_add_rewrite_rules() {
    add_rewrite_rule(
        '^([a-z0-9-]+)-(photographer|photoshoot|cinematographer)-in-([a-z0-9-]+)/?$',
        'index.php?jjwz_service_slug=$matches[1]&jjwz_location_slug=$matches[3]',
        'top'
    );
}

add_filter( 'query_vars', 'jjwz_register_query_vars' );
function jjwz_register_query_vars( $vars ) {
    $vars[] = 'jjwz_service_slug';
    $vars[] = 'jjwz_location_slug';
    return $vars;
}

add_filter( 'template_include', 'jjwz_route_service_location_template' );
function jjwz_route_service_location_template( $template ) {
    $service_slug  = get_query_var( 'jjwz_service_slug' );
    $location_slug = get_query_var( 'jjwz_location_slug' );

    if ( $service_slug && $location_slug ) {
        $theme_template = locate_template( [ 'landing-service-location.php' ] );
        if ( $theme_template ) {
            return $theme_template;
        }
    }
    return $template;
}

add_filter( 'document_title_parts', 'jjwz_service_location_document_title', 20 );
function jjwz_service_location_document_title( $title_parts ) {
    $service_slug  = get_query_var( 'jjwz_service_slug' );
    $location_slug = get_query_var( 'jjwz_location_slug' );

    if ( $service_slug && $location_slug ) {
        $seo_raw = get_option( 'jjw_service_city_seo', '[]' );
        $seo_items = json_decode( $seo_raw, true ) ?: [];
        $override_title = '';
        foreach ( $seo_items as $item ) {
            if ( ( $item['service'] ?? '' ) === $service_slug && ( $item['city'] ?? '' ) === $location_slug ) {
                if ( ! empty( $item['seo_title'] ) ) {
                    $override_title = $item['seo_title'];
                }
                break;
            }
        }

        if ( ! $override_title ) {
            $service_name = ucwords( str_replace( '-', ' ', $service_slug ) );
            $location_name = ucwords( str_replace( '-', ' ', $location_slug ) );
            $override_title = sprintf( 'Luxury %s Photographer in %s', $service_name, $location_name );
        }

        $title_parts['title'] = $override_title;
    }
    return $title_parts;
}

add_action( 'wp_head', 'jjwz_service_location_meta_description', 1 );
function jjwz_service_location_meta_description() {
    $service_slug  = get_query_var( 'jjwz_service_slug' );
    $location_slug = get_query_var( 'jjwz_location_slug' );

    if ( $service_slug && $location_slug ) {
        $seo_raw = get_option( 'jjw_service_city_seo', '[]' );
        $seo_items = json_decode( $seo_raw, true ) ?: [];
        $override_desc = '';
        foreach ( $seo_items as $item ) {
            if ( ( $item['service'] ?? '' ) === $service_slug && ( $item['city'] ?? '' ) === $location_slug ) {
                if ( ! empty( $item['meta_description'] ) ) {
                    $override_desc = $item['meta_description'];
                }
                break;
            }
        }

        if ( ! $override_desc ) {
            $service_name = ucwords( str_replace( '-', ' ', $service_slug ) );
            $location_name = ucwords( str_replace( '-', ' ', $location_slug ) );
            $override_desc = sprintf( 'Looking for a premium %s photographer in %s? Contact JJ WeddingZ Photography for luxury editorial photography and films.', strtolower($service_name), $location_name );
        }

        // Print custom meta description if SEO plugins are not active to avoid duplication, or force standard description
        if ( ! isset( $GLOBALS['wp_seo_desc_written'] ) ) {
            echo '<meta name="description" content="' . esc_attr( $override_desc ) . '">' . "\n";
            $GLOBALS['wp_seo_desc_written'] = true;
        }
    }
}

add_filter( 'rank_math/frontend/title', 'jjwz_seo_plugin_title_override', 20 );
add_filter( 'wpseo_title', 'jjwz_seo_plugin_title_override', 20 );
function jjwz_seo_plugin_title_override( $title ) {
    $service_slug  = get_query_var( 'jjwz_service_slug' );
    $location_slug = get_query_var( 'jjwz_location_slug' );
    if ( $service_slug && $location_slug ) {
        $title_parts = jjwz_service_location_document_title( [ 'title' => '' ] );
        return $title_parts['title'];
    }
    return $title;
}

add_filter( 'rank_math/frontend/description', 'jjwz_seo_plugin_desc_override', 20 );
add_filter( 'wpseo_metadesc', 'jjwz_seo_plugin_desc_override', 20 );
function jjwz_seo_plugin_desc_override( $desc ) {
    $service_slug  = get_query_var( 'jjwz_service_slug' );
    $location_slug = get_query_var( 'jjwz_location_slug' );
    if ( $service_slug && $location_slug ) {
        $GLOBALS['wp_seo_desc_written'] = true;
        $seo_raw = get_option( 'jjw_service_city_seo', '[]' );
        $seo_items = json_decode( $seo_raw, true ) ?: [];
        foreach ( $seo_items as $item ) {
            if ( ( $item['service'] ?? '' ) === $service_slug && ( $item['city'] ?? '' ) === $location_slug ) {
                if ( ! empty( $item['meta_description'] ) ) {
                    return $item['meta_description'];
                }
            }
        }
        $service_name = ucwords( str_replace( '-', ' ', $service_slug ) );
        $location_name = ucwords( str_replace( '-', ' ', $location_slug ) );
        return sprintf( 'Looking for a premium %s photographer in %s? Contact JJ WeddingZ Photography for luxury editorial photography and films.', strtolower($service_name), $location_name );
    }
    return $desc;
}

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * SITEMAP INTEGRATION FOR DYNAMIC CAMPAIGN PAGES
 * ═══════════════════════════════════════════════════════════════════════════
 */
add_filter( 'rank_math/sitemap/custom_links', 'jjwz_add_dynamic_landing_pages_to_rankmath_sitemap' );
function jjwz_add_dynamic_landing_pages_to_rankmath_sitemap( $links ) {
    $seo_raw = get_option( 'jjw_service_city_seo', '[]' );
    $seo_items = json_decode( $seo_raw, true ) ?: [];
    foreach ( $seo_items as $item ) {
        $service = $item['service'] ?? '';
        $city = $item['city'] ?? '';
        if ( $service && $city ) {
            $links[] = [
                'loc' => home_url( '/' . $service . '-photographer-in-' . $city . '/' ),
                'mod' => date( 'c' ),
                'chg' => 'weekly',
                'pri' => '0.8',
            ];
        }
    }
    return $links;
}

