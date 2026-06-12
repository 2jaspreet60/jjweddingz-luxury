<?php
/**
 * class-options-panel.php — JJW Core Global Settings Panel
 *
 * Provides a premium, tabbed settings experience:
 * - Tab 1: Branding & Contact
 * - Tab 2: Branches (Dynamic Repeater)
 * - Tab 3: Social Media (Dynamic Repeater)
 * - Tab 4: Watermark Settings
 * - Tab 5: WhatsApp Config
 * - Tab 6: Payment APIs
 * - Tab 7: CRM & Leads
 * - Tab 8: Blog Seeder
 *
 * @package JJW_Core
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Options_Panel {

    private string $menu_slug = 'jjwz-core-settings';
    private string $nonce_action = 'jjwz_save_options';

    public function __construct() {
        add_action( 'admin_menu',            [ $this, 'register_menu' ] );
        add_action( 'admin_post_jjwz_save',  [ $this, 'handle_save' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_jjwz_export_leads', [ $this, 'export_leads_csv' ] );
    }

    /* ─── Menu Registration ──────────────────────────────────────────────── */

    public function register_menu(): void {
        add_menu_page(
            'JJ WeddingZ Settings',
            'JJ WeddingZ',
            'manage_options',
            $this->menu_slug,
            [ $this, 'render_page' ],
            'dashicons-camera',
            20
        );

        add_submenu_page( $this->menu_slug, 'Global Settings',  'Global Settings',  'manage_options', $this->menu_slug, [ $this, 'render_page' ] );
        add_submenu_page( $this->menu_slug, 'CRM & Leads',      'CRM & Leads',      'manage_options', $this->menu_slug . '-crm',      [ $this, 'render_page' ] );
        add_submenu_page( $this->menu_slug, 'Blog Seeder',      'Blog Seeder',      'manage_options', $this->menu_slug . '-seeder',   [ $this, 'render_page' ] );
    }

    /* ─── Asset Enqueue ──────────────────────────────────────────────────── */

    public function enqueue_assets( string $hook ): void {
        if ( strpos( $hook, $this->menu_slug ) === false && strpos( $hook, 'jjwz' ) === false ) { return; }

        wp_enqueue_media(); // Enqueue WordPress Media Uploader

        wp_enqueue_style(
            'jjwz-admin-panel',
            JJWZ_CORE_URL . 'assets/admin/options-panel.css',
            [],
            JJWZ_CORE_VERSION
        );

        wp_add_inline_script( 'jquery', $this->get_admin_js() );
    }

    /* ─── Render Page ────────────────────────────────────────────────────── */

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) { return; }

        $tab = sanitize_key( $_GET['jjwz_tab'] ?? 'brand' );

        // Show save notification
        if ( isset( $_GET['jjwz_saved'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p><strong>✅ JJ WeddingZ Settings saved successfully.</strong></p></div>';
        }
        ?>
        <div class="wrap jjwz-admin-wrap">
            <div class="jjwz-admin-header">
                <div class="jjwz-admin-header__logo">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#c8a46a" stroke-width="1.5"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <div>
                        <h1 class="jjwz-admin-header__title">JJ WeddingZ Photography</h1>
                        <p class="jjwz-admin-header__sub">Global Settings Panel — Version <?php echo esc_html( JJWZ_CORE_VERSION ); ?></p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <nav class="jjwz-tabs" aria-label="Settings tabs">
                <?php
                $tabs = [
                    'brand'     => '🏠 Branding & Contact',
                    'branches'  => '🏢 Branches',
                    'social'    => '🔗 Social Media',
                    'watermark' => '🖼️ Watermark',
                    'whatsapp'  => '💬 WhatsApp Config',
                    'payments'  => '💳 Payment APIs',
                    'crm'       => '📊 CRM & Leads',
                    'seeder'    => '📝 Blog Seeder',
                ];
                foreach ( $tabs as $slug => $label ) :
                    $active = ( $tab === $slug ) ? ' jjwz-tab--active' : '';
                    $url    = admin_url( 'admin.php?page=' . $this->menu_slug . '&jjwz_tab=' . $slug );
                ?>
                <a href="<?php echo esc_url( $url ); ?>" class="jjwz-tab<?php echo $active; ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
                <?php endforeach; ?>
            </nav>

            <div class="jjwz-panel">
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="jjwz-form">
                    <?php wp_nonce_field( $this->nonce_action, 'jjwz_nonce' ); ?>
                    <input type="hidden" name="action" value="jjwz_save">
                    <input type="hidden" name="jjwz_tab" value="<?php echo esc_attr( $tab ); ?>">

                    <?php
                    switch ( $tab ) {
                        case 'brand':     $this->render_brand_tab();     break;
                        case 'branches':  $this->render_branches_tab();  break;
                        case 'social':    $this->render_social_tab();    break;
                        case 'watermark': $this->render_watermark_tab(); break;
                        case 'whatsapp':  $this->render_whatsapp_tab();  break;
                        case 'payments':  $this->render_payments_tab();  break;
                        case 'crm':       $this->render_crm_tab();       break;
                        case 'seeder':    $this->render_seeder_tab();    break;
                    }
                    ?>

                    <?php if ( $tab !== 'crm' && $tab !== 'seeder' ) : ?>
                    <div class="jjwz-form-footer">
                        <button type="submit" class="button button-primary jjwz-save-btn">
                            💾 Save Settings
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php
    }

    /* ─── BRANDING & CONTACT TAB ─────────────────────────────────────────── */

    private function render_brand_tab(): void {
        $branding_fields = [
            'jjw_logo'        => 'Logo',
            'jjw_logo_dark'   => 'Dark Logo',
            'jjw_logo_light'  => 'Light Logo',
            'jjw_logo_mobile' => 'Mobile Logo',
            'jjw_favicon'     => 'Favicon',
        ];

        $contact_fields = [
            'jjw_primary_phone'      => [ 'label' => 'Primary Phone Number',   'type' => 'text', 'placeholder' => '+91 98765 43210' ],
            'jjw_secondary_phone'    => [ 'label' => 'Secondary Phone Number', 'type' => 'text', 'placeholder' => '+91 98765 43210' ],
            'jjw_primary_whatsapp'   => [ 'label' => 'Primary WhatsApp',       'type' => 'text', 'placeholder' => '919876543210' ],
            'jjw_secondary_whatsapp' => [ 'label' => 'Secondary WhatsApp',     'type' => 'text', 'placeholder' => '919876543210' ],
            'jjw_email'              => [ 'label' => 'Primary Email',          'type' => 'email', 'placeholder' => 'info@jjweddingz.com' ],
            'jjw_support_email'      => [ 'label' => 'Support Email',          'type' => 'email', 'placeholder' => 'support@jjweddingz.com' ],
            'jjwz_copyright_text'    => [ 'label' => 'Footer Copyright Text',   'type' => 'text', 'placeholder' => '© 2026 JJ WeddingZ Photography. All Rights Reserved.' ],
        ];
        ?>
        <div class="jjwz-tab-content">
            <h2 class="jjwz-section-title">🖼️ Branding Assets</h2>
            <p class="jjwz-section-desc">Upload logos and favicon for use across the site templates.</p>
            <div class="jjwz-fields-grid" style="margin-bottom: 2rem;">
                <?php foreach ( $branding_fields as $key => $label ) : ?>
                <div class="jjwz-field-group">
                    <label class="jjwz-label"><?php echo esc_html( $label ); ?></label>
                    <div class="jjwz-media-upload" style="display:flex; gap:10px;">
                        <input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"
                               value="<?php echo esc_attr( get_option( $key, '' ) ); ?>" class="jjwz-input media-url" style="flex:1;">
                        <button type="button" class="button jjwz-media-upload-btn" data-target="<?php echo esc_attr( $key ); ?>">Choose File</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <h2 class="jjwz-section-title">📞 Primary Contact & Global Details</h2>
            <p class="jjwz-section-desc">Set contact phone numbers, emails, and footer details.</p>
            <div class="jjwz-fields-grid">
                <?php foreach ( $contact_fields as $key => $field ) : ?>
                <div class="jjwz-field-group">
                    <label for="<?php echo esc_attr( $key ); ?>" class="jjwz-label"><?php echo esc_html( $field['label'] ); ?></label>
                    <input type="<?php echo esc_attr( $field['type'] ); ?>"
                           id="<?php echo esc_attr( $key ); ?>"
                           name="<?php echo esc_attr( $key ); ?>"
                           value="<?php echo esc_attr( get_option( $key, '' ) ); ?>"
                           placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>"
                           class="jjwz-input">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /* ─── BRANCHES TAB (DYNAMIC REPEATER) ────────────────────────────────── */

    private function render_branches_tab(): void {
        $branches_raw = get_option( 'jjw_branches', '[]' );
        $branches = json_decode( $branches_raw, true ) ?: [];
        ?>
        <div class="jjwz-tab-content">
            <h2 class="jjwz-section-title">🏢 Dynamic Studio Branches</h2>
            <p class="jjwz-section-desc">Manage physical office branches. Drag/sort or add unlimited location instances.</p>

            <input type="hidden" name="jjw_branches" id="jjw-branches-data" value="<?php echo esc_attr( $branches_raw ); ?>">

            <div class="jjw-repeater-container" id="branches-repeater-container">
                <table class="wp-list-table widefat fixed striped" style="margin-bottom:1.5rem;">
                    <thead>
                        <tr>
                            <th style="width:20%;">Branch City</th>
                            <th style="width:30%;">Address</th>
                            <th>Contact Phone</th>
                            <th>WhatsApp</th>
                            <th>Email</th>
                            <th>Maps URL</th>
                            <th style="width:80px;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="branches-tbody">
                        <?php if ( ! empty( $branches ) ) : ?>
                            <?php foreach ( $branches as $i => $b ) : ?>
                            <tr class="repeater-row">
                                <td><input type="text" class="row-city jjwz-input" value="<?php echo esc_attr( $b['name'] ?? '' ); ?>" required></td>
                                <td><textarea class="row-address jjwz-input" rows="2"><?php echo esc_textarea( $b['address'] ?? '' ); ?></textarea></td>
                                <td><input type="text" class="row-phone jjwz-input" value="<?php echo esc_attr( $b['phone'] ?? '' ); ?>"></td>
                                <td><input type="text" class="row-whatsapp jjwz-input" value="<?php echo esc_attr( $b['whatsapp'] ?? '' ); ?>"></td>
                                <td><input type="email" class="row-email jjwz-input" value="<?php echo esc_attr( $b['email'] ?? '' ); ?>"></td>
                                <td><input type="url" class="row-maps jjwz-input" value="<?php echo esc_attr( $b['maps_url'] ?? '' ); ?>"></td>
                                <td style="text-align:center;"><button type="button" class="button button-link-delete delete-row-btn">Remove</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="button" class="button button-secondary" id="add-branch-btn">＋ Add New Branch</button>
            </div>
        </div>
        <?php
    }

    /* ─── SOCIAL MEDIA TAB (DYNAMIC REPEATER) ────────────────────────────── */

    private function render_social_tab(): void {
        $social_raw = get_option( 'jjw_social_media', '[]' );
        $socials = json_decode( $social_raw, true ) ?: [];
        ?>
        <div class="jjwz-tab-content">
            <h2 class="jjwz-section-title">🔗 Dynamic Social Media Channels</h2>
            <p class="jjwz-section-desc">Add unlimited social media networks, upload custom icons, and drag to change sorting order.</p>

            <input type="hidden" name="jjw_social_media" id="jjw-social-data" value="<?php echo esc_attr( $social_raw ); ?>">

            <div class="jjw-repeater-container" id="social-repeater-container">
                <table class="wp-list-table widefat fixed striped" style="margin-bottom:1.5rem;">
                    <thead>
                        <tr>
                            <th style="width:20%;">Social Name</th>
                            <th>Profile URL</th>
                            <th style="width:30%;">Icon Asset</th>
                            <th style="width:80px;text-align:center;">Sort</th>
                            <th style="width:100px;text-align:center;">Status</th>
                            <th style="width:80px;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="social-tbody">
                        <?php if ( ! empty( $socials ) ) : ?>
                            <?php foreach ( $socials as $i => $s ) : ?>
                            <tr class="repeater-row">
                                <td>
                                    <select class="row-name jjwz-select" style="width:100%;">
                                        <?php
                                        $opts = ['Instagram', 'Facebook', 'YouTube', 'Pinterest', 'X', 'Flickr', 'LinkedIn', 'Threads', 'Other'];
                                        foreach ($opts as $o) {
                                            $selected = ($s['name'] === $o) ? 'selected' : '';
                                            echo '<option value="'.esc_attr($o).'" '.$selected.'>'.esc_html($o).'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><input type="url" class="row-url jjwz-input" value="<?php echo esc_attr( $s['url'] ?? '' ); ?>" required></td>
                                <td>
                                    <div style="display:flex; gap:5px;">
                                        <input type="text" class="row-icon jjwz-input media-url" value="<?php echo esc_attr( $s['icon_url'] ?? '' ); ?>" style="flex:1;">
                                        <button type="button" class="button jjwz-media-upload-btn">Upload</button>
                                    </div>
                                </td>
                                <td><input type="number" class="row-sort jjwz-input" value="<?php echo esc_attr( $s['sort_order'] ?? 0 ); ?>" style="text-align:center;"></td>
                                <td>
                                    <select class="row-enabled jjwz-select" style="width:100%;">
                                        <option value="1" <?php selected($s['enabled'] ?? '1', '1'); ?>>Enabled</option>
                                        <option value="0" <?php selected($s['enabled'] ?? '1', '0'); ?>>Disabled</option>
                                    </select>
                                </td>
                                <td style="text-align:center;"><button type="button" class="button button-link-delete delete-row-btn">Remove</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="button" class="button button-secondary" id="add-social-btn">＋ Add Social Network</button>
            </div>
        </div>
        <?php
    }

    /* ─── WATERMARK SETTINGS TAB ─────────────────────────────────────────── */

    private function render_watermark_tab(): void {
        $enable   = get_option( 'jjw_watermark_enable', '0' );
        $text     = get_option( 'jjw_watermark_text', '© JJ WeddingZ Photography' );
        $opacity  = get_option( 'jjw_watermark_opacity', '0.15' );
        $position = get_option( 'jjw_watermark_position', 'bottom-right' );
        ?>
        <div class="jjwz-tab-content">
            <h2 class="jjwz-section-title">🖼️ Automated Image Watermarking</h2>
            <p class="jjwz-section-desc">Protect creative assets by overlaying custom watermark texts onto uploaded portfolio files.</p>

            <div class="jjwz-fields-grid">
                <div class="jjwz-field-group">
                    <label class="jjwz-label">Enable Watermarking</label>
                    <div class="jjwz-toggle-wrap">
                        <label class="jjwz-toggle">
                            <input type="checkbox" name="jjw_watermark_enable" value="1" <?php checked( $enable, '1' ); ?>>
                            <span class="jjwz-toggle__slider"></span>
                        </label>
                        <span>Apply text watermarks automatically to all new media library uploads.</span>
                    </div>
                </div>

                <div class="jjwz-field-group">
                    <label for="jjw_watermark_text" class="jjwz-label">Watermark Text</label>
                    <input type="text" id="jjw_watermark_text" name="jjw_watermark_text" value="<?php echo esc_attr( $text ); ?>" class="jjwz-input">
                </div>

                <div class="jjwz-field-group">
                    <label for="jjw_watermark_opacity" class="jjwz-label">Watermark Opacity</label>
                    <select id="jjw_watermark_opacity" name="jjw_watermark_opacity" class="jjwz-select">
                        <option value="0.05" <?php selected( $opacity, '0.05' ); ?>>5% (Very Subtle)</option>
                        <option value="0.10" <?php selected( $opacity, '0.10' ); ?>>10%</option>
                        <option value="0.15" <?php selected( $opacity, '0.15' ); ?>>15% (Default)</option>
                        <option value="0.25" <?php selected( $opacity, '0.25' ); ?>>25%</option>
                        <option value="0.40" <?php selected( $opacity, '0.40' ); ?>>40%</option>
                        <option value="0.60" <?php selected( $opacity, '0.60' ); ?>>60%</option>
                    </select>
                </div>

                <div class="jjwz-field-group">
                    <label for="jjw_watermark_position" class="jjwz-label">Watermark Placement</label>
                    <select id="jjw_watermark_position" name="jjw_watermark_position" class="jjwz-select">
                        <option value="bottom-right" <?php selected( $position, 'bottom-right' ); ?>>Bottom Right</option>
                        <option value="bottom-left" <?php selected( $position, 'bottom-left' ); ?>>Bottom Left</option>
                        <option value="top-right" <?php selected( $position, 'top-right' ); ?>>Top Right</option>
                        <option value="top-left" <?php selected( $position, 'top-left' ); ?>>Top Left</option>
                        <option value="center" <?php selected( $position, 'center' ); ?>>Center</option>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── WHATSAPP CONFIG TAB ────────────────────────────────────────────── */

    private function render_whatsapp_tab(): void {
        $mode = get_option( 'jjwz_whatsapp_mode', 'simple' );
        ?>
        <div class="jjwz-tab-content">
            <h2 class="jjwz-section-title">💬 WhatsApp Routing Config</h2>
            <p class="jjwz-section-desc">Choose between a simple direct wa.me link or a dynamic WhatsApp API engine.</p>
            <div class="jjwz-field-group">
                <label class="jjwz-label">WhatsApp Mode Switcher</label>
                <div class="jjwz-mode-switcher">
                    <label class="jjwz-mode-option <?php echo $mode === 'simple' ? 'is-selected' : ''; ?>">
                        <input type="radio" name="jjwz_whatsapp_mode" value="simple" <?php checked( $mode, 'simple' ); ?>>
                        <div class="jjwz-mode-option__content">
                            <strong>🔗 State A: Simple Link</strong>
                            <span>Renders all booking/chat buttons as a direct <code>wa.me/</code> hyperlink.</span>
                        </div>
                    </label>
                    <label class="jjwz-mode-option <?php echo $mode === 'api' ? 'is-selected' : ''; ?>">
                        <input type="radio" name="jjwz_whatsapp_mode" value="api" <?php checked( $mode, 'api' ); ?>>
                        <div class="jjwz-mode-option__content">
                            <strong>⚙️ State B: API Automated Engine</strong>
                            <span>Connects to a WhatsApp Business API endpoint for automated message routing.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- State A: Simple -->
            <div id="wa-simple-fields" class="jjwz-conditional-fields" <?php echo $mode !== 'simple' ? 'style="display:none"' : ''; ?>>
                <div class="jjwz-field-group">
                    <label for="jjwz_whatsapp_number" class="jjwz-label">WhatsApp Phone Number <span class="jjwz-required">*</span></label>
                    <input type="text" id="jjwz_whatsapp_number" name="jjwz_whatsapp_number"
                           value="<?php echo esc_attr( get_option( 'jjwz_whatsapp_number', '' ) ); ?>"
                           placeholder="919876543210 (country code + number, no spaces or +)"
                           class="jjwz-input">
                    <p class="jjwz-field-hint">Include country code without + sign. Example: 919876543210 for India +91.</p>
                </div>
            </div>

            <!-- State B: API -->
            <div id="wa-api-fields" class="jjwz-conditional-fields" <?php echo $mode !== 'api' ? 'style="display:none"' : ''; ?>>
                <div class="jjwz-fields-grid">
                    <div class="jjwz-field-group">
                        <label for="jjwz_wa_api_endpoint" class="jjwz-label">API Endpoint URL</label>
                        <input type="url" id="jjwz_wa_api_endpoint" name="jjwz_wa_api_endpoint"
                               value="<?php echo esc_attr( get_option( 'jjwz_wa_api_endpoint', '' ) ); ?>"
                               placeholder="https://api.yourwhatsapp.com/v1/messages"
                               class="jjwz-input">
                    </div>
                    <div class="jjwz-field-group">
                        <label for="jjwz_wa_bearer_token" class="jjwz-label">Bearer Authentication Token</label>
                        <input type="password" id="jjwz_wa_bearer_token" name="jjwz_wa_bearer_token"
                               value="<?php echo esc_attr( $this->decrypt_option( 'jjwz_wa_bearer_token' ) ); ?>"
                               placeholder="••••••••••••••••"
                               class="jjwz-input jjwz-input--secure">
                    </div>
                </div>
                <div class="jjwz-field-group" style="margin-top:1rem;">
                    <label for="jjwz_wa_json_payload" class="jjwz-label">JSON Payload Template</label>
                    <textarea id="jjwz_wa_json_payload" name="jjwz_wa_json_payload"
                              class="jjwz-textarea" rows="6"
                              placeholder='{"to":"{{phone}}","type":"template","template":{"name":"booking_inquiry","language":{"code":"en"},"components":[]}}'
                              ><?php echo esc_textarea( get_option( 'jjwz_wa_json_payload', '' ) ); ?></textarea>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── PAYMENTS TAB ───────────────────────────────────────────────────── */

    private function render_payments_tab(): void {
        ?>
        <div class="jjwz-tab-content">
            <h2 class="jjwz-section-title">💳 Payment API Configuration</h2>
            <p class="jjwz-section-desc">Securely store your payment gateway credentials.</p>

            <div class="jjwz-payment-section">
                <div class="jjwz-payment-section__header">
                    <strong style="font-size:16px;">Razorpay Gateway Settings</strong>
                </div>
                <div class="jjwz-fields-grid">
                    <div class="jjwz-field-group">
                        <label for="jjwz_razorpay_key_id" class="jjwz-label">Razorpay Key ID (Public)</label>
                        <input type="text" id="jjwz_razorpay_key_id" name="jjwz_razorpay_key_id"
                               value="<?php echo esc_attr( get_option( 'jjwz_razorpay_key_id', '' ) ); ?>"
                               placeholder="rzp_live_xxxxxxxxxxxxxx" class="jjwz-input">
                    </div>
                    <div class="jjwz-field-group">
                        <label for="jjwz_razorpay_key_secret" class="jjwz-label">Razorpay Key Secret</label>
                        <input type="password" id="jjwz_razorpay_key_secret" name="jjwz_razorpay_key_secret"
                               value="<?php echo esc_attr( $this->decrypt_option( 'jjwz_razorpay_key_secret' ) ); ?>"
                               placeholder="••••••••••••••••••••••" class="jjwz-input jjwz-input--secure">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── CRM TAB ────────────────────────────────────────────────────────── */

    private function render_crm_tab(): void {
        global $wpdb;
        $table  = $wpdb->prefix . 'jjwz_leads';
        $leads  = [];
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table ) {
            $leads  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 100", ARRAY_A );
        }
        ?>
        <div class="jjwz-tab-content">
            <div class="jjwz-crm-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h2 class="jjwz-section-title" style="margin:0;">📊 CRM & Lead Management</h2>
                <a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=jjwz_export_leads&nonce=' . wp_create_nonce( 'jjwz_export_leads' ) ) ); ?>"
                   class="button button-primary">📥 Export to CSV</a>
            </div>

            <div class="jjwz-leads-table-wrap">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>Event Date</th>
                            <th>Message</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $leads ) ) : ?>
                            <?php foreach ( $leads as $lead ) : ?>
                            <tr>
                                <td><?php echo esc_html( date( 'M j, Y g:i A', strtotime( $lead['created_at'] ) ) ); ?></td>
                                <td><?php echo esc_html( $lead['name'] ?? '—' ); ?></td>
                                <td><?php echo esc_html( $lead['email'] ?? '—' ); ?></td>
                                <td><?php echo esc_html( $lead['phone'] ?? '—' ); ?></td>
                                <td><?php echo esc_html( $lead['service'] ?? '—' ); ?></td>
                                <td><?php echo esc_html( $lead['event_date'] ?? '—' ); ?></td>
                                <td><?php echo wp_kses_post( wp_trim_words( $lead['message'] ?? '', 12, '…' ) ); ?></td>
                                <td><?php echo esc_html( $lead['source'] ?? 'Website' ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="8" style="text-align:center;padding:2rem;color:#888;">No leads yet. Contact form submissions will appear here.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /* ─── SEEDER TAB ─────────────────────────────────────────────────────── */

    private function render_seeder_tab(): void {
        $seeded  = get_option( 'jjwz_blog_seeded', false );
        $count   = (int) wp_count_posts( 'post' )->publish;
        ?>
        <div class="jjwz-tab-content">
            <h2 class="jjwz-section-title">📝 50-Post SEO Blog Seeder</h2>
            <p class="jjwz-section-desc">
                This tool programmatically inserts 50 high-intent SEO blog posts into WordPress with proper categories, focus keywords, and structured content.
            </p>

            <div class="jjwz-seeder-status" style="margin: 1rem 0; padding:1rem; background:#fafafa; border:1px solid #eee; border-radius:4px;">
                <div><strong>Seed Status:</strong> <?php echo $seeded ? '✅ Seeded' : '⏳ Not yet seeded'; ?></div>
                <div><strong>Current Published Posts:</strong> <?php echo $count; ?></div>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'jjwz_run_seeder', 'jjwz_seeder_nonce' ); ?>
                <input type="hidden" name="action" value="jjwz_run_seeder">
                <button type="submit" class="button button-primary" style="background:#c8a46a; border-color:#c8a46a;">
                    🚀 Run Blog Seeder Now
                </button>
            </form>
        </div>
        <?php
    }

    /* ─── Handle Save ────────────────────────────────────────────────────── */

    public function handle_save(): void {
        if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Unauthorized' ); }
        if ( ! wp_verify_nonce( $_POST['jjwz_nonce'] ?? '', $this->nonce_action ) ) { wp_die( 'Invalid nonce' ); }

        $tab = sanitize_key( $_POST['jjwz_tab'] ?? 'brand' );

        $plain_text_fields = [
            'jjw_logo', 'jjw_logo_dark', 'jjw_logo_light', 'jjw_logo_mobile', 'jjw_favicon',
            'jjw_primary_phone', 'jjw_secondary_phone', 'jjw_primary_whatsapp', 'jjw_secondary_whatsapp',
            'jjw_email', 'jjw_support_email', 'jjwz_copyright_text',
            'jjw_watermark_enable', 'jjw_watermark_text', 'jjw_watermark_opacity', 'jjw_watermark_position',
            'jjwz_whatsapp_number', 'jjwz_whatsapp_mode', 'jjwz_wa_api_endpoint', 'jjwz_wa_json_payload',
            'jjwz_razorpay_key_id',
        ];

        $encrypted_fields = [
            'jjwz_wa_bearer_token', 'jjwz_razorpay_key_secret',
        ];

        foreach ( $plain_text_fields as $f ) {
            if ( isset( $_POST[ $f ] ) ) {
                update_option( $f, sanitize_text_field( $_POST[ $f ] ) );
            } else {
                if ( $f === 'jjw_watermark_enable' ) {
                    update_option( $f, '0' );
                }
            }
        }

        foreach ( $encrypted_fields as $f ) {
            if ( isset( $_POST[ $f ] ) && ! empty( $_POST[ $f ] ) ) {
                update_option( $f, $this->encrypt_value( sanitize_text_field( $_POST[ $f ] ) ) );
            }
        }

        // Save dynamic repeaters
        if ( isset( $_POST['jjw_branches'] ) ) {
            $branches_raw = wp_unslash( $_POST['jjw_branches'] );
            $branches_arr = json_decode( $branches_raw, true );
            $sanitized_branches = [];
            if ( is_array( $branches_arr ) ) {
                foreach ( $branches_arr as $row ) {
                    $sanitized_branches[] = [
                        'name'     => sanitize_text_field( $row['name'] ?? '' ),
                        'address'  => sanitize_textarea_field( $row['address'] ?? '' ),
                        'phone'    => sanitize_text_field( $row['phone'] ?? '' ),
                        'whatsapp' => sanitize_text_field( $row['whatsapp'] ?? '' ),
                        'email'    => sanitize_email( $row['email'] ?? '' ),
                        'maps_url' => esc_url_raw( $row['maps_url'] ?? '' ),
                    ];
                }
            }
            update_option( 'jjw_branches', wp_json_encode( $sanitized_branches ) );
        }

        if ( isset( $_POST['jjw_social_media'] ) ) {
            $social_raw = wp_unslash( $_POST['jjw_social_media'] );
            $social_arr = json_decode( $social_raw, true );
            $sanitized_social = [];
            if ( is_array( $social_arr ) ) {
                foreach ( $social_arr as $row ) {
                    $sanitized_social[] = [
                        'name'       => sanitize_text_field( $row['name'] ?? '' ),
                        'url'        => esc_url_raw( $row['url'] ?? '' ),
                        'icon_url'   => esc_url_raw( $row['icon_url'] ?? '' ),
                        'sort_order' => (int) ($row['sort_order'] ?? 0),
                        'enabled'    => sanitize_key( $row['enabled'] ?? '1' ),
                    ];
                }
            }
            update_option( 'jjw_social_media', wp_json_encode( $sanitized_social ) );
        }

        wp_safe_redirect( admin_url( 'admin.php?page=' . $this->menu_slug . '&jjwz_tab=' . $tab . '&jjwz_saved=1' ) );
        exit;
    }

    /* ─── CSV Export ─────────────────────────────────────────────────────── */

    public function export_leads_csv(): void {
        if ( ! wp_verify_nonce( $_GET['nonce'] ?? '', 'jjwz_export_leads' ) || ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_leads';
        $leads = [];
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table ) {
            $leads = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A );
        }

        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="jjwz-leads-' . gmdate( 'Y-m-d' ) . '.csv"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        fprintf( $out, chr(0xEF).chr(0xBB).chr(0xBF) ); // UTF-8 BOM

        fputcsv( $out, [ 'Date', 'Name', 'Email', 'Phone', 'Service', 'Event Date', 'Message', 'Source' ] );

        foreach ( $leads as $lead ) {
            fputcsv( $out, [
                $lead['created_at']  ?? '',
                $lead['name']        ?? '',
                $lead['email']       ?? '',
                $lead['phone']       ?? '',
                $lead['service']     ?? '',
                $lead['event_date']  ?? '',
                $lead['message']     ?? '',
                $lead['source']      ?? 'Website',
            ] );
        }

        fclose( $out );
        exit;
    }

    /* ─── Encryption Helpers ─────────────────────────────────────────────── */

    private function encrypt_value( string $value ): string {
        if ( empty( $value ) ) { return ''; }
        $key       = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'jjwz-fallback-key-2026';
        $iv_length = openssl_cipher_iv_length( 'AES-256-CBC' );
        $iv        = openssl_random_pseudo_bytes( $iv_length );
        $encrypted = openssl_encrypt( $value, 'AES-256-CBC', substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        return base64_encode( $iv . '::' . $encrypted );
    }

    private function decrypt_option( string $option_key ): string {
        $stored = get_option( $option_key, '' );
        if ( empty( $stored ) ) { return ''; }
        try {
            $key       = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'jjwz-fallback-key-2026';
            $decoded   = base64_decode( $stored );
            $parts     = explode( '::', $decoded, 2 );
            if ( count( $parts ) !== 2 ) { return ''; }
            [ $iv, $encrypted ] = $parts;
            return (string) openssl_decrypt( $encrypted, 'AES-256-CBC', substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        } catch ( \Exception $e ) {
            return '';
        }
    }

    /* ─── Inline Admin JS ────────────────────────────────────────────────── */

    private function get_admin_js(): string {
        return <<<JS
        document.addEventListener('DOMContentLoaded', function() {
            // WordPress Media Uploader generic click handler
            jQuery('body').on('click', '.jjwz-media-upload-btn', function(e) {
                e.preventDefault();
                var button = jQuery(this);
                var input = button.siblings('input.media-url');
                if (!input.length) {
                    var targetId = button.data('target');
                    input = jQuery('#' + targetId);
                }
                var custom_uploader = wp.media({
                    title: 'Select Asset',
                    button: { text: 'Use Asset' },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    input.val(attachment.url).trigger('change');
                }).open();
            });

            // WhatsApp conditional toggling
            const modeRadios = document.querySelectorAll('input[name="jjwz_whatsapp_mode"]');
            const simpleFields = document.getElementById('wa-simple-fields');
            const apiFields = document.getElementById('wa-api-fields');

            if (modeRadios.length) {
                modeRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'simple') {
                            if (simpleFields) simpleFields.style.display = '';
                            if (apiFields) apiFields.style.display = 'none';
                        } else {
                            if (simpleFields) simpleFields.style.display = 'none';
                            if (apiFields) apiFields.style.display = '';
                        }
                        document.querySelectorAll('.jjwz-mode-option').forEach(opt => opt.classList.remove('is-selected'));
                        this.closest('.jjwz-mode-option')?.classList.add('is-selected');
                    });
                });
            }

            // --- Branches Repeater JS ---
            const addBranchBtn = document.getElementById('add-branch-btn');
            const branchesTbody = document.getElementById('branches-tbody');
            const branchesDataInput = document.getElementById('jjw-branches-data');

            function serializeBranches() {
                if (!branchesTbody) return;
                const rows = branchesTbody.querySelectorAll('.repeater-row');
                const data = [];
                rows.forEach(row => {
                    data.push({
                        name: row.querySelector('.row-city').value,
                        address: row.querySelector('.row-address').value,
                        phone: row.querySelector('.row-phone').value,
                        whatsapp: row.querySelector('.row-whatsapp').value,
                        email: row.querySelector('.row-email').value,
                        maps_url: row.querySelector('.row-maps').value,
                    });
                });
                branchesDataInput.value = JSON.stringify(data);
            }

            if (addBranchBtn && branchesTbody) {
                addBranchBtn.addEventListener('click', function() {
                    const rowHtml = `
                    <tr class="repeater-row">
                        <td><input type="text" class="row-city jjwz-input" placeholder="e.g. Amritsar" required></td>
                        <td><textarea class="row-address jjwz-input" rows="2" placeholder="Street details"></textarea></td>
                        <td><input type="text" class="row-phone jjwz-input" placeholder="+91 98765 43210"></td>
                        <td><input type="text" class="row-whatsapp jjwz-input" placeholder="919876543210"></td>
                        <td><input type="email" class="row-email jjwz-input" placeholder="email@branch.com"></td>
                        <td><input type="url" class="row-maps jjwz-input" placeholder="Google Maps link"></td>
                        <td style="text-align:center;"><button type="button" class="button button-link-delete delete-row-btn">Remove</button></td>
                    </tr>`;
                    branchesTbody.insertAdjacentHTML('beforeend', rowHtml);
                    serializeBranches();
                });

                branchesTbody.addEventListener('input', serializeBranches);
                branchesTbody.addEventListener('change', serializeBranches);
                branchesTbody.addEventListener('click', function(e) {
                    if (e.target.classList.contains('delete-row-btn')) {
                        e.target.closest('.repeater-row').remove();
                        serializeBranches();
                    }
                });
            }

            // --- Social Media Repeater JS ---
            const addSocialBtn = document.getElementById('add-social-btn');
            const socialTbody = document.getElementById('social-tbody');
            const socialDataInput = document.getElementById('jjw-social-data');

            function serializeSocial() {
                if (!socialTbody) return;
                const rows = socialTbody.querySelectorAll('.repeater-row');
                const data = [];
                rows.forEach(row => {
                    data.push({
                        name: row.querySelector('.row-name').value,
                        url: row.querySelector('.row-url').value,
                        icon_url: row.querySelector('.row-icon').value,
                        sort_order: parseInt(row.querySelector('.row-sort').value) || 0,
                        enabled: row.querySelector('.row-enabled').value,
                    });
                });
                socialDataInput.value = JSON.stringify(data);
            }

            if (addSocialBtn && socialTbody) {
                addSocialBtn.addEventListener('click', function() {
                    const rowHtml = `
                    <tr class="repeater-row">
                        <td>
                            <select class="row-name jjwz-select" style="width:100%;">
                                <option value="Instagram">Instagram</option>
                                <option value="Facebook">Facebook</option>
                                <option value="YouTube">YouTube</option>
                                <option value="Pinterest">Pinterest</option>
                                <option value="X">X</option>
                                <option value="Flickr">Flickr</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Threads">Threads</option>
                                <option value="Other">Other</option>
                            </select>
                        </td>
                        <td><input type="url" class="row-url jjwz-input" placeholder="Profile Link" required></td>
                        <td>
                            <div style="display:flex; gap:5px;">
                                <input type="text" class="row-icon jjwz-input media-url" placeholder="Uploaded Icon URL" style="flex:1;">
                                <button type="button" class="button jjwz-media-upload-btn">Upload</button>
                            </div>
                        </td>
                        <td><input type="number" class="row-sort jjwz-input" value="0" style="text-align:center;"></td>
                        <td>
                            <select class="row-enabled jjwz-select" style="width:100%;">
                                <option value="1">Enabled</option>
                                <option value="0">Disabled</option>
                            </select>
                        </td>
                        <td style="text-align:center;"><button type="button" class="button button-link-delete delete-row-btn">Remove</button></td>
                    </tr>`;
                    socialTbody.insertAdjacentHTML('beforeend', rowHtml);
                    serializeSocial();
                });

                socialTbody.addEventListener('input', serializeSocial);
                socialTbody.addEventListener('change', serializeSocial);
                socialTbody.addEventListener('click', function(e) {
                    if (e.target.classList.contains('delete-row-btn')) {
                        e.target.closest('.repeater-row').remove();
                        serializeSocial();
                    }
                });
            }
        });
        JS;
    }
}
