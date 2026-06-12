<?php
/**
 * single-jjwz_location.php — Single Location Hub template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

get_header();
the_post();

$post_id          = get_the_ID();
$hero_img         = jjwz_get_option( 'location_hero_image', '', $post_id );
$description      = jjwz_get_option( 'location_description', '', $post_id );
$seo_content      = jjwz_get_option( 'location_seo_content', '', $post_id );
$maps_iframe      = jjwz_get_option( 'location_google_map', '', $post_id );

$hero_img_url = is_array( $hero_img ) ? $hero_img['url'] : ( is_numeric( $hero_img ) ? wp_get_attachment_image_url( $hero_img, 'full' ) : $hero_img );
$wa_link     = jjwz_wa_link( 'Book a Consultation', 'btn btn--primary', 'location-wa-cta', 'Hi, I would like to book a consultation for the ' . get_the_title() . ' studio.' );
?>

<article id="location-<?php the_ID(); ?>" <?php post_class( 'jjwz-location-single' ); ?>>

    <!-- Location Hero -->
    <header class="post-hero" aria-label="Location header">
        <?php if ( $hero_img_url ) : ?>
        <div class="post-hero__bg" aria-hidden="true">
            <img src="<?php echo esc_url( $hero_img_url ); ?>" alt="" class="post-hero__img" fetchpriority="high" decoding="sync">
            <div class="post-hero__overlay"></div>
        </div>
        <?php endif; ?>
        <div class="container post-hero__content">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow post-hero__cat">📍 Studio Location Hub</span>
            <h1 class="post-hero__title"><?php the_title(); ?></h1>
        </div>
    </header>

    <!-- Content & Info Layout -->
    <div class="container section">
        <div class="post-layout">
            <div class="post-layout__main">
                
                <!-- Main Studio Description -->
                <div class="location-description lead" style="margin-bottom:3rem;">
                    <?php if ( $description ) : ?>
                        <?php echo wp_kses_post( $description ); ?>
                    <?php else : ?>
                        <?php the_content(); ?>
                    <?php endif; ?>
                </div>

                <!-- Google Maps Embed -->
                <?php if ( $maps_iframe ) : ?>
                <div class="location-maps-section" style="margin-bottom:4rem; border-radius:var(--radius-xl); overflow:hidden; box-shadow:var(--shadow-md); aspect-ratio:16/9; width:100%;">
                    <?php echo $maps_iframe; // Output maps iframe directly ?>
                </div>
                <?php endif; ?>

                <!-- SEO Editorial Text -->
                <?php if ( $seo_content ) : ?>
                <div class="location-seo-content" style="margin-top:3rem; border-top:1px solid var(--clr-border); padding-top:2.5rem;">
                    <h2 class="section-title" style="font-size:1.85rem; margin-bottom:1.5rem;">Regional <em>Photography Services</em></h2>
                    <div class="text-mist" style="font-size:0.95rem; line-height:1.75;">
                        <?php echo wp_kses_post( $seo_content ); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Related Portfolios shot at this Location -->
                <?php
                // Try to find portfolio items matching the Location taxonomy
                $location_name = get_the_title();
                $clean_loc_name = preg_replace( '/\s+Studio|\s+Branch/i', '', $location_name );

                $related_portfolio = new WP_Query( [
                    'post_type'      => 'jjwz_portfolio',
                    'posts_per_page' => 4,
                    'post_status'    => 'publish',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'jjwz_location_tax',
                            'field'    => 'name',
                            'terms'    => $clean_loc_name,
                        ]
                    ]
                ] );

                // Fallback to random portfolio items if no taxonomy matches
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
                <div class="location-portfolio-highlights" style="margin-top:4rem;">
                    <h2 class="section-title" style="font-size:2rem; margin-bottom:2rem;">Visual Stories from <em><?php echo esc_html( $clean_loc_name ); ?></em></h2>
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
            <aside class="post-layout__sidebar" aria-label="Studio details">
                
                <!-- Quick Contact -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Schedule Consultation</h3>
                    <p class="text-mist" style="font-size:0.9rem; margin-bottom:1.5rem;">Plan a visit or hold a remote video consultation to explore our photography portfolios and plan your custom session timeline.</p>
                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        <?php echo $wa_link; ?>
                        <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--outline" style="justify-content:center;">Submit Inquire Form</a>
                    </div>
                </div>

                <!-- Open Hours Info -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Studio Hours</h3>
                    <table style="width:100%; border-collapse:collapse; font-size:0.85rem; color:var(--clr-mist);">
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.5rem 0; font-weight:600;">Mon–Fri</th>
                            <td align="right" style="padding:0.5rem 0;">10:00 AM – 8:00 PM</td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.5rem 0; font-weight:600;">Saturday</th>
                            <td align="right" style="padding:0.5rem 0;">10:00 AM – 8:00 PM</td>
                        </tr>
                        <tr>
                            <th align="left" style="padding:0.5rem 0; font-weight:600;">Sunday</th>
                            <td align="right" style="padding:0.5rem 0;">10:00 AM – 8:00 PM</td>
                        </tr>
                    </table>
                </div>
                
            </aside>
        </div>
    </div>

</article>

<?php get_footer(); ?>
