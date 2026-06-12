<?php
/**
 * search.php — Search Results Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main id="primary" class="site-main">

    <!-- ═══════════════════════════════
         SEARCH HERO
         ═══════════════════════════════ -->
    <header class="section section--sm bg-cream" aria-label="Search results header">
        <div class="container text-center">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow"><?php esc_html_e( 'Search Results', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold" style="font-size: var(--text-4xl);">
                <?php
                /* translators: %s: search query. */
                printf( esc_html__( 'Search Results for: %s', 'jjweddingz' ), '<span>' . get_search_query() . '</span>' );
                ?>
            </h1>
        </div>
    </header>

    <!-- ═══════════════════════════════
         RESULTS LOOP
         ═══════════════════════════════ -->
    <section class="section" aria-label="Search articles grid">
        <div class="container">
            <?php if ( have_posts() ) : ?>

                <div class="blog-grid" data-anim="fade-in">
                    <?php while ( have_posts() ) : the_post();
                        $thumb      = get_the_post_thumbnail_url( null, 'jjwz-blog-card' );
                        $cats       = get_the_category();
                        $cat_name   = $cats ? $cats[0]->name : esc_html__( 'Page', 'jjweddingz' );
                        $word_count = str_word_count( get_the_content() );
                        $read_time  = max( 1, ceil( $word_count / 200 ) );
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
                        <div class="blog-card__media">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php if ( $thumb ) : ?>
                                    <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="800" height="480">
                                <?php else : ?>
                                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/placeholder-blog.png' ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="800" height="480" style="width:100%; height:100%; object-fit:cover;">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="blog-card__body">
                            <span class="blog-card__cat"><?php echo esc_html( $cat_name ); ?></span>
                            
                            <h2 class="blog-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <p class="blog-card__excerpt"><?php echo wp_strip_all_tags( get_the_excerpt() ); ?></p>
                            
                            <div class="flex-between" style="margin-top: auto; border-top: 1px solid var(--clr-border); padding-top: var(--sp-md);">
                                <span class="text-mist" style="font-size: var(--text-xs); font-weight: 500;">
                                    <?php echo get_the_date( 'M j, Y' ); ?>
                                </span>
                                <a href="<?php the_permalink(); ?>" class="blog-card__link" aria-label="<?php esc_attr_e( 'View', 'jjweddingz' ); ?>">
                                    <?php esc_html_e( 'View', 'jjweddingz' ); ?>
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
                <div class="text-center" style="padding-block: var(--sp-3xl); max-width: 600px; margin-inline: auto;">
                    <h2 class="section-title" style="margin-bottom: var(--sp-md);"><?php esc_html_e( 'No Results Found', 'jjweddingz' ); ?></h2>
                    <p class="text-mist" style="margin-bottom: var(--sp-2xl);"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'jjweddingz' ); ?></p>
                    
                    <!-- Search form -->
                    <form role="search" method="get" class="search-form flex" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="gap: var(--sp-sm); width: 100%;">
                        <label style="flex-grow: 1;">
                            <span class="screen-reader-text sr-only"><?php esc_html_e( 'Search for:', 'jjweddingz' ); ?></span>
                            <input type="search" class="search-field form-control" placeholder="<?php esc_attr_e( 'Search keyword…', 'jjweddingz' ); ?>" value="<?php echo get_search_query(); ?>" name="s" style="width: 100%;">
                        </label>
                        <button type="submit" class="btn btn--primary"><?php esc_html_e( 'Search', 'jjweddingz' ); ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php
get_footer();
