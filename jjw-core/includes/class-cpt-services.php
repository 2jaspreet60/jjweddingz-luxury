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
        add_filter( 'manage_jjwz_service_posts_columns', [ $this, 'set_admin_columns' ] );
        add_action( 'manage_jjwz_service_posts_custom_column', [ $this, 'fill_admin_column' ], 10, 2 );
        add_filter( 'manage_edit-jjwz_service_sortable_columns', [ $this, 'set_sortable_columns' ] );
        add_action( 'pre_get_posts', [ $this, 'handle_column_sorting' ] );
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

    public function set_admin_columns( array $columns ): array {
        $new_columns = [];
        foreach ( $columns as $key => $title ) {
            $new_columns[ $key ] = $title;
            if ( $key === 'title' ) {
                $new_columns['svc_display_order']    = __( 'Display Order', 'jjw-core' );
                $new_columns['svc_featured']         = __( 'Featured', 'jjw-core' );
                $new_columns['svc_show_on_homepage'] = __( 'Show on Homepage', 'jjw-core' );
                $new_columns['svc_category_group']   = __( 'Category Group', 'jjw-core' );
            }
        }
        return $new_columns;
    }

    public function fill_admin_column( string $column, int $post_id ): void {
        switch ( $column ) {
            case 'svc_display_order':
                $order = get_post_meta( $post_id, 'svc_display_order', true );
                echo esc_html( ( $order !== '' && $order !== false ) ? $order : '—' );
                break;
            case 'svc_featured':
                $featured = get_post_meta( $post_id, 'svc_featured', true );
                if ( $featured === '1' ) {
                    echo '<span style="color:#2271b1; font-weight:bold;">★ Yes</span>';
                } else {
                    echo '<span style="color:#ccc;">☆ No</span>';
                }
                break;
            case 'svc_show_on_homepage':
                $show = get_post_meta( $post_id, 'svc_show_on_homepage', true );
                if ( $show === '1' ) {
                    echo '<span style="color:#00a32a; font-weight:bold;">✔ Yes</span>';
                } else {
                    echo '<span style="color:#ccc;">✘ No</span>';
                }
                break;
            case 'svc_category_group':
                $category = get_post_meta( $post_id, 'svc_category_group', true );
                echo esc_html( $category ? ucwords( $category ) : '—' );
                break;
        }
    }

    public function set_sortable_columns( array $columns ): array {
        $columns['svc_display_order']    = 'svc_display_order';
        $columns['svc_featured']         = 'svc_featured';
        $columns['svc_show_on_homepage'] = 'svc_show_on_homepage';
        $columns['svc_category_group']   = 'svc_category_group';
        return $columns;
    }

    public function handle_column_sorting( WP_Query $query ): void {
        if ( ! is_admin() || ! $query->is_main_query() ) { return; }
        if ( $query->get( 'post_type' ) !== 'jjwz_service' ) { return; }

        $orderby = $query->get( 'orderby' );

        switch ( $orderby ) {
            case 'svc_display_order':
                $query->set( 'meta_query', [
                    'relation' => 'OR',
                    'exists_clause' => [
                        'key'     => 'svc_display_order',
                        'compare' => 'EXISTS',
                        'type'    => 'NUMERIC',
                    ],
                    'not_exists_clause' => [
                        'key'     => 'svc_display_order',
                        'compare' => 'NOT EXISTS',
                    ]
                ] );
                $query->set( 'orderby', 'exists_clause' );
                break;
            case 'svc_featured':
                $query->set( 'meta_query', [
                    'relation' => 'OR',
                    'exists_clause' => [
                        'key'     => 'svc_featured',
                        'compare' => 'EXISTS',
                    ],
                    'not_exists_clause' => [
                        'key'     => 'svc_featured',
                        'compare' => 'NOT EXISTS',
                    ]
                ] );
                $query->set( 'orderby', 'exists_clause' );
                break;
            case 'svc_show_on_homepage':
                $query->set( 'meta_query', [
                    'relation' => 'OR',
                    'exists_clause' => [
                        'key'     => 'svc_show_on_homepage',
                        'compare' => 'EXISTS',
                    ],
                    'not_exists_clause' => [
                        'key'     => 'svc_show_on_homepage',
                        'compare' => 'NOT EXISTS',
                    ]
                ] );
                $query->set( 'orderby', 'exists_clause' );
                break;
            case 'svc_category_group':
                $query->set( 'meta_query', [
                    'relation' => 'OR',
                    'exists_clause' => [
                        'key'     => 'svc_category_group',
                        'compare' => 'EXISTS',
                    ],
                    'not_exists_clause' => [
                        'key'     => 'svc_category_group',
                        'compare' => 'NOT EXISTS',
                    ]
                ] );
                $query->set( 'orderby', 'exists_clause' );
                break;
        }
    }
}
