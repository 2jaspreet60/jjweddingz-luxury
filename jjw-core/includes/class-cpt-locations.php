<?php
/**
 * class-cpt-locations.php — Locations Custom Post Type
 *
 * Registers the 'jjwz_location' CPT.
 *
 * @package JJW_Core
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_Locations {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt(): void {
        register_post_type( 'jjwz_location', [
            'labels' => [
                'name'               => __( 'Locations (CPT)',   'jjw-core' ),
                'singular_name'      => __( 'Location (CPT)',    'jjw-core' ),
                'add_new'            => __( 'Add New Location',  'jjw-core' ),
                'add_new_item'       => __( 'Add New Location',  'jjw-core' ),
                'edit_item'          => __( 'Edit Location',     'jjw-core' ),
                'new_item'           => __( 'New Location',      'jjw-core' ),
                'view_item'          => __( 'View Location',     'jjw-core' ),
                'search_items'       => __( 'Search Locations',  'jjw-core' ),
                'not_found'          => __( 'No locations found','jjw-core' ),
                'menu_name'          => __( 'Locations (CPT)',   'jjw-core' ),
            ],
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
            'menu_icon'          => 'dashicons-location',
            'menu_position'      => 32,
            'rewrite'            => [ 'slug' => 'locations', 'with_front' => false ],
        ] );
    }
}
