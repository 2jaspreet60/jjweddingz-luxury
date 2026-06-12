<?php
/**
 * page-services.php — Premium Services Page
 * Template Name: Services
 *
 * @package JJWeddingZ
 */

get_header();

// Elementor Canvas safeguard check
if ( function_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    get_footer();
    return;
}

/* ─── Dynamic field values ────────────────────────────────────────────── */
$post_id        = get_the_ID();
$page_headline  = jjwz_get_option( 'jjwz_services_headline', 'Our <em>Premium Services</em>', $post_id );
$service_blocks = jjwz_get_option( 'jjwz_service_blocks', [], $post_id );
$wa_link        = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', jjwz_get_option( 'jjwz_whatsapp_number', '919876543210' ) );

// Default service blocks when no ACF data
if ( empty( $service_blocks ) ) {
    $service_blocks = [
        [
            'svc_title' => 'Luxury Wedding Photography',
            'svc_desc'  => '<p>Selecting a wedding photographer is one of the most vital decisions you will make. Wedding photography has evolved; candid moments are now the staple of a complete wedding story. Our discreet professionals capture emotions in their purest forms while you and your guests remain completely immersed in the celebration.</p><p>We cover all wedding functions — Sanchao, Mehendi, Sangeet, Anand Karaj, and the Reception — with a full documentary approach that preserves every laugh, every tear, and every quiet in-between moment that makes your story uniquely yours.</p>',
            'svc_image' => null,
            'svc_slug'  => 'wedding-photography',
        ],
        [
            'svc_title' => 'Pre-Wedding Storytelling',
            'svc_desc'  => '<p>A pre-wedding shoot immortalizes your love story in a place close to your heart, away from prying eyes. It is not just about taking pictures; it is about telling your unique narrative in a way that makes you most comfortable. This session helps you build rapport with our team, ensuring absolute ease on the big day.</p><p>From the spiritual grandeur of Amritsar\'s heritage sites to the architectural majesty of Lutyens\' Delhi, we craft conceptual fine-art sessions that mirror international editorial publications.</p>',
            'svc_image' => null,
            'svc_slug'  => 'pre-wedding',
        ],
        [
            'svc_title' => 'Cinematography & Films',
            'svc_desc'  => '<p>Wedding cinematography is the art of documenting a marriage through a highly cinematic lens. Our creative team utilizes advanced lighting, diverse camera angles, and seamless editing to craft sweeping visual narratives. We deliver standard wide formats and optimized 9:16 vertical reels engineered to captivate audiences.</p><p>Shot on Sony FX3 cinema systems with G Master optics, our films feature authentic colour science, dynamic range, and Dolby-compatible audio — a true heirloom that will move generations.</p>',
            'svc_image' => null,
            'svc_slug'  => 'cinematography',
        ],
        [
            'svc_title' => 'Maternity & Baby Shoots (Newborn)',
            'svc_desc'  => '<p>The journey to parenthood is magical, fleeting, and deeply personal. Our maternity and newborn photography services are designed with the utmost care, patience, and safety in mind. We capture the serene beauty of your pregnancy and the delicate, precious first days of your baby\'s life.</p><p>Our dedicated studio maintains medical-grade sanitization protocols and precise climate control to ensure your newborn\'s complete safety and comfort throughout every session.</p>',
            'svc_image' => null,
            'svc_slug'  => 'maternity-newborn',
        ],
    ];
}

$service_icons = [ '💍', '🎬', '🎥', '👶' ];
?>

<!-- ═══════════════════════════════════════════════════════════
     SERVICES HERO
     ═══════════════════════════════════════════════════════════ -->
<section class="services-hero" aria-label="Services hero">
    <div class="container services-hero__inner">
        <?php jjwz_breadcrumb(); ?>
        <span class="eyebrow">What We Do</span>
        <h1 class="services-hero__headline display-title"><?php echo wp_kses_post( $page_headline ); ?></h1>
        <p class="lead services-hero__sub" style="margin-top:1.5rem;">Four distinct disciplines. One unwavering philosophy: authentic, unretouched, emotionally true visual storytelling.</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SERVICE QUICK NAV
     ═══════════════════════════════════════════════════════════ -->
