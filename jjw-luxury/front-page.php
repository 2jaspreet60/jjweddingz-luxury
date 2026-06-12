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
        <div class="text-center" style="margin-bottom: 3rem;">
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
                    <div class="branch-card__bg" aria-hidden="true"></div>
                    <div class="branch-card__overlay"></div>
                    <div class="branch-card__content">
                        <span class="branch-card__tag eyebrow"><?php echo $i === 0 ? 'Primary Branch' : 'Studio Branch'; ?></span>
                        <h3 class="branch-card__title"><?php echo esc_html( $branch_name ); ?> Studio</h3>
                        <p class="branch-card__desc"><?php echo esc_html( $branch_address ); ?></p>
                        <span class="branch-card__cta btn btn--outline-white">Find Us on Map →</span>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else : ?>
                <!-- Fallback defaults if options are empty -->
                <a href="#" class="branch-card branch-card--delhi" id="branch-delhi">
                    <div class="branch-card__bg" aria-hidden="true"></div>
                    <div class="branch-card__overlay"></div>
                    <div class="branch-card__content">
                        <span class="branch-card__tag eyebrow">Primary Branch</span>
                        <h3 class="branch-card__title">Delhi NCR Studio</h3>
                        <p class="branch-card__desc">Serving Gurugram, Delhi NCR, and surrounding districts.</p>
                        <span class="branch-card__cta btn btn--outline-white">Explore Branch →</span>
                    </div>
                </a>
                <a href="#" class="branch-card branch-card--amritsar" id="branch-amritsar">
                    <div class="branch-card__bg" aria-hidden="true"></div>
                    <div class="branch-card__overlay"></div>
                    <div class="branch-card__content">
                        <span class="branch-card__tag eyebrow">Punjab Branch</span>
                        <h3 class="branch-card__title">Amritsar Studio</h3>
                        <p class="branch-card__desc">Serving Amritsar, Ranjit Avenue, and surrounding Punjab cities.</p>
                        <span class="branch-card__cta btn btn--outline-white">Explore Branch →</span>
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
        <div class="section-header text-center" style="margin-bottom:3.5rem;">
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
                    $icon       = jjwz_get_option( 'svc_icon', '💍', $post_id );
                    $short_desc = jjwz_get_option( 'svc_short_desc', '', $post_id );
                    if ( empty( $short_desc ) ) {
                        $short_desc = wp_trim_words( get_the_excerpt(), 18 );
                    }
                    ?>
                    <div class="service-card" data-anim="fade-up" data-anim-delay="<?php echo $i * 80; ?>">
                        <div class="service-card__icon"><?php echo esc_html( $icon ); ?></div>
                        <h3 class="service-card__title"><?php the_title(); ?></h3>
                        <p class="service-card__desc"><?php echo esc_html( $short_desc ); ?></p>
                        <a href="<?php the_permalink(); ?>" class="service-card__link btn btn--ghost">Learn More</a>
                    </div>
                    <?php
                    $i++;
                endwhile;
                wp_reset_postdata();
            else :
                // Default placeholders if CPT is empty
                $default_services = [
                    [ 'icon' => '💍', 'title' => 'Luxury Wedding Photography', 'desc' => 'Candid, documentary, and editorial wedding coverage that captures pure, unscripted emotion.',            'url' => home_url('/services/wedding-photography') ],
                    [ 'icon' => '🎬', 'title' => 'Pre-Wedding Storytelling',   'desc' => 'Fine-art pre-wedding shoots at iconic locations that immortalize your unique love story.',              'url' => home_url('/services/pre-wedding') ],
                    [ 'icon' => '🎥', 'title' => 'Cinematography & Films',     'desc' => 'Sweeping cinematic films in 16:9 wide and 9:16 vertical formats — crafted for generations.',             'url' => home_url('/services/cinematography') ],
                    [ 'icon' => '👶', 'title' => 'Maternity & Newborn',        'desc' => 'Gentle, safe, and emotionally rich maternity and newborn sessions with medical-grade sanitization.',     'url' => home_url('/services/maternity-newborn') ],
                ];
                foreach ( $default_services as $i => $svc ) :
                ?>
                <div class="service-card" data-anim="fade-up" data-anim-delay="<?php echo $i * 80; ?>">
                    <div class="service-card__icon"><?php echo $svc['icon']; ?></div>
                    <h3 class="service-card__title"><?php echo esc_html( $svc['title'] ); ?></h3>
                    <p class="service-card__desc"><?php echo esc_html( $svc['desc'] ); ?></p>
                    <a href="<?php echo esc_url( $svc['url'] ); ?>" class="service-card__link btn btn--ghost">Learn More</a>
                </div>
                <?php
                endforeach;
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
                    <div class="film-card__body" style="padding-top:1.5rem;">
                        <h3 class="film-card__title" style="font-size:1.5rem; margin-bottom:0.5rem;"><?php the_title(); ?></h3>
                        <?php if ( $desc ) : ?>
                            <p class="film-card__desc" style="font-size:0.9rem; color:var(--clr-mist);"><?php echo esc_html( $desc ); ?></p>
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
    $founder_img_url = get_template_directory_uri() . '/assets/images/placeholder-founder.png';
}
?>
<section class="founder-section section" id="founder" aria-label="About our founder" style="background-color: var(--clr-cream);">
    <div class="container">
        <div class="grid-2 align-items-center">
            <div class="founder-section__media" data-anim="fade-right" style="position:relative; border-radius:var(--radius-xl); overflow:hidden; aspect-ratio:4/5; box-shadow:var(--shadow-md);">
                <?php if ( $founder_img_url ) : ?>
                    <img src="<?php echo esc_url( $founder_img_url ); ?>" alt="<?php echo esc_attr( $founder_name ); ?>" style="width:100%; height:100%; object-fit:cover;">
                <?php else : ?>
                    <div style="width:100%; height:100%; background:linear-gradient(135deg, #111, #2d2d2d); display:flex; align-items:center; justify-content:center;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="rgba(201,169,110,0.3)" stroke-width="1"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="founder-section__content" data-anim="fade-left">
                <span class="eyebrow">The Visionary Behind the Lens</span>
                <h2 class="section-title" style="margin-bottom:1.5rem;">Meet <em><?php echo esc_html( $founder_name ); ?></em></h2>
                <div class="founder-bio" style="font-size:1.1rem; line-height:1.8; color:var(--clr-mist); margin-bottom:2rem;">
                    <?php echo wp_kses_post( $founder_bio ); ?>
                </div>
                <div style="display:flex; align-items:center; gap:1.5rem;">
                    <div class="founder-signature" style="font-family:var(--font-display); font-size:2rem; color:var(--clr-gold); font-style:italic;">Jaspreet Singh</div>
                    <div style="width:40px; height:1px; background:var(--clr-gold);"></div>
                    <div style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--clr-fog);">Founder & Lead Photographer</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SECTION 9 — WHY CHOOSE US (100% IDENTITY PROMISE)
     ═══════════════════════════════════════════════════════════ -->
