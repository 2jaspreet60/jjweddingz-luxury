<?php
/**
 * single-jjwz_service.php — Single Service template (Luxury Editorial Rebuild)
 *
 * @package JJWeddingZ
 * @version 1.2.0
 */

get_header();
the_post();

$post_id    = get_the_ID();
$slug       = get_post_field( 'post_name', $post_id );
$icon       = get_post_meta( $post_id, 'svc_icon', true ) ?: '💍';
$short_desc = get_post_meta( $post_id, 'svc_short_desc', true ) ?: get_the_excerpt();
$seo_content = get_post_meta( $post_id, 'svc_seo_content', true ) ?: get_the_content();
$price      = get_post_meta( $post_id, 'svc_starting_price', true );
$highlights_raw = get_post_meta( $post_id, 'svc_key_highlights', true );
$features_raw   = get_post_meta( $post_id, 'svc_features_list', true );
$process_raw    = get_post_meta( $post_id, 'svc_process_steps', true );

// Cover image lookup
$cover_img = get_post_meta( $post_id, 'svc_cover_image', true );
if ( empty( $cover_img ) ) {
    $cover_img = get_post_meta( $post_id, 'svc_hero_image', true );
}
$cover_url = '';
if ( is_array( $cover_img ) && ! empty( $cover_img['url'] ) ) {
    $cover_url = $cover_img['url'];
} elseif ( is_numeric( $cover_img ) ) {
    $cover_url = wp_get_attachment_image_url( $cover_img, 'full' );
} elseif ( is_string( $cover_img ) && $cover_img ) {
    $cover_url = $cover_img;
}
if ( ! $cover_url ) {
    $cover_url = jjwz_get_option( 'jjw_default_placeholder_service' );
}
if ( ! $cover_url ) {
    $cover_url = get_template_directory_uri() . '/assets/images/placeholder-category-default.png';
}

// WhatsApp Link Generator
$wa_link = jjwz_wa_link( 'Chat on WhatsApp', 'btn btn--primary', 'service-wa-floating-cta', 'Hi, I would like to inquire about booking a ' . get_the_title() . ' session.' );
?>

<style>
/* Service Single Editorial Design */
.service-intro-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--sp-3xl);
    align-items: start;
}
.service-sidebar {
    position: sticky;
    top: 100px;
    background: var(--clr-cream);
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-xl);
    padding: var(--sp-xl);
    box-shadow: var(--shadow-sm);
}
.service-process-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--sp-xl);
    margin-top: var(--sp-2xl);
    position: relative;
}
.service-process-step {
    background: var(--clr-warm-white);
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-lg);
    padding: var(--sp-lg);
    text-align: center;
    position: relative;
}
.service-process-step__num {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--clr-gold);
    color: var(--clr-warm-white);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin-bottom: var(--sp-md);
}
.service-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--sp-lg);
    margin-top: var(--sp-xl);
}
.service-feature-card {
    background: var(--clr-cream);
    border-radius: var(--radius-lg);
    padding: var(--sp-lg);
    border-left: 3px solid var(--clr-gold);
    display: flex;
    gap: var(--sp-md);
}
.service-feature-card__icon {
    color: var(--clr-gold);
    font-size: 1.25rem;
    font-weight: 700;
}
.packages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: var(--sp-xl);
    margin-top: var(--sp-2xl);
}
.package-card {
    background: var(--clr-warm-white);
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-xl);
    padding: var(--sp-2xl);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all var(--transition-base);
    height: 100%;
}
.package-card:hover {
    border-color: var(--clr-gold);
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
}
.package-card--featured {
    border: 2px solid var(--clr-gold);
    background: var(--clr-gold-pale);
    position: relative;
}
.package-card__badge {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--clr-gold);
    color: var(--clr-warm-white);
    padding: 4px 16px;
    border-radius: var(--radius-full);
    font-size: var(--text-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}
.package-card__price {
    font-size: var(--text-3xl);
    font-family: var(--font-display);
    color: var(--clr-obsidian);
    margin-block: var(--sp-md);
}
.package-card__features {
    list-style: none;
    margin-block: var(--sp-lg);
    padding: 0;
}
.package-card__features li {
    padding-left: 24px;
    position: relative;
    margin-bottom: var(--sp-xs);
    font-size: var(--text-sm);
    color: var(--clr-mist);
}
.package-card__features li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: var(--clr-gold);
    font-weight: 700;
}
.availability-form-card {
    background: var(--clr-cream);
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-xl);
    padding: var(--sp-2xl);
    margin-top: var(--sp-3xl);
}

