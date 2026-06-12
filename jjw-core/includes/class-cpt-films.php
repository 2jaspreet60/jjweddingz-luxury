<?php
/**
 * class-cpt-films.php — Films Custom Post Type
 *
 * Registers the 'jjwz_film' CPT.
 *
 * @package JJW_Core
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_Films {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt(): void {
        register_post_type( 'jjwz_film', [
            'labels' => [
                'name'               => __( 'Films',             'jjw-core' ),
                'singular_name'      => __( 'Film',              'jjw-core' ),
                'add_new'            => __( 'Add New Film',      'jjw-core' ),
                'add_new_item'       => __( 'Add New Film Story', 'jjw-core' ),
                'edit_item'          => __( 'Edit Film',         'jjw-core' ),
                'new_item'           => __( 'New Film',          'jjw-core' ),
                'view_item'          => __( 'View Film',         'jjw-core' ),
                'search_items'       => __( 'Search Films',      'jjw-core' ),
                'not_found'          => __( 'No films found',    'jjw-core' ),
                'menu_name'          => __( 'Films',             'jjw-core' ),
            ],
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
            'menu_icon'          => 'dashicons-video-alt3',
            'menu_position'      => 28,
            'rewrite'            => [ 'slug' => 'films', 'with_front' => false ],
        ] );
    }
}
