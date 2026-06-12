<?php
/**
 * sidebar.php — Primary Sidebar Template
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
    <aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Blog Sidebar', 'jjweddingz' ); ?>">
        <?php dynamic_sidebar( 'blog-sidebar' ); ?>
    </aside>
<?php endif; ?>