<nav class="services-quick-nav" aria-label="Jump to service">
    <div class="container">
        <ul class="services-quick-nav__list">
            <?php foreach ( $service_blocks as $i => $svc ) : ?>
            <li>
                <a href="#service-<?php echo $i + 1; ?>" class="services-quick-nav__link" id="quick-nav-<?php echo $i + 1; ?>">
                    <span><?php echo $service_icons[ $i ] ?? '📸'; ?></span>
                    <?php echo esc_html( $svc['svc_title'] ); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<!-- ═══════════════════════════════════════════════════════════
     SERVICE BLOCKS
     ═══════════════════════════════════════════════════════════ -->
<?php foreach ( $service_blocks as $idx => $svc ) :
    $is_even = ( $idx % 2 === 0 );
    $img     = isset( $svc['svc_image']['url'] ) ? $svc['svc_image']['url']  : null;
    $img_alt = isset( $svc['svc_image']['alt'] ) ? $svc['svc_image']['alt']  : esc_html( $svc['svc_title'] );
    $slug    = $svc['svc_slug'] ?? '';
    $anchor  = 'service-' . ( $idx + 1 );
?>
<section class="service-block section <?php echo $is_even ? 'service-block--default' : 'service-block--alt'; ?>"
         id="<?php echo esc_attr( $anchor ); ?>"
         aria-label="<?php echo esc_attr( $svc['svc_title'] ); ?>">
    <div class="container">
        <div class="service-block__grid <?php echo $is_even ? '' : 'service-block__grid--reversed'; ?>">

            <!-- Text Content -->
            <div class="service-block__text" data-anim="fade-right">
                <span class="service-block__number eyebrow"><?php echo str_pad( $idx + 1, 2, '0', STR_PAD_LEFT ); ?></span>
                <h2 class="service-block__title section-title"><?php echo esc_html( $svc['svc_title'] ); ?></h2>
                <div class="service-block__desc lead">
                    <?php echo wp_kses_post( $svc['svc_desc'] ); ?>
                </div>
                <div class="service-block__features">
                    <?php
                    $feature_sets = [
                        [ 'Full Function Coverage (Sanchao to Reception)', 'Pre-Event Consultation', 'Dual-Card Backup on Every Unit', 'Delivery: 6–8 Weeks' ],
                        [ 'Custom Concept Development', 'Location Scouting Included', 'Multiple Outfit Changes', 'Premium Editing & Colour Grading' ],
                        [ '4K Cinema Quality Footage', 'Sony FX3 Cinema Systems', 'Cinematic Colour Grading', '16:9 Wide + 9:16 Vertical Reels' ],
                        [ 'Medical-Grade Sanitized Studio', 'Climate-Controlled Environment', 'Certified Newborn Posing', 'Premium Props & Wraps' ],
                    ];
                    $features = $feature_sets[ $idx ] ?? [];
                    foreach ( $features as $feat ) :
                    ?>
                    <div class="service-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold)" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?php echo esc_html( $feat ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="service-block__ctas">
                    <a href="<?php echo esc_url( $wa_link ); ?>"
                       class="btn btn--primary" id="svc-inquire-<?php echo $idx + 1; ?>"
                       target="_blank" rel="noopener noreferrer">
                        Inquire About This Service
                    </a>
                    <?php if ( $slug ) : ?>
                    <a href="<?php echo esc_url( home_url( '/services/' . $slug ) ); ?>"
                       class="btn btn--ghost" id="svc-more-<?php echo $idx + 1; ?>">
                        Full Details
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Image / Placeholder -->
            <div class="service-block__media" data-anim="fade-left">
                <div class="service-block__image-wrap">
                    <?php 
                    $service_img_url = $img;
                    if ( empty( $service_img_url ) ) {
                        $service_img_url = jjwz_get_option( 'jjw_default_placeholder_service' );
                    }
                    if ( empty( $service_img_url ) ) {
                        $service_img_url = get_template_directory_uri() . '/assets/images/placeholder-category-default.png';
                    }
                    ?>
                    <img src="<?php echo esc_url( $service_img_url ); ?>"
                         alt="<?php echo esc_attr( $img_alt ); ?>"
                         loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>"
                         class="service-block__img">
                    <div class="service-block__image-tag">
                        <span><?php echo esc_html( $svc['svc_title'] ); ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php if ( $idx < count( $service_blocks ) - 1 ) : ?>
<div class="services-divider" aria-hidden="true"><span class="services-divider__line"></span></div>
<?php endif; ?>

<?php endforeach; ?>

<!-- ═══════════════════════════════════════════════════════════
     WHY CHOOSE US
     ═══════════════════════════════════════════════════════════ -->
<section class="why-us section" style="background:var(--clr-cream);" aria-label="Why choose us">
    <div class="container">
        <div class="text-center" style="margin-bottom:3rem;">
            <span class="eyebrow">Why JJ WeddingZ</span>
            <h2 class="section-title">The <em>Difference</em> Is<br>in the Detail</h2>
        </div>
        <div class="why-us__grid">
            <?php
            $reasons = [
                [ 'icon' => '🛡️', 'title' => 'Identity Protection Guarantee',  'desc' => 'We sign a formal pledge: zero face-swapping, zero skin whitening, zero artificial identity alteration. Your face, your story.' ],
                [ 'icon' => '💾', 'title' => 'Dual-Card Data Redundancy',       'desc' => 'All camera units record to two memory cards simultaneously. Your memories are protected from the moment of capture.' ],
                [ 'icon' => '🎯', 'title' => 'Limited Client Roster',           'desc' => 'We accept a strictly limited number of commissions per season to guarantee every client receives our complete dedication.' ],
                [ 'icon' => '✈️', 'title' => 'Destination Wedding Ready',       'desc' => 'Two branches, one vision — and passports always ready. We travel across India and internationally for your love story.' ],
                [ 'icon' => '🔐', 'title' => 'Private Client Gallery Portal',   'desc' => 'Your gallery is exclusively yours. Password-protected, download-enabled, and never shared publicly without consent.' ],
                [ 'icon' => '📷', 'title' => 'Cinema-Grade Equipment',          'desc' => 'Nikon Z6 III stills + Sony FX3 cinema setups + G Master optics ensure a technical level matched only by international productions.' ],
            ];
            foreach ( $reasons as $i => $r ) :
            ?>
            <div class="why-card" data-anim="fade-up" data-anim-delay="<?php echo $i * 60; ?>">
                <div class="why-card__icon"><?php echo $r['icon']; ?></div>
                <h3 class="why-card__title"><?php echo esc_html( $r['title'] ); ?></h3>
                <p class="why-card__desc"><?php echo esc_html( $r['desc'] ); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     FULL FAQ — BOTH CATEGORIES
     ═══════════════════════════════════════════════════════════ -->
<section class="services-faq section" aria-label="Services FAQ">
    <div class="container">
        <div class="text-center" style="margin-bottom:3rem;">
            <span class="eyebrow">Frequently Asked</span>
            <h2 class="section-title">Questions &amp;<br><em>Answers</em></h2>
        </div>

        <div class="services-faq__tabs" role="tablist" aria-label="FAQ categories">
            <button class="faq-tab-btn is-active" data-tab="wedding" role="tab" aria-selected="true" id="faq-tab-wedding">Wedding &amp; Pre-Wedding</button>
            <button class="faq-tab-btn" data-tab="maternity" role="tab" aria-selected="false" id="faq-tab-maternity">Maternity, Baby &amp; Newborn</button>
        </div>

        <div class="services-faq__panels">
            <div class="faq-panel is-active" id="faq-panel-wedding" role="tabpanel" aria-labelledby="faq-tab-wedding">
                <?php echo jjwz_render_faq_accordion( 'wedding', -1 ); ?>
            </div>
            <div class="faq-panel" id="faq-panel-maternity" role="tabpanel" aria-labelledby="faq-tab-maternity" hidden>
                <?php echo jjwz_render_faq_accordion( 'maternity', -1 ); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
