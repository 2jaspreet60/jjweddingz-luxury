<?php
/**
 * 404.php — 404 Error / Not Found Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main id="primary" class="site-main">

    <section class="section section--lg bg-cream flex-center" style="min-height: calc(100vh - var(--header-height) - 100px); text-align: center;" aria-label="Error 404 header">
        <div class="container container--narrow" data-anim="fade-in">
            <span class="eyebrow" style="font-size: var(--text-base);"><?php esc_html_e( 'Error 404', 'jjweddingz' ); ?></span>
            <h1 class="display-title text-gold" style="font-size: clamp(4rem, 15vw, 8rem); line-height: 1; margin-bottom: var(--sp-md);">
                <?php esc_html_e( 'Lost', 'jjweddingz' ); ?>
            </h1>
            <h2 class="section-title" style="margin-bottom: var(--sp-lg); font-size: var(--text-2xl);">
                <?php esc_html_e( 'The page you are looking for does not exist.', 'jjweddingz' ); ?>
            </h2>
            <p class="text-mist" style="margin-bottom: var(--sp-2xl); max-width: 50ch; margin-inline: auto;">
                <?php esc_html_e( 'It might have been moved, deleted, or the address might have been typed incorrectly. Let’s get you back on track.', 'jjweddingz' ); ?>
            </p>
            <div class="flex flex-center" style="gap: var(--sp-md); flex-wrap: wrap;">
                <a href="<?php echo esc_url( home_url() ); ?>" class="btn btn--primary">
                    <?php esc_html_e( 'Back to Homepage', 'jjweddingz' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>" class="btn btn--outline">
                    <?php esc_html_e( 'Explore Portfolio', 'jjweddingz' ); ?>
                </a>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
