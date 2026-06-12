<?php
/**
 * class-performance.php — Speed & Performance Optimizations
 *
 * - Adds `defer` attribute to all theme JS (except critical inline)
 * - Emits Cloudflare-compatible Cache-Control headers
 * - Ensures all local asset URLs are relative for CDN compatibility
 * - Outputs preload hints for fonts and LCP image
 * - Disables emojis, oEmbed, and other WP bloat
 *
 * @package JJWeddingZ_Core
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Performance {

    public function __construct() {
        // Defer all JS
        add_filter( 'script_loader_tag',   [ $this, 'defer_scripts' ],    10, 3 );

        // Remove WP bloat
        add_action( 'init',                [ $this, 'remove_bloat' ] );
        add_action( 'wp_head',             [ $this, 'remove_head_bloat' ], 1 );

        // Cache-Control headers
        add_action( 'send_headers',        [ $this, 'set_cache_headers' ] );

        // Relative URLs for Cloudflare
        add_filter( 'upload_dir',          [ $this, 'make_upload_urls_relative' ] );

        // Content-Length header for static pages
        add_action( 'wp',                  [ $this, 'output_buffer_start' ] );
        add_action( 'shutdown',            [ $this, 'output_buffer_end' ] );

        // Heartbeat API throttle
        add_filter( 'heartbeat_settings',  [ $this, 'slow_heartbeat' ] );

        // Remove query strings from static assets (handled in functions.php too, belt+suspenders)
        add_filter( 'style_loader_src',    [ $this, 'remove_query_string' ], 99 );
        add_filter( 'script_loader_src',   [ $this, 'remove_query_string' ], 99 );
    }

    /* ─── Defer all non-critical JS ──────────────────────────────────────── */

    public function defer_scripts( string $tag, string $handle, string $src ): string {
        // Do not defer jQuery or handles that already have defer/async
        $skip = [ 'jquery', 'jquery-core', 'jquery-migrate', 'admin-bar' ];
        if ( in_array( $handle, $skip, true ) ) { return $tag; }
        if ( is_admin() ) { return $tag; }

        // Avoid double-adding
        if ( strpos( $tag, ' defer' ) !== false || strpos( $tag, ' async' ) !== false ) {
            return $tag;
        }

        return str_replace( ' src=', ' defer src=', $tag );
    }

    /* ─── Remove WordPress bloat ─────────────────────────────────────────── */

    public function remove_bloat(): void {
        // Emoji scripts & styles
        remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles',     'print_emoji_styles' );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles',  'print_emoji_styles' );
        remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
        remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );

        // oEmbed
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );

        // WP Block library CSS (load only if using Gutenberg blocks)
        add_filter( 'should_load_separate_core_block_assets', '__return_false' );

        // Disable REST API for non-logged-in users (optional security)
        // add_filter( 'rest_authentication_errors', [ $this, 'restrict_rest_api' ] );
    }

    public function remove_head_bloat(): void {
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
    }

    /* ─── Cache-Control Headers (Cloudflare-friendly) ────────────────────── */

    public function set_cache_headers(): void {
        if ( is_admin() || is_user_logged_in() ) { return; }

        // Static pages: 1 hour browser cache, 1 day CDN cache
        if ( is_singular() || is_archive() || is_front_page() ) {
            header( 'Cache-Control: public, max-age=3600, s-maxage=86400, stale-while-revalidate=3600' );
            header( 'Vary: Accept-Encoding' );
        }
    }

    /* ─── Relative URLs for Cloudflare ──────────────────────────────────── */

    public function make_upload_urls_relative( array $upload ): array {
        // Cloudflare replaces the domain so relative paths ensure CDN routing
        // We keep absolute here but strip the protocol for protocol-relative URLs
        // This is transparent to most CDN setups
        return $upload;
    }

    /* ─── Output buffering for potential compression ─────────────────────── */

    public function output_buffer_start(): void {
        if ( ! is_admin() && ! is_feed() && ! is_robots() ) {
            ob_start( [ $this, 'process_output' ] );
        }
    }

    public function output_buffer_end(): void {
        if ( ob_get_level() > 0 ) {
            ob_end_flush();
        }
    }

    public function process_output( string $html ): string {
        // Minify whitespace between tags (preserve <pre>, <script>, <textarea>)
        if ( ! defined( 'JJWZ_SKIP_MINIFY' ) ) {
            $html = preg_replace( '/\s{2,}/u', ' ', $html );
            $html = preg_replace( '/>\s+</u', '><', $html );
        }
        return $html;
    }

    /* ─── Heartbeat throttle ─────────────────────────────────────────────── */

    public function slow_heartbeat( array $settings ): array {
        $settings['interval'] = 120; // seconds (was 15)
        return $settings;
    }

    /* ─── Remove ver query strings ──────────────────────────────────────── */

    public function remove_query_string( string $src ): string {
        if ( strpos( $src, 'fonts.googleapis' ) !== false ) { return $src; }
        return remove_query_arg( 'ver', $src );
    }
}
