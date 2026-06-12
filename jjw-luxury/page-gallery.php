<?php
/**
 * page-gallery.php — Private Client Proofing Portal
 * Template Name: Client Gallery
 *
 * Renders a secure, password-gated client gallery.
 * Authentication is handled via a custom session-based check.
 * Gallery images are managed in the ACF 'gallery_images' field.
 *
 * @package JJWeddingZ
 */

get_header();

/* ─── Auth check via the core plugin (class-gallery-access.php) ────────── */
$is_authenticated = false;
$auth_error       = '';
$gallery_id       = get_the_ID();
$access_key       = get_post_meta( $gallery_id, 'gallery_access_key', true );
if ( empty( $access_key ) && function_exists( 'get_field' ) ) {
    $access_key = get_field( 'gallery_access_key' );
}

$client_name      = get_post_meta( $gallery_id, 'gallery_client_name', true );
if ( empty( $client_name ) && function_exists( 'get_field' ) ) {
    $client_name = get_field( 'gallery_client_name' );
}

$event_date       = get_post_meta( $gallery_id, 'gallery_event_date', true );
if ( empty( $event_date ) && function_exists( 'get_field' ) ) {
    $event_date = get_field( 'gallery_event_date' );
}

$gallery_images   = [];
if ( function_exists( 'get_field' ) ) {
    $gallery_images = get_field( 'gallery_images' );
}
if ( empty( $gallery_images ) ) {
    $image_ids_str = get_post_meta( $gallery_id, '_jjwz_gallery_images', true );
    if ( $image_ids_str ) {
        $image_ids = array_filter( array_map( 'intval', explode( ',', $image_ids_str ) ) );
        foreach ( $image_ids as $id ) {
            $gallery_images[] = [
                'url' => wp_get_attachment_url( $id ),
                'alt' => get_post_meta( $id, '_wp_attachment_image_alt', true ) ?: 'Gallery photo',
                'ID'  => $id,
            ];
        }
    }
}

$enable_download  = get_post_meta( $gallery_id, 'gallery_enable_dl', true );
if ( $enable_download === '' ) {
    $enable_download = true;
} else {
    $enable_download = filter_var( $enable_download, FILTER_VALIDATE_BOOLEAN );
}
if ( function_exists( 'get_field' ) && get_field( 'gallery_enable_dl' ) !== null ) {
    $enable_download = get_field( 'gallery_enable_dl' );
}

// Session-based auth
if ( ! session_id() ) { @session_start(); }
$session_key      = 'jjwz_gallery_auth_' . $gallery_id;
$is_authenticated = ( isset( $_SESSION[ $session_key ] ) && $_SESSION[ $session_key ] === true );

// Handle form submission
if ( isset( $_POST['jjwz_gallery_key'] ) && isset( $_POST['jjwz_gallery_nonce'] ) ) {
    if ( wp_verify_nonce( $_POST['jjwz_gallery_nonce'], 'jjwz_gallery_access_' . $gallery_id ) ) {
        $submitted_key = sanitize_text_field( trim( $_POST['jjwz_gallery_key'] ) );
        if ( $access_key && hash_equals( $access_key, $submitted_key ) ) {
            $_SESSION[ $session_key ] = true;
            $is_authenticated         = true;
        } else {
            $auth_error = 'Incorrect access key. Please try again or contact your photographer.';
        }
    }
}

// Handle logout
if ( isset( $_GET['jjwz_logout'] ) && $_GET['jjwz_logout'] === '1' ) {
    unset( $_SESSION[ $session_key ] );
    wp_safe_redirect( get_permalink() );
    exit;
}
?>

<!-- ═══════════════════════════════════════════════════════════
     GALLERY HERO
     ═══════════════════════════════════════════════════════════ -->