<section class="why-us-section section" id="why-choose-us" aria-label="Why choose JJ WeddingZ">
    <div class="container">
        <div class="text-center" style="margin-bottom: 4rem;">
            <span class="eyebrow">The JJ WeddingZ Promise</span>
            <h2 class="section-title">Why Couples <em>Choose Us</em></h2>
            <p class="lead text-center" style="margin-inline:auto; margin-top:1rem;">We merge international luxury editorial styling with non-negotiable technical and safety standards.</p>
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
        <div class="text-center" style="margin-bottom:3rem;">
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
                        $avatar_url = get_template_directory_uri() . '/assets/images/placeholder-testimonial.png';
                    }
                    ?>
                    <div class="testimonial-card" data-testimonial="<?php echo $i; ?>">
                        <div class="testi-avatar">
                            <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        </div>
                        <div class="testimonial-card__rating" style="color: var(--clr-gold); margin-bottom: var(--sp-md);" aria-label="<?php echo esc_attr( $rating ); ?> out of 5 stars">
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
                <p class="lead" style="margin-top:1rem;">We believe an informed client is a relaxed client. Here are answers to the questions we hear most often.</p>
                <a href="<?php echo esc_url( home_url( '/faqs' ) ); ?>" class="btn btn--outline" style="margin-top:1.5rem;" id="view-all-faqs">
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

        <div class="blog-preview__grid">
        <?php
        while ( $blog_q->have_posts() ) :
            $blog_q->the_post();
            $cats     = get_the_category();
            $cat_name = $cats ? $cats[0]->name : 'Photography';
            $thumb    = get_the_post_thumbnail_url( null, 'jjwz-blog-card' );
            ?>
            <article class="blog-card" id="post-<?php the_ID(); ?>">
                <a href="<?php the_permalink(); ?>" class="blog-card__link" aria-label="<?php the_title_attribute(); ?>">
                    <div class="blog-card__media">
                        <?php if ( $thumb ) : ?>
                            <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        <?php else : ?>
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/placeholder-blog.png' ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="blog-card__body">
                        <span class="blog-card__cat"><?php echo esc_html( $cat_name ); ?></span>
                        <h3 class="blog-card__title"><?php the_title(); ?></h3>
                        <p class="blog-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20, '…' ) ); ?></p>
                        <div class="blog-card__meta">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date( 'M j, Y' ); ?></time>
                            <span>·</span>
                            <span><?php echo ceil( str_word_count( get_the_content() ) / 200 ); ?> min read</span>
                        </div>
                    </div>
                </a>
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
            <div class="text-center" style="margin-bottom: 2.5rem;">
                <span class="eyebrow">Check Availability</span>
                <h2 class="section-title">Inquire About <em>Your Date</em></h2>
                <p class="lead text-center" style="margin-top:0.5rem; font-size:1rem; margin-inline:auto;">We only accept 20 luxury wedding commissions per year. Secure your consultation today.</p>
            </div>
            
            <form id="jjwz-contact-form" class="flex" style="flex-direction: column; gap: var(--sp-lg);">
                <!-- Honeypot -->
                <div style="display:none;">
                    <input type="text" name="jjwz_honey">
                </div>

                <!-- Name -->
                <div class="form-group">
                    <label for="form-name" class="form-label">Your Full Name <span class="text-gold">*</span></label>
                    <input type="text" id="form-name" name="name" class="form-control" placeholder="E.g. Priya Sharma" required autocomplete="name">
                </div>

                <!-- Email & Phone Grid -->
                <div class="grid-2" style="gap: var(--sp-md);">
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
                <div class="grid-2" style="gap: var(--sp-md);">
                    <div class="form-group">
                        <label for="form-date" class="form-label">Event / Shoot Date <span class="text-gold">*</span></label>
                        <input type="text" id="form-date" name="event_date" class="form-control" placeholder="E.g. 18/12/2026" required>
                    </div>
                    <div class="form-group">
                        <label for="form-service" class="form-label">Captured Milestone</label>
                        <select id="form-service" name="service" class="form-control" style="appearance: auto; background: #fff;">
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

                <button type="submit" class="btn btn--primary" style="margin-top:1rem; align-self:center; min-width:240px;">
                    Send Booking Inquiry
                </button>

                <div id="form-response" class="form-response-msg" hidden></div>
            </form>
        </div>
    </div>
</section>

<?php get_footer(); ?>
