<?php
/**
 * front-page.php — JJ WeddingZ Photography Homepage
 * All content pulled dynamically via ACF fields / wp_options fallbacks.
 *
 * @package JJWeddingZ
 * @version 1.2.0
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

/* ─── Pull dynamic global/home field values ────────────────────────────── */
$post_id         = get_the_ID();
$hero_type       = jjwz_get_option( 'jjwz_hero_bg_type',      'image', $post_id );
$hero_headline   = jjwz_get_option( 'jjwz_hero_headline',     'JJ WeddingZ Photography', $post_id );
$hero_sub        = jjwz_get_option( 'jjwz_hero_subheadline',  'Capturing your most profound milestones with international elegance and unscripted authenticity.', $post_id );
$hero_img        = jjwz_get_option( 'jjwz_hero_bg_image',     '', $post_id );
$hero_video      = jjwz_get_option( 'jjwz_hero_bg_video',     '', $post_id );
$hero_yt_id      = jjwz_get_option( 'jjwz_hero_youtube_id',   '', $post_id );
$value_prop      = jjwz_get_option( 'jjwz_value_prop',        'To JJ WeddingZ, photography is an art of observation. It is about finding the extraordinary in normal surroundings. Led by Jaspreet Singh, bringing over 11 years of professional photography experience, our team is dedicated to a singular vision: preserving your true essence. We strictly maintain 100% of your facial identity and original features in our editing process.', $post_id );

$branches_raw    = jjwz_get_option( 'jjw_branches', '[]' );
$branches        = json_decode( $branches_raw, true ) ?: [];
$wa_link         = jjwz_wa_link( 'Inquire About Your Date', 'btn btn--primary', 'hero-inquire-btn', 'I would like to check availability for my date.' );

// Helper functions for parsing video URLs
if ( ! function_exists( 'jjwz_get_youtube_id' ) ) {
    function jjwz_get_youtube_id( string $url ): string {
        preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match );
        return $match[1] ?? '';
    }
}

if ( ! function_exists( 'jjwz_get_vimeo_id' ) ) {
    function jjwz_get_vimeo_id( string $url ): string {
        preg_match( '/vimeo\.com\/(?:video\/)?([0-9]+)/', $url, $match );
        return $match[1] ?? '';
    }
}
?>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 1 — HERO VIDEO BLOCK
     ═══════════════════════════════════════════════════════════ -->
<section class="jjwz-hero" id="hero" aria-label="Hero Banner">

    <!-- Background Media -->
    <div class="hero__bg" aria-hidden="true">
        <?php if ( $hero_type === 'video' && $hero_video && isset( $hero_video['url'] ) ) : ?>
            <video class="hero__video" autoplay muted loop playsinline preload="none"
                   poster="<?php echo esc_url( $hero_img['url'] ?? '' ); ?>">
                <source src="<?php echo esc_url( $hero_video['url'] ); ?>" type="video/mp4">
            </video>
        <?php elseif ( $hero_type === 'youtube' && $hero_yt_id ) : ?>
            <div class="hero__yt-wrap" data-video-type="youtube" data-video-id="<?php echo esc_attr( $hero_yt_id ); ?>" data-autoplay="1">
                <iframe src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $hero_yt_id ); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo esc_attr( $hero_yt_id ); ?>&controls=0&showinfo=0&rel=0&iv_load_policy=3"
                        frameborder="0" allow="autoplay; encrypted-media" allowfullscreen title="Hero Video"
                        loading="lazy"></iframe>
            </div>
        <?php elseif ( $hero_img && isset( $hero_img['url'] ) ) : ?>
            <img src="<?php echo esc_url( $hero_img['url'] ); ?>"
                 alt="<?php echo esc_attr( $hero_img['alt'] ?? 'JJ WeddingZ Photography' ); ?>"
                 class="hero__img" fetchpriority="high" decoding="sync">
        <?php else : ?>
            <div class="hero__gradient-fallback"></div>
        <?php endif; ?>
        <div class="hero__overlay"></div>
    </div>

    <!-- Hero Content -->
    <div class="container hero__content">
        <div class="hero__text">
            <span class="hero__eyebrow eyebrow" data-anim="fade-up">Luxury Editorial Photography</span>
            <h1 class="hero__headline display-title" data-anim="fade-up" data-anim-delay="100">
                <?php echo wp_kses_post( $hero_headline ); ?>
            </h1>
            <p class="hero__sub lead" data-anim="fade-up" data-anim-delay="200">
                <?php echo wp_kses_post( $hero_sub ); ?>
            </p>
            <div class="hero__ctas" data-anim="fade-up" data-anim-delay="300">
                <?php echo $wa_link; ?>
                <a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>"
                   class="btn btn--outline-white" id="hero-portfolio-btn">
                    View Our Portfolio
                </a>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="hero__scroll-indicator" aria-hidden="true">
            <span class="scroll-line"></span>
            <span class="scroll-text">Scroll</span>
        </div>
    </div>

