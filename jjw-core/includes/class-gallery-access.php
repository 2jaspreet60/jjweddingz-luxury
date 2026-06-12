<?php
/**
 * class-gallery-access.php — Client Gallery Session Authentication
 *
 * Provides session-based password gating for client galleries.
 * Handles AJAX-based auth for JS-powered gallery access.
 *
 * @package JJWeddingZ_Core
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Gallery_Access {

    public function __construct() {
        // Start sessions before headers
        add_action( 'init', [ $this, 'start_session' ], 1 );

        // Register Gallery CPT
        add_action( 'init', [ $this, 'register_cpt' ] );

        // AJAX gallery auth handler
        add_action( 'wp_ajax_nopriv_jjwz_gallery_auth', [ $this, 'handle_ajax_auth' ] );
        add_action( 'wp_ajax_jjwz_gallery_auth',        [ $this, 'handle_ajax_auth' ] );

        // Restrict gallery RSS
        add_action( 'pre_get_posts', [ $this, 'restrict_gallery_rss' ] );

        // Metabox fallback for ACF Free
        add_action( 'add_meta_boxes',          [ $this, 'add_gallery_meta_boxes' ] );
        add_action( 'save_post',               [ $this, 'save_gallery_meta' ] );
        add_action( 'admin_enqueue_scripts',   [ $this, 'enqueue_gallery_admin_assets' ] );
    }

    /**
     * Register Client Galleries Custom Post Type.
     */
    public function register_cpt(): void {
        register_post_type( 'jjwz_gallery', [
            'labels' => [
                'name'               => __( 'Client Galleries',  'jjweddingz' ),
                'singular_name'      => __( 'Client Gallery',   'jjweddingz' ),
                'add_new'            => __( 'Add New Gallery',  'jjweddingz' ),
                'add_new_item'       => __( 'Add New Client Gallery', 'jjweddingz' ),
                'edit_item'          => __( 'Edit Gallery',      'jjweddingz' ),
                'new_item'           => __( 'New Gallery',       'jjweddingz' ),
                'view_item'          => __( 'View Gallery',      'jjweddingz' ),
                'search_items'       => __( 'Search Galleries',  'jjweddingz' ),
                'not_found'          => __( 'No galleries found','jjweddingz' ),
                'menu_name'          => __( 'Client Galleries',  'jjweddingz' ),
            ],
            'public'             => true,
            'has_archive'        => false,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'excerpt' ],
            'menu_icon'          => 'dashicons-images-alt2',
            'menu_position'      => 27,
            'rewrite'            => [ 'slug' => 'client-gallery', 'with_front' => false ],
        ] );
    }

    /* ─── Session start ──────────────────────────────────────────────────── */

    public function start_session(): void {
        if ( ! session_id() && ! headers_sent() ) {
            session_set_cookie_params( [
                'lifetime' => 86400, // 24 hours
                'path'     => '/',
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Strict',
            ] );
            session_start();
        }
    }

    /* ─── AJAX Authentication ────────────────────────────────────────────── */

    public function handle_ajax_auth(): void {
        if ( ! check_ajax_referer( 'jjwz_gallery_auth', 'nonce', false ) ) {
            wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
        }

        $gallery_id    = (int) ( $_POST['gallery_id'] ?? 0 );
        $submitted_key = sanitize_text_field( trim( $_POST['access_key'] ?? '' ) );

        if ( ! $gallery_id ) {
            wp_send_json_error( [ 'message' => 'Invalid gallery.' ], 400 );
        }

        $stored_key = get_post_meta( $gallery_id, 'gallery_access_key', true );
        if ( empty( $stored_key ) && function_exists( 'get_field' ) ) {
            $stored_key = get_field( 'gallery_access_key', $gallery_id );
        }

        if ( empty( $stored_key ) || ! hash_equals( (string) $stored_key, $submitted_key ) ) {
            wp_send_json_error( [ 'message' => 'Incorrect access key. Please check your email or contact your photographer.' ], 401 );
        }

        // Auth successful — set session flag
        $_SESSION[ 'jjwz_gallery_auth_' . $gallery_id ] = true;

        wp_send_json_success( [
            'message'    => 'Access granted. Welcome!',
            'gallery_id' => $gallery_id,
        ] );
    }

    /* ─── Check Auth for a Given Gallery ────────────────────────────────── */

    public static function is_authenticated( int $gallery_id ): bool {
        if ( ! session_id() ) { @session_start(); }
        return isset( $_SESSION[ 'jjwz_gallery_auth_' . $gallery_id ] ) && $_SESSION[ 'jjwz_gallery_auth_' . $gallery_id ] === true;
    }

    /* ─── Revoke Gallery Session ─────────────────────────────────────────── */

    public static function revoke( int $gallery_id ): void {
        if ( ! session_id() ) { @session_start(); }
        unset( $_SESSION[ 'jjwz_gallery_auth_' . $gallery_id ] );
    }

    /* ─── Restrict gallery from RSS feeds ───────────────────────────────── */

    public function restrict_gallery_rss( \WP_Query $q ): void {
        if ( $q->is_feed() ) {
            $q->set( 'post_type__not_in', [ 'jjwz_gallery' ] );
        }
    }

    /* ─── Metabox Fallback Registration ─────────────────────────────────── */

    public function add_gallery_meta_boxes(): void {
        add_meta_box(
            'jjwz-gallery-options',
            __( 'Secure Client Gallery Settings (ACF Fallback)', 'jjweddingz' ),
            [ $this, 'render_gallery_meta_box' ],
            'jjwz_gallery',
            'normal',
            'high'
        );
    }

    public function render_gallery_meta_box( \WP_Post $post ): void {
        wp_nonce_field( 'jjwz_save_gallery_meta', 'jjwz_gallery_meta_nonce' );

        $access_key  = get_post_meta( $post->ID, 'gallery_access_key', true );
        $client_name = get_post_meta( $post->ID, 'gallery_client_name', true );
        $event_date  = get_post_meta( $post->ID, 'gallery_event_date', true );
        $enable_dl   = get_post_meta( $post->ID, 'gallery_enable_dl', true );
        if ( $enable_dl === '' ) { $enable_dl = '1'; }

        $image_ids_str = get_post_meta( $post->ID, '_jjwz_gallery_images', true );
        $image_ids     = $image_ids_str ? array_filter( array_map( 'intval', explode( ',', $image_ids_str ) ) ) : [];
        ?>
        <div class="jjwz-meta-box-wrap" style="padding:10px 0;">
            <p style="color:#666;margin-bottom:15px;"><?php esc_html_e( 'Configure secure gallery access. These metadata settings act as a native uploader fallback when ACF Pro is not installed.', 'jjweddingz' ); ?></p>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="gallery_access_key"><?php esc_html_e( 'Access Key (Password)', 'jjweddingz' ); ?> <span style="color:#d94f4f;">*</span></label></th>
                    <td>
                        <input type="text" name="gallery_access_key" id="gallery_access_key" value="<?php echo esc_attr( $access_key ); ?>" class="regular-text" required placeholder="e.g. PriArj2026">
                        <p class="description"><?php esc_html_e( 'Access code required for viewing private albums.', 'jjweddingz' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gallery_client_name"><?php esc_html_e( 'Client Name', 'jjweddingz' ); ?></label></th>
                    <td>
                        <input type="text" name="gallery_client_name" id="gallery_client_name" value="<?php echo esc_attr( $client_name ); ?>" class="regular-text" placeholder="e.g. Priya & Arjun">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gallery_event_date"><?php esc_html_e( 'Event Date', 'jjweddingz' ); ?></label></th>
                    <td>
                        <input type="text" name="gallery_event_date" id="gallery_event_date" value="<?php echo esc_attr( $event_date ); ?>" class="regular-text" placeholder="e.g. December 12, 2026">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gallery_enable_dl"><?php esc_html_e( 'Enable Downloads', 'jjweddingz' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="gallery_enable_dl" value="1" <?php checked( $enable_dl, '1' ); ?>>
                            <?php esc_html_e( 'Allow client to download high-resolution photos and zipped albums.', 'jjweddingz' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Gallery Images', 'jjweddingz' ); ?></label></th>
                    <td>
                        <input type="hidden" name="_jjwz_gallery_images" id="_jjwz_gallery_images" value="<?php echo esc_attr( $image_ids_str ); ?>">
                        <button type="button" class="button button-secondary jjwz-select-gallery-images"><?php esc_html_e( '📸 Manage Album Photos', 'jjweddingz' ); ?></button>
                        <button type="button" class="button button-link delete jjwz-clear-gallery-images" style="color:#b32d2d;margin-left:10px;"><?php esc_html_e( 'Clear All', 'jjweddingz' ); ?></button>
                        
                        <div class="jjwz-gallery-thumbs-preview" style="margin-top:15px;border:1.5px dashed #cbd5e0;padding:12px;background:#f7fafc;border-radius:6px;min-height:90px;display:flex;flex-wrap:wrap;gap:8px;">
                            <?php if ( ! empty( $image_ids ) ) : ?>
                                <?php foreach ( $image_ids as $id ) : 
                                    $url = wp_get_attachment_image_url( $id, 'thumbnail' );
                                    if ( ! $url ) { continue; }
                                ?>
                                    <div class="jjwz-thumb-preview" style="border:1px solid #e2e8f0;padding:2px;background:#fff;border-radius:4px;">
                                        <img src="<?php echo esc_url( $url ); ?>" style="width:64px;height:64px;object-fit:cover;display:block;border-radius:2px;">
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <span style="color:#718096;font-size:13px;align-self:center;margin:auto;"><?php esc_html_e( 'No images selected yet. Click button above to add.', 'jjweddingz' ); ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    public function save_gallery_meta( int $post_id ): void {
        if ( ! isset( $_POST['jjwz_gallery_meta_nonce'] ) || ! wp_verify_nonce( $_POST['jjwz_gallery_meta_nonce'], 'jjwz_save_gallery_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

        if ( isset( $_POST['gallery_access_key'] ) ) {
            update_post_meta( $post_id, 'gallery_access_key', sanitize_text_field( $_POST['gallery_access_key'] ) );
        }
        if ( isset( $_POST['gallery_client_name'] ) ) {
            update_post_meta( $post_id, 'gallery_client_name', sanitize_text_field( $_POST['gallery_client_name'] ) );
        }
        if ( isset( $_POST['gallery_event_date'] ) ) {
            update_post_meta( $post_id, 'gallery_event_date', sanitize_text_field( $_POST['gallery_event_date'] ) );
        }
        update_post_meta( $post_id, 'gallery_enable_dl', isset( $_POST['gallery_enable_dl'] ) ? '1' : '0' );

        if ( isset( $_POST['_jjwz_gallery_images'] ) ) {
            update_post_meta( $post_id, '_jjwz_gallery_images', sanitize_text_field( $_POST['_jjwz_gallery_images'] ) );
        }
    }

    public function enqueue_gallery_admin_assets( string $hook ): void {
        global $post;
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) { return; }
        if ( ! $post || $post->post_type !== 'jjwz_gallery' ) { return; }

        wp_enqueue_media();
        wp_add_inline_script( 'jquery', $this->get_gallery_admin_js() );
    }

    private function get_gallery_admin_js(): string {
        return <<<JS
        jQuery(document).ready(function($) {
            var file_frame;
            $(document).on('click', '.jjwz-select-gallery-images', function(e) {
                e.preventDefault();

                if ( file_frame ) {
                    file_frame.open();
                    return;
                }

                file_frame = wp.media.frames.file_frame = wp.media({
                    title: 'Select Gallery Images',
                    button: { text: 'Add to Album' },
                    multiple: true
                });

                file_frame.on('select', function() {
                    var attachments = file_frame.state().get('selection').toJSON();
                    var ids = [];
                    var container = $('.jjwz-gallery-thumbs-preview');
                    container.empty();

                    attachments.forEach(function(attachment) {
                        ids.push(attachment.id);
                        var imgUrl = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        container.append('<div class="jjwz-thumb-preview" style="border:1px solid #e2e8f0;padding:2px;background:#fff;border-radius:4px;"><img src="' + imgUrl + '" style="width:64px;height:64px;object-fit:cover;display:block;border-radius:2px;"></div>');
                    });

                    $('#_jjwz_gallery_images').val(ids.join(','));
                });

                file_frame.open();
            });

            $(document).on('click', '.jjwz-clear-gallery-images', function(e) {
                e.preventDefault();
                $('#_jjwz_gallery_images').val('');
                $('.jjwz-gallery-thumbs-preview').html('<span style="color:#718096;font-size:13px;align-self:center;margin:auto;">No images selected yet. Click button above to add.</span>');
            });
        });
        JS;
    }
}

/* ─── class-seo-schema.php placeholder to make autoloader happy ─────────── */
