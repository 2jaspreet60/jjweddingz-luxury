<?php
/**
 * class-seo-schema.php — Structured Data (JSON-LD) Generator
 *
 * Automatically injects highly detailed Schema.org JSON-LD markup:
 * - BreadcrumbList
 * - Article / BlogPosting (for single blog posts)
 * - FAQPage (for FAQ custom posts or templates using FAQs)
 * - LocalBusiness (with Delhi and Amritsar branch locations)
 * - Service + City LocalBusiness (for campaign landing pages)
 *
 * @package JJWeddingZ_Core
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_SEO_Schema {

    /**
     * Constructor registers hooks.
     */
    public function __construct() {
        add_action( 'wp_head', [ $this, 'inject_schemas' ], 20 );
    }

    /**
     * Inject schemas based on query parameters.
     */
    public function inject_schemas(): void {
        $this->render_breadcrumb_schema();
        $this->render_article_schema();
        $this->render_faq_schema();
        $this->render_dual_branch_schema();
        $this->render_service_location_schema();
    }

    /**
     * 1. BreadcrumbList Schema
     */
    private function render_breadcrumb_schema(): void {
        if ( is_front_page() ) { return; }

        $items = [];
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => esc_html__( 'Home', 'jjweddingz' ),
            'item'     => home_url(),
        ];

        $position = 2;

        $service_slug  = get_query_var( 'jjwz_service_slug' );
        $location_slug = get_query_var( 'jjwz_location_slug' );
        $is_landing_page = ( $service_slug && $location_slug );

        if ( $is_landing_page ) {
            $service_name = ucwords( str_replace( '-', ' ', $service_slug ) );
            $location_name = ucwords( str_replace( '-', ' ', $location_slug ) );
            
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $service_name,
                'item'     => home_url( '/services' ),
            ];
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $service_name . ' in ' . $location_name,
                'item'     => home_url( '/' . $service_slug . '-photographer-in-' . $location_slug ),
            ];
        } elseif ( is_singular() ) {
            $post_type = get_post_type();
            if ( $post_type === 'post' ) {
                $cats = get_the_category();
                if ( $cats ) {
                    $items[] = [
                        '@type'    => 'ListItem',
                        'position' => $position++,
                        'name'     => $cats[0]->name,
                        'item'     => get_category_link( $cats[0]->term_id ),
                    ];
                }
            }
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            ];
        } elseif ( is_category() ) {
            $cat = get_queried_object();
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $cat->name,
                'item'     => get_category_link( $cat->term_id ),
            ];
        } elseif ( is_page() ) {
            $post = get_queried_object();
            if ( $post && $post->post_parent ) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => get_the_title( $post->post_parent ),
                    'item'     => get_permalink( $post->post_parent ),
                ];
            }
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            ];
        }

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];

        echo "\n" . '<!-- JJ WeddingZ Breadcrumb Schema -->' . "\n";
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

    /**
     * 2. Article (BlogPosting) Schema
     */
    private function render_article_schema(): void {
        if ( ! is_singular( 'post' ) ) { return; }

        global $post;
        $author_id = $post->post_author;
        $thumb     = get_the_post_thumbnail_url( $post->ID, 'jjwz-blog-hero' ) ?: get_the_post_thumbnail_url( $post->ID, 'full' );

        $schema = [
            '@context'         => 'https://schema.org',
            '@type'            => 'BlogPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => get_permalink(),
            ],
            'headline'      => get_the_title(),
            'description'   => wp_strip_all_tags( get_the_excerpt() ),
            'datePublished' => get_the_date( 'c' ),
            'dateModified'  => get_the_modified_date( 'c' ),
            'author'        => [
                '@type' => 'Person',
                'name'  => get_the_author_meta( 'display_name', $author_id ),
                'url'   => get_author_posts_url( $author_id ),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name'  => 'JJ WeddingZ Photography',
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '',
                ],
            ],
        ];

        if ( $thumb ) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url'   => $thumb,
            ];
        }

        echo "\n" . '<!-- JJ WeddingZ Article Schema -->' . "\n";
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

    /**
     * 3. FAQPage Schema
     */
    private function render_faq_schema(): void {
        $service_slug  = get_query_var( 'jjwz_service_slug' );
        $location_slug = get_query_var( 'jjwz_location_slug' );
        $is_landing_page = ( $service_slug && $location_slug );

        if ( ! ( $is_landing_page || is_front_page() || is_page_template( 'page-services.php' ) || is_page_template( 'page-faqs.php' ) || is_post_type_archive( 'jjwz_faq' ) || is_singular( 'jjwz_location' ) || is_singular( 'jjwz_service' ) ) ) {
            return;
        }

        $args = [
            'post_type'      => 'jjwz_faq',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
        ];

        if ( $is_landing_page ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'jjwz_service_cat',
                    'field'    => 'slug',
                    'terms'    => $service_slug,
                ]
            ];
        } elseif ( is_singular( 'jjwz_service' ) ) {
            $service_post_slug = get_post_field( 'post_name', get_the_ID() );
            $args['tax_query'] = [
                [
                    'taxonomy' => 'jjwz_service_cat',
                    'field'    => 'slug',
                    'terms'    => $service_post_slug,
                ]
            ];
        }

        $faqs = get_posts( $args );

        if ( empty( $faqs ) ) { return; }

        $qa_elements = [];
        foreach ( $faqs as $faq ) {
            $question = get_post_meta( $faq->ID, 'faq_question', true ) ?: $faq->post_title;
            $answer   = get_post_meta( $faq->ID, 'faq_answer', true ) ?: $faq->post_content;

            $qa_elements[] = [
                '@type'          => 'Question',
                'name'           => wp_strip_all_tags( $question ),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( apply_filters( 'the_content', $answer ) ),
                ],
            ];
        }

        $schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $qa_elements,
        ];

        echo "\n" . '<!-- JJ WeddingZ FAQ Schema -->' . "\n";
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

    /**
     * 4. Dynamic Branch LocalBusiness Schema
     */
    private function render_dual_branch_schema(): void {
        if ( ! is_front_page() ) { return; }

        $phone = jjwz_get_option( 'jjw_primary_phone', '' );
        if ( empty( $phone ) ) {
            $phone = jjwz_get_option( 'jjwz_header_phone', '' );
        }
        $logo  = has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '';

        $branches_raw = jjwz_get_option( 'jjw_branches', '[]' );
        $branches = json_decode( $branches_raw, true ) ?: [];

        if ( empty( $branches ) ) {
            return;
        }

        $graph = [];
        foreach ( $branches as $i => $b ) {
            $branch_name    = ! empty( $b['name'] ) ? $b['name'] : 'Branch';
            $branch_address = ! empty( $b['address'] ) ? $b['address'] : '';
            $branch_phone   = ! empty( $b['phone'] ) ? $b['phone'] : $phone;
            $branch_email   = ! empty( $b['email'] ) ? $b['email'] : '';
            $branch_maps    = ! empty( $b['maps_url'] ) ? $b['maps_url'] : '';

            $store = [
                '@type'       => 'PhotographyStore',
                '@id'         => home_url( '#branch-' . $i ),
                'name'        => 'JJ WeddingZ Photography — ' . esc_html( $branch_name ) . ' Studio',
                'description' => 'Luxury Wedding, Pre-Wedding, Maternity & Newborn Photography Studio in ' . esc_html( $branch_name ) . '.',
                'url'         => home_url(),
                'logo'        => $logo,
                'telephone'   => $branch_phone,
                'priceRange'  => '₹₹₹₹',
                'image'       => $logo,
                'address'     => [
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => esc_html( $branch_address ),
                    'addressLocality' => esc_html( $branch_name ),
                    'addressCountry'  => 'IN',
                ],
                'openingHoursSpecification' => [
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ],
                    'opens'     => '10:00',
                    'closes'    => '20:00',
                ],
            ];

            if ( $branch_email ) {
                $store['email'] = $branch_email;
            }
            if ( $branch_maps ) {
                $store['hasMap'] = $branch_maps;
            }

            $graph[] = $store;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        ];

        echo "\n" . '<!-- JJ WeddingZ Dynamic Branch LocalBusiness Schema -->' . "\n";
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

    /**
     * 5. Service + City Landing Page LocalBusiness Schema
     */
    private function render_service_location_schema(): void {
        $service_slug  = get_query_var( 'jjwz_service_slug' );
        $location_slug = get_query_var( 'jjwz_location_slug' );

        if ( ! ( $service_slug && $location_slug ) ) { return; }

        $service_name = ucwords( str_replace( '-', ' ', $service_slug ) );
        $location_name = ucwords( str_replace( '-', ' ', $location_slug ) );

        $phone = jjwz_get_option( 'jjw_primary_phone', '' );
        if ( empty( $phone ) ) {
            $phone = jjwz_get_option( 'jjwz_header_phone', '' );
        }
        $logo  = has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '';

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'LocalBusiness',
            'name'        => sprintf( 'JJ WeddingZ %s Photography in %s', $service_name, $location_name ),
            'description' => sprintf( 'Premium luxury %s photography and cinematic films in %s.', strtolower($service_name), $location_name ),
            'url'         => home_url( '/' . $service_slug . '-photographer-in-' . $location_slug ),
            'logo'        => $logo,
            'telephone'   => $phone,
            'priceRange'  => '₹₹₹₹',
            'image'       => $logo,
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => $location_name,
                'addressCountry'  => 'IN',
            ]
        ];

        echo "\n" . '<!-- JJ WeddingZ Service + City LocalBusiness Schema -->' . "\n";
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }
}