</section>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 2 — STATS BAND
     ═══════════════════════════════════════════════════════════ -->
<section class="stats-band section--sm" aria-label="Brand statistics">
    <div class="container">
        <div class="stats-band__grid">
            <?php
            $exp_years      = jjwz_get_option( 'jjw_experience_years', '11' );
            $weddings_count = jjwz_get_option( 'jjw_weddings_count', '500' );
            $branches_count = count( $branches ) ?: 2;
            $satisfaction   = jjwz_get_option( 'jjw_client_satisfaction', '100' );

            echo jjwz_stat_item( $exp_years,              'Years of Excellence' );
            echo jjwz_stat_item( $weddings_count,         'Weddings Photographed' );
            echo jjwz_stat_item( (string)$branches_count, 'Premium Branches', '' );
            echo jjwz_stat_item( $satisfaction,           'Client Satisfaction', '%' );
            ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 3 — VALUE PROPOSITION
     ═══════════════════════════════════════════════════════════ -->
<section class="value-prop section" id="about-intro" aria-label="Our philosophy">
    <div class="container">
        <div class="value-prop__grid">

            <div class="value-prop__left" data-anim="fade-right">
                <span class="eyebrow">Our Philosophy</span>
                <h2 class="section-title">Art of <em>Authentic</em><br>Observation</h2>
                <div class="value-prop__divider"></div>
                <div class="value-prop__body lead">
                    <?php echo wp_kses_post( $value_prop ); ?>
                </div>
                <div class="value-prop__actions">
                    <a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="btn btn--ghost" id="vp-about-link">
                        Meet Jaspreet Singh
                    </a>
                </div>
            </div>

            <div class="value-prop__right" data-anim="fade-left">
                <div class="value-prop__image-stack">
                    <div class="vp-stack__main"></div>
                    <div class="vp-stack__accent vp-stack__accent--1"></div>
                    <div class="vp-stack__accent vp-stack__accent--2"></div>
                    <div class="vp-stack__badge">
                        <span class="badge-number">100%</span>
                        <span class="badge-text">Identity<br>Retained</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 4 — BRANCH HUB
     ═══════════════════════════════════════════════════════════ -->
