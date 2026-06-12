<?php
/**
 * class-cpt-testimonials.php — Testimonials Custom Post Type
 *
 * Registers the 'jjwz_testimonial' CPT.
 *
 * @package JJW_Core
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_Testimonials {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt(): void {
        register_post_type( 'jjwz_testimonial', [
            'labels' => [
                'name'               => __( 'Testimonials',          'jjw-core' ),
                'singular_name'      => __( 'Testimonial',           'jjw-core' ),
                'add_new'            => __( 'Add New Testimonial',   'jjw-core' ),
                'add_new_item'       => __( 'Add New Testimonial',   'jjw-core' ),
                'edit_item'          => __( 'Edit Testimonial',      'jjw-core' ),
                'new_item'           => __( 'New Testimonial',       'jjw-core' ),
                'view_item'          => __( 'View Testimonial',      'jjw-core' ),
                'search_items'       => __( 'Search Testimonials',   'jjw-core' ),
                'not_found'          => __( 'No testimonials found', 'jjw-core' ),
                'menu_name'          => __( 'Testimonials',          'jjw-core' ),
            ],
            'public'             => true,
            'has_archive'        => false,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
            'menu_icon'          => 'dashicons-testimonial',
            'menu_position'      => 30,
            'rewrite'            => [ 'slug' => 'testimonials', 'with_front' => false ],
        ] );
    }
}
