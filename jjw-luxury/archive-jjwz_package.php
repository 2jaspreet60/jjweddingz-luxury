<?php
/**
 * archive-jjwz_package.php — Investment & Packages Archive Template
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
            <span class="eyebrow"><?php esc_html_e( 'Our Investment', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold"><?php esc_html_e( 'Luxury Photography Packages', 'jjweddingz' ); ?></h1>
            <p class="lead text-center" style="margin-inline: auto; margin-top: var(--sp-md);">
                <?php esc_html_e( 'Transparent, premium photography pricing designed to capture your most profound milestones with editorial perfection.', 'jjweddingz' ); ?>
            </p>
        </div>
    </section>

    <!-- Packages Grid Section -->
    <section class="section" aria-label="Packages list">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <div class="packages-grid grid-3" style="row-gap: 3.5rem; align-items: start;">
                    <?php
                    while ( have_posts() ) : the_post();
                        $post_id    = get_the_ID();
                        $price      = jjwz_get_option( 'package_price', '', $post_id );
                        $desc       = jjwz_get_option( 'package_description', '', $post_id );
                        $features   = jjwz_get_option( 'package_features', '', $post_id );
                        $album      = jjwz_get_option( 'package_album_included', false, $post_id );
                        $timeline   = jjwz_get_option( 'package_delivery_timeline', '', $post_id );
                        $featured   = jjwz_get_option( 'package_featured', false, $post_id );

                        // Split features list by newlines
                        $features_list = [];
                        if ( $features ) {
                            $features_list = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", "", $features ) ) ) );
                        }
                        
                        $card_class = $featured ? 'card package-card--featured' : 'card';
                        $wa_link    = jjwz_wa_link( 'Check Availability', 'btn btn--primary', 'pkg-wa-' . $post_id, 'Hi, I would like to inquire about availability for the ' . get_the_title() . ' package.' );
                        ?>
                        <article class="<?php echo esc_attr( $card_class ); ?>" id="package-<?php the_ID(); ?>" data-anim="fade-up" style="display: flex; flex-direction: column; height: 100%; border: 1.5px solid <?php echo $featured ? 'var(--clr-gold)' : 'var(--clr-border)'; ?>;">
                            
                            <?php if ( $featured ) : ?>
                            <div style="background: var(--clr-gold); color: var(--clr-warm-white); text-align: center; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; padding: 0.5rem 0;">
                                <?php esc_html_e( 'Most Popular Choice', 'jjweddingz' ); ?>
                            </div>
                            <?php endif; ?>

                            <div class="card__body" style="flex-grow: 1; display: flex; flex-direction: column; padding: var(--sp-2xl) var(--sp-xl);">
                                <h2 style="font-family: var(--font-display); font-size: 1.85rem; margin-bottom: 0.5rem; text-align: center; color: var(--clr-obsidian);"><?php the_title(); ?></h2>
                                
                                <?php if ( $price ) : ?>
                                <div style="font-family: var(--font-display); font-size: 2.5rem; color: var(--clr-gold); text-align: center; margin-bottom: 1.5rem; font-weight: 500;">
                                    <?php echo esc_html( $price ); ?>
                                </div>
                                <?php endif; ?>

                                <?php if ( $desc ) : ?>
                                <p style="font-size: 0.9rem; text-align: center; color: var(--clr-mist); margin-bottom: 2rem; line-height: 1.6;"><?php echo esc_html( $desc ); ?></p>
                                <?php endif; ?>

                                <div style="width: 100%; height: 1px; background: var(--clr-border); margin-bottom: 2rem;"></div>

                                <ul style="display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 2.5rem;">
                                    <?php foreach ( $features_list as $feat ) : ?>
                                    <li style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.9rem; color: var(--clr-mist);">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold)" stroke-width="2.5" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span><?php echo esc_html( $feat ); ?></span>
                                    </li>
                                    <?php endforeach; ?>

                                    <li style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.9rem; color: var(--clr-mist);">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold)" stroke-width="2.5" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span><strong><?php esc_html_e( 'Physical Album:', 'jjweddingz' ); ?></strong> <?php echo $album ? esc_html__( 'Included (Premium Leather-bound)', 'jjweddingz' ) : esc_html__( 'Available as Addon', 'jjweddingz' ); ?></span>
                                    </li>

                                    <?php if ( $timeline ) : ?>
                                    <li style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.9rem; color: var(--clr-mist);">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold)" stroke-width="2.5" style="flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span><strong><?php esc_html_e( 'Delivery Timeline:', 'jjweddingz' ); ?></strong> <?php echo esc_html( $timeline ); ?></span>
                                    </li>
                                    <?php endif; ?>
                                </ul>

                                <div style="margin-top: auto; text-align: center; width: 100%;">
                                    <?php echo $wa_link; ?>
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
                    <p class="lead" style="margin-inline:auto;"><?php esc_html_e( 'Our luxury photography investment options are currently being updated. Check back soon.', 'jjweddingz' ); ?></p>
                    <a href="<?php echo esc_url( home_url() ); ?>" class="btn btn--primary" style="margin-top:1.5rem;"><?php esc_html_e( 'Back to Home', 'jjweddingz' ); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