<section class="branch-hub section--sm" aria-label="Our branches">
    <div class="container">
        <div class="text-center branch-hub__header">
            <span class="eyebrow">Serving Northern India</span>
            <h2 class="section-title">Our <em>Branches</em></h2>
        </div>
        <div class="branch-hub__grid">
            <?php if ( ! empty( $branches ) ) : ?>
                <?php foreach ( $branches as $i => $b ) :
                    $branch_name = ! empty( $b['name'] ) ? $b['name'] : '';
                    $branch_address = ! empty( $b['address'] ) ? $b['address'] : '';
                    $branch_maps = ! empty( $b['maps_url'] ) ? $b['maps_url'] : '#';
                    $branch_class = ( $i % 2 === 0 ) ? 'branch-card--delhi' : 'branch-card--amritsar';
                ?>
                <a href="<?php echo esc_url( $branch_maps ); ?>" class="branch-card <?php echo esc_attr( $branch_class ); ?>" id="branch-<?php echo esc_attr( sanitize_title( $branch_name ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <div class="branch-card__media">
                        <div class="branch-card__bg-placeholder"></div>
                    </div>
                    <div class="branch-card__body">
                        <span class="branch-card__tag eyebrow"><?php echo $i === 0 ? 'Primary Branch' : 'Studio Branch'; ?></span>
                        <h3 class="branch-card__title"><?php echo esc_html( $branch_name ); ?> Studio</h3>
                        <p class="branch-card__desc"><?php echo esc_html( $branch_address ); ?></p>
                        <div class="branch-card__footer">
                            <span class="branch-card__cta btn btn--outline">Find Us on Map →</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else : ?>
                <!-- Fallback defaults if options are empty -->
                <a href="#" class="branch-card branch-card--delhi" id="branch-delhi">
                    <div class="branch-card__media">
                        <div class="branch-card__bg-placeholder"></div>
                    </div>
                    <div class="branch-card__body">
                        <span class="branch-card__tag eyebrow">Primary Branch</span>
                        <h3 class="branch-card__title">Delhi NCR Studio</h3>
                        <p class="branch-card__desc">Serving Gurugram, Delhi NCR, and surrounding districts.</p>
                        <div class="branch-card__footer">
                            <span class="branch-card__cta btn btn--outline">Explore Branch →</span>
                        </div>
                    </div>
                </a>
                <a href="#" class="branch-card branch-card--amritsar" id="branch-amritsar">
                    <div class="branch-card__media">
                        <div class="branch-card__bg-placeholder"></div>
                    </div>
                    <div class="branch-card__body">
                        <span class="branch-card__tag eyebrow">Punjab Branch</span>
                        <h3 class="branch-card__title">Amritsar Studio</h3>
                        <p class="branch-card__desc">Serving Amritsar, Ranjit Avenue, and surrounding Punjab cities.</p>
                        <div class="branch-card__footer">
                            <span class="branch-card__cta btn btn--outline">Explore Branch →</span>
                        </div>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 5 — SERVICES GRID
     ═══════════════════════════════════════════════════════════ -->