<section class="gallery-hero" aria-label="Client gallery">
    <div class="container gallery-hero__inner">
        <span class="eyebrow">Private Gallery</span>
        <h1 class="gallery-hero__title display-title">
            <?php if ( $is_authenticated && $client_name ) : ?>
                <?php echo esc_html( $client_name ); ?>'s <em>Gallery</em>
            <?php else : ?>
                Client <em>Gallery Access</em>
            <?php endif; ?>
        </h1>
        <?php if ( $is_authenticated && $event_date ) : ?>
            <p class="gallery-hero__date lead">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Event Date: <?php echo esc_html( $event_date ); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php if ( ! $is_authenticated ) : ?>
<!-- ═══════════════════════════════════════════════════════════
     ACCESS GATE (UNAUTHENTICATED)
     ═══════════════════════════════════════════════════════════ -->
<section class="gallery-gate section" aria-label="Gallery access form">
    <div class="container">
        <div class="gallery-gate__card">

            <div class="gallery-gate__icon" aria-hidden="true">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold)" stroke-width="1" aria-label="Secure gallery lock icon">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            <h2 class="gallery-gate__heading">Secure Gallery Access</h2>
            <p class="gallery-gate__subtext">This gallery is privately held for our valued client. Please enter your unique gallery access key below. You would have received this key via email or WhatsApp from our studio.</p>

            <?php if ( $auth_error ) : ?>
            <div class="gallery-gate__error" role="alert" aria-live="assertive">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo esc_html( $auth_error ); ?>
            </div>
            <?php endif; ?>

            <form class="gallery-gate__form" method="post" autocomplete="off" aria-label="Gallery access form" novalidate>
                <?php wp_nonce_field( 'jjwz_gallery_access_' . $gallery_id, 'jjwz_gallery_nonce' ); ?>
                <div class="form-group">
                    <label for="jjwz_gallery_key" class="form-label sr-only">Secure Gallery Access Key</label>
                    <input type="password"
                           id="jjwz_gallery_key"
                           name="jjwz_gallery_key"
                           class="form-control gallery-gate__input"
                           placeholder="Enter your Gallery Access Key"
                           autocomplete="off"
                           required
                           aria-required="true"
                           aria-describedby="gallery-key-hint">
                    <small id="gallery-key-hint" class="gallery-gate__hint">Your access key is case-sensitive.</small>
                </div>
                <button type="submit" class="btn btn--primary gallery-gate__submit" id="gallery-access-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 10 4 15 9 20"/><path d="M20 4v7a4 4 0 01-4 4H4"/></svg>
                    Access My Gallery
                </button>
            </form>

            <p class="gallery-gate__support">
                Don't have your key?
                <a href="<?php echo esc_url( 'https://wa.me/' . preg_replace( '/[^0-9]/', '', jjwz_get_option( 'jjwz_whatsapp_number', '919876543210' ) ) . '?text=I%20need%20my%20gallery%20access%20key' ); ?>"
                   target="_blank" rel="noopener noreferrer" id="gallery-support-wa">
                   Contact us on WhatsApp →
                </a>
            </p>

        </div>
    </div>
</section>

<?php else : ?>
<!-- ═══════════════════════════════════════════════════════════
     AUTHENTICATED GALLERY VIEW
     ═══════════════════════════════════════════════════════════ -->
