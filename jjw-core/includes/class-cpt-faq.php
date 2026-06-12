<?php
/**
 * class-cpt-faq.php — FAQ Custom Post Type + Taxonomy + Seeder
 *
 * Registers:
 * - CPT: jjwz_faq
 * - Taxonomy: faq_category (Wedding, Maternity, Baby Shoot, Newborn, Global)
 * - Pre-seeds all 8 FAQs from the specification on plugin activation
 *
 * @package JJWeddingZ_Core
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_CPT_FAQ {

    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
        add_action( 'init', [ $this, 'register_taxonomy' ] );
    }

    /* ─── Register CPT ───────────────────────────────────────────────────── */

    public function register_cpt(): void {
        register_post_type( 'jjwz_faq', [
            'labels' => [
                'name'               => __( 'FAQs',             'jjweddingz' ),
                'singular_name'      => __( 'FAQ',              'jjweddingz' ),
                'add_new'            => __( 'Add New FAQ',      'jjweddingz' ),
                'add_new_item'       => __( 'Add New FAQ',      'jjweddingz' ),
                'edit_item'          => __( 'Edit FAQ',         'jjweddingz' ),
                'new_item'           => __( 'New FAQ',          'jjweddingz' ),
                'view_item'          => __( 'View FAQ',         'jjweddingz' ),
                'search_items'       => __( 'Search FAQs',      'jjweddingz' ),
                'not_found'          => __( 'No FAQs found',    'jjweddingz' ),
                'not_found_in_trash' => __( 'No FAQs in trash', 'jjweddingz' ),
                'menu_name'          => __( 'FAQs',             'jjweddingz' ),
            ],
            'public'              => false,
            'publicly_queryable'  => true,   // Allows WP_Query + Elementor to query
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => [ 'slug' => 'faq', 'with_front' => false ],
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_icon'           => 'dashicons-editor-help',
            'menu_position'       => 25,
            'supports'            => [ 'title', 'editor', 'page-attributes', 'custom-fields' ],
        ] );
    }

    /* ─── Register Taxonomy ──────────────────────────────────────────────── */

    public function register_taxonomy(): void {
        register_taxonomy( 'faq_category', [ 'jjwz_faq' ], [
            'labels' => [
                'name'              => __( 'FAQ Categories',      'jjweddingz' ),
                'singular_name'     => __( 'FAQ Category',        'jjweddingz' ),
                'search_items'      => __( 'Search Categories',   'jjweddingz' ),
                'all_items'         => __( 'All Categories',      'jjweddingz' ),
                'edit_item'         => __( 'Edit Category',       'jjweddingz' ),
                'update_item'       => __( 'Update Category',     'jjweddingz' ),
                'add_new_item'      => __( 'Add New Category',    'jjweddingz' ),
                'new_item_name'     => __( 'New Category Name',   'jjweddingz' ),
                'menu_name'         => __( 'Categories',          'jjweddingz' ),
            ],
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'faq-category' ],
        ] );
    }

    /* ─── Seed FAQs (run on activation) ─────────────────────────────────── */

    public function seed_faqs(): void {
        // Don't re-seed if already done
        if ( get_option( 'jjwz_faqs_seeded' ) ) { return; }

        $this->register_cpt();
        $this->register_taxonomy();

        $faqs = $this->get_faq_data();

        foreach ( $faqs as $faq ) {
            // Check if already exists by title
            $existing = get_page_by_title( $faq['question'], OBJECT, 'jjwz_faq' );
            if ( $existing ) { continue; }

            $post_id = wp_insert_post( [
                'post_title'   => wp_strip_all_tags( $faq['question'] ),
                'post_content' => wp_kses_post( $faq['answer'] ),
                'post_status'  => 'publish',
                'post_type'    => 'jjwz_faq',
                'menu_order'   => $faq['order'],
            ] );

            if ( ! is_wp_error( $post_id ) ) {
                // Assign category
                wp_set_object_terms( $post_id, [ $faq['category'] ], 'faq_category' );

                // Set ACF fields if available
                if ( function_exists( 'update_field' ) ) {
                    update_field( 'faq_question', $faq['question'], $post_id );
                    update_field( 'faq_answer',   $faq['answer'],   $post_id );
                }
                // Fallback: save as post meta
                update_post_meta( $post_id, 'faq_question', sanitize_text_field( $faq['question'] ) );
                update_post_meta( $post_id, 'faq_answer',   wp_kses_post( $faq['answer'] ) );
            }
        }

        update_option( 'jjwz_faqs_seeded', true );
    }

    /* ─── FAQ Data Array ─────────────────────────────────────────────────── */

    private function get_faq_data(): array {
        return [
            // ── Wedding & Pre-Wedding FAQs ──────────────────────────────────
            [
                'question' => 'How far in advance should we book your services?',
                'answer'   => '<p>Because we take on a limited number of luxury weddings each season to ensure unparalleled quality, we highly recommend booking 6 to 8 months in advance. Popular dates — particularly during peak wedding seasons in October–February and spring months — tend to fill up very quickly. The earlier you reach out for a consultation, the better the chances of securing your preferred date.</p>',
                'category' => 'wedding',
                'order'    => 1,
            ],
            [
                'question' => 'Do you travel outside of Delhi and Amritsar for destination weddings?',
                'answer'   => '<p>Absolutely. While our primary branches are in Delhi NCR and Amritsar, our passports are always ready. We frequently travel across India and internationally for destination weddings — from the heritage palaces of Rajasthan to the beachside venues of Goa, and beyond to international locations across Asia, Europe, and the Middle East. Travel arrangements and associated costs are discussed transparently during the quotation process.</p>',
                'category' => 'wedding',
                'order'    => 2,
            ],
            [
                'question' => 'How long does it take to receive our final wedding gallery and cinematic films?',
                'answer'   => '<p>We take our post-production seriously. You will receive a selection of highlight images within one week of your wedding, and the complete, meticulously edited gallery will be delivered within 6 to 8 weeks. Cinematic wedding films, due to the complexity of the editing process, follow a similar timeline. We will keep you informed throughout the editing process.</p>',
                'category' => 'wedding',
                'order'    => 3,
            ],
            [
                'question' => 'What backup measures do you take during an active event shoot?',
                'answer'   => '<p>All our primary camera units, including our Nikon Z6 III and Sony FX3 cinema configurations, record to dual card slots simultaneously in real-time, providing immediate on-site data redundancy. Beyond in-camera redundancy, all data is immediately backed up to encrypted portable drives at the end of each function. We also bring backup camera bodies and lenses to every event as insurance against equipment failure.</p>',
                'category' => 'wedding',
                'order'    => 4,
            ],

            // ── Maternity, Baby Shoot & Newborn FAQs ───────────────────────
            [
                'question' => 'When is the best time to schedule a maternity shoot?',
                'answer'   => '<p>We recommend scheduling your maternity session between 28 and 34 weeks of pregnancy, when your bump is beautifully round and the pregnancy is visible and prominent, but you are still comfortable moving around and posing. Every pregnancy is unique, however — we are always happy to discuss the ideal timing for your specific situation during a free consultation call.</p>',
                'category' => 'maternity',
                'order'    => 5,
            ],
            [
                'question' => 'When should newborn photos be taken?',
                'answer'   => '<p>For those sleepy, curled-up newborn poses — the ones that look so naturally peaceful — it is best to photograph your baby within the first 7 to 14 days after birth. During this window, newborns sleep deeply and can be safely and comfortably posed. After 14 days, babies become more alert and those signature curled poses become more difficult to achieve. We recommend booking your newborn session before your due date so the date is reserved and we can be ready immediately after the birth.</p>',
                'category' => 'newborn',
                'order'    => 6,
            ],
            [
                'question' => 'Do you provide props for baby shoots?',
                'answer'   => '<p>Yes, absolutely. We provide a curated selection of high-quality, sanitized wraps, baskets, headbands, and minimalist props that align with our sophisticated, international design aesthetic. Our prop collection is regularly expanded and thoroughly sanitized between every single session. You are also welcome to bring personal items — a favourite heirloom blanket, a tiny outfit — to include in your session for added personalisation.</p>',
                'category' => 'baby-shoot',
                'order'    => 7,
            ],
            [
                'question' => 'Is your baby studio sanitized and climate-controlled?',
                'answer'   => '<p>Absolutely. Our dedicated newborn and baby photography environments maintain medical-grade sanitization protocols — all surfaces, props, wraps, and equipment are thoroughly cleaned and disinfected before every session. Our studio also maintains precise temperature controls, keeping the environment noticeably warmer than room temperature (around 26–28°C) to keep your newborn safe, comfortable, and settled throughout the session.</p>',
                'category' => 'newborn',
                'order'    => 8,
            ],
        ];
    }
}