<section class="services-overview section" aria-label="Our services">
    <div class="container">
        <div class="section-header text-center">
            <span class="eyebrow">What We Offer</span>
            <h2 class="section-title">Crafted for <em>Every Milestone</em></h2>
        </div>
        <div class="services-grid">
            <?php
            $services_args = [
                'post_type'      => 'jjwz_service',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'meta_value_num',
                'meta_key'       => 'svc_display_order',
                'order'          => 'ASC',
            ];
            
            // Try featured CPTs first
            $services_q = new WP_Query( array_merge( $services_args, [
                'meta_query' => [
                    [
                        'key'     => 'svc_featured',
                        'value'   => '1',
                        'compare' => '=',
                    ]
                ]
            ] ) );

            if ( ! $services_q->have_posts() ) {
                $services_q = new WP_Query( $services_args );
            }

            if ( $services_q->have_posts() ) :
                $i = 0;
                while ( $services_q->have_posts() ) : $services_q->the_post();
                    $post_id    = get_the_ID();
                    
                    // Retrieve Small Icon
                    $small_icon = get_post_meta( $post_id, 'svc_small_icon', true );
                    if ( empty( $small_icon ) ) {
                        $small_icon = get_post_meta( $post_id, 'svc_icon', true ) ?: '💍';
                    }
                    
                    // Retrieve Thumbnail
                    $thumbnail_img = get_post_meta( $post_id, 'svc_thumbnail', true );
                    if ( empty( $thumbnail_img ) ) {
                        $thumbnail_img = get_post_meta( $post_id, 'svc_cover_image', true );
                    }
                    if ( empty( $thumbnail_img ) ) {
                        $thumbnail_img = get_post_meta( $post_id, 'svc_hero_image', true );
                    }
                    
                    $thumb_url = '';
                    if ( is_array( $thumbnail_img ) && ! empty( $thumbnail_img['url'] ) ) {
                        $thumb_url = $thumbnail_img['url'];
                    } elseif ( is_numeric( $thumbnail_img ) ) {
                        $thumb_url = wp_get_attachment_image_url( $thumbnail_img, 'medium_large' );
                    } elseif ( is_string( $thumbnail_img ) && $thumbnail_img ) {
                        $thumb_url = $thumbnail_img;
                    }
                    if ( ! $thumb_url ) {
                        $thumb_url = jjwz_get_option( 'jjw_default_placeholder_service' );
                    }
                    if ( ! $thumb_url ) {
                        $thumb_url = get_template_directory_uri() . '/assets/images/placeholder-category-default.png';
                    }
                    
                    $starting_price = get_post_meta( $post_id, 'svc_starting_price', true );
                    $short_desc = get_post_meta( $post_id, 'svc_short_desc', true );
                    if ( empty( $short_desc ) ) {
                        $short_desc = wp_trim_words( get_the_excerpt(), 18 );
                    }
                    ?>
                    <div class="luxury-compact-card" data-anim="fade-up" data-anim-delay="<?php echo $i * 80; ?>">
                        <div class="luxury-compact-card__media">
                            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="luxury-compact-card__img" loading="lazy">
                            <div class="luxury-compact-card__overlay"></div>
                            <div class="luxury-compact-card__icon"><?php echo esc_html( $small_icon ); ?></div>
                        </div>
                        <div class="luxury-compact-card__body">
                            <h3 class="luxury-compact-card__title"><?php the_title(); ?></h3>
                            <p class="luxury-compact-card__desc"><?php echo esc_html( $short_desc ); ?></p>
                            <?php if ( $starting_price ) : ?>
                                <span class="luxury-compact-card__price"><?php echo esc_html( $starting_price ); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="luxury-compact-card__footer">
                            <a href="<?php the_permalink(); ?>" class="luxury-compact-card__link">
                                Explore Service <span class="arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    <?php
                    $i++;
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<?php
$portfolio_check_q = new WP_Query( [
    'post_type'      => 'jjwz_portfolio',
    'posts_per_page' => 1,
    'post_status'    => 'publish',
] );

if ( $portfolio_check_q->have_posts() ) :
    wp_reset_postdata();
?>
<!-- ═══════════════════════════════════════════════════════════
     SECTION 6 — FEATURED PORTFOLIO MASONRY
     ═══════════════════════════════════════════════════════════ -->
<section class="portfolio-section section" id="portfolio" aria-label="Portfolio preview">
    <div class="container">
        <div class="section-header portfolio-section__header">
            <div>
                <span class="eyebrow">Selected Work</span>
                <h2 class="section-title">Our <em>Portfolio</em></h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>" class="btn btn--outline" id="view-all-portfolio">
                View Full Portfolio
            </a>
        </div>

        <!-- Portfolio Category Filter -->
        <div class="portfolio-filter" role="tablist" aria-label="Portfolio category filter">
            <button class="filter-btn is-active" data-filter="all" role="tab" aria-selected="true" id="filter-all">All</button>
            <button class="filter-btn" data-filter="luxury wedding" role="tab" aria-selected="false" id="filter-wedding">Weddings</button>
            <button class="filter-btn" data-filter="pre-wedding" role="tab" aria-selected="false" id="filter-prewedding">Pre-Wedding</button>
            <button class="filter-btn" data-filter="maternity" role="tab" aria-selected="false" id="filter-maternity">Maternity</button>
            <button class="filter-btn" data-filter="newborn" role="tab" aria-selected="false" id="filter-newborn">Newborn</button>
            <button class="filter-btn" data-filter="baby shoot" role="tab" aria-selected="false" id="filter-baby">Baby Shoots</button>
        </div>

        <?php echo jjwz_portfolio_masonry_grid( 12, 'portfolio-section__grid' ); ?>

    </div>
</section>
<?php endif; ?>

<?php
$films_args = [
    'post_type'      => 'jjwz_film',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
];

