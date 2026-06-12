<?php
/**
 * class-cpt-portfolio.php — Portfolio Custom Post Type & Taxonomies
 *
 * Registers the 'jjwz_portfolio' CPT and all associated taxonomies:
 * - jjwz_portfolio_cat (Categories)
 * - jjwz_session_type (Session Types)
 * - jjwz_theme_type (Themes)
 * - jjwz_location_tax (Locations)
 *
 * @package JJW_Core
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_Portfolio {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ], 5 );
        add_action( 'init', [ $this, 'register_taxonomies' ], 5 );
    }

    /**
     * Register Portfolio CPT.
     */
    public function register_cpt(): void {
        register_post_type( 'jjwz_portfolio', [
            'labels' => [
                'name'               => __( 'Portfolios',       'jjw-core' ),
                'singular_name'      => __( 'Portfolio',        'jjw-core' ),
                'add_new'            => __( 'Add New Story',    'jjw-core' ),
                'add_new_item'       => __( 'Add New Portfolio Story', 'jjw-core' ),
                'edit_item'          => __( 'Edit Story',       'jjw-core' ),
                'new_item'           => __( 'New Story',        'jjw-core' ),
                'view_item'          => __( 'View Story',       'jjw-core' ),
                'search_items'       => __( 'Search Stories',   'jjw-core' ),
                'not_found'          => __( 'No stories found', 'jjw-core' ),
                'menu_name'          => __( 'Portfolios',       'jjw-core' ),
            ],
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
            'menu_icon'          => 'dashicons-format-image',
            'menu_position'      => 26,
            'rewrite'            => [ 'slug' => 'portfolio', 'with_front' => false ],
        ] );
    }

    /**
     * Register Portfolio Taxonomies.
     */
    public function register_taxonomies(): void {
        // 1. Categories (Linked to Portfolio and Films CPTs)
        register_taxonomy( 'jjwz_portfolio_cat', [ 'jjwz_portfolio', 'jjwz_film' ], [
            'labels' => [
                'name'              => __( 'Categories', 'jjw-core' ),
                'singular_name'     => __( 'Category',   'jjw-core' ),
                'search_items'      => __( 'Search Categories',    'jjw-core' ),
                'all_items'         => __( 'All Categories',       'jjw-core' ),
                'edit_item'         => __( 'Edit Category',        'jjw-core' ),
                'update_item'       => __( 'Update Category',      'jjw-core' ),
                'add_new_item'      => __( 'Add New Category',     'jjw-core' ),
                'menu_name'         => __( 'Categories',           'jjw-core' ),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'portfolio-category' ],
        ] );

        // 2. Session Types (Linked to Services, Portfolio, Locations, and Galleries)
        register_taxonomy( 'jjwz_session_type', [ 'jjwz_service', 'jjwz_portfolio', 'jjwz_location', 'jjwz_gallery' ], [
            'labels' => [
                'name'              => __( 'Session Types', 'jjw-core' ),
                'singular_name'     => __( 'Session Type',   'jjw-core' ),
                'search_items'      => __( 'Search Session Types', 'jjw-core' ),
                'all_items'         => __( 'All Session Types',    'jjw-core' ),
                'edit_item'         => __( 'Edit Session Type',    'jjw-core' ),
                'update_item'       => __( 'Update Session Type',  'jjw-core' ),
                'add_new_item'      => __( 'Add New Session Type', 'jjw-core' ),
                'menu_name'         => __( 'Session Types',        'jjw-core' ),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      =>       true,
            'rewrite'           => [ 'slug' => 'session-type' ],
        ] );

        // 3. Themes
        register_taxonomy( 'jjwz_theme_type', [ 'jjwz_portfolio' ], [
            'labels' => [
                'name'              => __( 'Themes', 'jjw-core' ),
                'singular_name'     => __( 'Theme',   'jjw-core' ),
                'search_items'      => __( 'Search Themes',        'jjw-core' ),
                'all_items'         => __( 'All Themes',           'jjw-core' ),
                'edit_item'         => __( 'Edit Theme',           'jjw-core' ),
                'update_item'       => __( 'Update Theme',         'jjw-core' ),
                'add_new_item'      => __( 'Add New Theme',        'jjw-core' ),
                'menu_name'         => __( 'Themes',               'jjw-core' ),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'portfolio-theme' ],
        ] );

        // 4. Locations (Linked to Portfolio, Services, Blog Posts, Galleries, and Locations CPT)
        register_taxonomy( 'jjwz_location_tax', [ 'jjwz_portfolio', 'jjwz_service', 'post', 'jjwz_gallery', 'jjwz_location' ], [
            'labels' => [
                'name'              => __( 'Locations (Taxonomy)', 'jjw-core' ),
                'singular_name'     => __( 'Location (Taxonomy)',   'jjw-core' ),
                'search_items'      => __( 'Search Locations',     'jjw-core' ),
                'all_items'         => __( 'All Locations',        'jjw-core' ),
                'edit_item'         => __( 'Edit Location',        'jjw-core' ),
                'update_item'       => __( 'Update Location',      'jjw-core' ),
                'add_new_item'      => __( 'Add New Location',     'jjw-core' ),
                'menu_name'         => __( 'Locations',            'jjw-core' ),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'portfolio-location' ],
        ] );

        // 5. Service Categories (Linked to Services, Portfolios, Galleries, FAQs, and Blog Posts)
        register_taxonomy( 'jjwz_service_cat', [ 'jjwz_service', 'jjwz_portfolio', 'jjwz_gallery', 'jjwz_faq', 'post' ], [
            'labels' => [
                'name'              => __( 'Service Categories', 'jjw-core' ),
                'singular_name'     => __( 'Service Category',   'jjw-core' ),
                'search_items'      => __( 'Search Service Categories', 'jjw-core' ),
                'all_items'         => __( 'All Service Categories',    'jjw-core' ),
                'edit_item'         => __( 'Edit Service Category',    'jjw-core' ),
                'update_item'       => __( 'Update Service Category',  'jjw-core' ),
                'add_new_item'      => __( 'Add New Service Category', 'jjw-core' ),
                'menu_name'         => __( 'Service Categories',        'jjw-core' ),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'service-category' ],
        ] );
    }

    /**
     * Seeds initial terms for taxonomies. Called on activation.
     */
    public function seed_terms(): void {
        $taxonomies_data = [
            'jjwz_portfolio_cat' => [
                'Wedding', 'Pre Wedding', 'Maternity', 'Newborn', 'Baby', 
                'Cake Smash', 'Birthday', 'Anniversary', 'Family', 'Films'
            ],
            'jjwz_session_type' => [
                'Studio', 'Outdoor', 'Lifestyle', 'Fine Art', 'Luxury', 'Traditional'
            ],
            'jjwz_theme_type' => [
                'Luxury', 'Modern', 'Fine Art', 'Classic', 'Minimal', 'Outdoor'
            ],
            'jjwz_location_tax' => [
                'Amritsar', 'Delhi', 'Ludhiana', 'Jalandhar', 'Mohali', 'Chandigarh', 'Patiala', 'Bathinda'
            ],
            'jjwz_service_cat' => [
                'Wedding', 'Pre Wedding', 'Maternity', 'Newborn', 'Baby', 
                'Cake Smash', 'Birthday', 'Anniversary', 'Family', 'Films'
            ],
        ];

        foreach ( $taxonomies_data as $taxonomy => $terms ) {
            foreach ( $terms as $term ) {
                if ( ! term_exists( $term, $taxonomy ) ) {
                    wp_insert_term( $term, $taxonomy, [ 'slug' => sanitize_title( $term ) ] );
                }
            }
        }
    }
}
