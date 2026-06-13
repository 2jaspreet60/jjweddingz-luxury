<?php
/**
 * class-service-importer-exporter.php — JJ WeddingZ Service Importer & Exporter Admin Utility
 *
 * @package JJW_Core
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Service_Importer_Exporter {

    private string $parent_slug = 'jjwz-core-settings';
    private string $importer_slug = 'jjwz-service-importer';
    private string $exporter_slug = 'jjwz-service-exporter';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_admin_menus' ], 25 );
        add_action( 'admin_post_jjwz_import_services', [ $this, 'handle_import' ] );
        add_action( 'admin_post_jjwz_export_services', [ $this, 'handle_export' ] );
        add_action( 'admin_post_jjwz_install_default_services', [ $this, 'handle_default_install' ] );
        add_action( 'admin_notices', [ $this, 'show_installer_notice' ] );
    }

    /* ─── Menu Registration ──────────────────────────────────────────────── */

    public function register_admin_menus(): void {
        add_submenu_page(
            $this->parent_slug,
            __( 'Service Importer', 'jjw-core' ),
            __( 'Service Importer', 'jjw-core' ),
            'manage_options',
            $this->importer_slug,
            [ $this, 'render_importer_page' ]
        );

        add_submenu_page(
            $this->parent_slug,
            __( 'Export Services', 'jjw-core' ),
            __( 'Export Services', 'jjw-core' ),
            'manage_options',
            $this->exporter_slug,
            [ $this, 'render_exporter_page' ]
        );
    }

    /* ─── Placeholder Resolution Helper ──────────────────────────────────── */

    public static function resolve_placeholders( string $text, string $city = '' ): string {
        $brand_name = get_option( 'jjw_brand_name', 'JJ WeddingZ' );
        $phone      = get_option( 'jjw_primary_phone', '+91 98765 43210' );
        $email      = get_option( 'jjw_email', 'info@jjweddingz.com' );
        $website    = home_url();

        $replacements = [
            '{brand_name}' => $brand_name,
            '{phone}'      => $phone,
            '{email}'      => $email,
            '{website}'    => $website,
            '{city}'       => $city ?: 'Amritsar',
        ];

        return str_replace( array_keys( $replacements ), array_values( $replacements ), $text );
    }

    /* ─── Show Admin Notice if No Services Exist ─────────────────────────── */

    public function show_installer_notice(): void {
        if ( ! current_user_can( 'manage_options' ) ) { return; }
        
        // Only show if zero services exist
        $count = wp_count_posts( 'jjwz_service' )->publish;
        if ( (int) $count > 0 ) { return; }

        // Prevent notice if dismissed
        if ( get_option( 'jjwz_installer_notice_dismissed' ) ) { return; }

        $install_url = wp_nonce_url( admin_url( 'admin-post.php?action=jjwz_install_default_services' ), 'jjwz_install_default' );
        ?>
        <div class="notice notice-warning is-dismissible jjwz-installer-notice" style="border-left-color: #c9a96e; padding: 12px 20px;">
            <p style="font-size: 14px; margin: 0 0 8px 0;">
                <strong>💍 JJ WeddingZ Services:</strong> No services found in the database. Install the default photography and photoshoot services to initialize your content architecture.
            </p>
            <p style="margin: 0;">
                <a href="<?php echo esc_url( $install_url ); ?>" class="button button-primary" style="background-color: #c9a96e; border-color: #c9a96e; color: #fff; font-weight: 600;">
                    🚀 Install Default JJ WeddingZ Services
                </a>
            </p>
        </div>
        <?php
    }

    /* ─── Handle Default Services Installation ───────────────────────────── */

    public function handle_default_install(): void {
        if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Unauthorized' ); }
        check_admin_referer( 'jjwz_install_default' );

        $seed_file = JJWZ_CORE_DIR . 'assets/seeds/services-seed.json';
        if ( ! file_exists( $seed_file ) ) {
            wp_die( 'Seed file services-seed.json not found in ' . esc_html( $seed_file ) );
        }

        $json_content = file_get_contents( $seed_file );
        $services_data = json_decode( $json_content, true );

        if ( ! is_array( $services_data ) ) {
            wp_die( 'Invalid seed JSON structure.' );
        }

        $imported = $this->import_dataset( $services_data, 'update' );

        update_option( 'jjwz_installer_notice_dismissed', 1 );
        
        wp_safe_redirect( admin_url( 'admin.php?page=' . $this->importer_slug . '&imported=' . $imported ) );
        exit;
    }

    /* ─── Render Importer Dashboard ──────────────────────────────────────── */

    public function render_importer_page(): void {
        $imported = isset( $_GET['imported'] ) ? (int) $_GET['imported'] : -1;
        ?>
        <div class="wrap jjwz-admin-wrap" style="max-width: 800px; margin-top: 20px;">
            <h1 class="wp-heading-inline" style="font-family: Georgia, serif; font-size: 28px; color: #0a0a0a;">📥 Service Importer</h1>
            <hr class="wp-header-end">

            <?php if ( $imported >= 0 ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>✅ Successfully processed <?php echo $imported; ?> service collections with dynamic packages and FAQs.</strong></p>
            </div>
            <?php endif; ?>

            <div class="card" style="background: #fff; border: 1px solid #e8e4dc; border-radius: 8px; padding: 25px; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <h2 style="color: #c9a96e; margin-top: 0; font-family: Georgia, serif;">Upload Service Database File</h2>
                <p style="color: #6b6b6b;">Choose a <code>.json</code> or <code>.csv</code> seed file to import photography services, local SEO overrides, pricing packages, and FAQs.</p>

                <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'jjwz_import_action', 'jjwz_import_nonce' ); ?>
                    <input type="hidden" name="action" value="jjwz_import_services">

                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="import_file">Database File</label></th>
                            <td>
                                <input type="file" name="import_file" id="import_file" accept=".json,.csv" required>
                                <p class="description">Select <code>services-seed.json</code> or <code>services-seed.csv</code>.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="import_mode">Import Behavior</label></th>
                            <td>
                                <select name="import_mode" id="import_mode" style="width: 250px;">
                                    <option value="update">🔄 Update Existing & Create Missing</option>
                                    <option value="skip">🛡️ Skip Duplicates (Only Create Missing)</option>
                                </select>
                                <p class="description">Defines what to do if a service with the same slug already exists.</p>
                            </td>
                        </tr>
                    </table>

                    <p class="submit" style="margin-top: 30px;">
                        <button type="submit" class="button button-primary" style="background: #c9a96e; border-color: #c9a96e; font-weight: 600; padding: 6px 20px; height: auto; font-size: 14px;">
                            🚀 Execute Import
                        </button>
                    </p>
                </form>
            </div>

            <div class="card" style="background: #faf8f5; border: 1px dashed #c9a96e; border-radius: 8px; padding: 25px; margin-top: 30px;">
                <h3 style="margin-top: 0; color: #0a0a0a;">Brand Placeholder Interpolation</h3>
                <p style="color: #6b6b6b; font-size: 13px;">The importer automatically translates the following placeholders using parameters configured in the <strong>JJ WeddingZ Global Settings</strong> panel:</p>
                <code style="display: block; padding: 10px; background: #fff; border: 1px solid #e8e4dc; border-radius: 4px; font-size: 12px; line-height: 1.6;">
                    {brand_name} &rarr; Brand Name (e.g. JJ WeddingZ / The Baby StudioZ)<br>
                    {phone}      &rarr; Contact Number (e.g. +91 98765 43210)<br>
                    {email}      &rarr; Operations Email (e.g. info@jjweddingz.com)<br>
                    {website}    &rarr; Live Website URL (e.g. <?php echo esc_html(home_url()); ?>)<br>
                    {city}       &rarr; Location Override (e.g. Amritsar / Delhi NCR)
                </code>
            </div>
        </div>
        <?php
    }

    /* ─── Render Exporter Dashboard ──────────────────────────────────────── */

    public function render_exporter_page(): void {
        ?>
        <div class="wrap jjwz-admin-wrap" style="max-width: 800px; margin-top: 20px;">
            <h1 class="wp-heading-inline" style="font-family: Georgia, serif; font-size: 28px; color: #0a0a0a;">📤 Export Services</h1>
            <hr class="wp-header-end">

            <div class="card" style="background: #fff; border: 1px solid #e8e4dc; border-radius: 8px; padding: 25px; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <h2 style="color: #c9a96e; margin-top: 0; font-family: Georgia, serif;">Download Service Database</h2>
                <p style="color: #6b6b6b;">Export all currently registered photography services, meta settings, packages, FAQs, and multi-city narratives for backup or site migration.</p>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'jjwz_export_action', 'jjwz_export_nonce' ); ?>
                    <input type="hidden" name="action" value="jjwz_export_services">

                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="export_format">Export Format</label></th>
                            <td>
                                <select name="export_format" id="export_format" style="width: 200px;">
                                    <option value="json">📄 JSON Document (Recommended)</option>
                                    <option value="csv">📊 CSV Spreadsheet</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <p class="submit" style="margin-top: 30px;">
                        <button type="submit" class="button button-primary" style="background: #0a0a0a; border-color: #0a0a0a; font-weight: 600; padding: 6px 20px; height: auto; font-size: 14px;">
                            📥 Download Backup File
                        </button>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    /* ─── Importer Handler ───────────────────────────────────────────────── */

    public function handle_import(): void {
        if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Unauthorized' ); }
        check_admin_referer( 'jjwz_import_action', 'jjwz_import_nonce' );

        if ( empty( $_FILES['import_file']['tmp_name'] ) ) {
            wp_die( 'Please choose a valid JSON or CSV file to import.' );
        }

        $file_path = $_FILES['import_file']['tmp_name'];
        $file_name = $_FILES['import_file']['name'];
        $ext = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );
        $mode = sanitize_key( $_POST['import_mode'] ?? 'update' );

        $services_dataset = [];

        if ( $ext === 'json' ) {
            $json_content = file_get_contents( $file_path );
            $services_dataset = json_decode( $json_content, true );
        } elseif ( $ext === 'csv' ) {
            $services_dataset = $this->parse_csv_file( $file_path );
        } else {
            wp_die( 'Unsupported file extension. Only .json and .csv files are supported.' );
        }

        if ( ! is_array( $services_dataset ) || empty( $services_dataset ) ) {
            wp_die( 'The uploaded dataset is empty or invalid.' );
        }

        $count = $this->import_dataset( $services_dataset, $mode );

        wp_safe_redirect( admin_url( 'admin.php?page=' . $this->importer_slug . '&imported=' . $count ) );
        exit;
    }

    /* ─── Parse CSV File into Structured Array ───────────────────────────── */

    private function parse_csv_file( string $path ): array {
        $data = [];
        if ( ( $handle = fopen( $path, 'r' ) ) !== false ) {
            $headers = fgetcsv( $handle );
            while ( ( $row = fgetcsv( $handle ) ) !== false ) {
                if ( count( $headers ) !== count( $row ) ) { continue; }
                $item = array_combine( $headers, $row );
                
                // Construct items matching the JSON schema
                $features = array_filter( array_map( 'trim', explode( "\n", $item['Features List'] ?? '' ) ) );
                $process  = array_filter( array_map( 'trim', explode( "\n", $item['Process Steps'] ?? '' ) ) );
                
                $faqs_amritsar = json_decode( $item['Amritsar FAQs'] ?? '[]', true ) ?: [];
                $faqs_delhi    = json_decode( $item['Delhi FAQs'] ?? '[]', true ) ?: [];
                $packages      = json_decode( $item['Packages JSON'] ?? '[]', true ) ?: [];

                $data[] = [
                    'name'              => $item['Service Name'] ?? '',
                    'slug'              => $item['Slug'] ?? '',
                    'icon'              => $item['Service Icon'] ?? '📸',
                    'brand'             => $item['Brand Context'] ?? 'both',
                    'price'             => $item['Starting Price'] ?? '',
                    'focus_keywords'    => $item['Focus Keywords'] ?? '',
                    'seo_title'         => $item['SEO Title'] ?? '',
                    'meta_description'  => $item['Meta Description'] ?? '',
                    'short_description' => $item['Short Description'] ?? '',
                    'generic_content'   => $item['Full Generic Content'] ?? '',
                    'features'          => $features,
                    'process'           => $process,
                    'packages'          => $packages,
                    'amritsar' => [
                        'seo_title'        => $item['Amritsar Title'] ?? '',
                        'meta_description' => $item['Amritsar Meta Description'] ?? '',
                        'content'          => $item['Amritsar Content'] ?? '',
                        'cta'              => $item['Amritsar CTA'] ?? '',
                        'faqs'             => $faqs_amritsar
                    ],
                    'delhi' => [
                        'seo_title'        => $item['Delhi Title'] ?? '',
                        'meta_description' => $item['Delhi Meta Description'] ?? '',
                        'content'          => $item['Delhi Content'] ?? '',
                        'cta'              => $item['Delhi CTA'] ?? '',
                        'faqs'             => $faqs_delhi
                    ]
                ];
            }
            fclose( $handle );
        }
        return $data;
    }

    /* ─── Execute Import Dataset ─────────────────────────────────────────── */

    private function import_dataset( array $dataset, string $mode ): int {
        $count = 0;

        foreach ( $dataset as $s ) {
            $slug = sanitize_title( $s['slug'] ?? $s['name'] );
            if ( empty( $slug ) ) { continue; }

            // Resolve brand placeholders inside text elements
            $name        = self::resolve_placeholders( $s['name'] );
            $generic_content = self::resolve_placeholders( $s['generic_content'] );
            $short_desc  = self::resolve_placeholders( $s['short_description'] );
            $seo_title   = self::resolve_placeholders( $s['seo_title'] );
            $seo_desc    = self::resolve_placeholders( $s['meta_description'] );
            $keywords    = self::resolve_placeholders( $s['focus_keywords'] );

            // Check if post already exists
            $existing_post = get_page_by_path( $slug, OBJECT, 'jjwz_service' );

            if ( $existing_post && $mode === 'skip' ) {
                continue; // Skip
            }

            $post_arr = [
                'post_title'    => $name,
                'post_name'     => $slug,
                'post_content'  => $generic_content,
                'post_excerpt'  => $short_desc,
                'post_status'   => 'publish',
                'post_type'     => 'jjwz_service',
                'post_author'   => 1,
            ];

            if ( $existing_post ) {
                $post_arr['ID'] = $existing_post->ID;
                $post_id = wp_update_post( $post_arr );
            } else {
                $post_id = wp_insert_post( $post_arr );
            }

            if ( is_wp_error( $post_id ) ) { continue; }

            // Save service metadata
            update_post_meta( $post_id, 'svc_icon', sanitize_text_field( $s['icon'] ?? '📸' ) );
            update_post_meta( $post_id, 'svc_small_icon', sanitize_text_field( $s['small_icon'] ?? ( $s['icon'] ?? '📸' ) ) );
            if ( ! empty( $s['thumbnail'] ) ) {
                update_post_meta( $post_id, 'svc_thumbnail', $s['thumbnail'] );
            }
            update_post_meta( $post_id, 'svc_featured', ! empty( $s['featured'] ) ? '1' : '0' );
            update_post_meta( $post_id, 'svc_display_order', isset( $s['display_order'] ) ? (int) $s['display_order'] : 0 );
            update_post_meta( $post_id, 'svc_brand', sanitize_text_field( $s['brand'] ?? 'both' ) );
            update_post_meta( $post_id, 'svc_starting_price', sanitize_text_field( $s['price'] ?? '' ) );
            update_post_meta( $post_id, 'svc_short_desc', $short_desc );
            update_post_meta( $post_id, 'svc_seo_content', $generic_content );
            update_post_meta( $post_id, 'svc_seo_title', $seo_title );
            update_post_meta( $post_id, 'svc_seo_desc', $seo_desc );
            update_post_meta( $post_id, 'svc_focus_keywords', $keywords );
            
            // Refined fields: category group
            update_post_meta( $post_id, 'svc_category_group', sanitize_text_field( $s['category_group'] ?? 'wedding' ) );

            // Refined fields: Locations link
            $location_ids = [];
            $delhi_loc = get_page_by_path( 'delhi-ncr-studio', OBJECT, 'jjwz_location' );
            if ( ! $delhi_loc ) { $delhi_loc = get_page_by_path( 'delhi', OBJECT, 'jjwz_location' ); }
            $amritsar_loc = get_page_by_path( 'amritsar-studio', OBJECT, 'jjwz_location' );
            if ( ! $amritsar_loc ) { $amritsar_loc = get_page_by_path( 'amritsar', OBJECT, 'jjwz_location' ); }
            if ( $delhi_loc ) { $location_ids[] = $delhi_loc->ID; }
            if ( $amritsar_loc ) { $location_ids[] = $amritsar_loc->ID; }
            update_post_meta( $post_id, 'svc_locations', $location_ids );

            // Refined fields: FAQ repeater
            $faq_repeater_data = [];
            if ( ! empty( $s['faqs'] ) ) {
                $f_idx = 0;
                foreach ( $s['faqs'] as $faq ) {
                    $q = self::resolve_placeholders( $faq['question'] );
                    $a = self::resolve_placeholders( $faq['answer'] );
                    update_post_meta( $post_id, 'svc_faq_repeater_' . $f_idx . '_faq_question', $q );
                    update_post_meta( $post_id, 'svc_faq_repeater_' . $f_idx . '_faq_answer', $a );
                    $faq_repeater_data[] = [
                        'faq_question' => $q,
                        'faq_answer'   => $a,
                    ];
                    $f_idx++;
                }
                update_post_meta( $post_id, 'svc_faq_repeater', $f_idx );
            }

            $features_text = is_array( $s['features'] ) ? implode( "\n", $s['features'] ) : $s['features'];
            $process_text  = is_array( $s['process'] ) ? implode( "\n", $s['process'] ) : $s['process'];
            update_post_meta( $post_id, 'svc_features_list', $features_text );
            update_post_meta( $post_id, 'svc_process_steps', $process_text );

            // Custom highlights - copy first 3 features
            $highlights = array_slice( (array) $s['features'], 0, 3 );
            update_post_meta( $post_id, 'svc_key_highlights', implode( "\n", $highlights ) );

            // City Specific overrides
            $cities = ['amritsar', 'delhi'];
            foreach ( $cities as $city ) {
                if ( isset( $s[ $city ] ) ) {
                    $c_data = $s[ $city ];
                    
                    $c_content = self::resolve_placeholders( $c_data['content'] ?? '', ucwords($city) );
                    $c_seo_t   = self::resolve_placeholders( $c_data['seo_title'] ?? '', ucwords($city) );
                    $c_seo_d   = self::resolve_placeholders( $c_data['meta_description'] ?? '', ucwords($city) );
                    $c_cta     = self::resolve_placeholders( $c_data['cta'] ?? '', ucwords($city) );
                    
                    // Local FAQs array resolution
                    $local_faqs = [];
                    if ( ! empty( $c_data['faqs'] ) ) {
                        foreach ( $c_data['faqs'] as $faq ) {
                            $local_faqs[] = [
                                'question' => self::resolve_placeholders( $faq['question'], ucwords($city) ),
                                'answer'   => self::resolve_placeholders( $faq['answer'], ucwords($city) )
                            ];
                        }
                    }

                    update_post_meta( $post_id, 'svc_' . $city . '_content', $c_content );
                    update_post_meta( $post_id, 'svc_' . $city . '_seo_title', $c_seo_t );
                    update_post_meta( $post_id, 'svc_' . $city . '_meta_desc', $c_seo_d );
                    update_post_meta( $post_id, 'svc_' . $city . '_cta', $c_cta );
                    update_post_meta( $post_id, 'svc_' . $city . '_faqs', wp_json_encode( $local_faqs, JSON_UNESCAPED_UNICODE ) );
                }
            }

            // Sync/Seed FAQs to database CPT jjwz_faq
            $this->seed_service_faqs( $slug, $s['faqs'] ?? [] );

            // Sync/Seed Packages to database CPT jjwz_package
            $package_ids = $this->seed_service_packages( $post_id, $slug, $s['packages'] ?? [] );
            
            // Link packages to the service post meta
            update_post_meta( $post_id, 'svc_packages', $package_ids );

            // Sync ACF Field Groups if active
            if ( function_exists( 'update_field' ) ) {
                update_field( 'svc_icon', $s['icon'] ?? '📸', $post_id );
                update_field( 'svc_small_icon', $s['small_icon'] ?? ( $s['icon'] ?? '📸' ), $post_id );
                if ( ! empty( $s['thumbnail'] ) ) {
                    update_field( 'svc_thumbnail', $s['thumbnail'], $post_id );
                }
                update_field( 'svc_brand', $s['brand'] ?? 'both', $post_id );
                update_field( 'svc_category_group', $s['category_group'] ?? 'wedding', $post_id );
                update_field( 'svc_locations', $location_ids, $post_id );
                update_field( 'svc_faq_repeater', $faq_repeater_data, $post_id );
                update_field( 'svc_featured', ! empty( $s['featured'] ) ? 1 : 0, $post_id );
                update_field( 'svc_display_order', isset( $s['display_order'] ) ? (int) $s['display_order'] : 0, $post_id );
                update_field( 'svc_starting_price', $s['price'] ?? '', $post_id );
                update_field( 'svc_short_desc', $short_desc, $post_id );
                update_field( 'svc_seo_content', $generic_content, $post_id );
                update_field( 'svc_seo_title', $seo_title, $post_id );
                update_field( 'svc_seo_desc', $seo_desc, $post_id );
                update_field( 'svc_focus_keywords', $keywords, $post_id );
                update_field( 'svc_packages', $package_ids, $post_id );
            }

            $count++;
        }

        // Flush rules to ensure new service city urls are picked up immediately
        flush_rewrite_rules();

        return $count;
    }

    /* ─── Seed Service FAQs (jjwz_faq) ───────────────────────────────────── */

    private function seed_service_faqs( string $service_slug, array $faqs ): void {
        // Ensure service category term exists
        $term = get_term_by( 'slug', $service_slug, 'faq_category' );
        if ( ! $term ) {
            $term_data = wp_insert_term( ucwords( str_replace( '-', ' ', $service_slug ) ), 'faq_category', [ 'slug' => $service_slug ] );
            $term_id   = is_wp_error( $term_data ) ? 0 : $term_data['term_id'];
        } else {
            $term_id = $term->term_id;
        }

        foreach ( $faqs as $faq ) {
            $q = self::resolve_placeholders( $faq['question'] );
            $a = self::resolve_placeholders( $faq['answer'] );

            $existing = get_page_by_title( $q, OBJECT, 'jjwz_faq' );
            
            $post_data = [
                'post_title'   => wp_strip_all_tags( $q ),
                'post_content' => wp_kses_post( $a ),
                'post_status'  => 'publish',
                'post_type'    => 'jjwz_faq',
                'menu_order'   => $faq['order'] ?? 0,
            ];

            if ( $existing ) {
                $post_data['ID'] = $existing->ID;
                $post_id = wp_update_post( $post_data );
            } else {
                $post_id = wp_insert_post( $post_data );
            }

            if ( ! is_wp_error( $post_id ) ) {
                if ( $term_id ) {
                    wp_set_object_terms( $post_id, [ $term_id ], 'faq_category' );
                }
                update_post_meta( $post_id, 'faq_question', sanitize_text_field( $q ) );
                update_post_meta( $post_id, 'faq_answer',   wp_kses_post( $a ) );
            }
        }
    }

    /* ─── Seed Service Packages (jjwz_package) ────────────────────────────── */

    private function seed_service_packages( int $service_post_id, string $service_slug, array $packages ): array {
        $package_ids = [];

        foreach ( $packages as $pkg ) {
            $pkg_name = self::resolve_placeholders( $pkg['name'] );
            $pkg_price = sanitize_text_field( $pkg['price'] );
            $pkg_desc = self::resolve_placeholders( $pkg['description'] );
            
            // Unique package title linking to service
            $pkg_title = ucwords( str_replace( '-', ' ', $service_slug ) ) . ' — ' . $pkg_name;
            
            $existing = get_page_by_title( $pkg_title, OBJECT, 'jjwz_package' );

            $features_text = is_array( $pkg['features'] ) ? implode( "\n", $pkg['features'] ) : $pkg['features'];
            $features_text = self::resolve_placeholders( $features_text );

            $post_data = [
                'post_title'   => $pkg_title,
                'post_content' => $pkg_desc,
                'post_status'  => 'publish',
                'post_type'    => 'jjwz_package',
            ];

            if ( $existing ) {
                $post_data['ID'] = $existing->ID;
                $post_id = wp_update_post( $post_data );
            } else {
                $post_id = wp_insert_post( $post_data );
            }

            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, 'package_category', sanitize_text_field( $service_slug ) );
                update_post_meta( $post_id, 'package_price', $pkg_price );
                update_post_meta( $post_id, 'package_description', $pkg_desc );
                update_post_meta( $post_id, 'package_features', $features_text );
                update_post_meta( $post_id, 'package_service', $service_post_id );

                if ( function_exists( 'update_field' ) ) {
                    update_field( 'package_category', $service_slug, $post_id );
                    update_field( 'package_price', $pkg_price, $post_id );
                    update_field( 'package_description', $pkg_desc, $post_id );
                    update_field( 'package_features', $features_text, $post_id );
                    update_field( 'package_service', $service_post_id, $post_id );
                }

                $package_ids[] = $post_id;
            }
        }

        return $package_ids;
    }

    /* ─── Exporter Handler ───────────────────────────────────────────────── */

    public function handle_export(): void {
        if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Unauthorized' ); }
        check_admin_referer( 'jjwz_export_action', 'jjwz_export_nonce' );

        $format = sanitize_key( $_POST['export_format'] ?? 'json' );

        $query = new WP_Query( [
            'post_type'      => 'jjwz_service',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC'
        ] );

        $export_data = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                $slug    = get_post_field( 'post_name', $post_id );

                // Extract features & process arrays
                $features_raw = get_post_meta( $post_id, 'svc_features_list', true );
                $features = array_filter( array_map( 'trim', explode( "\n", $features_raw ) ) );

                $process_raw = get_post_meta( $post_id, 'svc_process_steps', true );
                $process = array_filter( array_map( 'trim', explode( "\n", $process_raw ) ) );

                // Gather related FAQs
                $faq_query = new WP_Query( [
                    'post_type'      => 'jjwz_faq',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'faq_category',
                            'field'    => 'slug',
                            'terms'    => $slug
                        ]
                    ]
                ] );
                $faqs = [];
                if ( $faq_query->have_posts() ) {
                    while ( $faq_query->have_posts() ) {
                        $faq_query->the_post();
                        $faqs[] = [
                            'question' => get_post_meta( get_the_ID(), 'faq_question', true ) ?: get_the_title(),
                            'answer'   => get_post_meta( get_the_ID(), 'faq_answer', true ) ?: get_the_content(),
                            'order'    => get_post_field( 'menu_order', get_the_ID() )
                        ];
                    }
                    wp_reset_postdata();
                }

                // Gather related Packages
                $package_query = new WP_Query( [
                    'post_type'      => 'jjwz_package',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'meta_query'     => [
                        [
                            'key'   => 'package_service',
                            'value' => $post_id
                        ]
                    ]
                ] );
                $packages = [];
                if ( $package_query->have_posts() ) {
                    while ( $package_query->have_posts() ) {
                        $package_query->the_post();
                        $pkg_feats = get_post_meta( get_the_ID(), 'package_features', true );
                        $packages[] = [
                            'name'        => str_replace( ucwords( str_replace( '-', ' ', $slug ) ) . ' — ', '', get_the_title() ),
                            'price'       => get_post_meta( get_the_ID(), 'package_price', true ),
                            'description' => get_post_meta( get_the_ID(), 'package_description', true ) ?: get_the_content(),
                            'features'    => array_filter( array_map( 'trim', explode( "\n", $pkg_feats ) ) )
                        ];
                    }
                    wp_reset_postdata();
                }

                // Gather Amritsar & Delhi Overrides
                $amritsar_faqs = json_decode( get_post_meta( $post_id, 'svc_amritsar_faqs', true ), true ) ?: [];
                $delhi_faqs    = json_decode( get_post_meta( $post_id, 'svc_delhi_faqs', true ), true ) ?: [];

                $export_data[] = [
                    'name'              => get_the_title(),
                    'slug'              => $slug,
                    'icon'              => get_post_meta( $post_id, 'svc_icon', true ) ?: '📸',
                    'small_icon'        => get_post_meta( $post_id, 'svc_small_icon', true ) ?: get_post_meta( $post_id, 'svc_icon', true ) ?: '📸',
                    'thumbnail'         => get_post_meta( $post_id, 'svc_thumbnail', true ),
                    'brand'             => get_post_meta( $post_id, 'svc_brand', true ) ?: 'both',
                    'featured'          => get_post_meta( $post_id, 'svc_featured', true ) === '1',
                    'display_order'     => (int) get_post_meta( $post_id, 'svc_display_order', true ),
                    'category_group'    => get_post_meta( $post_id, 'svc_category_group', true ) ?: 'wedding',
                    'price'             => get_post_meta( $post_id, 'svc_starting_price', true ),
                    'focus_keywords'    => get_post_meta( $post_id, 'svc_focus_keywords', true ),
                    'seo_title'         => get_post_meta( $post_id, 'svc_seo_title', true ),
                    'meta_description'  => get_post_meta( $post_id, 'svc_seo_desc', true ),
                    'short_description' => get_post_meta( $post_id, 'svc_short_desc', true ) ?: get_the_excerpt(),
                    'generic_content'   => get_post_meta( $post_id, 'svc_seo_content', true ) ?: get_the_content(),
                    'features'          => $features,
                    'process'           => $process,
                    'faqs'              => $faqs,
                    'packages'          => $packages,
                    'amritsar' => [
                        'seo_title'        => get_post_meta( $post_id, 'svc_amritsar_seo_title', true ),
                        'meta_description' => get_post_meta( $post_id, 'svc_amritsar_meta_desc', true ),
                        'content'          => get_post_meta( $post_id, 'svc_amritsar_content', true ),
                        'cta'              => get_post_meta( $post_id, 'svc_amritsar_cta', true ),
                        'faqs'             => $amritsar_faqs
                    ],
                    'delhi' => [
                        'seo_title'        => get_post_meta( $post_id, 'svc_delhi_seo_title', true ),
                        'meta_description' => get_post_meta( $post_id, 'svc_delhi_meta_desc', true ),
                        'content'          => get_post_meta( $post_id, 'svc_delhi_content', true ),
                        'cta'              => get_post_meta( $post_id, 'svc_delhi_cta', true ),
                        'faqs'             => $delhi_faqs
                    ]
                ];
            }
            wp_reset_postdata();
        }

        if ( $format === 'json' ) {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=jjw-services-export-' . date( 'Y-m-d' ) . '.json' );
            echo json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
            exit;
        } elseif ( $format === 'csv' ) {
            header( 'Content-Type: text/csv; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=jjw-services-export-' . date( 'Y-m-d' ) . '.csv' );
            
            $csv_headers = [
                'Service Name', 'Slug', 'Service Icon', 'Small Icon', 'Thumbnail Image', 'Brand Context', 
                'Homepage Featured', 'Display Order', 'Category Group', 'Starting Price', 'Focus Keywords', 
                'SEO Title', 'Meta Description', 'Short Description', 'Full Generic Content', 
                'Features List', 'Process Steps', 'Amritsar Title', 'Amritsar Meta Description', 
                'Amritsar Content', 'Amritsar CTA', 'Amritsar FAQs',
                'Delhi Title', 'Delhi Meta Description', 'Delhi Content', 'Delhi CTA', 'Delhi FAQs',
                'Packages JSON'
            ];

            $fp = fopen( 'php://output', 'w' );
            fputcsv( $fp, $csv_headers );

            foreach ( $export_data as $item ) {
                fputcsv( $fp, [
                    $item['name'],
                    $item['slug'],
                    $item['icon'],
                    $item['small_icon'],
                    $item['thumbnail'],
                    $item['brand'],
                    $item['featured'] ? '1' : '0',
                    $item['display_order'],
                    $item['category_group'],
                    $item['price'],
                    $item['focus_keywords'],
                    $item['seo_title'],
                    $item['meta_description'],
                    $item['short_description'],
                    $item['generic_content'],
                    implode( "\n", $item['features'] ),
                    implode( "\n", $item['process'] ),
                    $item['amritsar']['seo_title'],
                    $item['amritsar']['meta_description'],
                    $item['amritsar']['content'],
                    $item['amritsar']['cta'],
                    json_encode( $item['amritsar']['faqs'], JSON_UNESCAPED_UNICODE ),
                    $item['delhi']['seo_title'],
                    $item['delhi']['meta_description'],
                    $item['delhi']['content'],
                    $item['delhi']['cta'],
                    json_encode( $item['delhi']['faqs'], JSON_UNESCAPED_UNICODE ),
                    json_encode( $item['packages'], JSON_UNESCAPED_UNICODE )
                ] );
            }
            fclose( $fp);
            exit;
        }
    }
}
new JJWZ_Service_Importer_Exporter();