@media (max-width: 992px) {
    .service-intro-grid {
        grid-template-columns: 1fr;
    }
    .service-sidebar {
        position: static;
        margin-top: var(--sp-2xl);
    }
}
</style>

<article id="service-<?php the_ID(); ?>" <?php post_class( 'jjwz-service-single' ); ?>>

    <!-- 1. Hero Section -->
    <header class="post-hero" aria-label="Service banner" style="background: var(--clr-obsidian); position: relative; overflow: hidden; padding-block: var(--sp-5xl);">
        <?php if ( $cover_url ) : ?>
        <div class="post-hero__bg" aria-hidden="true" style="position: absolute; inset: 0; opacity: 0.45; z-index: 1;">
            <img src="<?php echo esc_url( $cover_url ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;" fetchpriority="high" decoding="sync">
            <div class="post-hero__overlay" style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,10,10,0.3) 0%, rgba(10,10,10,0.85) 100%);"></div>
        </div>
        <?php endif; ?>
        
        <div class="container post-hero__content" style="position: relative; z-index: 2; color: var(--clr-warm-white); text-align: center;">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow" style="color: var(--clr-gold); margin-bottom: var(--sp-md); display: inline-block; font-weight: 600; text-transform: uppercase; letter-spacing: 0.15em;">
                <?php echo esc_html( $icon ); ?> Premium Photography Discipline
            </span>
            <h1 class="post-hero__title display-title" style="color: var(--clr-warm-white); margin-bottom: var(--sp-md);"><?php the_title(); ?></h1>
            <?php if ( $short_desc ) : ?>
                <p class="lead" style="color: rgba(255,255,255,0.8); margin-inline: auto; max-width: 800px; font-size: var(--text-md);"><?php echo esc_html( $short_desc ); ?></p>
            <?php endif; ?>
            <?php if ( $price ) : ?>
                <span class="eyebrow" style="color: var(--clr-gold); font-size: var(--text-md); margin-top: var(--sp-md); display: inline-block;">Starting from <?php echo esc_html( $price ); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <!-- Content Sections -->
    <div class="container section">
        <div class="service-intro-grid">
            
            <!-- 2. Service Introduction & Main Editorial Copy -->
            <div class="service-main-content">
                <span class="eyebrow">Visual Narrative</span>
                <h2 class="section-title" style="text-align: left; margin-bottom: var(--sp-lg);">Editorial Narrative &amp; <em>Creative Philosophy</em></h2>
                <div class="service-description lead" style="margin-bottom: var(--sp-3xl); color: var(--clr-mist); line-height: 1.8;">
                    <?php echo wp_kses_post( wpautop( $seo_content ) ); ?>
                </div>

                <!-- 4. Features & Benefits Section -->
                <?php if ( ! empty( $features_raw ) ) : 
                    $features_list = array_filter( array_map( 'trim', explode( "\n", $features_raw ) ) );
                ?>
                <div class="service-features-block" style="margin-top: 4rem;">
                    <span class="eyebrow">Deliverables</span>
                    <h2 class="section-title" style="text-align: left; margin-bottom: var(--sp-md);">Features &amp; <em>Exclusive Inclusions</em></h2>
                    <p style="color: var(--clr-mist); max-width: 600px;">Every session is backed by our full technical guarantee and structured creative resources to ensure unmatched quality:</p>
                    <div class="service-features-grid">
                        <?php foreach ( $features_list as $feat ) : ?>
                        <div class="service-feature-card">
                            <span class="service-feature-card__icon">✓</span>
                            <span style="font-family: var(--font-body); font-size: var(--text-sm); color: var(--clr-obsidian); font-weight: 500;"><?php echo esc_html( $feat ); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 5. Our Photography Process Section -->
                <?php if ( ! empty( $process_raw ) ) : 
                    $process_steps = array_filter( array_map( 'trim', explode( "\n", $process_raw ) ) );
                ?>
                <div class="service-process-block" style="margin-top: 4rem;">
                    <span class="eyebrow">Workflow Journey</span>
                    <h2 class="section-title" style="text-align: left; margin-bottom: var(--sp-md);">Our <em>Photography Process</em></h2>
                    <p style="color: var(--clr-mist);">From initial storyboarding to fine-art print handovers, here is how we document your story:</p>
                    <div class="service-process-grid">
                        <?php foreach ( $process_steps as $idx => $step ) : ?>
                        <div class="service-process-step">
                            <span class="service-process-step__num"><?php echo $idx + 1; ?></span>
                            <h3 style="font-family: var(--font-heading); font-size: var(--text-md); margin-bottom: 8px; color: var(--clr-obsidian);"><?php echo esc_html( $step ); ?></h3>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- 3. Sidebar: Why Choose us / Identity Guarantee -->
            <aside class="service-sidebar" aria-label="Service overview details">
                <h3 style="font-family: var(--font-heading); font-size: 1.5rem; margin-bottom: var(--sp-md); color: var(--clr-obsidian);">Capturing Legacies</h3>
                <p style="font-size: var(--text-sm); color: var(--clr-mist); margin-bottom: var(--sp-lg); line-height: 1.6;">We document milestones with absolute artistic dedication, utilizing elite camera systems and safe data workflows.</p>
                
                <div style="display: flex; flex-direction: column; gap: var(--sp-sm); margin-bottom: 2rem;">
                    <a href="#availability" class="btn btn--primary" style="justify-content: center; font-size: 12px; padding: 12px 20px;">Check Date Availability</a>
                    <?php echo $wa_link; ?>
                </div>

                <div style="border-top: 1px solid var(--clr-border); padding-top: var(--sp-md); margin-top: var(--sp-md);">
                    <h4 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 8px; color: var(--clr-obsidian);">🛡️ 100% Identity Promise</h4>
                    <p style="font-size: 11px; color: var(--clr-mist); line-height: 1.5;">We sign a formal pledge: zero face-swapping, zero skin whitening, and zero artificial plastic filters. Retaining your authentic visual identity.</p>
                </div>
                <div style="margin-top: 1rem;">
                    <h4 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 8px; color: var(--clr-obsidian);">💾 Dual-Card Data Security</h4>
                    <p style="font-size: 11px; color: var(--clr-mist); line-height: 1.5;">All active camera units record to dual card slots in real-time. Your memories are fully safe from the moment of capture.</p>
                </div>
            </aside>

        </div>
    </div>

    <!-- 4. Dynamic Package Comparison Section -->
    <?php
    // Fetch mapped packages via relationship meta
    $linked_packages = get_post_meta( $post_id, 'svc_packages', true ) ?: [];
    
    // Fallback: query packages matching service category slug
    if ( empty( $linked_packages ) ) {
        $pkg_query = new WP_Query( [
            'post_type'      => 'jjwz_package',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => 'package_service',
                    'value'   => $post_id,
                    'compare' => '='
                ],
                [
                    'key'     => 'package_category',
                    'value'   => $slug,
                    'compare' => '='
                ]
            ]
        ] );
        if ( $pkg_query->have_posts() ) {
            $linked_packages = wp_list_pluck( $pkg_query->posts, 'ID' );
        }
        wp_reset_postdata();
    }

    if ( ! empty( $linked_packages ) ) :
    ?>
    <section class="section packages-section" style="background: var(--clr-cream); border-top: 1px solid var(--clr-border); border-bottom: 1px solid var(--clr-border);">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Pricing &amp; Commissions</span>
                <h2 class="section-title">Investment <em>Collections</em></h2>
                <p class="lead" style="margin-inline: auto;">Choose a collection tailored to your milestones. All packages include high-end color profiling and secure digital delivery.</p>
            </div>

            <div class="packages-grid">
                <?php foreach ( $linked_packages as $idx => $pkg_id ) : 
                    $pkg_title = get_the_title( $pkg_id );
                    // Strip service name prefix from title
                    $display_name = str_replace( get_the_title() . ' — ', '', $pkg_title );
                    
                    $pkg_price = get_post_meta( $pkg_id, 'package_price', true );
                    $pkg_desc  = get_post_meta( $pkg_id, 'package_description', true ) ?: get_post_field( 'post_content', $pkg_id );
                    $pkg_feats_raw = get_post_meta( $pkg_id, 'package_features', true );
                    $pkg_feats = array_filter( array_map( 'trim', explode( "\n", $pkg_feats_raw ) ) );
                    
                    $is_featured = ( $idx === 1 ); // make second card featured by default
                ?>
                <div class="package-card <?php echo $is_featured ? 'package-card--featured' : ''; ?>">
                    <?php if ( $is_featured ) : ?>
                        <span class="package-card__badge">Most Popular</span>
                    <?php endif; ?>
                    <div>
                        <h3 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-obsidian); margin-bottom: 8px;"><?php echo esc_html( $display_name ); ?></h3>
                        <p style="font-size: var(--text-sm); color: var(--clr-mist); line-height: 1.5; margin-bottom: var(--sp-md);"><?php echo esc_html( $pkg_desc ); ?></p>
                        <span class="package-card__price"><?php echo esc_html( $pkg_price ); ?></span>
                        
                        <ul class="package-card__features">
                            <?php foreach ( $pkg_feats as $f ) : ?>
                                <li><?php echo esc_html( $f ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div style="margin-top: 2rem;">
                        <a href="#availability" class="btn <?php echo $is_featured ? 'btn--primary' : 'btn--outline'; ?>" style="width: 100%; justify-content: center; font-size: 12px;">
                            Book This Collection
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 6. Portfolio Showcase Section -->
    <?php
    // Query portfolios mapped explicitly via relationship field or categories
    $portfolio_query = new WP_Query( [
        'post_type'      => 'jjwz_portfolio',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'portfolio_services',
                'value'   => $post_id,
                'compare' => 'LIKE' // post objects are serialized or mapped as arrays
            ]
        ]
    ] );

    // Fallback: query portfolios where service category term slug matches current service slug
    if ( ! $portfolio_query->have_posts() ) {
        $portfolio_query = new WP_Query( [
            'post_type'      => 'jjwz_portfolio',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'tax_query'      => [
                [
                    'taxonomy' => 'jjwz_service_cat',
                    'field'    => 'slug',
                    'terms'    => $slug,
                ]
            ]
        ] );
    }

    if ( $portfolio_query->have_posts() ) :
    ?>
    <section class="section portfolio-section" style="background: var(--clr-warm-white);">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Our Work Gallery</span>
                <h2 class="section-title">Featured <em>Visual Stories</em></h2>
                <p class="lead" style="margin-inline: auto;">Browse our editorial stories capturing authentic milestones and raw emotions.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                <?php while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); 
                    $thumb = get_the_post_thumbnail_url( null, 'jjwz-portfolio' );
                    if ( ! $thumb ) {
                        $thumb = jjwz_get_option( 'jjw_default_placeholder_portfolio' );
                    }
                    $venue = get_post_meta( get_the_ID(), 'portfolio_venue', true ) ?: 'Premium Venue';
                    $city  = get_post_meta( get_the_ID(), 'portfolio_city', true ) ?: 'Amritsar';
                ?>
                <div class="portfolio-card card">
                    <a href="<?php the_permalink(); ?>">
                        <div class="card__media" style="aspect-ratio: 4/3; overflow: hidden;">
                            <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card__body" style="padding: 1.5rem;">
                            <h3 style="font-size: 1.25rem; margin-bottom: 8px; color: var(--clr-obsidian);"><?php the_title(); ?></h3>
                            <span style="font-size: 12px; color: var(--clr-mist);"><?php echo esc_html( $venue ); ?> • <?php echo esc_html( $city ); ?></span>
                        </div>
                    </a>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 7. Testimonials Section -->
    <?php
    $testimonial_query = new WP_Query( [
        'post_type'      => 'jjwz_testimonial',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'testimonial_service',
                'value'   => get_the_title(),
                'compare' => 'LIKE',
            ]
        ],
    ] );

    if ( ! $testimonial_query->have_posts() ) {
        $testimonial_query = new WP_Query( [
            'post_type'      => 'jjwz_testimonial',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        ] );
    }

    if ( $testimonial_query->have_posts() ) :
    ?>
    <section class="section testimonials-section" style="background: var(--clr-cream); border-top: 1px solid var(--clr-border);">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Client Feedback</span>
                <h2 class="section-title">Kind Words from <em>Our Clients</em></h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                <?php while ( $testimonial_query->have_posts() ) : $testimonial_query->the_post(); 
                    $rating = get_post_meta( get_the_ID(), 'testimonial_rating', true ) ?: '5';
                    $review = get_post_meta( get_the_ID(), 'testimonial_review', true ) ?: get_the_content();
                    $couple = get_the_title();
                ?>
                <div class="testimonial-card" style="padding: 2rem; background: var(--clr-warm-white); border-radius: var(--radius-xl); border: 1px solid var(--clr-border); display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                    <div>
                        <div style="color: var(--clr-gold); font-size: 1.2rem; margin-bottom: 1rem;">
                            <?php echo str_repeat('★', (int) $rating); ?>
                        </div>
                        <blockquote style="font-size: 14px; line-height: 1.8; color: var(--clr-mist); font-family: var(--font-body); font-style: italic; margin-bottom: 1.5rem;">
                            "<?php echo esc_html( $review ); ?>"
                        </blockquote>
                    </div>
                    <div style="border-top: 1px solid var(--clr-border); padding-top: 1rem; margin-top: auto;">
                        <cite style="font-style: normal; font-weight: 600; color: var(--clr-obsidian); font-size: 14px; display: block;"><?php echo esc_html( $couple ); ?></cite>
                        <span style="font-size: 11px; color: var(--clr-gold); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">Premium Milestone Client</span>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 8. FAQ Section -->
    <?php
    $faq_ids = get_post_meta( $post_id, 'svc_faqs', true ) ?: [];
    
    // Fallback: query FAQs from tax jjwz_service_cat
    if ( empty( $faq_ids ) ) {
        $faq_query = new WP_Query( [
            'post_type'      => 'jjwz_faq',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'tax_query'      => [
                [
                    'taxonomy' => 'faq_category',
                    'field'    => 'slug',
                    'terms'    => $slug,
                ]
            ]
        ] );
        if ( $faq_query->have_posts() ) {
            $faq_ids = wp_list_pluck( $faq_query->posts, 'ID' );
        }
        wp_reset_postdata();
    }

    if ( ! empty( $faq_ids ) ) :
    ?>
    <section class="section faq-section" style="background: var(--clr-warm-white); border-top: 1px solid var(--clr-border);">
        <div class="container" style="max-width: 900px;">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Got Questions?</span>
                <h2 class="section-title">Frequently Asked <em>Questions</em></h2>
            </div>

            <div class="jjwz-accordion" role="list" aria-label="Service FAQs">
                <?php foreach ( $faq_ids as $faq_id ) : 
                    $uid = 'faq-' . $faq_id;
                    $q   = get_post_meta( $faq_id, 'faq_question', true ) ?: get_the_title( $faq_id );
                    $a   = get_post_meta( $faq_id, 'faq_answer', true ) ?: get_post_field( 'post_content', $faq_id );
                ?>
                <div class="accordion__item" role="listitem" style="border-bottom: 1px solid var(--clr-border); padding: 1.25rem 0;">
                    <button class="accordion__trigger"
                            id="<?php echo esc_attr( $uid . '-btn' ); ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo esc_attr( $uid . '-panel' ); ?>"
                            style="width: 100%; display: flex; justify-content: space-between; align-items: center; background: none; border: none; cursor: pointer; text-align: left; padding: 0.5rem 0; font-family: var(--font-heading); font-size: 1.35rem; color: var(--clr-obsidian);">
                        <span><?php echo esc_html( $q ); ?></span>
                        <span class="accordion__icon" aria-hidden="true" style="color: var(--clr-gold); transition: transform 0.3s ease;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </span>
                    </button>
                    <div class="accordion__panel"
                         id="<?php echo esc_attr( $uid . '-panel' ); ?>"
                         role="region"
                         aria-labelledby="<?php echo esc_attr( $uid . '-btn' ); ?>"
                         hidden
                         style="display: none; padding: 1rem 0; font-family: var(--font-body); font-size: 0.95rem; line-height: 1.75; color: var(--clr-mist);">
                        <div>
                            <?php echo wp_kses_post( wpautop( $a ) ); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 9. Related Blogs Section -->
    <?php
    $blog_query = new WP_Query( [
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'category_name'  => $slug, // matches category slug
    ] );

    if ( ! $blog_query->have_posts() ) {
        // Fallback to recent posts
        $blog_query = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        ] );
    }

    if ( $blog_query->have_posts() ) :
    ?>
    <section class="section blogs-section" style="background: var(--clr-cream); border-top: 1px solid var(--clr-border);">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Creative Insights</span>
                <h2 class="section-title">Related <em>Readings &amp; Tips</em></h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); 
                    $thumb = get_the_post_thumbnail_url( null, 'jjwz-blog-card' );
                    if ( ! $thumb ) {
                        $thumb = jjwz_get_option( 'jjw_default_placeholder_blog' );
                    }
                ?>
                <div class="blog-card card" style="background: var(--clr-warm-white);">
                    <a href="<?php the_permalink(); ?>">
                        <div class="card__media" style="aspect-ratio: 16/10; overflow: hidden;">
                            <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card__body" style="padding: 1.5rem;">
                            <span style="font-size: 11px; color: var(--clr-gold); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 6px;">Photography Guide</span>
                            <h3 style="font-size: 1.25rem; color: var(--clr-obsidian); margin-bottom: 8px; line-height: 1.35;"><?php the_title(); ?></h3>
                            <p style="font-size: 13px; color: var(--clr-mist); line-height: 1.6;"><?php echo wp_trim_words( get_the_excerpt(), 18 ); ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 10. Contact CTA & 11. Availability Form Section -->
    <section id="availability" class="section availability-section" style="background: var(--clr-warm-white); border-top: 1px solid var(--clr-border);">
        <div class="container" style="max-width: 800px;">
            <div class="text-center" style="margin-bottom: 2.5rem;">
                <span class="eyebrow">Secure Your Date</span>
                <h2 class="section-title">Check <em>Availability &amp; Packages</em></h2>
                <p class="lead" style="margin-inline: auto;">Submit your details below. Our bookings coordinator will cross-reference schedules and send pricing catalogs within 24 hours.</p>
            </div>

            <div class="availability-form-card">
                <form id="jjwz-availability-form" method="post" action="">
                    <!-- Nonce fields for AJAX security -->
                    <input type="hidden" name="action" value="jjwz_submit_lead">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'jjwz_lead_nonce' ); ?>">
                    <input type="hidden" name="source" value="<?php echo esc_url( get_permalink() ); ?>">
                    <!-- Honeypot -->
                    <input type="text" name="jjwz_honey" style="display: none;" autocomplete="off">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Full Name <span style="color:red;">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Jaspreet Singh" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address <span style="color:red;">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="jaspreet@example.com" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+91 98765 43210">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Target Event Date</label>
                            <input type="date" name="event_date" class="form-control">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label class="form-label">Selected Photography Service</label>
                        <select name="service" class="form-control" style="appearance: none;">
                            <option value="<?php echo esc_attr( get_the_title() ); ?>"><?php echo esc_html( get_the_title() ); ?></option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label class="form-label">Event Details / Message</label>
                        <textarea name="message" class="form-control" placeholder="Tell us about your visual vision, venue location, or specific requirements..." rows="4"></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn--primary" style="padding-inline: 3rem; font-size: 13px;">
                            🚀 Send Availability Query
                        </button>
                    </div>
                </form>

                <div id="form-feedback" style="display: none; margin-top: 1.5rem; padding: 1.25rem; border-radius: var(--radius-md); text-align: center; font-family: var(--font-body); font-weight: 500;"></div>
            </div>
        </div>
    </section>

