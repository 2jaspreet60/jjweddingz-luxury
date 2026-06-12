<?php
/**
 * functions.php — JJ WeddingZ Photography Theme Bootstrap
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ═══════════════════════════════════════════════════════════════════════════
   CONSTANTS
   ═══════════════════════════════════════════════════════════════════════════ */

define( 'JJWZ_VERSION',   '1.0.0' );
define( 'JJWZ_DIR',       get_template_directory() );
define( 'JJWZ_URI',       get_template_directory_uri() );
define( 'JJWZ_ASSETS',    JJWZ_URI . '/assets' );
define( 'JJWZ_INC',       JJWZ_DIR . '/inc' );

/* ═══════════════════════════════════════════════════════════════════════════
   REQUIRE INC FILES
   ═══════════════════════════════════════════════════════════════════════════ */

require_once JJWZ_INC . '/template-functions.php';
require_once JJWZ_INC . '/acf-fields.php';

// Load Elementor dynamic tags only if Elementor is active
add_action( 'elementor/dynamic_tags/register', function() {
    require_once JJWZ_INC . '/elementor-dynamic-tags.php';
} );

/* ═══════════════════════════════════════════════════════════════════════════
   THEME SETUP
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'after_setup_theme', 'jjwz_theme_setup' );

function jjwz_theme_setup() {
    // Content Width
    global $content_width;
    if ( ! isset( $content_width ) ) { $content_width = 1320; }

    // Theme supports
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ] );
    add_theme_support( 'custom-logo', [
        'height'      => 80,
        'width'       => 240,
        'flex-width'  => true,
        'flex-height' => true,
    ] );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'post-formats', [ 'video', 'gallery', 'quote' ] );

    // Navigation Menus
    register_nav_menus( [
        'primary'   => __( 'Primary Navigation',  'jjweddingz' ),
        'footer'    => __( 'Footer Navigation',   'jjweddingz' ),
        'social'    => __( 'Social Links',         'jjweddingz' ),
    ] );

    // Translations
    load_theme_textdomain( 'jjweddingz', JJWZ_DIR . '/languages' );

    // Image Sizes
    add_image_size( 'jjwz-hero',         2560, 1440, true );
    add_image_size( 'jjwz-portfolio',    1200, 900,  true );
    add_image_size( 'jjwz-card',          800, 600,  true );
    add_image_size( 'jjwz-thumb',         600, 400,  true );
    add_image_size( 'jjwz-square',        600, 600,  true );
    add_image_size( 'jjwz-portrait',      600, 800,  true );
    add_image_size( 'jjwz-blog-hero',    1600, 700,  true );
    add_image_size( 'jjwz-blog-card',     800, 480,  true );
}

/* ═══════════════════════════════════════════════════════════════════════════
   ENQUEUE SCRIPTS & STYLES
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'wp_enqueue_scripts', 'jjwz_enqueue_assets' );

function jjwz_enqueue_assets() {
    // Google Fonts — performance-optimised with display=swap
    wp_enqueue_style(
        'jjwz-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Inter:wght@300;400;500;600;700&display=swap',
        [],
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'jjwz-main',
        JJWZ_ASSETS . '/css/main.css',
        [ 'jjwz-fonts' ],
        JJWZ_VERSION
    );

    // Parent style.css (design tokens)
    wp_enqueue_style(
        'jjwz-style',
        get_stylesheet_uri(),
        [ 'jjwz-main' ],
        JJWZ_VERSION
    );

    // Video handler (defer via class-performance.php filter)
    wp_enqueue_script(
        'jjwz-video',
        JJWZ_ASSETS . '/js/video-handler.js',
        [],
        JJWZ_VERSION,
        true
    );

    // Gallery JS (only on gallery page)
    if ( is_page_template( 'page-gallery.php' ) ) {
        wp_enqueue_script(
            'jjwz-gallery',
            JJWZ_ASSETS . '/js/gallery.js',
            [],
            JJWZ_VERSION,
            true
        );
    }

    // Main theme JS (defer via class-performance.php filter)
    wp_enqueue_script(
        'jjwz-theme',
        JJWZ_ASSETS . '/js/theme.js',
        [],
        JJWZ_VERSION,
        true
    );

    // Localise script with dynamic data
    wp_localize_script( 'jjwz-theme', 'JJWZ', [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'jjwz_nonce' ),
        'siteUrl'   => get_site_url(),
        'whatsapp'  => jjwz_get_option( 'jjwz_whatsapp_number', '' ),
    ] );

    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   INLINE CRITICAL CSS IN <head>
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'wp_head', 'jjwz_inline_critical_css', 5 );

function jjwz_inline_critical_css() {
    $critical_file = JJWZ_DIR . '/assets/css/critical.css';
    if ( file_exists( $critical_file ) ) {
        $css = file_get_contents( $critical_file );
        echo '<style id="jjwz-critical">' . wp_strip_all_tags( $css ) . '</style>' . "\n";
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   PRELOAD RESOURCES (LCP OPTIMISATION)
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'wp_head', 'jjwz_preload_resources', 1 );

function jjwz_preload_resources() {
    // Preconnect to Google Fonts
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // Preload critical font subsets
    $font_url = 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400&family=Inter:wght@400;600&display=swap';
    echo '<link rel="preload" as="style" href="' . esc_url( $font_url ) . '">' . "\n";

    // Hero image preload on homepage
    if ( is_front_page() ) {
        $hero_img = jjwz_get_option( 'jjwz_hero_bg_image' );
        if ( $hero_img && isset( $hero_img['url'] ) ) {
            echo '<link rel="preload" as="image" href="' . esc_url( $hero_img['url'] ) . '" fetchpriority="high">' . "\n";
        }
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   WIDGETS
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'widgets_init', 'jjwz_widgets_init' );

function jjwz_widgets_init() {
    $defaults = [
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ];

    register_sidebar( array_merge( $defaults, [
        'name' => __( 'Blog Sidebar', 'jjweddingz' ),
        'id'   => 'blog-sidebar',
    ] ) );

    register_sidebar( array_merge( $defaults, [
        'name' => __( 'Footer Column 1', 'jjweddingz' ),
        'id'   => 'footer-1',
    ] ) );

    register_sidebar( array_merge( $defaults, [
        'name' => __( 'Footer Column 2', 'jjweddingz' ),
        'id'   => 'footer-2',
    ] ) );

    register_sidebar( array_merge( $defaults, [
        'name' => __( 'Footer Column 3', 'jjweddingz' ),
        'id'   => 'footer-3',
    ] ) );
}

/* ═══════════════════════════════════════════════════════════════════════════
   CUSTOM IMAGE SIZES IN MEDIA LIBRARY
   ═══════════════════════════════════════════════════════════════════════════ */

