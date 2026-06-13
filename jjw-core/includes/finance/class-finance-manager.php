<?php
/**
 * class-finance-manager.php — JJ WeddingZ Quote & Invoice Management Engine
 *
 * Handles:
 * - Registration of 'jjwz_quote' and 'jjwz_invoice' CPTs
 * - Admin metaboxes for client billing, line items, and payment milestones
 * - Razorpay order generation & HMAC-SHA256 signature verification
 * - WhatsApp automation payment hooks
 *
 * @package JJW_Core
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Finance_Manager {

    public function __construct() {
        // CPT Registration
        add_action( 'init', [ $this, 'register_cpts' ] );

        // Metaboxes
        add_action( 'add_meta_boxes', [ $this, 'register_finance_metaboxes' ] );
        add_action( 'save_post', [ $this, 'save_finance_meta' ] );

        // Razorpay AJAX API Endpoints
        add_action( 'wp_ajax_nopriv_jjwz_create_razorpay_order', [ $this, 'ajax_create_razorpay_order' ] );
        add_action( 'wp_ajax_jjwz_create_razorpay_order',        [ $this, 'ajax_create_razorpay_order' ] );

        add_action( 'wp_ajax_nopriv_jjwz_verify_razorpay_payment', [ $this, 'ajax_verify_razorpay_payment' ] );
        add_action( 'wp_ajax_jjwz_verify_razorpay_payment',        [ $this, 'ajax_verify_razorpay_payment' ] );
    }

    /* ─── CPT Registrations ──────────────────────────────────────────────── */

    public function register_cpts(): void {
        // Quotes CPT
        register_post_type( 'jjwz_quote', [
            'labels' => [
                'name'               => __( 'Quotes', 'jjw-core' ),
                'singular_name'      => __( 'Quote', 'jjw-core' ),
                'add_new'            => __( 'Add New Quote', 'jjw-core' ),
                'add_new_item'       => __( 'Add New Quote', 'jjw-core' ),
                'edit_item'          => __( 'Edit Quote', 'jjw-core' ),
                'view_item'          => __( 'View Quote', 'jjw-core' ),
                'search_items'       => __( 'Search Quotes', 'jjw-core' ),
                'not_found'          => __( 'No quotes found', 'jjw-core' ),
                'menu_name'          => __( 'Quotes', 'jjw-core' ),
            ],
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => 'jjwz-core-settings',
            'supports'           => [ 'title' ],
            'menu_icon'          => 'dashicons-media-text',
        ] );

        // Invoices CPT
        register_post_type( 'jjwz_invoice', [
            'labels' => [
                'name'               => __( 'Invoices', 'jjw-core' ),
                'singular_name'      => __( 'Invoice', 'jjw-core' ),
                'add_new'            => __( 'Add New Invoice', 'jjw-core' ),
                'add_new_item'       => __( 'Add New Invoice', 'jjw-core' ),
                'edit_item'          => __( 'Edit Invoice', 'jjw-core' ),
                'view_item'          => __( 'View Invoice', 'jjw-core' ),
                'search_items'       => __( 'Search Invoices', 'jjw-core' ),
                'not_found'          => __( 'No invoices found', 'jjw-core' ),
                'menu_name'          => __( 'Invoices', 'jjw-core' ),
            ],
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => 'jjwz-core-settings',
            'supports'           => [ 'title' ],
            'menu_icon'          => 'dashicons-media-spreadsheet',
        ] );
    }

    /* ─── Metabox Registrations ─────────────────────────────────────────── */

    public function register_finance_metaboxes(): void {
        $screens = [ 'jjwz_quote', 'jjwz_invoice' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'jjwz-finance-details',
                __( 'Billing & Milestone Configuration', 'jjw-core' ),
                [ $this, 'render_finance_metabox' ],
                $screen,
                'normal',
                'high'
            );
        }
    }

    public function render_finance_metabox( WP_Post $post ): void {
        wp_nonce_field( 'jjwz_save_finance_meta', 'jjwz_finance_nonce' );

        $brand          = get_post_meta( $post->ID, 'finance_brand', true ) ?: 'JJ WeddingZ';
        $gallery_id     = (int) get_post_meta( $post->ID, 'finance_gallery_id', true );
        $client_email   = get_post_meta( $post->ID, 'finance_client_email', true );
        $client_phone   = get_post_meta( $post->ID, 'finance_client_phone', true );

        $subtotal       = get_post_meta( $post->ID, 'finance_subtotal', true ) ?: '0';
        $tax            = get_post_meta( $post->ID, 'finance_tax', true ) ?: '0';
        $discount       = get_post_meta( $post->ID, 'finance_discount', true ) ?: '0';
        $total          = get_post_meta( $post->ID, 'finance_total', true ) ?: '0';

        // Milestones
        $m_booking      = get_post_meta( $post->ID, 'milestone_booking_amount', true ) ?: '0';
        $m_booking_st   = get_post_meta( $post->ID, 'milestone_booking_status', true ) ?: 'Pending';
        $m_shoot        = get_post_meta( $post->ID, 'milestone_shoot_amount', true ) ?: '0';
        $m_shoot_st     = get_post_meta( $post->ID, 'milestone_shoot_status', true ) ?: 'Pending';
        $m_album        = get_post_meta( $post->ID, 'milestone_album_amount', true ) ?: '0';
        $m_album_st     = get_post_meta( $post->ID, 'milestone_album_status', true ) ?: 'Pending';
        $m_final        = get_post_meta( $post->ID, 'milestone_final_amount', true ) ?: '0';
        $m_final_st     = get_post_meta( $post->ID, 'milestone_final_status', true ) ?: 'Pending';

        $status         = get_post_meta( $post->ID, 'finance_status', true ) ?: 'Draft';

        // Get list of galleries for dropdown mapping
        $galleries = get_posts( [ 'post_type' => 'jjwz_gallery', 'posts_per_page' => -1, 'post_status' => 'publish' ] );
        ?>
        <div class="jjwz-metabox-container" style="font-family:-apple-system,BlinkMacSystemFont,sans-serif;color:#2d3748;">
            <table class="form-table" role="presentation">
                <!-- Brand Context -->
                <tr>
                    <th scope="row"><label for="finance_brand">Studio Brand</label></th>
                    <td>
                        <select name="finance_brand" id="finance_brand" style="width:250px;">
                            <option value="JJ WeddingZ" <?php selected( $brand, 'JJ WeddingZ' ); ?>>JJ WeddingZ</option>
                            <option value="The Baby StudioZ" <?php selected( $brand, 'The Baby StudioZ' ); ?>>The Baby StudioZ</option>
                        </select>
                    </td>
                </tr>

                <!-- Client Access Link -->
                <tr>
                    <th scope="row"><label for="finance_gallery_id">Linked Client Gallery</label></th>
                    <td>
                        <select name="finance_gallery_id" id="finance_gallery_id" style="width:250px;">
                            <option value="0">— Unlinked / Guest Quote —</option>
                            <?php foreach ( $galleries as $gal ) : ?>
                                <option value="<?php echo $gal->ID; ?>" <?php selected( $gallery_id, $gal->ID ); ?>><?php echo esc_html( $gal->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Select the secure gallery which maps client portal logins to this bill.</p>
                    </td>
                </tr>

                <!-- Client Contact details -->
                <tr>
                    <th scope="row"><label for="finance_client_email">Client Email Address</label></th>
                    <td>
                        <input type="email" name="finance_client_email" id="finance_client_email" value="<?php echo esc_attr( $client_email ); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="finance_client_phone">Client Phone Number</label></th>
                    <td>
                        <input type="text" name="finance_client_phone" id="finance_client_phone" value="<?php echo esc_attr( $client_phone ); ?>" class="regular-text">
                        <p class="description">Required for routing SMS/WhatsApp payment links.</p>
                    </td>
                </tr>

                <!-- Total Summary -->
                <tr>
                    <th scope="row">Financial Totals</th>
                    <td>
                        <div style="display:flex;gap:15px;align-items:center;">
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px;">Subtotal (₹)</label>
                                <input type="number" name="finance_subtotal" value="<?php echo esc_attr( $subtotal ); ?>" style="width:100px;">
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px;">GST/Tax (₹)</label>
                                <input type="number" name="finance_tax" value="<?php echo esc_attr( $tax ); ?>" style="width:100px;">
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px;">Discount (₹)</label>
                                <input type="number" name="finance_discount" value="<?php echo esc_attr( $discount ); ?>" style="width:100px;">
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px;">Total Amount (₹)</label>
                                <input type="number" name="finance_total" value="<?php echo esc_attr( $total ); ?>" style="width:120px;font-weight:bold;">
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Milestones Repeater -->
                <tr>
                    <th scope="row">Payment Milestones</th>
                    <td>
                        <div style="background:#f7fafc;border:1px solid #e2e8f0;border-radius:6px;padding:15px;display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                            <!-- Milestone 1: Booking -->
                            <div style="border-right:1px solid #e2e8f0;padding-right:15px;">
                                <strong style="color:#c9a96e;font-size:13px;display:block;margin-bottom:8px;">1. Booking Amount</strong>
                                <div style="display:flex;gap:10px;">
                                    <input type="number" name="milestone_booking_amount" value="<?php echo esc_attr( $m_booking ); ?>" style="width:120px;" placeholder="Amount">
                                    <select name="milestone_booking_status">
                                        <option value="Pending" <?php selected( $m_booking_st, 'Pending' ); ?>>Pending</option>
                                        <option value="Paid" <?php selected( $m_booking_st, 'Paid' ); ?>>Paid</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Milestone 2: Shoot Day -->
                            <div>
                                <strong style="color:#c9a96e;font-size:13px;display:block;margin-bottom:8px;">2. Shoot Day Amount</strong>
                                <div style="display:flex;gap:10px;">
                                    <input type="number" name="milestone_shoot_amount" value="<?php echo esc_attr( $m_shoot ); ?>" style="width:120px;" placeholder="Amount">
                                    <select name="milestone_shoot_status">
                                        <option value="Pending" <?php selected( $m_shoot_st, 'Pending' ); ?>>Pending</option>
                                        <option value="Paid" <?php selected( $m_shoot_st, 'Paid' ); ?>>Paid</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Milestone 3: Album Approval -->
                            <div style="border-right:1px solid #e2e8f0;padding-right:15px;margin-top:10px;padding-top:10px;border-top:1px solid #edf2f7;">
                                <strong style="color:#c9a96e;font-size:13px;display:block;margin-bottom:8px;">3. Album Approval Amount</strong>
                                <div style="display:flex;gap:10px;">
                                    <input type="number" name="milestone_album_amount" value="<?php echo esc_attr( $m_album ); ?>" style="width:120px;" placeholder="Amount">
                                    <select name="milestone_album_status">
                                        <option value="Pending" <?php selected( $m_album_st, 'Pending' ); ?>>Pending</option>
                                        <option value="Paid" <?php selected( $m_album_st, 'Paid' ); ?>>Paid</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Milestone 4: Final Delivery -->
                            <div style="margin-top:10px;padding-top:10px;border-top:1px solid #edf2f7;">
                                <strong style="color:#c9a96e;font-size:13px;display:block;margin-bottom:8px;">4. Final Delivery Amount</strong>
                                <div style="display:flex;gap:10px;">
                                    <input type="number" name="milestone_final_amount" value="<?php echo esc_attr( $m_final ); ?>" style="width:120px;" placeholder="Amount">
                                    <select name="milestone_final_status">
                                        <option value="Pending" <?php selected( $m_final_st, 'Pending' ); ?>>Pending</option>
                                        <option value="Paid" <?php selected( $m_final_st, 'Paid' ); ?>>Paid</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Post Status -->
                <tr>
                    <th scope="row"><label for="finance_status">Overall Billing Status</label></th>
                    <td>
                        <select name="finance_status" id="finance_status" style="width:200px;">
                            <option value="Draft" <?php selected( $status, 'Draft' ); ?>>Draft</option>
                            <option value="Pending" <?php selected( $status, 'Pending' ); ?>>Pending</option>
                            <option value="Partially Paid" <?php selected( $status, 'Partially Paid' ); ?>>Partially Paid</option>
                            <option value="Paid" <?php selected( $status, 'Paid' ); ?>>Paid</option>
                            <option value="Overdue" <?php selected( $status, 'Overdue' ); ?>>Overdue</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    public function save_finance_meta( int $post_id ): void {
        if ( ! isset( $_POST['jjwz_finance_nonce'] ) || ! wp_verify_nonce( $_POST['jjwz_finance_nonce'], 'jjwz_save_finance_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

        $fields = [
            'finance_brand', 'finance_gallery_id', 'finance_client_email', 'finance_client_phone',
            'finance_subtotal', 'finance_tax', 'finance_discount', 'finance_total',
            'milestone_booking_amount', 'milestone_booking_status',
            'milestone_shoot_amount', 'milestone_shoot_status',
            'milestone_album_amount', 'milestone_album_status',
            'milestone_final_amount', 'milestone_final_status',
            'finance_status'
        ];

        foreach ( $fields as $f ) {
            if ( isset( $_POST[ $f ] ) ) {
                update_post_meta( $post_id, $f, sanitize_text_field( $_POST[ $f ] ) );
            }
        }
    }

    /* ─── RAZORPAY PAYMENT INTEGRATION ──────────────────────────────────── */

    /**
     * AJAX endpoint to initialize Razorpay payment order
     */
    public function ajax_create_razorpay_order(): void {
        $invoice_id = (int) ( $_POST['invoice_id'] ?? 0 );
        $milestone  = sanitize_key( $_POST['milestone'] ?? '' );

        if ( ! $invoice_id || ! $milestone ) {
            wp_send_json_error( [ 'message' => 'Invoice ID and Milestone are required.' ], 400 );
        }

        // Retrieve milestone amount
        $amount = (float) get_post_meta( $invoice_id, 'milestone_' . $milestone . '_amount', true );
        if ( $amount <= 0 ) {
            wp_send_json_error( [ 'message' => 'Milestone amount must be greater than zero.' ], 400 );
        }

        $key_id = get_option( 'jjwz_razorpay_key_id' );
        $key_secret = get_option( 'jjwz_razorpay_key_secret' );

        if ( empty( $key_id ) || empty( $key_secret ) ) {
            wp_send_json_error( [ 'message' => 'Payment gateway credentials are not configured.' ], 500 );
        }

        // Amount in paisa
        $amount_in_paisa = round( $amount * 100 );

        $payload = [
            'amount'          => $amount_in_paisa,
            'currency'        => 'INR',
            'receipt'         => 'jjwz_inv_' . $invoice_id . '_' . $milestone,
            'payment_capture' => 1
        ];

        $response = wp_remote_post( 'https://api.razorpay.com/v1/orders', [
            'body'    => wp_json_encode( $payload ),
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Basic ' . base64_encode( $key_id . ':' . $key_secret )
            ],
            'timeout' => 15
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( [ 'message' => 'Razorpay API error: ' . $response->get_error_message() ], 500 );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['id'] ) ) {
            wp_send_json_success( [
                'order_id' => $data['id'],
                'key_id'   => $key_id,
                'amount'   => $amount_in_paisa,
                'currency' => 'INR'
            ] );
        } else {
            $err_msg = $data['error']['description'] ?? 'Unable to create payment order.';
            wp_send_json_error( [ 'message' => $err_msg ], 500 );
        }
    }

    /**
     * AJAX endpoint to verify signature and mark milestone paid
     */
    public function ajax_verify_razorpay_payment(): void {
        $order_id   = sanitize_text_field( $_POST['razorpay_order_id'] ?? '' );
        $payment_id = sanitize_text_field( $_POST['razorpay_payment_id'] ?? '' );
        $signature  = sanitize_text_field( $_POST['razorpay_signature'] ?? '' );
        $invoice_id = (int) ( $_POST['invoice_id'] ?? 0 );
        $milestone  = sanitize_key( $_POST['milestone'] ?? '' );

        if ( empty( $order_id ) || empty( $payment_id ) || empty( $signature ) || ! $invoice_id || ! $milestone ) {
            wp_send_json_error( [ 'message' => 'Missing payment verification parameters.' ], 400 );
        }

        $key_secret = get_option( 'jjwz_razorpay_key_secret' );

        // Generate expected signature
        $expected_signature = hash_hmac( 'sha256', $order_id . '|' . $payment_id, $key_secret );

        if ( ! hash_equals( $expected_signature, $signature ) ) {
            wp_send_json_error( [ 'message' => 'Payment signature verification failed. Unauthorized transaction.' ], 403 );
        }

        // Signature verified! Save payment status
        update_post_meta( $invoice_id, 'milestone_' . $milestone . '_status', 'Paid' );
        update_post_meta( $invoice_id, 'milestone_' . $milestone . '_payment_id', $payment_id );
        update_post_meta( $invoice_id, 'milestone_' . $milestone . '_paid_date', current_time( 'mysql' ) );

        // Evaluate overall invoice status
        $st_booking = get_post_meta( $invoice_id, 'milestone_booking_status', true ) ?: 'Pending';
        $st_shoot   = get_post_meta( $invoice_id, 'milestone_shoot_status', true ) ?: 'Pending';
        $st_album   = get_post_meta( $invoice_id, 'milestone_album_status', true ) ?: 'Pending';
        $st_final   = get_post_meta( $invoice_id, 'milestone_final_status', true ) ?: 'Pending';

        if ( $st_booking === 'Paid' && $st_shoot === 'Paid' && $st_album === 'Paid' && $st_final === 'Paid' ) {
            update_post_meta( $invoice_id, 'finance_status', 'Paid' );
        } else {
            update_post_meta( $invoice_id, 'finance_status', 'Partially Paid' );
        }

        // Trigger WhatsApp trigger hook
        do_action( 'jjwz_payment_received', $invoice_id, $milestone );

        wp_send_json_success( [ 'message' => 'Payment verified and recorded successfully!' ] );
    }
}
new JJWZ_Finance_Manager();