</article>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion functionality
    const triggers = document.querySelectorAll('.accordion__trigger');
    triggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const panel = document.getElementById(this.getAttribute('aria-controls'));
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            if (panel) {
                panel.style.display = isExpanded ? 'none' : 'block';
                panel.removeAttribute('hidden');
            }
            
            const icon = this.querySelector('.accordion__icon');
            if (icon) {
                icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });
    });

    // Form Submission AJAX
    const form = document.getElementById('jjwz-availability-form');
    const feedback = document.getElementById('form-feedback');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const origBtnText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending... ⏳';
            feedback.style.display = 'none';

            fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origBtnText;
                
                feedback.style.display = 'block';
                if (data.success) {
                    feedback.className = 'notice notice-success';
                    feedback.style.backgroundColor = '#d4edda';
                    feedback.style.color = '#155724';
                    feedback.style.border = '1px solid #c3e6cb';
                    feedback.innerHTML = '<strong>✅ Success!</strong> ' + data.data.message;
                    form.reset();
                } else {
                    feedback.className = 'notice notice-error';
                    feedback.style.backgroundColor = '#f8d7da';
                    feedback.style.color = '#721c24';
                    feedback.style.border = '1px solid #f5c6cb';
                    feedback.innerHTML = '<strong>❌ Error:</strong> ' + data.data.message;
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origBtnText;
                feedback.style.display = 'block';
                feedback.style.backgroundColor = '#f8d7da';
                feedback.style.color = '#721c24';
                feedback.style.border = '1px solid #f5c6cb';
                feedback.innerHTML = '<strong>❌ Network Error:</strong> Could not submit form. Please check your connection.';
            });
        });
    }
});
</script>

<?php get_footer(); ?>
