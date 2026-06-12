<?php
/**
 * Template Name: Service + City Landing Page
 *
 * Renders dynamically created Service + City landing pages.
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

get_header();

$service_slug  = get_query_var( 'jjwz_service_slug' );
$location_slug = get_query_var( 'jjwz_location_slug' );

$seo_raw = get_option( 'jjw_service_city_seo', '[]' );
$seo_items = json_decode( $seo_raw, true ) ?: [];

$seo_title = '';
$meta_desc = '';
$intro_content = '';
$faq_content = '';
$cta_content = '';

foreach ( $seo_items as $item ) {
    if ( ( $item['service'] ?? '' ) === $service_slug && ( $item['city'] ?? '' ) === $location_slug ) {
        $seo_title = $item['seo_title'] ?? '';
        $meta_desc = $item['meta_description'] ?? '';
        $intro_content = $item['intro_content'] ?? '';
        $faq_content = $item['faq_content'] ?? '';
        $cta_content = $item['cta_content'] ?? '';
        break;
    }
}

$service_name = ucwords( str_replace( '-', ' ', $service_slug ) );
$location_name = ucwords( str_replace( '-', ' ', $location_slug ) );

if ( ! $seo_title ) {
    $seo_title = sprintf( 'Luxury %s Photographer in %s', $service_name, $location_name );
}
if ( ! $intro_content ) {
    $intro_content = sprintf( 'JJ WeddingZ specializes in luxury, editorial %s photography in %s. We focus on natural storytelling, elegant compositions, and authentic details to capture your memories beautifully.', strtolower( $service_name ), $location_name );
}
if ( ! $cta_content ) {
    $cta_content = sprintf( 'Ready to document your %s in %s? Get in touch with our team today to inquire about availability and packages.', strtolower( $service_name ), $location_name );
}

// ─── Fetch Hero Image ───
$hero_bg_url = '';
$svc_post = get_page_by_path( $service_slug, OBJECT, 'jjwz_service' );
if ( $svc_post ) {
    $svc_img = get_post_meta( $svc_post->ID, 'svc_hero_image', true );
    if ( empty( $svc_img ) && function_exists( 'get_field' ) ) {
        $svc_img = get_field( 'svc_hero_image', $svc_post->ID );
    }
    if ( is_array( $svc_img ) && ! empty( $svc_img['url'] ) ) {
        $hero_bg_url = $svc_img['url'];
    } elseif ( is_numeric( $svc_img ) ) {
        $hero_bg_url = wp_get_attachment_image_url( $svc_img, 'full' );
    } elseif ( is_string( $svc_img ) && $svc_img ) {
        $hero_bg_url = $svc_img;
    }
}

if ( ! $hero_bg_url ) {
    $loc_post = get_page_by_path( $location_slug, OBJECT, 'jjwz_location' );
    if ( $loc_post ) {
        $loc_img = get_post_meta( $loc_post->ID, 'location_hero_image', true );
        if ( empty( $loc_img ) && function_exists( 'get_field' ) ) {
            $loc_img = get_field( 'location_hero_image', $loc_post->ID );
        }
        if ( is_array( $loc_img ) && ! empty( $loc_img['url'] ) ) {
            $hero_bg_url = $loc_img['url'];
        } elseif ( is_numeric( $loc_img ) ) {
            $hero_bg_url = wp_get_attachment_image_url( $loc_img, 'full' );
        } elseif ( is_string( $loc_img ) && $loc_img ) {
            $hero_bg_url = $loc_img;
        }
    }
}

if ( ! $hero_bg_url ) {
    $hero_bg_url = jjwz_get_option( 'jjw_default_placeholder_portfolio' );
}

if ( ! $hero_bg_url ) {
    $hero_bg_url = get_template_directory_uri() . '/assets/images/placeholder-category-wedding.png';
}

$wa_link = jjwz_wa_link( 'Check Availability', 'btn btn--primary', 'seo-landing-wa-cta', 'Hi, I would like to inquire about booking a ' . $service_name . ' in ' . $location_name . '.' );
?>

<div class="landing-service-location">

    <!-- Hero Banner -->
    <header class="post-hero" aria-label="Service in Location Header">
        <?php if ( $hero_bg_url ) : ?>
        <div class="post-hero__bg" aria-hidden="true">
            <img src="<?php echo esc_url( $hero_bg_url ); ?>" alt="" class="post-hero__img" fetchpriority="high" decoding="sync">
            <div class="post-hero__overlay"></div>
        </div>
        <?php endif; ?>
        <div class="container post-hero__content">
            <nav class="jjwz-breadcrumb" aria-label="Breadcrumb">
                <ol class="breadcrumb__list">
                    <li><a href="<?php echo esc_url( home_url() ); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/services' ) ); ?>">Services</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/' . $service_slug . '-photographer-in-' . $location_slug ) ); ?>"><?php echo esc_html( $service_name ); ?> in <?php echo esc_html( $location_name ); ?></a></li>
                </ol>
            </nav>
            <span class="eyebrow post-hero__cat">✨ Premium Photography Campaign</span>
            <h1 class="post-hero__title" style="font-size: clamp(2.25rem, 5vw, 4rem); max-width: 900px;"><?php echo esc_html( $seo_title ); ?></h1>
        </div>
    </header>

    <!-- Intro Narrative Section -->
    <section class="section intro-section" style="background: var(--clr-white); color: var(--clr-obsidian);">
        <div class="container" style="max-width: 1200px;">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 3rem; align-items: start;">
                <div>
                    <span class="eyebrow" style="color: var(--clr-gold); display: block; margin-bottom: 1rem;">The Art of Preserving Milestones</span>
                    <h2 class="section-title" style="font-size: 2rem; text-align: left; line-height: 1.25;">Uncompromising<br><em>Luxury & Authenticity</em></h2>
                </div>
                <div class="lead" style="font-size: 1.15rem; line-height: 1.8; color: var(--clr-mist);">
                    <?php echo wp_kses_post( wpautop( $intro_content ) ); ?>
                    <div style="margin-top: 2rem;">
                        <?php echo $wa_link; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Portfolios Grid -->
    <?php
    $portfolio_query = new WP_Query( [
        'post_type'      => 'jjwz_portfolio',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'tax_query'      => [
            'relation' => 'AND',
            [
                'taxonomy' => 'jjwz_service_cat',
                'field'    => 'slug',
                'terms'    => $service_slug,
            ],
            [
                'taxonomy' => 'jjwz_location_tax',
                'field'    => 'slug',
                'terms'    => $location_slug,
            ],
        ],
    ] );

    if ( ! $portfolio_query->have_posts() ) {
        $portfolio_query = new WP_Query( [
            'post_type'      => 'jjwz_portfolio',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'tax_query'      => [
                [
                    'taxonomy' => 'jjwz_service_cat',
                    'field'    => 'slug',
                    'terms'    => $service_slug,
                ],
            ],
        ] );
    }

    if ( $portfolio_query->have_posts() ) :
    ?>
    <section class="section portfolio-section" style="background: var(--clr-warm-white); border-top: 1px solid var(--clr-border);">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Visual Highlights</span>
                <h2 class="section-title">Featured <em><?php echo esc_html( $service_name ); ?> Stories</em></h2>
            </div>
            
            <div class="portfolio-grid grid-3" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                <?php while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); 
                    $thumb = get_the_post_thumbnail_url( null, 'large' );
                    if ( ! $thumb ) {
                        $thumb = jjwz_get_option( 'jjw_default_placeholder_portfolio' );
                    }
                    $venue = get_post_meta( get_the_ID(), 'portfolio_venue', true ) ?: 'Premium Venue';
                ?>
                <div class="portfolio-card card" style="background: var(--clr-white); border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; height: 100%;">
                    <a href="<?php the_permalink(); ?>" style="text-decoration: none; display: flex; flex-direction: column; height: 100%;">
                        <div class="card__media" style="aspect-ratio: 4/3; overflow: hidden; position: relative;">
                            <?php if ( $thumb ) : ?>
                                <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" style="width:100%; height:100%; object-fit:cover; transition: transform 0.6s ease;">
                            <?php else : ?>
                                <div style="width:100%; height:100%; background:var(--clr-border);"></div>
                            <?php endif; ?>
                        </div>
                        <div class="card__body" style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--clr-obsidian); font-weight: 600;"><?php the_title(); ?></h3>
                            <span class="portfolio-card__meta" style="font-size: 0.85rem; color: var(--clr-mist); font-family: var(--font-body);"><?php echo esc_html( $venue ); ?> • <?php echo esc_html( $location_name ); ?></span>
                        </div>
                    </a>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials Section -->
    <?php
    $testimonial_query = new WP_Query( [
        'post_type'      => 'jjwz_testimonial',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'testimonial_location',
                'value'   => $location_name,
                'compare' => 'LIKE',
            ],
            [
                'key'     => 'testimonial_service',
                'value'   => $service_name,
                'compare' => 'LIKE',
            ],
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
    <section class="section testimonials-section" style="background: var(--clr-white); border-top: 1px solid var(--clr-border);">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Kind Words</span>
                <h2 class="section-title">Client <em>Testimonials</em></h2>
            </div>
            
            <div class="testimonials-grid grid-3" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                <?php while ( $testimonial_query->have_posts() ) : $testimonial_query->the_post(); 
                    $rating   = get_post_meta( get_the_ID(), 'testimonial_rating', true ) ?: '5';
                    $review   = get_post_meta( get_the_ID(), 'testimonial_review', true ) ?: get_the_content();
                    $couple   = get_the_title();
                    $relation = get_post_meta( get_the_ID(), 'testimonial_service', true ) ?: 'Premium Session';
                ?>
                <div class="testimonial-card" style="padding: 2.25rem; background: var(--clr-warm-white); border-radius: var(--radius-xl); border: 1px solid var(--clr-border); display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                    <div>
                        <div class="rating-stars" style="color: var(--clr-gold); font-size: 1.15rem; margin-bottom: 1.25rem;" aria-label="<?php echo esc_attr($rating); ?> star rating">
                            <?php echo str_repeat('★', (int) $rating); ?>
                        </div>
                        <blockquote style="font-size: 0.95rem; line-height: 1.75; color: var(--clr-mist); font-family: var(--font-body); font-style: italic; margin-bottom: 1.5rem;">
                            "<?php echo esc_html( $review ); ?>"
                        </blockquote>
                    </div>
                    <div style="border-top: 1px solid var(--clr-border); padding-top: 1.25rem; margin-top: auto;">
                        <cite style="font-style: normal; font-weight: 600; color: var(--clr-obsidian); font-size: 1.05rem; display: block;"><?php echo esc_html( $couple ); ?></cite>
                        <span style="font-size: 0.8rem; color: var(--clr-gold); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;"><?php echo esc_html( $relation ); ?></span>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- FAQ Accordions Section -->
    <?php
    $faq_query = new WP_Query( [
        'post_type'      => 'jjwz_faq',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'jjwz_service_cat',
                'field'    => 'slug',
                'terms'    => $service_slug,
            ],
        ],
    ] );

    if ( ! $faq_query->have_posts() ) {
        $faq_query = new WP_Query( [
            'post_type'      => 'jjwz_faq',
            'posts_per_page' => 5,
            'post_status'    => 'publish',
        ] );
    }
    ?>
    <section class="section faq-section" style="background: var(--clr-warm-white); border-top: 1px solid var(--clr-border);">
        <div class="container" style="max-width: 900px;">
            <div class="section-header text-center" style="margin-bottom: 3.5rem;">
                <span class="eyebrow">Common Questions</span>
                <h2 class="section-title">Frequently Asked <em>Questions</em></h2>
            </div>

            <?php if ( $faq_content ) : ?>
            <div class="faq-intro-block lead text-center" style="margin-bottom: 3rem; font-size: 1.1rem; color: var(--clr-mist); font-family: var(--font-body);">
                <?php echo wp_kses_post( wpautop( $faq_content ) ); ?>
            </div>
            <?php endif; ?>

            <?php if ( $faq_query->have_posts() ) : ?>
            <div class="jjwz-accordion" role="list" aria-label="Frequently Asked Questions">
                <?php while ( $faq_query->have_posts() ) : $faq_query->the_post(); 
                    $uid = 'faq-' . get_the_ID();
                    $q   = get_post_meta( get_the_ID(), 'faq_question', true ) ?: get_the_title();
                    $a   = get_post_meta( get_the_ID(), 'faq_answer', true ) ?: get_the_content();
                ?>
                <div class="accordion__item" role="listitem" style="border-bottom: 1px solid var(--clr-border); padding: 1.25rem 0;">
                    <button class="accordion__trigger"
                            id="<?php echo esc_attr( $uid . '-btn' ); ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo esc_attr( $uid . '-panel' ); ?>"
                            style="width: 100%; display: flex; justify-content: space-between; align-items: center; background: none; border: none; cursor: pointer; text-align: left; padding: 0.5rem 0; font-family: var(--font-heading); font-size: 1.35rem; color: var(--clr-obsidian);">
                        <span class="accordion__question"><?php echo esc_html( $q ); ?></span>
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
                        <div class="accordion__body">
                            <?php echo wp_kses_post( wpautop( $a ) ); ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Map & Prefooter CTA Section -->
    <?php
    $maps_iframe = '';
    $loc_post = get_page_by_path( $location_slug, OBJECT, 'jjwz_location' );
    if ( $loc_post ) {
        $maps_iframe = get_post_meta( $loc_post->ID, 'location_google_map', true );
        if ( empty( $maps_iframe ) && function_exists( 'get_field' ) ) {
            $maps_iframe = get_field( 'location_google_map', $loc_post->ID );
        }
    }
    
    if ( ! $maps_iframe ) {
        // Look up options panel branches repeater for maps URL fallback
        $branches_raw = get_option( 'jjw_branches', '[]' );
        $branches     = json_decode( $branches_raw, true ) ?: [];
        foreach ( $branches as $b ) {
            if ( strtolower( $b['name'] ?? '' ) === strtolower( $location_slug ) ) {
                if ( ! empty( $b['maps_url'] ) ) {
                    $maps_iframe = '<div class="text-center" style="padding: 4rem 2rem; background: var(--clr-warm-white); border-radius: var(--radius-xl);"><h3 style="font-family: var(--font-heading); margin-bottom:1rem; font-size: 1.5rem;">Visit Our ' . esc_html( $location_name ) . ' Branch</h3><a href="' . esc_url( $b['maps_url'] ) . '" target="_blank" rel="noopener" class="btn btn--outline">Get Directions on Google Maps 🗺️</a></div>';
                }
                break;
            }
        }
    }
    ?>
    
    <?php if ( $maps_iframe ) : ?>
    <section class="section maps-section" style="background: var(--clr-white); padding: 0;">
        <div class="container" style="max-width: 100%; padding: 0;">
            <div class="location-maps-wrapper" style="border-top: 1px solid var(--clr-border); border-bottom: 1px solid var(--clr-border); width:100%; aspect-ratio:21/9; max-height: 450px; overflow:hidden;">
                <?php 
                if ( strpos( $maps_iframe, '<iframe' ) !== false ) {
                    echo str_replace( '<iframe', '<iframe style="border:0; width:100%; height:100%; filter: grayscale(1) invert(0.9) contrast(1.2);"', $maps_iframe );
                } else {
                    echo $maps_iframe; 
                }
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Final CTA Banner -->
    <section class="prefooter-cta section--sm" style="background: var(--clr-obsidian); color: var(--clr-white); border-top: 1px solid var(--clr-border); padding: 5rem 0;">
        <div class="container text-center" style="max-width: 800px;">
            <span class="eyebrow" style="color: var(--clr-gold); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.15em; display: block; margin-bottom: 1.5rem;">Begin Your Story</span>
            <h2 class="section-title" style="color: var(--clr-warm-white); font-size: clamp(2rem, 4vw, 3.25rem); line-height: 1.2; margin-bottom: 2rem;">Ready to Create Something<br><em>Timeless Together?</em></h2>
            <p style="font-size: 1.1rem; color: var(--clr-fog); margin-bottom: 2.5rem; line-height: 1.8; font-family: var(--font-body);"><?php echo esc_html( $cta_content ); ?></p>
            <div>
                <?php echo $wa_link; ?>
            </div>
        </div>
    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ accordion functionality
    const triggers = document.querySelectorAll('.accordion__trigger');
    triggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const panel = document.getElementById(this.getAttribute('aria-controls'));
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Toggle current
            this.setAttribute('aria-expanded', !isExpanded);
            if (panel) {
                panel.style.display = isExpanded ? 'none' : 'block';
                panel.removeAttribute('hidden');
            }
            
            // Icon animation
            const icon = this.querySelector('.accordion__icon');
            if (icon) {
                icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });
    });
});
</script>

<?php
get_footer();
