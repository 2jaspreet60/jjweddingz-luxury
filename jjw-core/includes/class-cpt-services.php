<?php
/**
 * class-cpt-services.php — Services Custom Post Type
 *
 * Registers the 'jjwz_service' CPT.
 *
 * @package JJW_Core
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_Services {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt(): void {
        register_post_type( 'jjwz_service', [
            'labels' => [
                'name'               => __( 'Services',          'jjw-core' ),
                'singular_name'      => __( 'Service',           'jjw-core' ),
                'add_new'            => __( 'Add New Service',   'jjw-core' ),
                'add_new_item'       => __( 'Add New Service',   'jjw-core' ),
                'edit_item'          => __( 'Edit Service',      'jjw-core' ),
                'new_item'           => __( 'New Service',       'jjw-core' ),
                'view_item'          => __( 'View Service',      'jjw-core' ),
                'search_items'       => __( 'Search Services',   'jjw-core' ),
                'not_found'          => __( 'No services found', 'jjw-core' ),
                'menu_name'          => __( 'Services',          'jjw-core' ),
            ],
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
            'menu_icon'          => 'dashicons-admin-tools',
            'menu_position'      => 27,
            'rewrite'            => [ 'slug' => 'services', 'with_front' => false ],
        ] );
    }
}