// Try featured first
$films_q = new WP_Query( array_merge( $films_args, [
    'meta_query' => [
        [
            'key'     => 'film_featured',
            'value'   => '1',
            'compare' => '=',
        ]
    ]
] ) );

if ( ! $films_q->have_posts() ) {
    $films_q = new WP_Query( $films_args );
}

if ( $films_q->have_posts() ) :
?>
<!-- ═══════════════════════════════════════════════════════════
     SECTION 7 — FEATURED FILMS
     ═══════════════════════════════════════════════════════════ -->
<section class="films-section section" id="films" aria-label="Featured cinematic films">
    <div class="container">
        <div class="section-header portfolio-section__header">
            <div>
                <span class="eyebrow">Cinematic Stories</span>
                <h2 class="section-title">Featured <em>Films</em></h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/films' ) ); ?>" class="btn btn--outline" id="view-all-films">
                View All Films
            </a>
        </div>

        <div class="films-grid grid-3">
            <?php
            while ( $films_q->have_posts() ) : $films_q->the_post();
                $post_id     = get_the_ID();
                $youtube_url = jjwz_get_option( 'film_youtube_url', '', $post_id );
                $vimeo_url   = jjwz_get_option( 'film_vimeo_url', '', $post_id );
                $desc        = jjwz_get_option( 'film_description', '', $post_id );
                
                $video_type = 'youtube';
                $video_id   = '';
                if ( $vimeo_url ) {
                    $video_type = 'vimeo';
                    $video_id   = jjwz_get_vimeo_id( $vimeo_url );
                } else {
                    $video_id   = jjwz_get_youtube_id( $youtube_url );
                }
                
                $thumb = get_the_post_thumbnail_url( $post_id, 'jjwz-portfolio' );
                ?>
                <div class="film-card" data-anim="fade-up">
                    <div class="film-card__video jjwz-video-wrap jjwz-video-wrap--16-9" 
                         data-video-type="<?php echo esc_attr( $video_type ); ?>" 
                         data-video-id="<?php echo esc_attr( $video_id ); ?>"
                         <?php if ( $thumb ) : ?>
                             style="background-image: url('<?php echo esc_url( $thumb ); ?>');"
                         <?php endif; ?>>
                    </div>
                    <div class="film-card__body">
                        <h3 class="film-card__title"><?php the_title(); ?></h3>
                        <?php if ( $desc ) : ?>
                            <p class="film-card__desc"><?php echo esc_html( $desc ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 8 — ABOUT FOUNDER (JASPREET SINGH)
     ═══════════════════════════════════════════════════════════ -->
<?php
$founder_name = jjwz_get_option( 'jjwz_about_founder_name', 'Jaspreet Singh' );
$founder_bio  = jjwz_get_option( 'jjwz_about_founder_bio', '<p>Jaspreet began his photography journey over 11 years ago, driven by an unwavering belief that true luxury imagery lies not in artificial perfection but in the authentic preservation of genuine human emotion. His philosophy is simple and non-negotiable: we protect your identity. Our editing methodology maintains 100% of your original facial features and natural skin tones — we reject face-swapping, skin-whitening filters, and synthetic AI enhancement entirely.</p>' );
$founder_img  = jjwz_get_option( 'jjwz_about_founder_img' );
$founder_img_url = is_array( $founder_img ) ? $founder_img['url'] : ( is_numeric( $founder_img ) ? wp_get_attachment_image_url( $founder_img, 'full' ) : $founder_img );
if ( empty( $founder_img_url ) ) {
    $founder_img_url = jjwz_get_option( 'jjw_default_placeholder_founder' );
}
if ( empty( $founder_img_url ) ) {
    $founder_img_url = get_template_directory_uri() . '/assets/images/placeholder-founder.png';
}
$enable_home = jjwz_get_option( 'jjwz_about_founder_enable_homepage', '1' );
if ( '1' === $enable_home ) :
?>
<section class="founder-section section" id="founder" aria-label="About our founder">
    <div class="container">
        <div class="grid-2 align-items-center">
            <div class="founder-section__media" data-anim="fade-right">
                <?php if ( $founder_img_url ) : ?>
                    <img src="<?php echo esc_url( $founder_img_url ); ?>" alt="<?php echo esc_attr( $founder_name ); ?>">
                <?php else : ?>
                    <div class="founder-section__placeholder">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke-width="1"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="founder-section__content" data-anim="fade-left">
                <span class="eyebrow"><?php echo esc_html( jjwz_get_option( 'jjwz_about_founder_short_title', 'The Visionary Behind the Lens' ) ); ?></span>
                <h2 class="section-title">Meet <em><?php echo esc_html( $founder_name ); ?></em></h2>
                <div class="founder-bio">
                    <?php echo wp_kses_post( $founder_bio ); ?>
                </div>
                <div class="founder-sig-wrapper">
                    <?php 
                    $signature_img = jjwz_get_option( 'jjwz_about_founder_signature' );
                    if ( ! empty( $signature_img ) ) : 
                    ?>
                        <img src="<?php echo esc_url( $signature_img ); ?>" alt="Founder Signature" class="founder-sig-img" style="max-height: 80px; width: auto; display: block; margin-bottom: 0.5rem;">
                    <?php else : ?>
                        <div class="founder-signature"><?php echo esc_html( $founder_name ); ?></div>
                    <?php endif; ?>
                    <div class="founder-sig-line"></div>
                    <div class="founder-sig-role"><?php echo esc_html( jjwz_get_option( 'jjwz_about_founder_designation', 'Founder & Lead Photographer' ) ); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 9 — WHY CHOOSE US (100% IDENTITY PROMISE)
     ═══════════════════════════════════════════════════════════ -->
<section class="why-us-section section" id="why-choose-us" aria-label="Why choose JJ WeddingZ">
    <div class="container">
        <div class="section-header text-center">
            <span class="eyebrow">The JJ WeddingZ Promise</span>
            <h2 class="section-title">Why Couples <em>Choose Us</em></h2>
            <p class="lead text-center section-header__desc">We merge international luxury editorial styling with non-negotiable technical and safety standards.</p>
        </div>

        <div class="why-us-grid">
            <div class="why-us-card" data-anim="fade-up">
                <div class="why-us-card__icon">👤</div>
                <h3 class="why-us-card__title">100% Identity Retained</h3>
                <p class="why-us-card__desc">We protect your identity. Our editing maintains 100% of your facial structure and original skin tones. No artificial face-swapping or plastic skin-smoothing.</p>
            </div>
            <div class="why-us-card" data-anim="fade-up" data-anim-delay="100">
                <div class="why-us-card__icon">🧼</div>
                <h3 class="why-us-card__title">Medical-Grade Sanitization</h3>
                <p class="why-us-card__desc">For newborn and baby sessions, we follow strict safety protocols. Every prop, blanket, and wrap undergoes hospital-grade UV-C and steam sanitization.</p>
            </div>
            <div class="why-us-card" data-anim="fade-up" data-anim-delay="200">
                <div class="why-us-card__icon">📷</div>
                <h3 class="why-us-card__title">Elite Production Standard</h3>
                <p class="why-us-card__desc">We capture milestones using state-of-the-art Sony FX3/FX6 cinema cameras and high-fidelity Canon EOS R5 bodies with ultra-sharp prime lenses.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 10 — TESTIMONIALS CAROUSEL
     ═══════════════════════════════════════════════════════════ -->
<?php
$testimonials_q = new WP_Query( [
    'post_type'      => 'jjwz_testimonial',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
] );

if ( $testimonials_q->have_posts() ) :
?>
<section class="testimonials-section section" aria-label="Client testimonials">
    <div class="container">
        <div class="section-header text-center">
            <span class="eyebrow">Client Love</span>
            <h2 class="section-title">What Our Couples <em>Say</em></h2>
        </div>
        <div class="testimonials-slider">
            <div class="testimonials-track" id="testimonials-track" aria-live="polite">
                <?php
                $i = 0;
                while ( $testimonials_q->have_posts() ) : $testimonials_q->the_post();
                    $post_id  = get_the_ID();
                    $service  = jjwz_get_option( 'testimonial_service', '', $post_id );
                    $location = jjwz_get_option( 'testimonial_location', '', $post_id );
                    $rating   = jjwz_get_option( 'testimonial_rating', '5', $post_id );
                    $review   = jjwz_get_option( 'testimonial_review', get_the_content(), $post_id );
                    if ( empty( $review ) ) {
                        $review = get_the_excerpt();
                    }
                    $avatar_url = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
                    if ( empty( $avatar_url ) ) {
                        $avatar_url = jjwz_get_option( 'jjw_default_placeholder_testimonial' );
                    }
                    if ( empty( $avatar_url ) ) {
                        $avatar_url = get_template_directory_uri() . '/assets/images/placeholder-testimonial.png';
                    }
                    ?>
                    <div class="testimonial-card" data-testimonial="<?php echo $i; ?>">
                        <div class="testi-avatar">
                            <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        </div>
                        <div class="testimonial-card__rating" aria-label="<?php echo esc_attr( $rating ); ?> out of 5 stars">
                            <?php for ( $s = 0; $s < (int)$rating; $s++ ) echo '★'; ?>
                        </div>
                        <blockquote class="testi-quote">"<?php echo esc_html( $review ); ?>"</blockquote>
                        <cite class="testi-author">
                            <strong><?php the_title(); ?></strong>
                            <span><?php echo esc_html( $location ); ?> <?php if ( $service ) { echo '· ' . esc_html( $service ); } ?></span>
                        </cite>
                    </div>
                    <?php
                    $i++;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <div class="testi-nav" aria-label="Testimonial navigation">
            <button class="testi-btn" id="testi-prev" aria-label="Previous testimonial">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="testi-dots" id="testi-dots" aria-hidden="true"></div>
            <button class="testi-btn" id="testi-next" aria-label="Next testimonial">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 8 (FAQ Preview) & SECTION 9 (Blog Preview)
     Moved inside main list logic dynamically.
     ═══════════════════════════════════════════════════════════ -->
<section class="faq-preview section" aria-label="Frequently asked questions preview">
    <div class="container">
        <div class="faq-preview__grid">
            <div class="faq-preview__left" data-anim="fade-right">
                <span class="eyebrow">Common Questions</span>
                <h2 class="section-title">Everything You<br>Need to <em>Know</em></h2>
                <p class="lead faq-preview__lead">We believe an informed client is a relaxed client. Here are answers to the questions we hear most often.</p>
                <a href="<?php echo esc_url( home_url( '/faqs' ) ); ?>" class="btn btn--outline faq-preview__btn" id="view-all-faqs">
                    View All FAQs
                </a>
            </div>
            <div class="faq-preview__right" data-anim="fade-left">
                <?php echo jjwz_render_faq_accordion( '', 4 ); ?>
            </div>
        </div>
    </div>
</section>

<?php
$blog_q = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
] );

