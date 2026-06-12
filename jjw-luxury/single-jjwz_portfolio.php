<?php
/**
 * single-jjwz_portfolio.php — Single Portfolio Story Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

get_header();
the_post();

$post_id          = get_the_ID();
$gallery          = jjwz_get_option( 'portfolio_gallery', [], $post_id );
$video_url        = jjwz_get_option( 'portfolio_video_url', '', $post_id );
$short_desc       = jjwz_get_option( 'portfolio_short_desc', '', $post_id );
$full_story       = jjwz_get_option( 'portfolio_full_story', '', $post_id );
$venue            = jjwz_get_option( 'portfolio_venue', '', $post_id );
$photographer     = jjwz_get_option( 'portfolio_photographer', '', $post_id );
$editor           = jjwz_get_option( 'portfolio_editor', '', $post_id );
$shoot_date       = jjwz_get_option( 'portfolio_shoot_date', '', $post_id );
$client_name      = jjwz_get_option( 'portfolio_client_name', '', $post_id );
$city             = jjwz_get_option( 'portfolio_city', '', $post_id );
$album_included   = jjwz_get_option( 'portfolio_album_included', false, $post_id );
$video_included   = jjwz_get_option( 'portfolio_video_included', false, $post_id );
$status           = jjwz_get_option( 'portfolio_status', 'delivered', $post_id );

$session_types    = get_the_terms( $post_id, 'jjwz_session_type' );
$themes           = get_the_terms( $post_id, 'jjwz_theme_type' );
$locations        = get_the_terms( $post_id, 'jjwz_location_tax' );
$cats             = get_the_terms( $post_id, 'jjwz_portfolio_cat' );

$main_image       = get_the_post_thumbnail_url( $post_id, 'jjwz-hero' );
$wa_link          = jjwz_wa_link( 'Inquire About Similar Shoot', 'btn btn--primary', 'portfolio-wa-cta', 'Hi, I saw your portfolio story for ' . get_the_title() . ' and would love to consult with you.' );

// Helper to get YouTube ID from URL
if ( ! function_exists( 'jjwz_get_youtube_id' ) ) {
    function jjwz_get_youtube_id( string $url ): string {
        preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match );
        return $match[1] ?? '';
    }
}

// Helper to get Vimeo ID from URL
if ( ! function_exists( 'jjwz_get_vimeo_id' ) ) {
    function jjwz_get_vimeo_id( string $url ): string {
        preg_match( '/vimeo\.com\/(?:video\/)?([0-9]+)/', $url, $match );
        return $match[1] ?? '';
    }
}
?>

<article id="portfolio-<?php the_ID(); ?>" <?php post_class( 'jjwz-portfolio-single' ); ?>>

    <!-- Portfolio Hero -->
    <header class="post-hero" aria-label="Portfolio header">
        <?php if ( $main_image ) : ?>
        <div class="post-hero__bg" aria-hidden="true">
            <img src="<?php echo esc_url( $main_image ); ?>" alt="" class="post-hero__img" fetchpriority="high" decoding="sync">
            <div class="post-hero__overlay"></div>
        </div>
        <?php endif; ?>
        <div class="container post-hero__content">
            <?php jjwz_breadcrumb(); ?>
            <?php if ( ! empty( $cats ) ) : ?>
            <span class="eyebrow post-hero__cat">
                <?php echo esc_html( $cats[0]->name ); ?>
            </span>
            <?php endif; ?>
            <h1 class="post-hero__title"><?php the_title(); ?></h1>
            
            <div class="post-hero__meta" style="margin-top:1.5rem; gap:1.5rem; flex-wrap:wrap; display:flex;">
                <?php if ( $venue ) : ?>
                    <span style="font-size:0.95rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--clr-gold);">📍 <?php echo esc_html( $venue ); ?></span>
                <?php endif; ?>
                <?php if ( $shoot_date ) : ?>
                    <span style="font-size:0.95rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--clr-fog);">📅 <?php echo esc_html( date( 'F Y', strtotime( $shoot_date ) ) ); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Content & Info Layout -->
    <div class="container section">
        <div class="post-layout">
            <div class="post-layout__main">
                
                <!-- Short Description / Standout Quote -->
                <?php if ( $short_desc ) : ?>
                    <blockquote style="font-family:var(--font-display); font-size:1.65rem; color:var(--clr-gold); font-style:italic; line-height:1.5; border-left:3px solid var(--clr-gold); padding-left:1.5rem; margin-bottom:2.5rem;">
                        "<?php echo esc_html( $short_desc ); ?>"
                    </blockquote>
                <?php endif; ?>

                <!-- Full Story Narrative -->
                <?php if ( $full_story ) : ?>
                    <div class="portfolio-story-content lead" style="margin-bottom:3.5rem;">
                        <?php echo wp_kses_post( $full_story ); ?>
                    </div>
                <?php else : ?>
                    <div class="portfolio-story-content lead" style="margin-bottom:3.5rem;">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

                <!-- Video Embed Section -->
                <?php if ( $video_url ) : 
                    $v_type = 'youtube';
                    $v_id = '';
                    if ( strpos( $video_url, 'vimeo' ) !== false ) {
                        $v_type = 'vimeo';
                        $v_id = jjwz_get_vimeo_id( $video_url );
                    } else {
                        $v_id = jjwz_get_youtube_id( $video_url );
                    }
                ?>
                <div class="portfolio-video-section" style="margin-bottom:4rem;">
                    <h2 class="section-title" style="font-size:2rem; margin-bottom:1.5rem;">The Cinematic <em>Highlight Reel</em></h2>
                    <div class="jjwz-video-wrap jjwz-video-wrap--16-9" data-video-type="<?php echo esc_attr( $v_type ); ?>" data-video-id="<?php echo esc_attr( $v_id ); ?>"></div>
                </div>
                <?php endif; ?>

                <!-- Image Gallery Grid -->
                <?php if ( ! empty( $gallery ) ) : ?>
                <div class="portfolio-gallery-section" style="margin-top:3.5rem;">
                    <h2 class="section-title" style="font-size:2rem; margin-bottom:2rem;">Selected <em>Gallery Frames</em></h2>
                    <div class="jjwz-masonry" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:1rem;">
                        <?php foreach ( $gallery as $img ) : ?>
                            <div class="gallery-frame-card" style="border-radius:var(--radius-md); overflow:hidden; aspect-ratio:4/3; box-shadow:var(--shadow-xs);">
                                <img src="<?php echo esc_url( $img['sizes']['jjwz-portfolio'] ?? $img['url'] ); ?>" 
                                     alt="<?php echo esc_attr( $img['alt'] ?? 'Gallery frame' ); ?>" 
                                     loading="lazy"
                                     style="width:100%; height:100%; object-fit:cover; transition:transform 0.5s;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar Info -->
            <aside class="post-layout__sidebar" aria-label="Shoot details">
                
                <!-- Details Widget -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Shoot Details</h3>
                    <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">Client</th>
                            <td align="right" style="padding:0.75rem 0; color:var(--clr-mist);"><?php echo esc_html( $client_name ?: get_the_title() ); ?></td>
                        </tr>
                        <?php if ( $city ) : ?>
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">City</th>
                            <td align="right" style="padding:0.75rem 0; color:var(--clr-mist);"><?php echo esc_html( $city ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( $venue ) : ?>
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">Venue</th>
                            <td align="right" style="padding:0.75rem 0; color:var(--clr-mist);"><?php echo esc_html( $venue ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( $photographer ) : ?>
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">Photographer</th>
                            <td align="right" style="padding:0.75rem 0; color:var(--clr-mist);"><?php echo esc_html( $photographer ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( $editor ) : ?>
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">Editor</th>
                            <td align="right" style="padding:0.75rem 0; color:var(--clr-mist);"><?php echo esc_html( $editor ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">Album Included</th>
                            <td align="right" style="padding:0.75rem 0; color:var(--clr-mist);"><?php echo $album_included ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr style="border-bottom:1px solid var(--clr-border);">
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">Video Included</th>
                            <td align="right" style="padding:0.75rem 0; color:var(--clr-mist);"><?php echo $video_included ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <th align="left" style="padding:0.75rem 0; font-weight:600; color:var(--clr-obsidian);">Status</th>
                            <td align="right" style="padding:0.75rem 0; text-transform:capitalize; color:var(--clr-gold); font-weight:600;"><?php echo esc_html( $status ); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Session & Location Taxonomies -->
                <?php if ( ! empty( $session_types ) || ! empty( $themes ) || ! empty( $locations ) ) : ?>
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Categorization</h3>
                    <div style="display:flex; flex-wrap:wrap; gap:0.5rem; padding-top:0.5rem;">
                        <?php if ( ! empty( $session_types ) ) : foreach ( $session_types as $t ) : ?>
                            <span style="background:var(--clr-cream); color:var(--clr-gold); padding:0.3rem 0.75rem; border-radius:var(--radius-sm); font-size:0.8rem; font-weight:500;"><?php echo esc_html( $t->name ); ?></span>
                        <?php endforeach; endif; ?>
                        
                        <?php if ( ! empty( $themes ) ) : foreach ( $themes as $t ) : ?>
                            <span style="background:var(--clr-cream); color:var(--clr-mist); padding:0.3rem 0.75rem; border-radius:var(--radius-sm); font-size:0.8rem; font-weight:500;"><?php echo esc_html( $t->name ); ?></span>
                        <?php endforeach; endif; ?>

                        <?php if ( ! empty( $locations ) ) : foreach ( $locations as $t ) : ?>
                            <span style="background:var(--clr-gold-pale); color:var(--clr-obsidian); padding:0.3rem 0.75rem; border-radius:var(--radius-sm); font-size:0.8rem; font-weight:500;">📍 <?php echo esc_html( $t->name ); ?></span>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Booking CTA -->
                <div class="sidebar-cta">
                    <div class="sidebar-cta__icon">💍</div>
                    <h3 class="sidebar-cta__heading">Plan Your Premium Documentation</h3>
                    <p class="sidebar-cta__desc">Let us capture your milestone with elite editorial precision. Inquire today for pricing catalog and date bookings.</p>
                    <?php echo $wa_link; ?>
                </div>

            </aside>
        </div>
    </div>

</article>

<?php get_footer(); ?>
