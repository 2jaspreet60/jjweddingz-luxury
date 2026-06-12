<?php
/**
 * Template Name: Portfolio Grid
 *
 * page-portfolio.php — Editorial Portfolio Grid Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

// Elementor Canvas safeguard check
if ( function_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    get_footer();
    return;
}
?>

<main id="primary" class="site-main">

    <!-- ═══════════════════════════════
         HERO SECTION
         ═══════════════════════════════ -->
    <section class="section section--sm bg-cream" aria-label="Portfolio header">
        <div class="container text-center">
            <?php jjwz_breadcrumb(); ?>
            <span class="eyebrow"><?php esc_html_e( 'Our Gallery', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold"><?php esc_html_e( 'Visual Masterpieces', 'jjweddingz' ); ?></h1>
            <p class="lead text-center" style="margin-inline: auto; margin-top: var(--sp-md);">
                <?php esc_html_e( 'A curated selection of our finest stories, capturing timeless moments of love, maternity, and the delicate beauty of newborn life across Delhi NCR and Amritsar.', 'jjweddingz' ); ?>
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════
         FILTER & MASONRY GRID
         ═══════════════════════════════ -->
    <section class="section" aria-label="Portfolio gallery grid">
        <div class="container container--wide">
            
            <!-- Filters -->
            <div class="portfolio-filter" role="tablist" aria-label="Portfolio categories">
                <button class="filter-btn is-active" data-filter="all" role="tab" aria-selected="true" aria-controls="portfolio-grid">
                    <?php esc_html_e( 'All Works', 'jjweddingz' ); ?>
                </button>
                <button class="filter-btn" data-filter="luxury wedding" role="tab" aria-selected="false" aria-controls="portfolio-grid">
                    <?php esc_html_e( 'Wedding', 'jjweddingz' ); ?>
                </button>
                <button class="filter-btn" data-filter="pre-wedding" role="tab" aria-selected="false" aria-controls="portfolio-grid">
                    <?php esc_html_e( 'Pre-Wedding', 'jjweddingz' ); ?>
                </button>
                <button class="filter-btn" data-filter="maternity" role="tab" aria-selected="false" aria-controls="portfolio-grid">
                    <?php esc_html_e( 'Maternity', 'jjweddingz' ); ?>
                </button>
                <button class="filter-btn" data-filter="newborn" role="tab" aria-selected="false" aria-controls="portfolio-grid">
                    <?php esc_html_e( 'Newborn', 'jjweddingz' ); ?>
                </button>
                <button class="filter-btn" data-filter="baby shoot" role="tab" aria-selected="false" aria-controls="portfolio-grid">
                    <?php esc_html_e( 'Baby Shoot', 'jjweddingz' ); ?>
                </button>
                <button class="filter-btn" data-filter="cinematic film" role="tab" aria-selected="false" aria-controls="portfolio-grid">
                    <?php esc_html_e( 'Films', 'jjweddingz' ); ?>
                </button>
            </div>

            <!-- Grid -->
            <div id="portfolio-grid" role="tabpanel" aria-label="Portfolio grid items" data-anim="fade-in">
                <?php
                // Render 18 masonry grid cards (CPT query or fallback gradient placeholders)
                echo jjwz_portfolio_masonry_grid( 18, 'portfolio-masonry' );
                ?>
            </div>

            <!-- Lead Form CTA Section -->
            <div class="text-center" style="margin-top: var(--sp-4xl);" data-anim="fade-up">
                <h3 class="section-title" style="margin-bottom: var(--sp-md);"><?php esc_html_e( 'Inspired by Our Work?', 'jjweddingz' ); ?></h3>
                <p class="text-mist" style="margin-bottom: var(--sp-xl); max-width: 600px; margin-inline: auto;">
                    <?php esc_html_e( 'Let’s connect to discuss how we can bring your vision to life. Plan a consultation with our photographers in Delhi or Amritsar today.', 'jjweddingz' ); ?>
                </p>
                <div class="flex flex-center" style="gap: var(--sp-md);">
                    <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--primary">
                        <?php esc_html_e( 'Get in Touch', 'jjweddingz' ); ?>
                    </a>
                    <?php echo jjwz_wa_link( esc_html__( 'WhatsApp Chat', 'jjweddingz' ), 'btn btn--outline' ); ?>
                </div>
            </div>

        </div>
    </section>

</main>

<?php
get_footer();