add_filter( 'image_size_names_choose', 'jjwz_custom_image_sizes' );

function jjwz_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, [
        'jjwz-hero'      => __( 'JJ Hero (2560×1440)', 'jjweddingz' ),
        'jjwz-portfolio' => __( 'JJ Portfolio (1200×900)', 'jjweddingz' ),
        'jjwz-card'      => __( 'JJ Card (800×600)', 'jjweddingz' ),
        'jjwz-blog-hero' => __( 'JJ Blog Hero (1600×700)', 'jjweddingz' ),
    ] );
}

/* ═══════════════════════════════════════════════════════════════════════════
   BODY CLASS ADDITIONS
   ═══════════════════════════════════════════════════════════════════════════ */

add_filter( 'body_class', 'jjwz_body_classes' );

function jjwz_body_classes( $classes ) {
    if ( is_singular() ) {
        $classes[] = 'singular';
    }
    if ( is_front_page() ) {
        $classes[] = 'jjwz-home';
    }
    // Always add theme class
    $classes[] = 'jjwz-theme';
    return $classes;
}

/* ═══════════════════════════════════════════════════════════════════════════
   EXCERPT MODIFICATIONS
   ═══════════════════════════════════════════════════════════════════════════ */

add_filter( 'excerpt_length', fn() => 28 );
add_filter( 'excerpt_more',   fn() => '&hellip;' );

/* ═══════════════════════════════════════════════════════════════════════════
   DISABLE XMLRPC (SECURITY)
   ═══════════════════════════════════════════════════════════════════════════ */

add_filter( 'xmlrpc_enabled', '__return_false' );

/* ═══════════════════════════════════════════════════════════════════════════
   RELATIVE URLS FOR CDN / CLOUDFLARE COMPATIBILITY
   ═══════════════════════════════════════════════════════════════════════════ */

add_filter( 'wp_calculate_image_srcset_meta', 'jjwz_relative_srcset', 10, 4 );

function jjwz_relative_srcset( $image_meta, $size_array, $image_src, $attachment_id ) {
    return $image_meta; // Hook point — class-performance.php handles actual URL manipulation
}

/* ═══════════════════════════════════════════════════════════════════════════
   REMOVE QUERY STRINGS FROM STATIC ASSETS (CLOUDFLARE CACHE)
   ═══════════════════════════════════════════════════════════════════════════ */

add_filter( 'style_loader_src',  'jjwz_remove_ver_query', 10, 2 );
add_filter( 'script_loader_src', 'jjwz_remove_ver_query', 10, 2 );

