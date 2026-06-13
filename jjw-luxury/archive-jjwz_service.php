<?php
/**
 * archive-jjwz_service.php — Premium Grouped Services Archive Template
 *
 * @package JJWeddingZ
 * @version 1.3.0
 */

get_header();

// Fetch Hero settings from Global Options page
$hero_img = jjwz_get_option( 'services_archive_hero_image' );
$hero_eyebrow = jjwz_get_option( 'services_archive_eyebrow', 'OUR SERVICES' );
$hero_title = jjwz_get_option( 'services_archive_title', 'Luxury Photography & Films' );
$hero_subtitle = jjwz_get_option( 'services_archive_subtitle', 'Crafted for Every Milestone' );

$hero_bg_url = '';
if ( is_array( $hero_img ) && ! empty( $hero_img['url'] ) ) {
    $hero_bg_url = $hero_img['url'];
} elseif ( is_numeric( $hero_img ) ) {
    $hero_bg_url = wp_get_attachment_image_url( $hero_img, 'full' );
} elseif ( is_string( $hero_img ) && $hero_img ) {
    $hero_bg_url = $hero_img;
}
if ( ! $hero_bg_url ) {
    $hero_bg_url = jjwz_get_option( 'jjw_default_placeholder_portfolio' );
}
if ( ! $hero_bg_url ) {
    $hero_bg_url = get_template_directory_uri() . '/assets/images/placeholder-category-default.png';
}

$active_brand = get_option( 'jjw_active_brand', 'jjw' );

// Query active services matching brand context, sorted by display order ASC
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

$grouped_services = [
    'wedding'    => [],
    'maternity'  => [],
    'family'     => [],
    'commercial' => [],
];

$slug_category_map = [
    'wedding-photography'             => 'wedding',
    'wedding-cinematography'          => 'wedding',
    'pre-wedding-photography'         => 'wedding',
    'destination-wedding-photography' => 'wedding',
    'couple-photoshoot'               => 'wedding',
    'maternity-photoshoot'            => 'maternity',
    'newborn-photoshoot'              => 'maternity',
    'baby-photoshoot'                 => 'maternity',
    'cake-smash-photoshoot'           => 'family',
    'first-birthday-photoshoot'       => 'family',
    'kids-photography'                => 'family',
    'family-photography'              => 'family',
    'anniversary-photoshoot'          => 'family',
    'studio-photography'              => 'commercial',
    'outdoor-photography'             => 'commercial',
];

if ( $services_query->have_posts() ) {
    while ( $services_query->have_posts() ) {
        $services_query->the_post();
        $pid = get_the_ID();
        $slug = get_post_field( 'post_name', $pid );
        
        $cat = get_post_meta( $pid, 'svc_category_group', true );
        if ( empty( $cat ) ) {
            $cat = $slug_category_map[ $slug ] ?? 'wedding';
        }
        
        $small_icon = get_post_meta( $pid, 'svc_small_icon', true );
        if ( empty( $small_icon ) ) {
            $small_icon = get_post_meta( $pid, 'svc_icon', true ) ?: '📸';
        }
        
        $thumb_img = get_post_meta( $pid, 'svc_thumbnail', true );
        if ( empty( $thumb_img ) ) {
            $thumb_img = get_post_meta( $pid, 'svc_cover_image', true );
        }
        if ( empty( $thumb_img ) ) {
            $thumb_img = get_post_meta( $pid, 'svc_hero_image', true );
        }
        
        $thumb_url = '';
        if ( is_array( $thumb_img ) && ! empty( $thumb_img['url'] ) ) {
            $thumb_url = $thumb_img['url'];
        } elseif ( is_numeric( $thumb_img ) ) {
            $thumb_url = wp_get_attachment_image_url( $thumb_img, 'medium_large' );
        } elseif ( is_string( $thumb_img ) && $thumb_img ) {
            $thumb_url = $thumb_img;
        }
        if ( ! $thumb_url ) {
            $thumb_url = jjwz_get_option( 'jjw_default_placeholder_service' );
        }
        if ( ! $thumb_url ) {
            $thumb_url = get_template_directory_uri() . '/assets/images/placeholder-category-default.png';
        }
        
        $price = get_post_meta( $pid, 'svc_starting_price', true );
        $excerpt = get_post_meta( $pid, 'svc_short_desc', true ) ?: get_the_excerpt();
        
        if ( isset( $grouped_services[ $cat ] ) ) {
            $grouped_services[ $cat ][] = [
                'ID'         => $pid,
                'title'      => get_the_title(),
                'permalink'  => get_permalink(),
                'thumb_url'  => $thumb_url,
                'small_icon' => $small_icon,
                'price'      => $price,
                'excerpt'    => wp_trim_words( wp_strip_all_tags( $excerpt ), 18 ),
            ];
        }
    }
    wp_reset_postdata();
}

$category_labels = [
    'wedding'    => 'Wedding Services',
    'maternity'  => 'Maternity & Newborn',
    'family'     => 'Family & Kids',
    'commercial' => 'Commercial',
];
?>