if ( $blog_q->have_posts() ) :
?>
<section class="blog-preview section" aria-label="Latest articles">
    <div class="container">
        <div class="section-header portfolio-section__header">
            <div>
                <span class="eyebrow">Wedding Insights</span>
                <h2 class="section-title">From Our <em>Blog</em></h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/blog' ) ); ?>" class="btn btn--outline" id="view-all-blog">
                Read All Articles
            </a>
        </div>

        <div class="blog-grid">
        <?php
        while ( $blog_q->have_posts() ) :
            $blog_q->the_post();
            $cats     = get_the_category();
            $cat_name = $cats ? $cats[0]->name : 'Photography';
            $thumb    = get_the_post_thumbnail_url( null, 'jjwz-blog-card' );
            if ( empty( $thumb ) ) {
                $thumb = jjwz_get_option( 'jjw_default_placeholder_blog' );
            }
            if ( empty( $thumb ) ) {
                $thumb = get_template_directory_uri() . '/assets/images/placeholder-blog.png';
            }
            $word_count = str_word_count( get_the_content() );
            $read_time  = max( 1, ceil( $word_count / 200 ) );
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
                <div class="blog-card__media">
                    <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="800" height="480">
                    </a>
                </div>
                <div class="blog-card__body">
                    <span class="blog-card__cat"><?php echo esc_html( $cat_name ); ?></span>
                    <h3 class="blog-card__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <p class="blog-card__excerpt"><?php echo wp_strip_all_tags( get_the_excerpt() ); ?></p>
                    <div class="flex-between" style="margin-top: auto; border-top: 1px solid var(--clr-border); padding-top: var(--sp-md);">
                        <span class="text-mist" style="font-size: var(--text-xs); font-weight: 500;">
                            <?php echo get_the_date( 'M j, Y' ); ?> &bull; <?php echo $read_time; ?> min read
                        </span>
                        <a href="<?php the_permalink(); ?>" class="blog-card__link" aria-label="<?php esc_attr_e( 'Read post', 'jjweddingz' ); ?>">
                            Read
                        </a>
                    </div>
                </div>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 11 — DATE AVAILABILITY BOOKING FORM
     ═══════════════════════════════════════════════════════════ -->
