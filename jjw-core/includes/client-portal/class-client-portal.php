<?php
/**
 * class-client-portal.php — Client Portal Routing, Auth, & Favorites Sync Engine
 *
 * Handles:
 * - Dynamic URL rewrite registration for `/client-portal/`
 * - Frontend template routing mapping to `page-client-portal.php`
 * - Session authentication gates and AJAX handlers for portal interactions
 *
 * @package JJW_Core
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Client_Portal {

    public function __construct() {
        // Rewrite rules
        add_action( 'init', [ $this, 'add_rewrite_rules' ] );
        add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
        add_filter( 'template_include', [ $this, 'route_portal_template' ] );

        // AJAX handlers
        add_action( 'wp_ajax_nopriv_jjwz_portal_login', [ $this, 'handle_portal_login' ] );
        add_action( 'wp_ajax_jjwz_portal_login',        [ $this, 'handle_portal_login' ] );

        add_action( 'wp_ajax_nopriv_jjwz_portal_logout', [ $this, 'handle_portal_logout' ] );
        add_action( 'wp_ajax_jjwz_portal_logout',        [ $this, 'handle_portal_logout' ] );

        add_action( 'wp_ajax_nopriv_jjwz_portal_save_favorites', [ $this, 'handle_save_favorites' ] );
        add_action( 'wp_ajax_jjwz_portal_save_favorites',        [ $this, 'handle_save_favorites' ] );

        add_action( 'wp_ajax_nopriv_jjwz_portal_save_album_selections', [ $this, 'handle_save_album_selections' ] );
        add_action( 'wp_ajax_jjwz_portal_save_album_selections',        [ $this, 'handle_save_album_selections' ] );
    }

    /* ─── Routing & Rewrites ────────────────────────────────────────────── */

    public function add_rewrite_rules(): void {
        add_rewrite_rule( '^client-portal/?$', 'index.php?jjwz_client_portal=1', 'top' );
    }

    public function register_query_vars( array $vars ): array {
        $vars[] = 'jjwz_client_portal';
        return $vars;
    }

    public function route_portal_template( string $template ): string {
        if ( get_query_var( 'jjwz_client_portal' ) ) {
            $theme_template = locate_template( [ 'page-client-portal.php' ] );
            if ( $theme_template ) {
                return $theme_template;
            }
        }
        return $template;
    }

    /* ─── AJAX Handlers ─────────────────────────────────────────────────── */

    public function handle_portal_login(): void {
        $access_key = sanitize_text_field( trim( $_POST['access_key'] ?? '' ) );
        if ( empty( $access_key ) ) {
            wp_send_json_error( [ 'message' => 'Please enter a valid access key.' ], 400 );
        }

        $galleries = get_posts( [
            'post_type'      => 'jjwz_gallery',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'     => 'gallery_access_key',
                    'value'   => $access_key,
                    'compare' => '=',
                ]
            ]
        ] );

        if ( ! empty( $galleries ) ) {
            $gallery = $galleries[0];
            $gallery_id = $gallery->ID;

            // Check if gallery is expired
            $expiry = get_post_meta( $gallery_id, 'gallery_expiry', true );
            $today = date( 'Y-m-d' );
            if ( ! empty( $expiry ) && $expiry < $today ) {
                wp_send_json_error( [ 'message' => 'This gallery access has expired. Please contact support.' ], 403 );
            }

            // Start PHP Session
            if ( ! session_id() && ! headers_sent() ) {
                session_start();
            }

            $_SESSION['jjwz_client_gallery_id'] = $gallery_id;
            $_SESSION['jjwz_client_brand']      = get_post_meta( $gallery_id, 'gallery_brand', true ) ?: 'JJ WeddingZ';

            wp_send_json_success( [
                'message'    => 'Access granted. Welcome!',
                'redirect'   => home_url( '/client-portal/' )
            ] );
        } else {
            wp_send_json_error( [ 'message' => 'Invalid Access Key. Please check your credentials.' ], 401 );
        }
    }

    public function handle_portal_logout(): void {
        if ( ! session_id() && ! headers_sent() ) {
            session_start();
        }
        unset( $_SESSION['jjwz_client_gallery_id'] );
        unset( $_SESSION['jjwz_client_brand'] );
        wp_send_json_success( [ 'message' => 'Logged out successfully.' ] );
    }

    public function handle_save_favorites(): void {
        if ( ! session_id() && ! headers_sent() ) {
            session_start();
        }

        $gallery_id = isset( $_SESSION['jjwz_client_gallery_id'] ) ? (int) $_SESSION['jjwz_client_gallery_id'] : 0;
        if ( ! $gallery_id ) {
            wp_send_json_error( [ 'message' => 'Session expired. Please log in again.' ], 401 );
        }

        $favorites_raw = sanitize_text_field( $_POST['favorites'] ?? '[]' );
        $favorites = json_decode( html_entity_decode( stripslashes( $favorites_raw ) ), true );

        if ( ! is_array( $favorites ) ) {
            wp_send_json_error( [ 'message' => 'Invalid favorites payload.' ], 400 );
        }

        update_post_meta( $gallery_id, 'gallery_favorites', wp_json_encode( array_filter( array_map( 'intval', $favorites ) ) ) );

        wp_send_json_success( [ 'message' => 'Favorites updated.' ] );
    }

    public function handle_save_album_selections(): void {
        if ( ! session_id() && ! headers_sent() ) {
            session_start();
        }

        $gallery_id = isset( $_SESSION['jjwz_client_gallery_id'] ) ? (int) $_SESSION['jjwz_client_gallery_id'] : 0;
        if ( ! $gallery_id ) {
            wp_send_json_error( [ 'message' => 'Session expired. Please log in again.' ], 401 );
        }

        $selections_raw = sanitize_text_field( $_POST['selections'] ?? '[]' );
        $selections = json_decode( html_entity_decode( stripslashes( $selections_raw ) ), true );

        if ( ! is_array( $selections ) ) {
            wp_send_json_error( [ 'message' => 'Invalid album selections payload.' ], 400 );
        }

        update_post_meta( $gallery_id, 'gallery_album_selections', wp_json_encode( array_filter( array_map( 'intval', $selections ) ) ) );

        wp_send_json_success( [ 'message' => 'Album selections updated.' ] );
    }
}
new JJWZ_Client_Portal();
