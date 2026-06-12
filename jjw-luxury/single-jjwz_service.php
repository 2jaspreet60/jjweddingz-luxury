<?php
/**
 * single-jjwz_service.php — Single Service template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

get_header();
the_post();

$post_id    = get_the_ID();
$icon       = jjwz_get_option( 'svc_icon', '💍', $post_id );
$hero_img   = jjwz_get_option( 'svc_hero_image', '', $post_id );
$short_desc = jjwz_get_option( 'svc_short_desc', '', $post_id );
$seo_content = jjwz_get_option( 'svc_seo_content', '', $post_id );

$hero_img_url = is_array( $hero_img ) ? $hero_img['url'] : ( is_numeric( $hero_img ) ? wp_get_attachment_image_url( $hero_img, 'full' ) : $hero_img );
$wa_link     = jjwz_wa_link( 'Book a Session', 'btn btn--primary', 'service-wa-cta', 'Hi, I would like to book a ' . get_the_title() . ' session.' );
?>

<article id="service-<?php the_ID(); ?>" <?php post_class( 'jjwz-service-single' ); ?>>

    <!-- Service Hero -->
    <header class="post-hero" aria-label="Service header">
        <?php if ( $hero_img_url ) : ?>
        <div class="post-hero__bg" aria-hidden="true">
            <img src="<?php echo esc_url( $hero_img_url ); ?>" alt="" class="post-hero__img" fetchpriority="high" decoding="sync">
            <div class="post-hero__overlay"></div>
        </div>
        <?php endif; ?>
        <div class="container post-hero__content">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow post-hero__cat"><?php echo esc_html( $icon ); ?> Our Services</span>
            <h1 class="post-hero__title"><?php the_title(); ?></h1>
            <?php if ( $short_desc ) : ?>
                <p class="lead" style="color:rgba(255,255,255,0.8); margin-top:1.5rem; margin-inline:auto; max-width:800px;"><?php echo esc_html( $short_desc ); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <!-- Content & Info Layout -->
    <div class="container section">
        <div class="post-layout">
            <div class="post-layout__main">
                
                <!-- Main Narrative Content -->
                <div class="service-description lead" style="margin-bottom:3rem;">
                    <?php if ( $seo_content ) : ?>
                        <?php echo wp_kses_post( $seo_content ); ?>
                    <?php else : ?>
                        <?php the_content(); ?>
                    <?php endif; ?>
                </div>

                <!-- Related Portfolio Stories -->
                <?php
                // Try to find portfolio items matching the service category/title
                $related_portfolio = new WP_Query( [
                    'post_type'      => 'jjwz_portfolio',
                    'posts_per_page' => 4,
                    'post_status'    => 'publish',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'jjwz_service_cat',
                            'field'    => 'slug',
                            'terms'    => get_post_field( 'post_name', get_the_ID() ),
                        ]
                    ]
                ] );

                // Fallback to general featured portfolio items if no category match
                if ( ! $related_portfolio->have_posts() ) {
                    $related_portfolio = new WP_Query( [
                        'post_type'      => 'jjwz_portfolio',
                        'posts_per_page' => 4,
                        'post_status'    => 'publish',
                        'orderby'        => 'rand',
                    ] );
                }

                if ( $related_portfolio->have_posts() ) :
                ?>
                <div class="service-portfolio-highlights" style="margin-top:4rem;">
                    <h2 class="section-title" style="font-size:2rem; margin-bottom:2rem;">Related <em>Visual Stories</em></h2>
                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:1.5rem;">
                        <?php while ( $related_portfolio->have_posts() ) : $related_portfolio->the_post(); 
                            $thumb = get_the_post_thumbnail_url( null, 'jjwz-portfolio' );
                        ?>
                        <div class="portfolio-card card">
                            <a href="<?php the_permalink(); ?>">
                                <div class="card__media" style="aspect-ratio:4/3; overflow:hidden;">
                                    <?php if ( $thumb ) : ?>
                                        <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else : ?>
                                        <div style="width:100%; height:100%; background:var(--clr-border);"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="card__body">
                                    <h3 style="font-size:1.2rem; line-height:1.4; color:var(--clr-obsidian); margin:0;"><?php the_title(); ?></h3>
                                </div>
                            </a>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <aside class="post-layout__sidebar" aria-label="Service options">
                
                <!-- Quick Contact -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Capturing Milestones</h3>
                    <p class="text-mist" style="font-size:0.9rem; margin-bottom:1.5rem;">Interested in capturing your milestone with editorial excellence? Let's discuss pricing catalogs, schedules, and custom visual concepts.</p>
                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        <?php echo $wa_link; ?>
                        <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--outline" style="justify-content:center;">Inquire by Form</a>
                    </div>
                </div>

                <!-- Promise Badge -->
                <div class="sidebar-cta" style="background:var(--clr-obsidian); color:var(--clr-warm-white);">
                    <div class="sidebar-cta__icon">👤</div>
                    <h3 class="sidebar-cta__heading" style="color:var(--clr-warm-white);">100% Identity Promise</h3>
                    <p class="sidebar-cta__desc">We protect your facial identity. We sign a formal guarantee of zero face-swapping, zero skin whitening, and zero artificial plastic filters.</p>
                </div>
                
            </aside>
        </div>
    </div>

</article>

<?php get_footer(); ?>
