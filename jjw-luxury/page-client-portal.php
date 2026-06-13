<?php
/**
 * page-client-portal.php — Secure Client Portal & Interactive Media Workspace
 *
 * Template Name: Client Portal
 *
 * @package JJWeddingZ
 * @version 1.2.0
 */

// Start session if not started
if ( ! session_id() ) { @session_start(); }

$gallery_id = isset( $_SESSION['jjwz_client_gallery_id'] ) ? (int) $_SESSION['jjwz_client_gallery_id'] : 0;
$gallery = $gallery_id ? get_post( $gallery_id ) : null;

// Failsafe check: if gallery post was trashed or doesn't exist, clear session
if ( $gallery_id && ( ! $gallery || $gallery->post_status !== 'publish' ) ) {
    unset( $_SESSION['jjwz_client_gallery_id'] );
    unset( $_SESSION['jjwz_client_brand'] );
    $gallery_id = 0;
    $gallery = null;
}

$is_expired = false;
$today = date( 'Y-m-d' );
if ( $gallery ) {
    $expiry = get_post_meta( $gallery_id, 'gallery_expiry', true );
    if ( ! empty( $expiry ) && $expiry < $today ) {
        $is_expired = true;
    }
}

get_header();
?>

<!-- Include Razorpay Checkout SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<style>
    /* Premium Portal Stylesheet */
    :root {
        --clr-portal-gold: #c9a96e;
        --clr-portal-dark: #0a0a0a;
        --clr-portal-gray: #718096;
        --clr-portal-light: #faf8f5;
        --clr-portal-border: #cbd5e0;
    }
    .portal-wrap {
        background-color: var(--clr-portal-light);
        min-height: 80vh;
        padding-block: 80px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .portal-container {
        max-width: 1200px;
        margin-inline: auto;
        padding-inline: 20px;
    }
    /* Login Card Styles */
    .portal-login-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 50px 40px;
        max-width: 450px;
        margin-inline: auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        text-align: center;
    }
    .portal-login-card h2 {
        font-family: Georgia, serif;
        font-size: 28px;
        color: var(--clr-portal-dark);
        margin-top: 0;
        margin-bottom: 10px;
        letter-spacing: 0.05em;
    }
    .portal-login-card p {
        color: var(--clr-portal-gray);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .portal-input-group {
        position: relative;
        margin-bottom: 20px;
    }
    .portal-input-group input {
        width: 100%;
        padding: 12px 20px;
        border: 1.5px solid var(--clr-portal-border);
        border-radius: 6px;
        font-size: 15px;
        box-sizing: border-box;
        text-align: center;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s;
    }
    .portal-input-group input:focus {
        border-color: var(--clr-portal-gold);
    }
    .portal-btn {
        background-color: var(--clr-portal-dark);
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.2s, transform 0.2s;
    }
    .portal-btn:hover {
        background-color: var(--clr-portal-gold);
    }
    .portal-btn:active {
        transform: scale(0.98);
    }
    /* Dashboard Header Styles */
    .portal-header {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .portal-header__left h2 {
        font-family: Georgia, serif;
        font-size: 24px;
        margin: 0 0 5px 0;
        color: var(--clr-portal-dark);
    }
    .portal-header__left span {
        color: var(--clr-portal-gray);
        font-size: 13px;
    }
    /* Timeline Stepper */
    .portal-stepper {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 30px 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        overflow-x: auto;
    }
    .portal-stepper__title {
        font-family: Georgia, serif;
        font-size: 18px;
        color: var(--clr-portal-dark);
        margin-top: 0;
        margin-bottom: 20px;
        text-align: center;
    }
    .portal-steps {
        display: flex;
        justify-content: space-between;
        min-width: 900px;
        position: relative;
        padding-inline: 20px;
    }
    .portal-steps::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 50px;
        right: 50px;
        height: 3px;
        background-color: #edf2f7;
        z-index: 1;
    }
    .portal-step {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }
    .portal-step__dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: #e2e8f0;
        border: 4px solid #fff;
        margin: 15px auto 10px auto;
        box-shadow: 0 0 0 1px #e2e8f0;
        transition: background-color 0.3s;
    }
    .portal-step__label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--clr-portal-gray);
        letter-spacing: 0.05em;
    }
    .portal-step--active .portal-step__dot {
        background-color: var(--clr-portal-gold);
        box-shadow: 0 0 0 2px var(--clr-portal-gold);
    }
    .portal-step--active .portal-step__label {
        color: var(--clr-portal-gold);
    }
    .portal-step--completed .portal-step__dot {
        background-color: var(--clr-portal-dark);
        box-shadow: 0 0 0 1px var(--clr-portal-dark);
    }
    .portal-step--completed .portal-step__label {
        color: var(--clr-portal-dark);
    }
    /* Finance / Invoices Section */
    .portal-finance {
        margin-bottom: 30px;
    }
    .portal-finance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .portal-invoice-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .portal-invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 12px;
        margin-bottom: 15px;
    }
    .portal-invoice-title {
        font-weight: 700;
        font-size: 16px;
        color: var(--clr-portal-dark);
    }
    .portal-invoice-badge {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 3px 8px;
        border-radius: 4px;
    }
    .portal-invoice-badge--pending { background: #fee2e2; color: #ef4444; }
    .portal-invoice-badge--partially { background: #fef3c7; color: #d97706; }
    .portal-invoice-badge--paid { background: #d1fae5; color: #10b981; }
    /* Gallery Masonry Grid */
    .portal-gallery-header {
        border-bottom: 1px solid var(--clr-portal-border);
        padding-bottom: 10px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .portal-gallery-header h3 {
        font-family: Georgia, serif;
        font-size: 20px;
        color: var(--clr-portal-dark);
        margin: 0;
    }
    .portal-gallery-grid {
        column-count: 4;
        column-gap: 15px;
    }
    @media (max-width: 992px) {
        .portal-gallery-grid { column-count: 3; }
    }
    @media (max-width: 768px) {
        .portal-gallery-grid { column-count: 2; }
    }
    @media (max-width: 480px) {
        .portal-gallery-grid { column-count: 1; }
    }
    .portal-photo-item {
        background-color: #fff;
        border: 1px solid #edf2f7;
        border-radius: 6px;
        margin-bottom: 15px;
        break-inside: avoid;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }
    .portal-photo-item img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.3s;
        /* Protect images from standard drag-saving */
        -webkit-user-drag: none;
        user-drag: none;
    }
    .portal-photo-item:hover img {
        transform: scale(1.02);
    }
    /* Hover Watermark Overlay */
    .portal-photo-item::after {
        content: "JJ WeddingZ — Review Only";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        color: rgba(255,255,255,0.15);
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        pointer-events: none;
        z-index: 2;
        width: 200%;
        text-align: center;
    }
    .portal-photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.25);
        opacity: 0;
        transition: opacity 0.2s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        z-index: 3;
    }
    .portal-photo-item:hover .portal-photo-overlay {
        opacity: 1;
    }
    .portal-photo-action {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #fff;
        color: var(--clr-portal-dark);
        border: none;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background-color 0.2s, color 0.2s, transform 0.2s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .portal-photo-action:hover {
        transform: scale(1.1);
    }
    .portal-photo-action--fav.is-active {
        background-color: #e53e3e;
        color: #fff;
    }
    .portal-photo-action--album.is-active {
        background-color: var(--clr-portal-gold);
        color: #fff;
    }
</style>

<div class="portal-wrap">
    <div class="portal-container">

        <?php if ( ! $gallery_id ) : ?>
            <!-- STATE A: UNAUTHENTICATED LOGIN CARD -->
            <div class="portal-login-card">
                <h2>CLIENT PORTAL</h2>
                <p>Welcome to your secure memory archive. Enter your exclusive Access Key below to load your milestone dashboard.</p>
                <form id="jjwz-portal-login-form">
                    <div class="portal-input-group">
                        <input type="text" id="portal-access-key" required placeholder="Access Key">
                    </div>
                    <button type="submit" class="portal-btn">Access Archive</button>
                    <div id="portal-login-err" style="color:#e53e3e;font-size:13px;margin-top:15px;display:none;"></div>
                </form>
            </div>

            <script>
                jQuery(document).ready(function($) {
                    $('#jjwz-portal-login-form').on('submit', function(e) {
                        e.preventDefault();
                        var key = $('#portal-access-key').val();
                        var btn = $(this).find('button');
                        var err = $('#portal-login-err');

                        btn.prop('disabled', true).text('Verifying…');
                        err.hide();

                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'jjwz_portal_login',
                            access_key: key
                        }, function(res) {
                            if (res.success) {
                                window.location.href = res.data.redirect;
                            } else {
                                btn.prop('disabled', false).text('Access Archive');
                                err.text(res.data.message).fadeIn();
                            }
                        }).fail(function() {
                            btn.prop('disabled', false).text('Access Archive');
                            err.text('Server error. Please try again.').fadeIn();
                        });
                    });
                });
            </script>

        <?php elseif ( $is_expired ) : ?>
            <!-- EXPIRED WARNING SCREEN -->
            <div class="portal-login-card" style="max-width:550px;">
                <div style="font-size:48px;margin-bottom:15px;">⏳</div>
                <h2>ACCESS EXPIRED</h2>
                <p>Your online secure gallery access expired on <strong><?php echo esc_html( date( 'M j, Y', strtotime( $expiry ) ) ); ?></strong>. To restore review access or request an extension, please contact your account manager directly.</p>
                <?php
                $wa_number = get_option( 'jjwz_whatsapp_number' );
                if ( $wa_number ) :
                    $wa_link = 'https://wa.me/' . preg_replace( '/[^\d]/', '', $wa_number ) . '?text=' . rawurlencode( 'Hi, I would like to request an extension for my client gallery access key: ' . get_post_meta( $gallery_id, 'gallery_access_key', true ) );
                ?>
                    <a href="<?php echo esc_url( $wa_link ); ?>" class="portal-btn" style="text-decoration:none;display:inline-block;box-sizing:border-box;line-height:1.2;text-align:center;" target="_blank" rel="noopener noreferrer">💬 Contact via WhatsApp</a>
                <?php else : ?>
                    <div style="font-weight:600;color:var(--clr-portal-gold);"><?php echo esc_html( get_option( 'jjw_email', 'info@jjweddingz.com' ) ); ?></div>
                <?php endif; ?>
                <div style="margin-top:20px;">
                    <a href="#" class="portal-logout-btn" style="color:var(--clr-portal-gray);font-size:12px;text-decoration:none;">Log Out</a>
                </div>
            </div>

        <?php else : 
            // STATE B: DASHBOARD VIEW
            $client_name = get_post_meta( $gallery_id, 'gallery_client_name', true ) ?: 'Client';
            $brand       = get_post_meta( $gallery_id, 'gallery_brand', true ) ?: 'JJ WeddingZ';
            $event_date  = get_post_meta( $gallery_id, 'gallery_event_date', true ) ?: 'TBD';
            $access_key  = get_post_meta( $gallery_id, 'gallery_access_key', true );
            $curr_status = get_post_meta( $gallery_id, 'gallery_status', true ) ?: 'Raw Backed Up';
            $enable_dl   = get_post_meta( $gallery_id, 'gallery_enable_dl', true ) === '1';
            $dl_expiry   = get_post_meta( $gallery_id, 'gallery_download_expiry', true );

            // Check download expiry
            $dl_expired = false;
            if ( ! empty( $dl_expiry ) && $dl_expiry < $today ) {
                $dl_expired = true;
            }

            // Retrieve favorites and album selections count
            $favs_meta = get_post_meta( $gallery_id, 'gallery_favorites', true );
            $favs_list = $favs_meta ? json_decode( $favs_meta, true ) : [];
            if ( ! is_array( $favs_list ) ) { $favs_list = []; }

            $album_meta = get_post_meta( $gallery_id, 'gallery_album_selections', true );
            $album_list = $album_meta ? json_decode( $album_meta, true ) : [];
            if ( ! is_array( $album_list ) ) { $album_list = []; }

            // Get linked invoices
            $invoices = get_posts( [
                'post_type'  => 'jjwz_invoice',
                'meta_query' => [
                    [
                        'key'   => 'finance_gallery_id',
                        'value' => $gallery_id
                    ]
                ],
                'posts_per_page' => -1
            ] );

            // Progress timeline setup
            $all_stages = [
                'Raw Backed Up', 'Shoot Completed', 'Culling', 'Color Grading', 
                'High-End Retouching', 'Album Designing', 'Album Approved', 'Album Printing', 
                'Album Delivered', 'Ready for Delivery'
            ];
            $current_idx = array_search( $curr_status, $all_stages );
            if ( $current_idx === false ) { $current_idx = 0; }
        ?>
            <!-- HEADER -->
            <div class="portal-header">
                <div class="portal-header__left">
                    <h2><?php echo esc_html( $client_name ); ?></h2>
                    <span>💍 <?php echo esc_html( $brand ); ?> &bull; 📅 <?php echo esc_html( $event_date ); ?></span>
                </div>
                <div style="display:flex;align-items:center;gap:15px;">
                    <div style="background:#f7fafc;border:1px solid #e2e8f0;padding:8px 15px;border-radius:6px;font-size:12px;font-weight:600;color:var(--clr-portal-gray);">
                        ❤️ Favorites: <span id="favs-counter" style="color:var(--clr-portal-dark);"><?php echo count( $favs_list ); ?></span>
                        &nbsp;|&nbsp;
                        📖 Album Prints: <span id="album-counter" style="color:var(--clr-portal-gold);"><?php echo count( $album_list ); ?></span>
                    </div>
                    <button type="button" class="button portal-logout-btn" style="height:35px;line-height:33px;">Log Out</button>
                </div>
            </div>

            <!-- WORKFLOW TRACKING STEPPER -->
            <div class="portal-stepper">
                <h3 class="portal-stepper__title">COMMISSION PROCESSING STATUS</h3>
                <div class="portal-steps">
                    <?php foreach ( $all_stages as $idx => $st ) : 
                        $class = '';
                        if ( $idx === $current_idx ) {
                            $class = 'portal-step--active';
                        } elseif ( $idx < $current_idx ) {
                            $class = 'portal-step--completed';
                        }
                    ?>
                    <div class="portal-step <?php echo $class; ?>">
                        <div class="portal-step__label"><?php echo esc_html( $st ); ?></div>
                        <div class="portal-step__dot"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- FINANCE & PAYMENT MILESTONES -->
            <?php if ( ! empty( $invoices ) ) : ?>
            <div class="portal-finance">
                <h3 style="font-family:Georgia,serif;font-size:18px;margin-bottom:15px;color:var(--clr-portal-dark);">BILLING & PAYMENT MILESTONES</h3>
                <div class="portal-finance-grid">
                    <?php foreach ( $invoices as $inv ) : 
                        $total      = (float) get_post_meta( $inv->ID, 'finance_total', true );
                        $status     = get_post_meta( $inv->ID, 'finance_status', true ) ?: 'Pending';
                        
                        $milestones = [
                            'booking' => [ 'label' => 'Booking Amount', 'status' => get_post_meta( $inv->ID, 'milestone_booking_status', true ) ?: 'Pending', 'amt' => (float) get_post_meta( $inv->ID, 'milestone_booking_amount', true ) ],
                            'shoot'   => [ 'label' => 'Shoot Day Amount', 'status' => get_post_meta( $inv->ID, 'milestone_shoot_status', true ) ?: 'Pending', 'amt' => (float) get_post_meta( $inv->ID, 'milestone_shoot_amount', true ) ],
                            'album'   => [ 'label' => 'Album Approval Amount', 'status' => get_post_meta( $inv->ID, 'milestone_album_status', true ) ?: 'Pending', 'amt' => (float) get_post_meta( $inv->ID, 'milestone_album_amount', true ) ],
                            'final'   => [ 'label' => 'Final Delivery Amount', 'status' => get_post_meta( $inv->ID, 'milestone_final_status', true ) ?: 'Pending', 'amt' => (float) get_post_meta( $inv->ID, 'milestone_final_amount', true ) ]
                        ];
                    ?>
                    <div class="portal-invoice-card" id="portal-invoice-card-<?php echo $inv->ID; ?>">
                        <div class="portal-invoice-header">
                            <span class="portal-invoice-title">Invoice #<?php echo $inv->ID; ?></span>
                            <span class="portal-invoice-badge portal-invoice-badge--<?php echo strtolower(str_replace(' ', '', $status)); ?>"><?php echo esc_html($status); ?></span>
                        </div>
                        <div style="font-size:13px;line-height:1.8;color:var(--clr-portal-gray);">
                            <?php foreach ( $milestones as $key => $m ) : 
                                if ( $m['amt'] <= 0 ) { continue; }
                            ?>
                            <div style="display:flex;justify-content:space-between;margin-bottom:10px;align-items:center;border-bottom:1px dashed #edf2f7;padding-bottom:5px;">
                                <span><?php echo esc_html( $m['label'] ); ?> (₹<?php echo number_format($m['amt']); ?>)</span>
                                <div>
                                    <?php if ( $m['status'] === 'Paid' ) : ?>
                                        <span style="color:#10b981;font-weight:bold;">✔ Paid</span>
                                    <?php else : ?>
                                        <button type="button" class="button portal-pay-btn" data-invoice-id="<?php echo $inv->ID; ?>" data-milestone="<?php echo $key; ?>" data-label="<?php echo esc_attr( $m['label'] ); ?>" style="font-size:11px;background:var(--clr-portal-gold);color:#fff;border-color:var(--clr-portal-gold);font-weight:600;height:24px;padding:0 8px;">💳 Pay Now</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- PHOTO GALLERY & WATERMARK MEDIA GRID -->
            <div class="portal-gallery">
                <div class="portal-gallery-header">
                    <h3>SECURE PHOTO ARCHIVE</h3>
                    <div>
                        <?php if ( $enable_dl && ! $dl_expired ) : ?>
                            <button type="button" class="button button-primary jjwz-download-all" style="background:var(--clr-portal-dark);border-color:var(--clr-portal-dark);font-weight:600;">📥 Download All Images</button>
                        <?php else : ?>
                            <span style="color:var(--clr-portal-gray);font-size:12px;font-style:italic;">
                                <?php if ( $dl_expired ) : ?>
                                    ⚠️ Downloads expired on <?php echo esc_html( date( 'M j, Y', strtotime($dl_expiry) ) ); ?>.
                                <?php else : ?>
                                    🔒 Downloads locked until balance payment is finalized.
                                <?php endif; ?>
                            </span>
                        <?php endendif; ?>
                    </div>
                </div>

                <?php
                $image_ids_str = get_post_meta( $gallery_id, '_jjwz_gallery_images', true );
                $image_ids     = $image_ids_str ? array_filter( array_map( 'intval', explode( ',', $image_ids_str ) ) ) : [];

                if ( ! empty( $image_ids ) ) :
                ?>
                    <div class="portal-gallery-grid">
                        <?php foreach ( $image_ids as $img_id ) : 
                            $thumb_url = wp_get_attachment_image_url( $img_id, 'medium_large' );
                            $full_url  = wp_get_attachment_image_url( $img_id, 'full' );
                            if ( ! $thumb_url ) { continue; }

                            $is_fav   = in_array( $img_id, $favs_list );
                            $is_album = in_array( $img_id, $album_list );
                        ?>
                        <div class="portal-photo-item" data-photo-id="<?php echo $img_id; ?>" data-full-url="<?php echo esc_url( $full_url ); ?>">
                            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="Gallery Preview" loading="lazy">
                            <div class="portal-photo-overlay">
                                <button type="button" class="portal-photo-action portal-photo-action--fav <?php echo $is_fav ? 'is-active' : ''; ?>" title="Add to Favorites">❤️</button>
                                <button type="button" class="portal-photo-action portal-photo-action--album <?php echo $is_album ? 'is-active' : ''; ?>" title="Select for Print Album">📖</button>
                                <?php if ( $enable_dl && ! $dl_expired ) : ?>
                                    <a href="<?php echo esc_url( $full_url ); ?>" download class="portal-photo-action" style="text-decoration:none;" title="Download High-Res">📥</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div style="background:#fff;border:1.5px dashed var(--clr-portal-border);border-radius:8px;padding:50px;text-align:center;color:var(--clr-portal-gray);">
                        <span style="font-size:36px;display:block;margin-bottom:10px;">📸</span>
                        No preview files have been uploaded to this album yet. Check back shortly.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Block client clicks and drag saves -->
            <script>
                jQuery(document).ready(function($) {
                    // Prevent right-click on photos
                    $('.portal-photo-item img').on('contextmenu', function(e) {
                        e.preventDefault();
                        return false;
                    });

                    // Heart Favorites selection
                    var favorites = <?php echo wp_json_encode( $favs_list ); ?>;
                    var albumSelections = <?php echo wp_json_encode( $album_list ); ?>;

                    $('.portal-photo-action--fav').on('click', function(e) {
                        e.preventDefault();
                        var btn = $(this);
                        var itemId = parseInt(btn.closest('.portal-photo-item').data('photo-id'));
                        
                        var idx = favorites.indexOf(itemId);
                        if (idx > -1) {
                            favorites.splice(idx, 1);
                            btn.removeClass('is-active');
                        } else {
                            favorites.push(itemId);
                            btn.addClass('is-active');
                        }

                        $('#favs-counter').text(favorites.length);

                        // Save via AJAX
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'jjwz_portal_save_favorites',
                            favorites: JSON.stringify(favorites)
                        });
                    });

                    // Album Photo Selection
                    $('.portal-photo-action--album').on('click', function(e) {
                        e.preventDefault();
                        var btn = $(this);
                        var itemId = parseInt(btn.closest('.portal-photo-item').data('photo-id'));
                        
                        var idx = albumSelections.indexOf(itemId);
                        if (idx > -1) {
                            albumSelections.splice(idx, 1);
                            btn.removeClass('is-active');
                        } else {
                            albumSelections.push(itemId);
                            btn.addClass('is-active');
                        }

                        $('#album-counter').text(albumSelections.length);

                        // Save via AJAX
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'jjwz_portal_save_album_selections',
                            selections: JSON.stringify(albumSelections)
                        });
                    });

                    // Razorpay Order Creation and Checkout Integration
                    $('.portal-pay-btn').on('click', function(e) {
                        e.preventDefault();
                        var btn = $(this);
                        var invoiceId = btn.data('invoice-id');
                        var milestone = btn.data('milestone');
                        var label = btn.data('label');

                        btn.prop('disabled', true).text('Initializing…');

                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'jjwz_create_razorpay_order',
                            invoice_id: invoiceId,
                            milestone: milestone
                        }, function(res) {
                            if (res.success) {
                                var options = {
                                    key: res.data.key_id,
                                    amount: res.data.amount,
                                    currency: res.data.currency,
                                    name: '<?php echo esc_js($brand); ?>',
                                    description: 'Payment for: ' + label,
                                    order_id: res.data.order_id,
                                    handler: function(payment) {
                                        btn.text('Verifying…');
                                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                            action: 'jjwz_verify_razorpay_payment',
                                            razorpay_order_id: payment.razorpay_order_id,
                                            razorpay_payment_id: payment.razorpay_payment_id,
                                            razorpay_signature: payment.razorpay_signature,
                                            invoice_id: invoiceId,
                                            milestone: milestone
                                        }, function(verifyRes) {
                                            if (verifyRes.success) {
                                                alert('Payment recorded successfully! Refreshing dashboard.');
                                                window.location.reload();
                                            } else {
                                                alert('Verification error: ' + verifyRes.data.message);
                                                btn.prop('disabled', false).text('💳 Pay Now');
                                            }
                                        });
                                    },
                                    modal: {
                                        ondismiss: function() {
                                            btn.prop('disabled', false).text('💳 Pay Now');
                                        }
                                    }
                                };
                                var rzp = new Razorpay(options);
                                rzp.open();
                            } else {
                                alert('Razorpay error: ' + res.data.message);
                                btn.prop('disabled', false).text('💳 Pay Now');
                            }
                        }).fail(function() {
                            alert('Server communication error.');
                            btn.prop('disabled', false).text('💳 Pay Now');
                        });
                    });
                });
            </script>
        <?php endif; ?>

        <!-- Logout Action handler -->
        <script>
            jQuery(document).ready(function($) {
                $('.portal-logout-btn').on('click', function(e) {
                    e.preventDefault();
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                        action: 'jjwz_portal_logout'
                    }, function() {
                        window.location.reload();
                    });
                });
            });
        </script>

    </div>
</div>

<?php
get_footer();