<section class="client-gallery section" aria-label="Private photo gallery">
    <div class="container">

        <!-- Gallery Toolbar -->
        <div class="gallery-toolbar flex-between">
            <div class="gallery-toolbar__info">
                <span class="gallery-image-count">
                    <?php echo ! empty( $gallery_images ) ? count( $gallery_images ) . ' photos' : 'Gallery loading...'; ?>
                </span>
            </div>
            <div class="gallery-toolbar__actions">
                <?php if ( $enable_download ) : ?>
                <button class="btn btn--outline gallery-dl-all-btn" id="download-all-btn"
                        aria-label="Download all gallery images">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download All
                </button>
                <?php endif; ?>
                <a href="<?php echo esc_url( add_query_arg( 'jjwz_logout', '1', get_permalink() ) ); ?>"
                   class="btn btn--ghost gallery-logout-btn" id="gallery-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Exit Gallery
                </a>
            </div>
        </div>

        <!-- Photo Grid -->
        <?php if ( ! empty( $gallery_images ) ) : ?>
        <div class="client-gallery__grid jjwz-masonry" id="client-gallery-grid">
            <?php foreach ( $gallery_images as $img_index => $image ) :
                $img_url   = isset( $image['url'] )   ? $image['url']   : '';
                $img_alt   = isset( $image['alt'] )   ? $image['alt']   : 'Gallery photo ' . ( $img_index + 1 );
                $img_full  = isset( $image['url'] )   ? $image['url']   : $img_url;
                $img_id    = isset( $image['ID'] )    ? $image['ID']    : 0;
                if ( ! $img_url ) { continue; }
            ?>
            <div class="gallery-item" data-index="<?php echo $img_index; ?>" data-full="<?php echo esc_url( $img_full ); ?>" data-image-id="<?php echo esc_attr( $img_id ); ?>">
                <div class="gallery-item__media">
                    <img src="<?php echo esc_url( $img_url ); ?>"
                         alt="<?php echo esc_attr( $img_alt ); ?>"
                         loading="<?php echo $img_index < 6 ? 'eager' : 'lazy'; ?>"
                         decoding="async">
                </div>
                <div class="gallery-item__overlay">
                    <button class="gallery-item__view-btn" data-index="<?php echo $img_index; ?>" aria-label="View full size photo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    </button>
                    <?php if ( $enable_download ) : ?>
                    <a href="<?php echo esc_url( $img_url ); ?>"
                       download="jjweddingz-photo-<?php echo $img_index + 1; ?>.jpg"
                       class="gallery-item__dl-btn"
                       aria-label="Download photo <?php echo $img_index + 1; ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Lightbox -->
        <div class="gallery-lightbox" id="gallery-lightbox" role="dialog" aria-modal="true" aria-label="Photo lightbox" hidden>
            <div class="lightbox__backdrop" id="lightbox-backdrop"></div>
            <div class="lightbox__inner">
                <button class="lightbox__close" id="lightbox-close" aria-label="Close lightbox">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <button class="lightbox__nav lightbox__nav--prev" id="lightbox-prev" aria-label="Previous photo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="lightbox__media" id="lightbox-media">
                    <img src="" alt="" id="lightbox-img" class="lightbox__img">
                </div>
                <button class="lightbox__nav lightbox__nav--next" id="lightbox-next" aria-label="Next photo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                <div class="lightbox__footer">
                    <span class="lightbox__counter" id="lightbox-counter">1 / <?php echo count( $gallery_images ); ?></span>
                    <?php if ( $enable_download ) : ?>
                    <a href="#" class="lightbox__download" id="lightbox-download" download aria-label="Download current photo">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php else : ?>
        <div class="gallery-empty" role="status" aria-live="polite">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold)" stroke-width="0.75" aria-hidden="true"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <h3>Gallery Coming Soon</h3>
            <p>Your photographs are being carefully edited and curated. We will notify you as soon as your gallery is ready.</p>
        </div>
        <?php endif; ?>

        <!-- Client Comment Section for Album Selection -->
        <div class="gallery-comments" id="gallery-comments">
            <h3 class="gallery-comments__title">Album Selection Notes</h3>
            <p class="gallery-comments__desc">Use the comment box below to note the photo numbers you love most for your print album. Our team will review your selections during the album design process.</p>
            <?php if ( comments_open() ) : ?>
                <?php comment_form( [
                    'title_reply'          => 'Add Your Album Notes',
                    'label_submit'         => 'Submit Notes',
                    'comment_field'        => '<div class="form-group"><label for="comment" class="form-label">Your Notes (e.g. "Photo 1, 4, 7 for the album")</label><textarea id="comment" name="comment" class="form-control" rows="5" placeholder="List photo numbers and any comments..." required aria-required="true"></textarea></div>',
                    'class_submit'         => 'btn btn--primary',
                    'comment_notes_before' => '',
                    'comment_notes_after'  => '',
                ] ); ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php endif; // end is_authenticated ?>

<?php get_footer(); ?>
