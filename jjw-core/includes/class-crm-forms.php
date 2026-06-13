<?php
/**
 * class-crm-forms.php — Lead Capture, Database Storage & FluentCRM Integration
 *
 * - Creates wp_jjwz_leads custom table on activation
 * - Saves all contact form submissions to the DB
 * - Provides AJAX form handler
 * - Hooks into FluentCRM Free to append contacts + tags
 * - Sends admin notification email on new lead
 *
 * @package JJWeddingZ_Core
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CRM_Forms {

    public function __construct() {
        add_action( 'wp_ajax_nopriv_jjwz_submit_lead', [ $this, 'handle_lead_submission' ] );
        add_action( 'wp_ajax_jjwz_submit_lead',        [ $this, 'handle_lead_submission' ] );
        add_action( 'wp_enqueue_scripts',               [ $this, 'enqueue_form_assets' ] );
        add_action( 'admin_init',                      [ $this, 'maybe_upgrade_db' ] );
    }

    public function maybe_upgrade_db(): void {
        $db_version = get_option( 'jjwz_db_version', '1.0' );
        if ( version_compare( $db_version, '2.0', '<' ) ) {
            self::create_leads_table();
            update_option( 'jjwz_db_version', '2.0' );
        }
    }

    /* ─── Create Database Table ──────────────────────────────────────────── */

    public static function create_leads_table(): void {
        global $wpdb;
        $table      = $wpdb->prefix . 'jjwz_leads';
        $tasks_table = $wpdb->prefix . 'jjwz_tasks';
        $charset    = $wpdb->get_charset_collate();

        // Standard WP format: two spaces between PRIMARY KEY and keys, uppercase field declarations,
        // and one field per line.
        $sql = "CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(150) NOT NULL DEFAULT '',
            email varchar(200) NOT NULL DEFAULT '',
            phone varchar(30) NOT NULL DEFAULT '',
            service varchar(100) NOT NULL DEFAULT '',
            event_date varchar(50) NOT NULL DEFAULT '',
            message text,
            source varchar(200) NOT NULL DEFAULT '',
            ip_address varchar(45) NOT NULL DEFAULT '',
            status varchar(50) NOT NULL DEFAULT 'New Lead',
            city varchar(100) NOT NULL DEFAULT '',
            budget varchar(50) NOT NULL DEFAULT '0',
            notes text,
            assigned_user bigint(20) unsigned NOT NULL DEFAULT 0,
            follow_up_date date DEFAULT NULL,
            event_type varchar(100) NOT NULL DEFAULT '',
            venue varchar(255) NOT NULL DEFAULT '',
            package_selected varchar(150) NOT NULL DEFAULT '',
            brand varchar(50) NOT NULL DEFAULT 'JJ WeddingZ',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_email (email),
            KEY idx_created (created_at)
        ) {$charset};";

        $tasks_sql = "CREATE TABLE {$tasks_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) unsigned NOT NULL DEFAULT 0,
            task_title varchar(255) NOT NULL DEFAULT '',
            assigned_to bigint(20) unsigned NOT NULL DEFAULT 0,
            due_date date DEFAULT NULL,
            priority varchar(20) NOT NULL DEFAULT 'Medium',
            status varchar(20) NOT NULL DEFAULT 'Pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_lead (lead_id)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
        dbDelta( $tasks_sql );
    }

    /* ─── Enqueue Contact Form Assets ───────────────────────────────────── */

    public function enqueue_form_assets(): void {
        wp_localize_script( 'jjwz-theme', 'JJWZ_FORMS', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'jjwz_lead_nonce' ),
            'strings' => [
                'sending'  => 'Sending…',
                'success'  => 'Thank you! We will be in touch within 24 hours.',
                'error'    => 'Something went wrong. Please try WhatsApp for immediate assistance.',
            ],
        ] );
    }

    /* ─── AJAX Lead Handler ──────────────────────────────────────────────── */

    public function handle_lead_submission(): void {
        // Verify nonce
        if ( ! check_ajax_referer( 'jjwz_lead_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
        }

        // Sanitize inputs
        $name       = sanitize_text_field( $_POST['name']       ?? '' );
        $email      = sanitize_email(       $_POST['email']      ?? '' );
        $phone      = sanitize_text_field( $_POST['phone']      ?? '' );
        $service    = sanitize_text_field( $_POST['service']    ?? '' );
        $event_date = sanitize_text_field( $_POST['event_date'] ?? '' );
        $message    = sanitize_textarea_field( $_POST['message'] ?? '' );
        $source     = sanitize_text_field( $_POST['source']     ?? home_url() );

        // Validate required
        if ( empty( $name ) || empty( $email ) ) {
            wp_send_json_error( [ 'message' => 'Name and email are required.' ], 400 );
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( [ 'message' => 'Please enter a valid email address.' ], 400 );
        }

        // Honeypot check
        if ( ! empty( $_POST['jjwz_honey'] ) ) {
            wp_send_json_success( [ 'message' => 'Thank you! We will be in touch.' ] );
        }

        // Rate limit: max 3 submissions per IP per hour
        $ip        = $this->get_client_ip();
        $rate_key  = 'jjwz_rate_' . md5( $ip );
        $rate_count = (int) get_transient( $rate_key );
        if ( $rate_count >= 3 ) {
            wp_send_json_error( [ 'message' => 'Too many requests. Please contact us via WhatsApp.' ], 429 );
        }
        set_transient( $rate_key, $rate_count + 1, HOUR_IN_SECONDS );

        // Insert to DB
        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_leads';
        $inserted = $wpdb->insert( $table, [
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'service'    => $service,
            'event_date' => $event_date,
            'message'    => $message,
            'source'     => $source,
            'ip_address' => $ip,
            'created_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ] );

        if ( ! $inserted ) {
            wp_send_json_error( [ 'message' => 'Database error. Please contact us directly.' ], 500 );
        }

        // Admin email notification
        $this->send_admin_notification( $name, $email, $phone, $service, $event_date, $message );

        // FluentCRM integration
        $this->sync_to_fluent_crm( $name, $email, $phone, $service );

        wp_send_json_success( [
            'message' => 'Thank you ' . esc_html( $name ) . '! We will contact you within 24 hours to discuss your project.',
            'lead_id' => $wpdb->insert_id,
        ] );
    }

    /* ─── Admin Notification Email ───────────────────────────────────────── */

    private function send_admin_notification( string $name, string $email, string $phone, string $service, string $event_date, string $message ): void {
        $admin_email = get_option( 'admin_email' );
        $subject     = '📸 New JJ WeddingZ Inquiry: ' . $name;
        $body        = "
<h2>New Photography Inquiry</h2>
<table cellpadding='8' style='border-collapse:collapse;'>
    <tr><th align='left'>Name:</th><td>{$name}</td></tr>
    <tr><th align='left'>Email:</th><td>{$email}</td></tr>
    <tr><th align='left'>Phone:</th><td>{$phone}</td></tr>
    <tr><th align='left'>Service:</th><td>{$service}</td></tr>
    <tr><th align='left'>Event Date:</th><td>{$event_date}</td></tr>
    <tr><th align='left'>Message:</th><td>{$message}</td></tr>
</table>
<p><a href='" . admin_url( 'admin.php?page=jjwz-core-settings&jjwz_tab=crm' ) . "'>View in CRM Dashboard →</a></p>
        ";

        wp_mail( $admin_email, $subject, $body, [
            'Content-Type: text/html; charset=UTF-8',
            'From: JJ WeddingZ Photography <noreply@' . preg_replace( '/^www\./', '', parse_url( home_url(), PHP_URL_HOST ) ) . '>',
        ] );
    }

    /* ─── FluentCRM Integration ──────────────────────────────────────────── */

    private function sync_to_fluent_crm( string $name, string $email, string $phone, string $service ): void {
        if ( ! get_option( 'jjwz_fluent_crm_enabled', '0' ) ) { return; }
        if ( ! defined( 'FLUENTCRM' ) ) { return; }

        // FluentCRM v2 API
        if ( ! class_exists( '\FluentCrm\App\Models\Subscriber' ) ) { return; }

        $data = [
            'email'       => $email,
            'first_name'  => explode( ' ', trim( $name ), 2 )[0],
            'last_name'   => explode( ' ', trim( $name ), 2 )[1] ?? '',
            'phone'       => $phone,
            'status'      => 'subscribed',
            'tags'        => [ 'jjwz-inquiry', 'jjwz-' . sanitize_title( $service ) ],
        ];

        try {
            $handler = new \FluentCrm\App\Services\ContactsQuery();
            \FluentCrm\App\Models\Subscriber::createOrUpdate( $data, true, true );
        } catch ( \Exception $e ) {
            error_log( 'JJWZ FluentCRM sync error: ' . $e->getMessage() );
        }
    }

    /* ─── IP Helper ──────────────────────────────────────────────────────── */

    private function get_client_ip(): string {
        $keys = [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ];
        foreach ( $keys as $key ) {
            $val = $_SERVER[ $key ] ?? '';
            if ( $val ) {
                $parts = explode( ',', $val );
                $ip    = trim( $parts[0] );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }
}