<section class="booking-section section" id="booking" aria-label="Book your date">
    <div class="container container--narrow">
        <div class="contact-form-wrap" data-anim="fade-up">
            <div class="text-center booking-section__header">
                <span class="eyebrow">Check Availability</span>
                <h2 class="section-title">Inquire About <em>Your Date</em></h2>
                <p class="lead text-center">We only accept 20 luxury wedding commissions per year. Secure your consultation today.</p>
            </div>
            
            <form id="jjwz-contact-form" class="booking-form">
                <!-- Honeypot -->
                <div class="booking-form__honey">
                    <input type="text" name="jjwz_honey">
                </div>

                <!-- Name -->
                <div class="form-group">
                    <label for="form-name" class="form-label">Your Full Name <span class="text-gold">*</span></label>
                    <input type="text" id="form-name" name="name" class="form-control" placeholder="E.g. Priya Sharma" required autocomplete="name">
                </div>

                <!-- Email & Phone Grid -->
                <div class="form-grid">
                    <div class="form-group">
                        <label for="form-email" class="form-label">Email Address <span class="text-gold">*</span></label>
                        <input type="email" id="form-email" name="email" class="form-control" placeholder="E.g. priya@example.com" required autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label for="form-phone" class="form-label">Phone Number <span class="text-gold">*</span></label>
                        <input type="tel" id="form-phone" name="phone" class="form-control" placeholder="E.g. +91 98765 43210" required autocomplete="tel">
                    </div>
                </div>

                <!-- Date & Service Grid -->
                <div class="form-grid">
                    <div class="form-group">
                        <label for="form-date" class="form-label">Event / Shoot Date <span class="text-gold">*</span></label>
                        <input type="text" id="form-date" name="event_date" class="form-control" placeholder="E.g. 18/12/2026" required>
                    </div>
                    <div class="form-group">
                        <label for="form-service" class="form-label">Captured Milestone</label>
                        <select id="form-service" name="service" class="form-control form-select">
                            <option value="Wedding Photography">Wedding Photography</option>
                            <option value="Pre-Wedding Shoot">Pre-Wedding Shoot</option>
                            <option value="Cinematography / Film">Cinematography / Film</option>
                            <option value="Maternity & Newborn">Maternity & Newborn</option>
                            <option value="Baby & Kids Shoot">Baby & Kids Shoot</option>
                            <option value="Other">Other / Portraits</option>
                        </select>
                    </div>
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label for="form-message" class="form-label">Tell Us About Your Vision</label>
                    <textarea id="form-message" name="message" class="form-control" placeholder="Share venue details, theme plans, or destination venues..."></textarea>
                </div>

                <input type="hidden" name="source" value="<?php echo esc_url( home_url() ); ?>">

                <button type="submit" class="btn btn--primary booking-form__submit">
                    Send Booking Inquiry
                </button>

                <div id="form-response" class="form-response-msg" hidden></div>
            </form>
        </div>
    </div>
</section>

<?php get_footer(); ?>