<main id="primary" class="site-main">

    <!-- ═══════════════════════════════
         REFINED HERO BANNER
         ═══════════════════════════════ -->
    <header class="services-hero-refined post-hero" aria-label="Services Header">
        <?php if ( $hero_bg_url ) : ?>
            <div class="post-hero__bg">
                <img src="<?php echo esc_url( $hero_bg_url ); ?>" alt="Our Services" class="post-hero__img" fetchpriority="high" decoding="sync">
                <div class="post-hero__overlay" style="background: linear-gradient(180deg, rgba(10, 10, 10, 0.4) 0%, rgba(10, 10, 10, 0.85) 100%);"></div>
            </div>
        <?php endif; ?>
        <div class="container post-hero__content text-center" style="padding-bottom: var(--sp-2xl);">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow text-gold" style="letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: var(--sp-xs); display: block;"><?php echo esc_html( $hero_eyebrow ); ?></span>
            <h1 class="post-hero__title display-title" style="font-family: var(--font-heading); font-weight: 400; margin-bottom: var(--sp-xs); color: var(--clr-warm-white);"><?php echo wp_kses_post( $hero_title ); ?></h1>
            <p class="lead" style="font-family: var(--font-body); font-size: var(--text-md); color: rgba(255, 255, 255, 0.9); font-style: italic; max-width: 600px; margin-inline: auto;"><?php echo esc_html( $hero_subtitle ); ?></p>
        </div>
    </header>

    <!-- ═══════════════════════════════
         DYNAMIC SERVICES GRID (GROUPED)
         ═══════════════════════════════ -->
    <section class="services-archive-section section" aria-label="Grouped Services" style="background-color: var(--clr-ivory); padding-block: var(--sp-4xl);">
        <div class="container">
            <?php 
            $has_any_service = false;
            foreach ( $grouped_services as $posts ) {
                if ( ! empty( $posts ) ) { $has_any_service = true; break; }
            }
            
            if ( $has_any_service ) :
                foreach ( $grouped_services as $cat_slug => $posts ) :
                    if ( empty( $posts ) ) { continue; }
                    $cat_label = $category_labels[ $cat_slug ];
                    ?>
                    <div class="services-archive-group" style="margin-bottom: var(--sp-4xl);">
                        <div class="services-archive-group__header" style="border-bottom: 1px solid var(--clr-border); padding-bottom: var(--sp-xs); margin-bottom: var(--sp-lg); text-align: left;">
                            <h2 class="services-archive-group__title" style="font-family: var(--font-heading); font-size: var(--text-xl); font-weight: 400; color: var(--clr-obsidian); letter-spacing: 0.05em; text-transform: uppercase; display: inline-block; position: relative;">
                                <?php echo esc_html( $cat_label ); ?>
                            </h2>
                        </div>
                        <div class="services-grid" style="margin-bottom: var(--sp-xl);">
                            <?php foreach ( $posts as $post ) : ?>
                                <article class="luxury-compact-card">
                                    <div class="luxury-compact-card__media">
                                        <img src="<?php echo esc_url( $post['thumb_url'] ); ?>" alt="<?php echo esc_attr( $post['title'] ); ?>" class="luxury-compact-card__img" loading="lazy">
                                        <div class="luxury-compact-card__overlay"></div>
                                        <div class="luxury-compact-card__icon"><?php echo esc_html( $post['small_icon'] ); ?></div>
                                    </div>
                                    <div class="luxury-compact-card__body">
                                        <h3 class="luxury-compact-card__title"><?php echo esc_html( $post['title'] ); ?></h3>
                                        <p class="luxury-compact-card__desc"><?php echo esc_html( $post['excerpt'] ); ?></p>
                                        <?php if ( $post['price'] ) : ?>
                                            <span class="luxury-compact-card__price"><?php echo esc_html( $post['price'] ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="luxury-compact-card__footer">
                                        <a href="<?php echo esc_url( $post['permalink'] ); ?>" class="luxury-compact-card__link">
                                            Explore Service <span class="arrow">&rarr;</span>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php 
                endforeach; 
            else :
            ?>
                <div class="text-center" style="padding-block: var(--sp-5xl);">
                    <h2 class="section-title">No Dynamic Services Registered</h2>
                    <p class="text-mist" style="margin-top: var(--sp-md);">Run the default installer notice in WP Admin or upload seeds to populate.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ═══════════════════════════════
         WHY CHOOSE US & PROCESS (LOWER SCROLL)
         ═══════════════════════════════ -->
    <section class="why-us section" style="background:var(--clr-cream); border-top: 1px solid var(--clr-border);" aria-label="Why choose us">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3.5rem;">
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
                <div class="why-card" data-anim="fade-up" data-anim-delay="<?php echo $i * 50; ?>">
                    <div class="why-card__icon"><?php echo $r['icon']; ?></div>
                    <h3 class="why-card__title"><?php echo esc_html( $r['title'] ); ?></h3>
                    <div class="why-card__desc" style="font-family: var(--font-body); font-size: var(--text-sm); color: var(--clr-mist); line-height: 1.6;"><?php echo esc_html( $r['desc'] ); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<?php 
get_footer(); 
