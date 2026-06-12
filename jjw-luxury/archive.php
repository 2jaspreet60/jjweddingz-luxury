<?php
/**
 * archive.php — Blog Archive / Listing Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main id="primary" class="site-main">

    <!-- ═══════════════════════════════
         ARCHIVE HERO
         ═══════════════════════════════ -->
    <header class="section section--sm bg-cream" aria-label="Archive header">
        <div class="container text-center">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow"><?php esc_html_e( 'Journal', 'jjweddingz' ); ?></span>
            <?php
            the_archive_title( '<h1 class="display-title text-gold">', '</h1>' );
            the_archive_description( '<div class="archive-description text-mist" style="margin-top: var(--sp-md); max-width: 600px; margin-inline: auto;">', '</div>' );
            ?>
        </div>
    </header>

    <!-- ═══════════════════════════════
         POSTS GRID
         ═══════════════════════════════ -->
    <section class="section" aria-label="Archive articles grid">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                
                <div class="blog-grid" data-anim="fade-in">
                    <?php while ( have_posts() ) : the_post();
                        $thumb      = get_the_post_thumbnail_url( null, 'jjwz-blog-card' );
                        $cats       = get_the_category();
                        $cat_name   = $cats ? $cats[0]->name : '';
                        $word_count = str_word_count( get_the_content() );
                        $read_time  = max( 1, ceil( $word_count / 200 ) );
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
                        <div class="blog-card__media">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php if ( $thumb ) : ?>
                                    <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="800" height="480">
                                <?php else : ?>
                                    <div class="ratio-16-9 bg-cream flex-center" style="background: var(--clr-border);">
                                        <span class="text-gold" style="font-family: var(--font-display); font-size: var(--text-2xl);">JJ</span>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="blog-card__body">
                            <?php if ( $cat_name ) : ?>
                                <span class="blog-card__cat"><?php echo esc_html( $cat_name ); ?></span>
                            <?php endif; ?>
                            
                            <h2 class="blog-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <p class="blog-card__excerpt"><?php echo wp_strip_all_tags( get_the_excerpt() ); ?></p>
                            
                            <div class="flex-between" style="margin-top: auto; border-top: 1px solid var(--clr-border); padding-top: var(--sp-md);">
                                <span class="text-mist" style="font-size: var(--text-xs); font-weight: 500;">
                                    <?php echo get_the_date( 'M j, Y' ); ?> &bull; <?php echo $read_time; ?> min read
                                </span>
                                <a href="<?php the_permalink(); ?>" class="blog-card__link" aria-label="<?php esc_attr_e( 'Read post', 'jjweddingz' ); ?>">
                                    <?php esc_html_e( 'Read', 'jjweddingz' ); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrap" style="margin-top: var(--sp-3xl); display: flex; justify-content: center;">
                    <?php
                    the_posts_pagination( [
                        'mid_size'  => 2,
                        'prev_text' => __( '← Prev', 'jjweddingz' ),
                        'next_text' => __( 'Next →', 'jjweddingz' ),
                    ] );
                    ?>
                </div>

            <?php else : ?>
                <div class="text-center" style="padding-block: var(--sp-3xl);">
                    <h2 class="section-title"><?php esc_html_e( 'No Articles Found', 'jjweddingz' ); ?></h2>
                    <p class="text-mist" style="margin-top: var(--sp-md);"><?php esc_html_e( 'We haven’t published any journals in this category yet. Check back soon.', 'jjweddingz' ); ?></p>
                    <a href="<?php echo esc_url( home_url() ); ?>" class="btn btn--primary" style="margin-top: var(--sp-xl);">
                        <?php esc_html_e( 'Return Home', 'jjweddingz' ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php
get_footer();
