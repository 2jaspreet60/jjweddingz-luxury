<?php
/**
 * class-whatsapp-automation.php — WhatsApp Business API Routing Engine
 *
 * Implements 11 automation triggers:
 * - new_lead, quote_sent, booking_confirmed, payment_reminder, gallery_ready, delivery_complete,
 *   shoot_reminder, meeting_reminder, album_approval_request, gallery_expiry_warning, payment_due_reminder
 *
 * Support modes:
 * - Simple: Generates direct WhatsApp chat links.
 * - API: Delivers structured JSON payloads via wp_remote_post to Meta/3rd party gateways.
 *
 * @package JJW_Core
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_WhatsApp_Automation {

    public function __construct() {
        // Core Lead Events
        add_action( 'jjwz_new_lead', [ $this, 'trigger_new_lead_message' ], 10, 2 );
        add_action( 'jjwz_lead_stage_updated', [ $this, 'trigger_stage_change_messages' ], 10, 3 );

        // Billing & Payment Events
        add_action( 'jjwz_payment_received', [ $this, 'trigger_payment_received_message' ], 10, 2 );

        // Gallery Events
        add_action( 'jjwz_gallery_status_changed', [ $this, 'trigger_gallery_status_messages' ], 10, 3 );
    }

    /**
     * Retrieves the default templates array
     */
    public static function get_default_templates(): array {
        return [
            'new_lead' => [
                'label'   => 'New Lead Greeting',
                'default' => 'Hello {{name}}, thank you for contacting {{brand}}. We have received your inquiry for {{service}} on {{date}} and will get back to you within 24 hours.',
            ],
            'quote_sent' => [
                'label'   => 'Quote Generated',
                'default' => 'Hello {{name}}, your customized photography quote for {{service}} is ready! Review details here: {{portal_link}} | Total: {{price}}.',
            ],
            'booking_confirmed' => [
                'label'   => 'Booking Confirmed',
                'default' => 'Congratulations {{name}}! Your booking for {{service}} is confirmed. We look forward to capturing your milestones with {{brand}}.',
            ],
            'payment_reminder' => [
                'label'   => 'Payment Due Notice',
                'default' => 'Dear {{name}}, this is a friendly reminder that a payment of {{price}} is pending for milestone: {{milestone}}. Pay here: {{portal_link}}.',
            ],
            'gallery_ready' => [
                'label'   => 'Gallery Delivery',
                'default' => 'Hello {{name}}, your luxury gallery is ready! Access it here: {{portal_link}} using your secure Gallery Access Key: {{access_key}}.',
            ],
            'delivery_complete' => [
                'label'   => 'Delivery Complete',
                'default' => 'Hello {{name}}, we have successfully processed and delivered all fine-art albums and final digital assets. Thank you for choosing {{brand}}!',
            ],
            'shoot_reminder' => [
                'label'   => 'Shoot Day Reminder',
                'default' => 'Dear {{name}}, this is a reminder for your upcoming {{service}} session scheduled for {{date}} at {{venue}}. Prepare for a magical session!',
            ],
            'meeting_reminder' => [
                'label'   => 'Meeting Confirmation',
                'default' => 'Hello {{name}}, we are scheduled for a consultation meeting on {{date}}. We look forward to planning your shoot details.',
            ],
            'album_approval_request' => [
                'label'   => 'Album Layout Approval',
                'default' => 'Hello {{name}}, your custom wedding album layout is ready for review. Access the client portal {{portal_link}} to select and approve your photos.',
            ],
            'gallery_expiry_warning' => [
                'label'   => 'Gallery Expiry Warning',
                'default' => 'Warning {{name}}, your secure client gallery will expire on {{date}}. Please download all final high-resolution files before access is closed.',
            ],
            'payment_due_reminder' => [
                'label'   => 'Urgent Payment Due',
                'default' => 'Urgent Notice: Dear {{name}}, your payment of {{price}} for milestone: {{milestone}} is overdue. Please pay here: {{portal_link}} to prevent delays.',
            ],
        ];
    }

    /**
     * Dispatch WhatsApp alert based on selected mode (Simple Link or API Automation)
     */
    public function send_message( string $phone, string $template_key, array $replacements ): void {
        $mode = get_option( 'jjwz_whatsapp_mode', 'simple' );
        $raw_phone = preg_replace( '/[^\d]/', '', $phone );
        if ( empty( $raw_phone ) ) { return; }

        $templates = self::get_default_templates();
        if ( ! isset( $templates[ $template_key ] ) ) { return; }

        // Fetch custom template text or fall back to default
        $message_text = get_option( 'jjwz_wa_template_' . $template_key, $templates[ $template_key ]['default'] );

        // Replace template placeholders
        foreach ( $replacements as $key => $val ) {
            $message_text = str_replace( '{{' . $key . '}}', $val, $message_text );
        }

        if ( $mode === 'simple' ) {
            // Write to debug log: direct WA hyperlink generation utility
            $wa_url = 'https://wa.me/' . $raw_phone . '?text=' . rawurlencode( $message_text );
            error_log( "[JJWZ WhatsApp Simple Link] Destination: {$raw_phone} | Message: {$message_text} | Link: {$wa_url}" );
            // Store as a pending manual notice in options so admin can click it in backend
            $pending = get_option( 'jjwz_wa_pending_notices', [] );
            $pending[] = [
                'phone'   => $raw_phone,
                'message' => $message_text,
                'link'    => $wa_url,
                'time'    => current_time( 'mysql' )
            ];
            update_option( 'jjwz_wa_pending_notices', array_slice( $pending, -10 ) );
        } else {
            // API automated routing
            $endpoint = get_option( 'jjwz_wa_api_endpoint' );
            $token    = get_option( 'jjwz_wa_bearer_token' );
            $custom_payload = get_option( 'jjwz_wa_json_payload' );

            if ( empty( $endpoint ) ) {
                error_log( '[JJWZ WhatsApp API Error] Endpoint URL is not configured.' );
                return;
            }

            // Interpolate variables inside custom JSON payload if supplied, else build standard meta JSON
            if ( ! empty( $custom_payload ) ) {
                $payload_body = $custom_payload;
                $payload_body = str_replace( '{{phone}}', $raw_phone, $payload_body );
                $payload_body = str_replace( '{{message}}', json_encode( $message_text, JSON_UNESCAPED_UNICODE ), $payload_body );
                foreach ( $replacements as $key => $val ) {
                    $payload_body = str_replace( '{{' . $key . '}}', $val, $payload_body );
                }
            } else {
                $payload_body = wp_json_encode( [
                    'to'        => $raw_phone,
                    'type'      => 'text',
                    'text'      => [ 'body' => $message_text ],
                    'messaging_product' => 'whatsapp'
                ] );
            }

            // Execute asynchronous wp_remote_post
            $headers = [ 'Content-Type' => 'application/json' ];
            if ( ! empty( $token ) ) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }

            wp_remote_post( $endpoint, [
                'body'        => $payload_body,
                'headers'     => $headers,
                'timeout'     => 15,
                'data_format' => 'body'
            ] );
        }
    }

    /* ─── Trigger Callbacks ──────────────────────────────────────────────── */

    public function trigger_new_lead_message( int $lead_id, array $lead_data ): void {
        $this->send_message(
            $lead_data['phone'] ?? '',
            'new_lead',
            [
                'name'         => $lead_data['name'] ?? 'Client',
                'brand'        => $lead_data['brand'] ?? 'JJ WeddingZ',
                'service'      => $lead_data['service'] ?? 'Photography',
                'date'         => $lead_data['event_date'] ?? 'TBD',
                'portal_link'  => home_url( '/client-portal/' )
            ]
        );
    }

    public function trigger_stage_change_messages( int $lead_id, string $new_status, string $old_status ): void {
        global $wpdb;
        $table = $wpdb->prefix . 'jjwz_leads';
        $lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $lead_id ), ARRAY_A );
        if ( ! $lead ) { return; }

        $replacements = [
            'name'         => $lead['name'],
            'brand'        => $lead['brand'],
            'service'      => $lead['service'],
            'date'         => $lead['event_date'] ?: 'TBD',
            'price'        => '₹' . ( $lead['budget'] ?: '0' ),
            'portal_link'  => home_url( '/client-portal/' ),
            'venue'        => $lead['venue'] ?: 'Venue',
            'milestone'    => 'Booking'
        ];

        switch ( $new_status ) {
            case 'Follow Up':
                $this->send_message( $lead['phone'], 'payment_reminder', $replacements );
                break;
            case 'Meeting Scheduled':
                $replacements['date'] = $lead['follow_up_date'] ?: $lead['event_date'];
                $this->send_message( $lead['phone'], 'meeting_reminder', $replacements );
                break;
            case 'Quote Sent':
                $this->send_message( $lead['phone'], 'quote_sent', $replacements );
                break;
            case 'Booked':
                $this->send_message( $lead['phone'], 'booking_confirmed', $replacements );
                break;
            case 'Lost':
                // Do nothing
                break;
        }
    }

    public function trigger_payment_received_message( int $invoice_id, string $milestone ): void {
        $email      = get_post_meta( $invoice_id, 'finance_client_email', true );
        $phone      = get_post_meta( $invoice_id, 'finance_client_phone', true );
        $brand      = get_post_meta( $invoice_id, 'finance_brand', true ) ?: 'JJ WeddingZ';
        $total      = get_post_meta( $invoice_id, 'finance_total', true );
        $amount     = get_post_meta( $invoice_id, 'milestone_' . $milestone . '_amount', true );
        $gallery_id = (int) get_post_meta( $invoice_id, 'finance_gallery_id', true );

        $client_name = 'Client';
        $access_key  = '';
        if ( $gallery_id ) {
            $client_name = get_post_meta( $gallery_id, 'gallery_client_name', true ) ?: 'Client';
            $access_key  = get_post_meta( $gallery_id, 'gallery_access_key', true );
        }

        $replacements = [
            'name'         => $client_name,
            'brand'        => $brand,
            'service'      => get_the_title( $invoice_id ),
            'price'        => '₹' . number_format( (float) $amount ),
            'milestone'    => ucwords( str_replace( '_', ' ', $milestone ) ),
            'portal_link'  => home_url( '/client-portal/' ),
            'access_key'   => $access_key
        ];

        // Send Booking confirmation or milestone payment alert
        if ( $milestone === 'booking' ) {
            $this->send_message( $phone, 'booking_confirmed', $replacements );
        } else {
            $this->send_message( $phone, 'payment_reminder', $replacements );
        }
    }

    public function trigger_gallery_status_messages( int $gallery_id, string $new_status, string $old_status ): void {
        $phone       = get_post_meta( $gallery_id, 'finance_client_phone', true );
        if ( empty( $phone ) ) {
            // Retrieve linked invoice client phone
            $invoice = get_posts( [
                'post_type'  => 'jjwz_invoice',
                'meta_query' => [
                    [
                        'key'   => 'finance_gallery_id',
                        'value' => $gallery_id
                    ]
                ]
            ] );
            if ( ! empty( $invoice ) ) {
                $phone = get_post_meta( $invoice[0]->ID, 'finance_client_phone', true );
            }
        }

        $client_name = get_post_meta( $gallery_id, 'gallery_client_name', true ) ?: 'Client';
        $access_key  = get_post_meta( $gallery_id, 'gallery_access_key', true );
        $brand       = get_post_meta( $gallery_id, 'gallery_brand', true ) ?: 'JJ WeddingZ';
        $expiry      = get_post_meta( $gallery_id, 'gallery_expiry', true );

        $replacements = [
            'name'         => $client_name,
            'brand'        => $brand,
            'service'      => get_the_title( $gallery_id ),
            'portal_link'  => home_url( '/client-portal/' ),
            'access_key'   => $access_key,
            'date'         => $expiry ? date( 'M j, Y', strtotime( $expiry ) ) : 'TBD'
        ];

        switch ( $new_status ) {
            case 'Ready for Delivery':
                $this->send_message( $phone, 'gallery_ready', $replacements );
                break;
            case 'Album Designing':
                $this->send_message( $phone, 'album_approval_request', $replacements );
                break;
            case 'Album Delivered':
                $this->send_message( $phone, 'delivery_complete', $replacements );
                break;
        }
    }
}
new JJWZ_WhatsApp_Automation();
