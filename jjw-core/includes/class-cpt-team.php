<?php
/**
 * class-cpt-team.php — Team Custom Post Type
 *
 * Registers the 'jjwz_team' CPT.
 *
 * @package JJW_Core
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_Team {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
    }

    public function register_cpt(): void {
        register_post_type( 'jjwz_team', [
            'labels' => [
                'name'               => __( 'Team Members',          'jjw-core' ),
                'singular_name'      => __( 'Team Member',           'jjw-core' ),
                'add_new'            => __( 'Add New Member',        'jjw-core' ),
                'add_new_item'       => __( 'Add New Team Member',   'jjw-core' ),
                'edit_item'          => __( 'Edit Member',           'jjw-core' ),
                'new_item'           => __( 'New Member',            'jjw-core' ),
                'view_item'          => __( 'View Member',           'jjw-core' ),
                'search_items'       => __( 'Search Members',        'jjw-core' ),
                'not_found'          => __( 'No members found',      'jjw-core' ),
                'menu_name'          => __( 'Team',                  'jjw-core' ),
            ],
            'public'             => true,
            'has_archive'        => false,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
            'menu_icon'          => 'dashicons-groups',
            'menu_position'      => 31,
            'rewrite'            => [ 'slug' => 'team', 'with_front' => false ],
        ] );
    }
}
