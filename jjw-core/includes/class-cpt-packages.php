<?php
/**
 * class-cpt-packages.php — Packages Custom Post Type
 *
 * Registers the 'jjwz_package' CPT.
 *
 * @package JJW_Core
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_Packages {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt(): void {
        register_post_type( 'jjwz_package', [
            'labels' => [
                'name'               => __( 'Packages',          'jjw-core' ),
                'singular_name'      => __( 'Package',           'jjw-core' ),
                'add_new'            => __( 'Add New Package',   'jjw-core' ),
                'add_new_item'       => __( 'Add New Package',   'jjw-core' ),
                'edit_item'          => __( 'Edit Package',      'jjw-core' ),
                'new_item'           => __( 'New Package',       'jjw-core' ),
                'view_item'          => __( 'View Package',      'jjw-core' ),
                'search_items'       => __( 'Search Packages',   'jjw-core' ),
                'not_found'          => __( 'No packages found', 'jjw-core' ),
                'menu_name'          => __( 'Packages',          'jjw-core' ),
            ],
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'page-attributes' ],
            'menu_icon'          => 'dashicons-rewards',
            'menu_position'      => 29,
            'rewrite'            => [ 'slug' => 'packages', 'with_front' => false ],
        ] );
    }
}
