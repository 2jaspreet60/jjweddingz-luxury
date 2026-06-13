<?php
/**
 * page-services.php — Premium Services Dynamic Archive Page
 * Template Name: Services
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

$post_id        = get_the_ID();
$page_headline  = jjwz_get_option( 'jjwz_services_headline', 'Our <em>Premium Services</em>', $post_id );
$page_desc      = jjwz_get_option( 'jjwz_services_sub', 'Exquisite visual storytelling across distinct photography disciplines. Meticulously documented with natural compositions and true color science.', $post_id );

// Multi-brand context lookup
$active_brand = get_option( 'jjw_active_brand', 'jjw' );

// Query all services ordered by display order
$services_query = new WP_Query( [
    'post_type'      => 'jjwz_service',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_key'       => 'svc_display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
    'meta_query'     => [
        'relation' => 'OR',
        [
            'key'     => 'svc_brand',
            'value'   => $active_brand,
            'compare' => '=',
        ],
        [
            'key'     => 'svc_brand',
            'value'   => 'both',
            'compare' => '=',
        ],
        [
            'key'     => 'svc_brand',
            'compare' => 'NOT EXISTS',
        ]
    ]
] );
?>

<style>
/* Service Archive Specific Styles */
.services-section {
    background-color: var(--clr-ivory);
    padding-bottom: var(--sp-5xl);
}
.services-luxury-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--sp-xl);
    margin-top: var(--sp-2xl);
}
.luxury-service-card {
    background: var(--clr-warm-white);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: transform var(--transition-base), box-shadow var(--transition-base);
    border: 1px solid var(--clr-border);
    position: relative;
}
.luxury-service-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
    border-color: var(--clr-gold);
}
.luxury-service-card__media {
    position: relative;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    background: var(--clr-border);
}
.luxury-service-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}
.luxury-service-card:hover .luxury-service-card__img {
    transform: scale(1.06);
}
.luxury-service-card__icon {
    position: absolute;
    bottom: var(--sp-md);
    right: var(--sp-md);
    background: rgba(10, 10, 10, 0.75);
    backdrop-filter: blur(8px);
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    z-index: 2;
}
.luxury-service-card__body {
    padding: var(--sp-xl);
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.luxury-service-card__title {
    font-size: var(--text-xl);
    font-weight: 500;
    color: var(--clr-obsidian);
    margin-bottom: var(--sp-sm);
    line-height: 1.25;
}
.luxury-service-card__desc {
    font-family: var(--font-body);
    font-size: var(--text-sm);
    color: var(--clr-mist);
    line-height: 1.6;
    margin-bottom: var(--sp-lg);
    flex-grow: 1;
}
.luxury-service-card__footer {
    padding: var(--sp-xl);
    padding-top: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(0, 0, 0, 0.03);
}
.luxury-service-card__price {
    font-family: var(--font-body);
    font-weight: 600;
    font-size: var(--text-sm);
    color: var(--clr-gold);
}
.luxury-service-card__links {
    display: flex;
    gap: var(--sp-md);
    align-items: center;
}
.luxury-service-card__link-more {
    font-family: var(--font-body);
    font-size: var(--text-sm);
    font-weight: 600;
    text-transform: uppercase;
    color: var(--clr-obsidian);
    letter-spacing: 0.05em;
    transition: color var(--transition-fast);
}
.luxury-service-card__link-more:hover {
    color: var(--clr-gold);
}
.luxury-service-card__link-portfolio {
    font-family: var(--font-body);
    font-size: var(--text-xs);
    color: var(--clr-mist);
    border-bottom: 1px dotted var(--clr-mist);
    transition: all var(--transition-fast);
}
.luxury-service-card__link-portfolio:hover {
    color: var(--clr-gold);
    border-bottom-color: var(--clr-gold);
}

@media (max-width: 768px) {
    .services-luxury-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- ═══════════════════════════════════════════════════════════
     SERVICES HERO
     ═══════════════════════════════════════════════════════════ -->
<section class="services-hero" aria-label="Services hero" style="background: var(--clr-cream); padding-block: var(--sp-4xl);">
    <div class="container services-hero__inner text-center">
        <?php jjwz_breadcrumb(); ?>
        <span class="eyebrow">Our Photography Disciplines</span>
        <h1 class="services-hero__headline display-title" style="margin-bottom: var(--sp-md);"><?php echo wp_kses_post( $page_headline ); ?></h1>
        <p class="lead services-hero__sub" style="margin-inline: auto; max-width: 750px;"><?php echo esc_html( $page_desc ); ?></p>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SERVICES DYNAMIC GRID
     ═══════════════════════════════════════════════════════════ -->
<section class="services-section section" aria-label="Photography services collections">
    <div class="container">
        
        <?php if ( $services_query->have_posts() ) : ?>
            <div class="services-luxury-grid">
                <?php while ( $services_query->have_posts() ) : $services_query->the_post(); 
                    $curr_post_id = get_the_ID();
                    $slug         = get_post_field( 'post_name', $curr_post_id );
                    $icon         = get_post_meta( $curr_post_id, 'svc_icon', true ) ?: '📸';
                    $price        = get_post_meta( $curr_post_id, 'svc_starting_price', true );
                    $short_desc   = get_post_meta( $curr_post_id, 'svc_short_desc', true ) ?: get_the_excerpt();
                    
                    // Fetch Cover Image or fallback to Hero or Placeholder
                    $cover_img = get_post_meta( $curr_post_id, 'svc_cover_image', true );
                    if ( empty( $cover_img ) ) {
                        $cover_img = get_post_meta( $curr_post_id, 'svc_hero_image', true );
                    }
                    
                    $cover_url = '';
                    if ( is_array( $cover_img ) && ! empty( $cover_img['url'] ) ) {
                        $cover_url = $cover_img['url'];
                    } elseif ( is_numeric( $cover_img ) ) {
                        $cover_url = wp_get_attachment_image_url( $cover_img, 'large' );
                    } elseif ( is_string( $cover_img ) && $cover_img ) {
                        $cover_url = $cover_img;
                    }
                    
                    if ( ! $cover_url ) {
                        $cover_url = jjwz_get_option( 'jjw_default_placeholder_service' );
                    }
                    if ( ! $cover_url ) {
                        $cover_url = get_template_directory_uri() . '/assets/images/placeholder-category-default.png';
                    }

                    $portfolio_link = home_url( '/service-category/' . $slug . '/' );
                ?>
                <article class="luxury-service-card">
                    <div>
                        <div class="luxury-service-card__media">
                            <img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php the_title_attribute(); ?>" class="luxury-service-card__img" loading="lazy">
                            <div class="luxury-service-card__icon"><?php echo esc_html( $icon ); ?></div>
                        </div>
                        <div class="luxury-service-card__body">
                            <h2 class="luxury-service-card__title"><?php the_title(); ?></h2>
                            <p class="luxury-service-card__desc"><?php echo esc_html( wp_strip_all_tags( $short_desc ) ); ?></p>
                        </div>
                    </div>
                    
                    <div class="luxury-service-card__footer">
                        <?php if ( $price ) : ?>
                            <span class="luxury-service-card__price"><?php echo esc_html( $price ); ?></span>
                        <?php else : ?>
                            <span></span>
                        <?php endif; ?>
                        
                        <div class="luxury-service-card__links">
                            <a href="<?php the_permalink(); ?>" class="luxury-service-card__link-more">Learn More</a>
                            <a href="<?php echo esc_url( $portfolio_link ); ?>" class="luxury-service-card__link-portfolio">View Portfolio</a>
                        </div>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <div class="text-center" style="padding: 4rem 2rem;">
                <p class="lead">No dynamic photography services registered yet. Please check back later or run the installer notice in WP Admin.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     WHY CHOOSE US
     ═══════════════════════════════════════════════════════════ -->
<section class="why-us section" style="background:var(--clr-cream); border-top: 1px solid var(--clr-border);" aria-label="Why choose us">
    <div class="container">
        <div class="text-center" style="margin-bottom:4rem;">
            <span class="eyebrow">Why JJ WeddingZ Difference</span>
            <h2 class="section-title">The <em>Unwavering Standards</em> of<br>Our Creative Process</h2>
        </div>
        <div class="why-us__grid">
            <?php
            $reasons = [
                [ 'icon' => '🛡️', 'title' => '100% Identity Protection',  'desc' => 'We sign a formal pledge: zero face-swapping, zero skin whitening, zero artificial identity alteration. Your face, your story.' ],
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
     DYNAMIC FAQ COMPONENT
     ═══════════════════════════════════════════════════════════ -->
<section class="services-faq section" aria-label="Services FAQ">
    <div class="container">
        <div class="text-center" style="margin-bottom:3rem;">
            <span class="eyebrow">Frequently Asked</span>
            <h2 class="section-title">Questions &amp;<br><em>Answers</em></h2>
        </div>

        <div class="services-faq__tabs" role="tablist" aria-label="FAQ categories">
            <button class="faq-tab-btn is-active" data-tab="wedding" role="tab" aria-selected="true" id="faq-tab-wedding">Wedding &amp; Pre-Wedding</button>
            <button class="faq-tab-btn" data-tab="maternity" role="tab" aria-selected="false" id="faq-tab-maternity">Maternity &amp; Baby Shoots</button>
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
