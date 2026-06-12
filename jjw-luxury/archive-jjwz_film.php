<?php
/**
 * archive-jjwz_film.php — Cinematic Films Archive Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

get_header();

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

<main id="primary" class="site-main">

    <!-- Page Header -->
    <section class="archive-header section section--sm bg-cream" aria-label="Archive title">
        <div class="container text-center">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow"><?php esc_html_e( 'Cinematography', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold"><?php esc_html_e( 'Cinematic Wedding & Milestone Films', 'jjweddingz' ); ?></h1>
            <p class="lead text-center" style="margin-inline: auto; margin-top: var(--sp-md);">
                <?php esc_html_e( 'Sweeping visual narratives captured on cinema-grade systems, optimized for editorial elegance and authentic emotion.', 'jjweddingz' ); ?>
            </p>
        </div>
    </section>

    <!-- Films Grid Section -->
    <section class="section" aria-label="Films grid">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <div class="films-grid grid-3" style="row-gap: 3.5rem;">
                    <?php
                    while ( have_posts() ) : the_post();
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
                        <article class="film-card" id="film-<?php the_ID(); ?>" data-anim="fade-up">
                            <div class="film-card__video jjwz-video-wrap jjwz-video-wrap--16-9" 
                                 data-video-type="<?php echo esc_attr( $video_type ); ?>" 
                                 data-video-id="<?php echo esc_attr( $video_id ); ?>"
                                 <?php if ( $thumb ) : ?>
                                     style="background-image: url('<?php echo esc_url( $thumb ); ?>');"
                                 <?php endif; ?>>
                            </div>
                            <div class="film-card__body" style="padding-top:1.5rem;">
                                <h2 class="film-card__title" style="font-size:1.45rem; font-family:var(--font-display); margin-bottom:0.5rem;"><?php the_title(); ?></h2>
                                <?php if ( $desc ) : ?>
                                    <p class="film-card__desc" style="font-size:0.9rem; color:var(--clr-mist); line-height:1.6;"><?php echo esc_html( $desc ); ?></p>
                                <?php endif; ?>
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
                    <p class="lead" style="margin-inline:auto;"><?php esc_html_e( 'Our cinematic film reels are currently being curated. Check back soon.', 'jjweddingz' ); ?></p>
                    <a href="<?php echo esc_url( home_url() ); ?>" class="btn btn--primary" style="margin-top:1.5rem;"><?php esc_html_e( 'Back to Home', 'jjweddingz' ); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
