<?php
/**
 * archive-jjwz_location.php — CPT Location Hub Archive Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- Page Header -->
    <section class="archive-header section section--sm bg-cream" aria-label="Archive title">
        <div class="container text-center">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow"><?php esc_html_e( '📍 Where to Find Us', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold"><?php esc_html_e( 'Our Premium Studios', 'jjweddingz' ); ?></h1>
            <p class="lead text-center" style="margin-inline: auto; margin-top: var(--sp-md); max-width: 700px;">
                <?php esc_html_e( 'Experience editorial luxury wedding and portrait photography at our physical branches. Schedule an appointment to view bespoke albums.', 'jjweddingz' ); ?>
            </p>
        </div>
    </section>

    <!-- Locations Grid Section -->
    <section class="section" aria-label="Locations list">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <div class="locations-grid grid-3" style="row-gap: 3.5rem; align-items: start; display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                    <?php
                    while ( have_posts() ) : the_post();
                        $post_id    = get_the_ID();
                        $hero_img   = jjwz_get_option( 'location_hero_image', '', $post_id );
                        $desc       = jjwz_get_option( 'location_description', '', $post_id );
                        if ( empty( $desc ) ) {
                            $desc = wp_trim_words( get_the_excerpt(), 20 );
                        }
                        
                        $hero_img_url = is_array( $hero_img ) ? $hero_img['url'] : ( is_numeric( $hero_img ) ? wp_get_attachment_image_url( $hero_img, 'large' ) : $hero_img );
                        if ( ! $hero_img_url ) {
                            $hero_img_url = jjwz_get_option( 'jjw_default_placeholder_portfolio' );
                        }
                        if ( ! $hero_img_url ) {
                            $hero_img_url = get_template_directory_uri() . '/assets/images/placeholder-category-wedding.png';
                        }
                        
                        $wa_link = jjwz_wa_link( 'Book Studio Session', 'btn btn--primary', 'loc-wa-' . $post_id, 'Hi, I would like to book a session at your ' . get_the_title() . ' studio.' );
                        ?>
                        <article class="card location-card" id="location-<?php the_ID(); ?>" data-anim="fade-up" style="display: flex; flex-direction: column; height: 100%; background: var(--clr-white); border: 1px solid var(--clr-border); border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-sm);">
                            <div class="card__media" style="aspect-ratio: 16/10; overflow: hidden; position: relative;">
                                <?php if ( $hero_img_url ) : ?>
                                    <img src="<?php echo esc_url( $hero_img_url ); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;">
                                <?php else : ?>
                                    <div style="width: 100%; height: 100%; background: var(--clr-border);"></div>
                                <?php endif; ?>
                            </div>

                            <div class="card__body" style="flex-grow: 1; display: flex; flex-direction: column; padding: 2rem;">
                                <h2 style="font-family: var(--font-display); font-size: 1.6rem; margin-bottom: 0.75rem; color: var(--clr-obsidian);"><?php the_title(); ?></h2>
                                
                                <div style="font-size: 0.9rem; color: var(--clr-mist); margin-bottom: 2rem; line-height: 1.6; flex-grow: 1;">
                                    <?php echo wp_kses_post( wp_strip_all_tags( $desc ) ); ?>
                                </div>

                                <div style="display: flex; gap: 0.75rem; margin-top: auto;">
                                    <a href="<?php the_permalink(); ?>" class="btn btn--outline" style="flex: 1; justify-content: center; text-align: center;">View Studio Hub</a>
                                    <div style="flex: 1;">
                                        <?php echo $wa_link; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php
                    endwhile;
                    ?>
                </div>

                <!-- Pagination -->
                <div class="archive-pagination" style="margin-top: 4.5rem; text-align: center;">
                    <?php
                    echo paginate_links( [
                        'prev_text' => '<span class="pagination-arrow">← Prev</span>',
                        'next_text' => '<span class="pagination-arrow">Next →</span>',
                    ] );
                    ?>
                </div>

            <?php else : ?>
                <div class="text-center" style="padding: 4rem 2rem;">
                    <p class="lead" style="margin-inline:auto;"><?php esc_html_e( 'Our dynamic photography branch hubs are currently being updated. Check back soon.', 'jjweddingz' ); ?></p>
                    <a href="<?php echo esc_url( home_url() ); ?>" class="btn btn--primary" style="margin-top:1.5rem;"><?php esc_html_e( 'Back to Home', 'jjweddingz' ); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
