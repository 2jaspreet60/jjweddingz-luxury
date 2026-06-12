<?php
/**
 * page.php — Default Page Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main id="primary" class="site-main">

    <?php
    while ( have_posts() ) :
        the_post();

        // Elementor Page Builder integration
        if ( function_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
            the_content();
        } else {
            // Default premium layout
            ?>
            <section class="section section--sm bg-cream" aria-label="Page header">
                <div class="container text-center">
                    <?php jjwz_breadcrumb(); ?>
                    <h1 class="display-title text-gold"><?php the_title(); ?></h1>
                </div>
            </section>

            <section class="section bg-warm-white" aria-label="Page content">
                <div class="container container--narrow">
                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>
            <?php
        }
    endwhile;
    ?>

</main>

<?php
get_footer();