function jjwz_remove_ver_query( $src, $handle ) {
    // Keep for Google Fonts; strip from local assets
    if ( strpos( $src, 'googleapis' ) !== false ) {
        return $src;
    }
    if ( strpos( $src, '?ver=' ) !== false ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}

/* ═══════════════════════════════════════════════════════════════════════════
   HELPER: GET WP OPTION OR ACF FIELD WITH FALLBACK
   ═══════════════════════════════════════════════════════════════════════════ */

if ( ! function_exists( 'jjwz_get_option' ) ) {
    function jjwz_get_option( string $key, $fallback = '', $post_id = null ) {
        if ( $post_id !== null ) {
            // Check post meta (from custom DB or WP post meta)
            $val = get_post_meta( $post_id, $key, true );
            if ( $val !== false && $val !== '' && $val !== null ) {
                return $val;
            }
            // Check ACF field on this post
            if ( function_exists( 'get_field' ) ) {
                $val = get_field( $key, $post_id );
                if ( $val !== '' && $val !== null && $val !== false ) {
                    return $val;
                }
            }
            return $fallback;
        }

        // Global Options: Check WP get_option
        $val = get_option( $key );
        if ( $val !== false && $val !== '' && $val !== null ) {
            return $val;
        }
        // Check ACF Option Page
        if ( function_exists( 'get_field' ) ) {
            $val = get_field( $key, 'option' );
            if ( $val !== '' && $val !== null && $val !== false ) {
                return $val;
            }
        }
        return $fallback;
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   SCHEMA.ORG STRUCTURED DATA — LOCAL BUSINESS
   ═══════════════════════════════════════════════════════════════════════════ */

add_action( 'wp_head', 'jjwz_schema_local_business' );

function jjwz_schema_local_business() {
    if ( ! is_front_page() && ! is_home() ) { return; }

    $phone = jjwz_get_option( 'jjwz_header_phone', '' );
    $schema = [
        '@context'          => 'https://schema.org',
        '@type'             => [ 'LocalBusiness', 'ProfessionalService' ],
        'name'              => 'JJ WeddingZ Photography',
        'description'       => 'Luxury Wedding, Maternity & Newborn Photography across Delhi NCR and Amritsar. 11+ years of professional experience.',
        'url'               => home_url(),
        'logo'              => has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '',
        'telephone'         => $phone,
        'priceRange'        => '₹₹₹₹',
        'areaServed'        => [ 'Delhi', 'NCR', 'Amritsar', 'Punjab', 'Northern India' ],
        'knowsAbout'        => [ 'Wedding Photography', 'Maternity Photography', 'Newborn Photography', 'Pre-Wedding Photography', 'Cinematography' ],
        'foundingDate'      => '2013',
        'founder'           => [ '@type' => 'Person', 'name' => 'Jaspreet Singh' ],
        'address'           => [
            '@type'           => 'PostalAddress',
            'addressRegion'   => 'Delhi',
            'addressCountry'  => 'IN',
        ],
        'sameAs' => array_filter( [
            jjwz_get_option( 'jjwz_social_instagram', '' ),
            jjwz_get_option( 'jjwz_social_facebook', '' ),
            jjwz_get_option( 'jjwz_social_youtube', '' ),
        ] ),
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/* ═══════════════════════════════════════════════════════════════════════════
   CUSTOM NAV WALKER (for clean header markup)
   ═══════════════════════════════════════════════════════════════════════════ */

class JJWZ_Nav_Walker extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="nav__dropdown" role="menu">';
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes   = $item->classes ?? [];
        $has_child = in_array( 'menu-item-has-children', $classes );
        $li_class  = 'nav__item' . ( $has_child ? ' nav__item--has-dropdown' : '' );
        $li_class .= in_array( 'current-menu-item', $classes ) ? ' is-active' : '';

        $output .= '<li class="' . esc_attr( $li_class ) . '">';
        $url     = $item->url ?? '#';
        $title   = apply_filters( 'the_title', $item->title, $item->ID );

        $output .= '<a href="' . esc_url( $url ) . '" class="nav__link"';
        if ( in_array( 'current-menu-item', $classes ) ) {
            $output .= ' aria-current="page"';
        }
        $output .= '>' . esc_html( $title );
        if ( $has_child ) {
            $output .= '<svg class="nav__chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
        }
        $output .= '</a>';
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   YOAST / RANK MATH SEO BREADCRUMB FALLBACK
   ═══════════════════════════════════════════════════════════════════════════ */

if ( ! function_exists( 'jjwz_breadcrumb' ) ) {
    function jjwz_breadcrumb() {
        if ( function_exists( 'yoast_breadcrumb' ) ) {
            yoast_breadcrumb( '<nav class="jjwz-breadcrumb" aria-label="Breadcrumb">', '</nav>' );
        } elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
            rank_math_the_breadcrumbs();
        } else {
            // Minimal built-in fallback
            if ( is_front_page() ) { return; }
            echo '<nav class="jjwz-breadcrumb" aria-label="Breadcrumb"><ol class="breadcrumb__list">';
            echo '<li><a href="' . home_url() . '">Home</a></li>';
            if ( is_singular() ) {
                $cats = get_the_category();
                if ( $cats ) {
                    echo '<li><a href="' . get_category_link( $cats[0]->term_id ) . '">' . esc_html( $cats[0]->name ) . '</a></li>';
                }
                echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';
            } elseif ( is_category() ) {
                echo '<li aria-current="page">' . single_cat_title( '', false ) . '</li>';
            } elseif ( is_page() ) {
                echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';
            }
            echo '</ol></nav>';
        }
    }
}
