<?php
/**
 * page-about.php — About Us Editorial Layout
 * Template Name: About Us
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
$post_id      = get_the_ID();
$headline     = jjwz_get_option( 'jjwz_about_headline', 'Eleven Years of Uncompromising<br><em>Visual Excellence</em>', $post_id );
$intro        = jjwz_get_option( 'jjwz_about_intro', '<p>Founded in 2013 by Jaspreet Singh, JJ WeddingZ Photography has spent over a decade establishing itself as the benchmark for luxury wedding, maternity, and newborn photography across Northern India. Operating dual creative branches from Delhi NCR and Amritsar, our team of highly trained visual artists, cinematographers, and post-production specialists brings an internationally refined aesthetic to every single project we undertake.</p>', $post_id );
$founder_name = jjwz_get_option( 'jjwz_about_founder_name', 'Jaspreet Singh' );
$founder_bio  = jjwz_get_option( 'jjwz_about_founder_bio', '<p>Jaspreet began his photography journey over 11 years ago, driven by an unwavering belief that true luxury imagery lies not in artificial perfection but in the authentic preservation of genuine human emotion. His philosophy is simple and non-negotiable: we protect your identity. Our editing methodology maintains 100% of your original facial features and natural skin tones — we reject face-swapping, skin-whitening filters, and synthetic AI enhancement entirely.</p><p>This commitment to authenticity has earned JJ WeddingZ a devoted clientele across Delhi NCR, Amritsar, and an expanding roster of international destination wedding commissions.</p>' );
$founder_img  = jjwz_get_option( 'jjwz_about_founder_img' );
$founder_img_url = '';
if ( is_array( $founder_img ) && isset( $founder_img['url'] ) ) {
    $founder_img_url = $founder_img['url'];
} elseif ( is_numeric( $founder_img ) ) {
    $founder_img_url = wp_get_attachment_image_url( $founder_img, 'full' );
} elseif ( is_string( $founder_img ) && ! empty( $founder_img ) ) {
    $founder_img_url = $founder_img;
}
if ( empty( $founder_img_url ) ) {
    $founder_img_url = jjwz_get_option( 'jjw_default_placeholder_founder' );
}
if ( empty( $founder_img_url ) ) {
    $founder_img_url = get_template_directory_uri() . '/assets/images/placeholder-founder.png';
}
$hero_img     = jjwz_get_option( 'jjwz_about_hero_img', '', $post_id );
$gear_items   = jjwz_get_option( 'jjwz_about_gear', [], $post_id );
$wa_link      = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', jjwz_get_option( 'jjwz_whatsapp_number', '919876543210' ) );

// Default gear if no ACF data
if ( empty( $gear_items ) ) {
    $gear_items = [
        [ 'gear_name' => 'Nikon Z6 III',          'gear_desc' => 'Primary Still Photography — Full-Frame Mirrorless, 24.5MP BSI CMOS with Dual Card Slots for Instant Redundancy' ],
        [ 'gear_name' => 'Sony FX3',              'gear_desc' => 'Cinema Videography — Full-Frame Cinema Line, 4K 120fps, S-Cinetone Color Science for Master Cinematography' ],
        [ 'gear_name' => 'G Master Series Lenses', 'gear_desc' => 'Sony 24-70mm f/2.8 GM II & 85mm f/1.4 GM — Unmatched Bokeh Rendering and Optical Sharpness' ],
        [ 'gear_name' => 'Nikon Nikkor Z Series',  'gear_desc' => 'Nikkor Z 50mm f/1.2 S & 70-200mm f/2.8 VR S — Compression Telephoto for Clean Separation and Bokeh' ],
        [ 'gear_name' => 'Profoto Lighting',       'gear_desc' => 'Profoto B10X Plus & A10 Units — Off-Camera HSS Flash with Color-Calibrated Output' ],
        [ 'gear_name' => 'DJI RS3 Pro Gimbal',     'gear_desc' => 'Motorised 3-Axis Stabilisation for Fluid Cinematic Movement Sequences' ],
    ];
}
?>

<!-- ═══════════════════════════════════════════════════════════
     ABOUT HERO
     ═══════════════════════════════════════════════════════════ -->
<section class="about-hero" aria-label="About hero">
    <div class="about-hero__bg" aria-hidden="true">
        <?php if ( $hero_img && isset( $hero_img['url'] ) ) : ?>
            <img src="<?php echo esc_url( $hero_img['url'] ); ?>" alt="" class="about-hero__img" fetchpriority="high" decoding="sync">
        <?php else : ?>
            <div class="about-hero__gradient"></div>
        <?php endif; ?>
        <div class="about-hero__overlay"></div>
    </div>
    <div class="container about-hero__content">
        <?php jjwz_breadcrumb(); ?>
        <span class="eyebrow">Our Story</span>
        <h1 class="about-hero__headline display-title"><?php echo wp_kses_post( $headline ); ?></h1>
        <div class="about-hero__scroll-hint" aria-hidden="true">
            <span class="scroll-line"></span><span class="scroll-text">Scroll</span>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     BRAND NARRATIVE
     ═══════════════════════════════════════════════════════════ -->
<section class="brand-narrative section" aria-label="Brand narrative">
    <div class="container">
        <div class="brand-narrative__grid">
            <div class="brand-narrative__text" data-anim="fade-right">
                <?php echo wp_kses_post( $intro ); ?>
                <div class="brand-narrative__signature">
                    <div class="brand-nar__signature-line"></div>
                    <span>Jaspreet Singh, Founder &amp; Lead Photographer</span>
                </div>
            </div>
            <div class="brand-narrative__stats" data-anim="fade-left">
                <div class="brand-stat-card">
                    <div class="brand-stat-card__number">11<span>+</span></div>
                    <div class="brand-stat-card__label">Years of Excellence</div>
                </div>
                <div class="brand-stat-card">
                    <div class="brand-stat-card__number">2</div>
                    <div class="brand-stat-card__label">Branch Locations</div>
                </div>
                <div class="brand-stat-card">
                    <div class="brand-stat-card__number">500<span>+</span></div>
                    <div class="brand-stat-card__label">Weddings Documented</div>
                </div>
                <div class="brand-stat-card brand-stat-card--highlight">
                    <div class="brand-stat-card__number">100<span>%</span></div>
                    <div class="brand-stat-card__label">Identity Preserved</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     FOUNDER EDITORIAL PROFILE
     ═══════════════════════════════════════════════════════════ -->
<section class="founder-profile section" style="background:var(--clr-cream);" aria-label="Founder profile">
    <div class="container">
        <div class="founder-profile__grid">
            <div class="founder-profile__portrait" data-anim="fade-right">
                <?php if ( $founder_img_url ) : ?>
                    <img src="<?php echo esc_url( $founder_img_url ); ?>"
                         alt="<?php echo esc_attr( $founder_name ); ?> — Founder, JJ WeddingZ Photography"
                         loading="lazy">
                <?php endif; ?>
                <div class="founder-profile__name-tag">
                    <strong><?php echo esc_html( $founder_name ); ?></strong>
                    <span><?php echo esc_html( jjwz_get_option( 'jjwz_about_founder_designation', 'Founder &amp; Lead Photographer' ) ); ?></span>
                    <span><?php echo esc_html( jjwz_get_option( 'jjwz_about_founder_experience', '11+' ) ); ?> Experience</span>
                </div>
            </div>
            <div class="founder-profile__bio" data-anim="fade-left">
                <span class="eyebrow">Meet the Artist</span>
                <h2 class="section-title"><?php echo esc_html( $founder_name ); ?></h2>
                <div class="founder-bio__text lead">
                    <?php echo wp_kses_post( $founder_bio ); ?>
                </div>
                <div class="founder-profile__values">
                    <?php
                    $values = [
                        [ 'icon' => '🎯', 'title' => '100% Identity Retained',  'desc' => 'We never alter facial features, skin tone, or natural identity in post-production.' ],
                        [ 'icon' => '🤐', 'title' => 'Client Discretion First', 'desc' => 'Your private moments remain private. Strict gallery confidentiality guaranteed.' ],
                        [ 'icon' => '🌍', 'title' => 'Destination Ready',       'desc' => 'Passports always prepared. We travel across India and internationally.' ],
                    ];
                    foreach ( $values as $v ) :
                    ?>
                    <div class="founder-value-item">
                        <div class="founder-value-item__icon"><?php echo $v['icon']; ?></div>
                        <div>
                            <strong class="founder-value-item__title"><?php echo esc_html( $v['title'] ); ?></strong>
                            <p class="founder-value-item__desc"><?php echo esc_html( $v['desc'] ); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php
                // Load founder contact details
                $founder_email     = jjwz_get_option( 'jjwz_about_founder_email', '' );
                $founder_phone     = jjwz_get_option( 'jjwz_about_founder_phone', '' );
                $founder_whatsapp  = jjwz_get_option( 'jjwz_about_founder_whatsapp', '' );
                $founder_location  = jjwz_get_option( 'jjwz_about_founder_location', '' );

                $show_email     = jjwz_get_option( 'jjwz_about_founder_show_email', '1' );
                $show_phone     = jjwz_get_option( 'jjwz_about_founder_show_phone', '1' );
                $show_whatsapp  = jjwz_get_option( 'jjwz_about_founder_show_whatsapp', '1' );
                $show_location  = jjwz_get_option( 'jjwz_about_founder_show_location', '1' );

                $show_instagram = jjwz_get_option( 'jjwz_about_founder_show_instagram', '1' );
                $show_facebook  = jjwz_get_option( 'jjwz_about_founder_show_facebook', '1' );
                $show_youtube   = jjwz_get_option( 'jjwz_about_founder_show_youtube', '1' );
                $show_linkedin  = jjwz_get_option( 'jjwz_about_founder_show_linkedin', '1' );

                $instagram = jjwz_get_option( 'jjwz_about_founder_instagram', '' );
                $facebook  = jjwz_get_option( 'jjwz_about_founder_facebook', '' );
                $youtube   = jjwz_get_option( 'jjwz_about_founder_youtube', '' );
                $linkedin  = jjwz_get_option( 'jjwz_about_founder_linkedin', '' );

                if ( ( '1' === $show_email && $founder_email ) || ( '1' === $show_phone && $founder_phone ) || ( '1' === $show_whatsapp && $founder_whatsapp ) || ( '1' === $show_location && $founder_location ) ) :
                ?>
                <div class="founder-contact-block" style="margin-top: 2.5rem; border-top: 1px solid var(--clr-border); padding-top: 2rem;">
                    <h4 style="font-family: var(--font-display); font-size: var(--text-lg); margin-bottom: 1rem; color: var(--clr-obsidian); letter-spacing: -0.01em;">Direct Contact</h4>
                    <ul class="founder-contact-list" style="display: flex; flex-direction: column; gap: 0.75rem; font-size: var(--text-sm); list-style: none; padding: 0;">
                        <?php if ( '1' === $show_email && $founder_email ) : ?>
                            <li style="display: flex; align-items: center; gap: 0.75rem; color: var(--clr-mist);">
                                <span style="color: var(--clr-gold); font-size: 1.1rem; width: 20px;">✉</span> <a href="mailto:<?php echo esc_attr( $founder_email ); ?>" style="color: inherit; transition: color var(--transition-fast);"><?php echo esc_html( $founder_email ); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ( '1' === $show_phone && $founder_phone ) : ?>
                            <li style="display: flex; align-items: center; gap: 0.75rem; color: var(--clr-mist);">
                                <span style="color: var(--clr-gold); font-size: 1.1rem; width: 20px;">📞</span> <a href="tel:<?php echo esc_attr( preg_replace('/[^0-9+]/', '', $founder_phone) ); ?>" style="color: inherit; transition: color var(--transition-fast);"><?php echo esc_html( $founder_phone ); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ( '1' === $show_whatsapp && $founder_whatsapp ) : ?>
                            <li style="display: flex; align-items: center; gap: 0.75rem; color: var(--clr-mist);">
                                <span style="color: var(--clr-gold); font-size: 1.1rem; width: 20px;">💬</span> <a href="https://wa.me/<?php echo esc_attr( preg_replace('/[^0-9]/', '', $founder_whatsapp) ); ?>" target="_blank" rel="noopener noreferrer" style="color: inherit; transition: color var(--transition-fast);">Chat on WhatsApp</a>
                            </li>
                        <?php endif; ?>
                        <?php if ( '1' === $show_location && $founder_location ) : ?>
                            <li style="display: flex; align-items: center; gap: 0.75rem; color: var(--clr-mist);">
                                <span style="color: var(--clr-gold); font-size: 1.1rem; width: 20px;">📍</span> <span style="font-weight: 400;"><?php echo esc_html( $founder_location ); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <?php
                    $has_socials = ( '1' === $show_instagram && $instagram ) || ( '1' === $show_facebook && $facebook ) || ( '1' === $show_youtube && $youtube ) || ( '1' === $show_linkedin && $linkedin );
                    if ( $has_socials ) :
                    ?>
                    <div class="founder-socials" style="margin-top: 1.5rem; display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <span style="font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.08em; color: var(--clr-fog); font-weight: 500;">Connect:</span>
                        <div style="display: flex; gap: 0.75rem;">
                            <?php if ( '1' === $show_instagram && $instagram ) : ?>
                                <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline" style="padding: 0.35rem 0.75rem; font-size: var(--text-xs); line-height: 1;">Instagram</a>
                            <?php endif; ?>
                            <?php if ( '1' === $show_facebook && $facebook ) : ?>
                                <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline" style="padding: 0.35rem 0.75rem; font-size: var(--text-xs); line-height: 1;">Facebook</a>
                            <?php endif; ?>
                            <?php if ( '1' === $show_youtube && $youtube ) : ?>
                                <a href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline" style="padding: 0.35rem 0.75rem; font-size: var(--text-xs); line-height: 1;">YouTube</a>
                            <?php endif; ?>
                            <?php if ( '1' === $show_linkedin && $linkedin ) : ?>
                                <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline" style="padding: 0.35rem 0.75rem; font-size: var(--text-xs); line-height: 1;">LinkedIn</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     DYNAMIC TEAM SECTION (HIDDEN IF EMPTY)
     ═══════════════════════════════════════════════════════════ -->
<?php
$team_q = new WP_Query( [
    'post_type'      => 'jjwz_team',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
] );

if ( $team_q->have_posts() ) :
?>
<section class="team-section section" aria-label="Our Team" style="background-color: var(--clr-warm-white);">
    <div class="container">
        <div class="text-center" style="margin-bottom:3.5rem;">
            <span class="eyebrow">Creative Team</span>
            <h2 class="section-title">The Masters Behind <em>the Lens</em></h2>
            <p class="lead" style="margin-top:1rem;margin-inline:auto;">Collaborating under Jaspreet's guidance to document your story with singular vision and uncompromising technical standards.</p>
        </div>
        <div class="team-grid">
            <?php 
            while ( $team_q->have_posts() ) : 
                $team_q->the_post();
                $member_id    = get_the_ID();
                $designation  = jjwz_get_option( 'team_designation', '', $member_id );
                $experience   = jjwz_get_option( 'team_experience', '', $member_id );
                $instagram_url = jjwz_get_option( 'team_instagram', '', $member_id );
                $thumb        = get_the_post_thumbnail_url( $member_id, 'medium_large' );
                ?>
                <div class="team-card" data-anim="fade-up">
                    <div class="team-card__media">
                        <?php if ( $thumb ) : ?>
                            <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <div class="team-placeholder">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke-width="1"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span><?php esc_html_e( 'Visual Artist', 'jjw-luxury' ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="team-card__body">
                        <h3 class="team-card__name"><?php the_title(); ?></h3>
                        <div class="team-card__meta">
                            <span class="team-card__role"><?php echo esc_html( $designation ); ?></span>
                            <?php if ( $experience ) : ?>
                                <span class="team-card__exp"><?php echo esc_html( $experience ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
            endwhile; 
            wp_reset_postdata(); 
            ?>
        </div>
    </div>
</section>
<?php 
endif; 
?>

<!-- ═══════════════════════════════════════════════════════════
     GEAR SHOWCASE
     ═══════════════════════════════════════════════════════════ -->
<section class="gear-showcase section" aria-label="Camera equipment">
    <div class="container">
        <div class="text-center" style="margin-bottom:3rem;">
            <span class="eyebrow">Our Equipment</span>
            <h2 class="section-title">Professional <em>Cinema &amp; Photography</em><br>Arsenal</h2>
            <p class="lead" style="margin-top:1rem;margin-inline:auto;">Every piece of equipment is professionally maintained, insured, and configured with dual-slot recording for immediate on-site data redundancy.</p>
        </div>
        <div class="gear-grid">
            <?php foreach ( $gear_items as $i => $gear ) : ?>
            <div class="gear-card" data-anim="fade-up" data-anim-delay="<?php echo $i * 60; ?>">
                <div class="gear-card__icon" aria-hidden="true">
                    <?php if ( ! empty( $gear['gear_icon'] ) ) : ?>
                        <img src="<?php echo esc_url( $gear['gear_icon'] ); ?>" alt="" loading="lazy">
                    <?php else : ?>
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold)" stroke-width="1" aria-hidden="true"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <?php endif; ?>
                </div>
                <h3 class="gear-card__title"><?php echo esc_html( $gear['gear_name'] ); ?></h3>
                <p class="gear-card__desc"><?php echo esc_html( $gear['gear_desc'] ); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     BRAND TIMELINE
     ═══════════════════════════════════════════════════════════ -->
<section class="brand-timeline section" style="background:var(--clr-obsidian);" aria-label="Brand journey">
    <div class="container">
        <div class="text-center" style="margin-bottom:3.5rem;">
            <span class="eyebrow" style="color:var(--clr-gold);">Our Journey</span>
            <h2 class="section-title" style="color:var(--clr-warm-white);">Eleven Years of<br><em>Defining Moments</em></h2>
        </div>
        <div class="timeline-wrap">
            <?php
            $timeline_raw = jjwz_get_option( 'jjw_timeline', '[]' );
            $milestones   = json_decode( $timeline_raw, true ) ?: [];
            if ( empty( $milestones ) ) {
                $milestones = [
                    [ 'year' => '2013', 'title' => 'Founded in Amritsar',               'desc' => 'JJ WeddingZ Photography established by Jaspreet Singh with a singular focus on authentic, emotion-driven wedding documentation.' ],
                    [ 'year' => '2016', 'title' => 'Delhi NCR Branch Launch',           'desc' => 'Rapid client demand from Delhi and NCR necessitated the launch of a dedicated metropolitan branch, bringing our services to India\'s capital region.' ],
                    [ 'year' => '2018', 'title' => 'Cinematic Department Established',  'desc' => 'Full cinema-grade videography services added using Sony FX3 systems, expanding our offer to include sweeping wedding films.' ],
                    [ 'year' => '2020', 'title' => 'Maternity & Newborn Studio Launch', 'desc' => 'A dedicated, sanitized maternity and newborn photography studio established with medical-grade safety protocols.' ],
                    [ 'year' => '2022', 'title' => 'International Destination Commissions', 'desc' => 'First international destination wedding assignments accepted. JJ WeddingZ now travels globally.' ],
                    [ 'year' => '2024', 'title' => '500+ Weddings Milestone',           'desc' => 'Crossing the 500 premium weddings threshold, JJ WeddingZ cements its status as Northern India\'s most trusted luxury photography house.' ],
                ];
            }
            foreach ( $milestones as $m ) :
            ?>
            <div class="timeline-item" data-anim="fade-up">
                <div class="timeline-year"><?php echo esc_html( $m['year'] ); ?></div>
                <div class="timeline-content">
                    <h3><?php echo esc_html( $m['title'] ); ?></h3>
                    <p><?php echo esc_html( $m['desc'] ); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<?php get_footer(); ?>
