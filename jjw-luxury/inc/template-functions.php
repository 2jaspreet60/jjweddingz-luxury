<?php
/**
 * inc/template-functions.php — Shared helper functions.
 *
 * @package JJWeddingZ
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ─── Nav Fallback ───────────────────────────────────────────────────────── */

function jjwz_nav_fallback() {
    echo '<ul class="nav__list">';
    echo '<li class="nav__item"><a href="' . esc_url( home_url( '/' ) ) . '" class="nav__link">Home</a></li>';
    echo '<li class="nav__item"><a href="' . esc_url( home_url( '/about' ) ) . '" class="nav__link">About</a></li>';
    echo '<li class="nav__item"><a href="' . esc_url( home_url( '/services' ) ) . '" class="nav__link">Services</a></li>';
    echo '<li class="nav__item"><a href="' . esc_url( home_url( '/portfolio' ) ) . '" class="nav__link">Portfolio</a></li>';
    echo '<li class="nav__item"><a href="' . esc_url( home_url( '/blog' ) ) . '" class="nav__link">Blog</a></li>';
    echo '<li class="nav__item"><a href="' . esc_url( home_url( '/contact' ) ) . '" class="nav__link">Contact</a></li>';
    echo '</ul>';
}

/* ─── Portfolio / Masonry grid placeholder cards ─────────────────────────── */

function jjwz_portfolio_masonry_grid( int $count = 12, string $css_class = '' ) {
    $categories = [
        'Luxury Wedding',
        'Pre-Wedding',
        'Baby Shoot',
        'Newborn',
        'Maternity',
        'Cinematic Film',
    ];

    // Try to pull from 'portfolio' CPT first
    $portfolio_q = new WP_Query( [
        'post_type'      => 'jjwz_portfolio',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );

    $html  = '<div class="jjwz-masonry ' . esc_attr( $css_class ) . '" data-masonry>';
    $index = 0;

    if ( $portfolio_q->have_posts() ) {
        while ( $portfolio_q->have_posts() ) {
            $portfolio_q->the_post();
            $thumb = get_the_post_thumbnail_url( null, 'jjwz-portfolio' );
            $cat   = get_the_terms( get_the_ID(), 'jjwz_portfolio_cat' );
            $label = $cat ? $cat[0]->name : $categories[ $index % count( $categories ) ];
            $html .= jjwz_masonry_card( get_the_ID(), $thumb, get_the_title(), $label, get_permalink() );
            $index++;
        }
        wp_reset_postdata();
    } else {
        // Placeholder cards when no portfolio CPT entries exist
        for ( $i = 0; $i < $count; $i++ ) {
            $label = $categories[ $i % count( $categories ) ];
            $aspect = ( $i % 3 === 0 ) ? 'portrait' : ( ( $i % 3 === 1 ) ? 'landscape' : 'square' );
            $html  .= jjwz_masonry_placeholder_card( $i + 1, $label, $aspect );
        }
    }

    $html .= '</div>';
    return $html;
}

function jjwz_masonry_card( $id, $thumb, $title, $label, $link ) {
    if ( $thumb ) {
        $img = '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">';
    } else {
        $clean_label = strtolower( trim( $label ) );
        $fallback_img = 'placeholder-category-default.png';
        if ( strpos( $clean_label, 'wedding' ) !== false ) {
            $fallback_img = 'placeholder-category-wedding.png';
        } elseif ( strpos( $clean_label, 'baby' ) !== false || strpos( $clean_label, 'newborn' ) !== false ) {
            $fallback_img = 'placeholder-category-baby.png';
        } elseif ( strpos( $clean_label, 'maternity' ) !== false ) {
            $fallback_img = 'placeholder-category-maternity.png';
        }
        $img_url = get_template_directory_uri() . '/assets/images/' . $fallback_img;
        $img = '<img src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">';
    }
    return '
    <div class="masonry-card" data-category="' . esc_attr( strtolower( $label ) ) . '">
        <a href="' . esc_url( $link ) . '" class="masonry-card__link">
            <div class="masonry-card__media">' . $img . '</div>
            <div class="masonry-card__overlay">
                <span class="masonry-card__cat">' . esc_html( $label ) . '</span>
                <h3 class="masonry-card__title">' . esc_html( $title ) . '</h3>
                <span class="masonry-card__view">View Story →</span>
            </div>
        </a>
    </div>';
}

function jjwz_masonry_placeholder_card( int $num, string $label, string $aspect = 'landscape' ) {
    $gradients = [
        'linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%)',
        'linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%)',
        'linear-gradient(135deg, #1c1c1c 0%, #3a3a3a 100%)',
        'linear-gradient(135deg, #111 0%, #2a2a2a 50%, #1a1a1a 100%)',
        'linear-gradient(135deg, #0f0f0f 0%, #222 100%)',
        'linear-gradient(135deg, #1d1d1d 0%, #333 100%)',
    ];
    $grad = $gradients[ ( $num - 1 ) % count( $gradients ) ];
    $aspect_class = 'masonry-card--' . $aspect;

    return '
    <div class="masonry-card ' . $aspect_class . '" data-category="' . esc_attr( strtolower( $label ) ) . '">
        <div class="masonry-card__link">
            <div class="masonry-card__media masonry-card__media--placeholder" style="background:' . $grad . ';">
                <div class="masonry-card__placeholder-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(201,169,110,0.4)" stroke-width="1" aria-hidden="true">
                        <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                </div>
                <div class="masonry-card__placeholder-num">' . str_pad( $num, 2, '0', STR_PAD_LEFT ) . '</div>
            </div>
            <div class="masonry-card__overlay">
                <span class="masonry-card__cat">' . esc_html( $label ) . '</span>
                <h3 class="masonry-card__title">Portfolio Story #' . $num . '</h3>
                <span class="masonry-card__view">View Story →</span>
            </div>
        </div>
    </div>';
}

/* ─── FAQ accordion renderer ─────────────────────────────────────────────── */

function jjwz_render_faq_accordion( string $taxonomy_slug = '', int $limit = -1 ) {
    $args = [
        'post_type'      => 'jjwz_faq',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];

    if ( $taxonomy_slug ) {
        $args['tax_query'] = [ [
            'taxonomy' => 'faq_category',
            'field'    => 'slug',
            'terms'    => $taxonomy_slug,
        ] ];
    }

    $faq_q = new WP_Query( $args );

    if ( ! $faq_q->have_posts() ) { return ''; }

    $html = '<div class="jjwz-accordion" role="list" aria-label="Frequently Asked Questions">';
    while ( $faq_q->have_posts() ) {
        $faq_q->the_post();
        $uid     = 'faq-' . get_the_ID();
        $question = function_exists( 'get_field' ) ? get_field( 'faq_question' ) : get_the_title();
        $answer   = function_exists( 'get_field' ) ? get_field( 'faq_answer' )   : get_the_content();
        if ( ! $question ) { $question = get_the_title(); }
        if ( ! $answer   ) { $answer   = get_the_excerpt(); }

        $html .= '
        <div class="accordion__item" role="listitem">
            <button class="accordion__trigger"
                    id="' . esc_attr( $uid . '-btn' ) . '"
                    aria-expanded="false"
                    aria-controls="' . esc_attr( $uid . '-panel' ) . '">
                <span class="accordion__question">' . esc_html( $question ) . '</span>
                <span class="accordion__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </span>
            </button>
            <div class="accordion__panel"
                 id="' . esc_attr( $uid . '-panel' ) . '"
                 role="region"
                 aria-labelledby="' . esc_attr( $uid . '-btn' ) . '"
                 hidden>
                <div class="accordion__body">' . wp_kses_post( $answer ) . '</div>
            </div>
        </div>';
    }
    wp_reset_postdata();
    $html .= '</div>';
    return $html;
}

/* ─── Page Hero Banner ───────────────────────────────────────────────────── */

function jjwz_page_hero( string $title, string $subtitle = '', string $bg_color = '' ) {
    $style = $bg_color ? ' style="background:' . esc_attr( $bg_color ) . ';"' : '';
    ?>
    <section class="jjwz-page-hero"<?php echo $style; ?> aria-label="Page hero">
        <div class="container page-hero__inner">
            <?php jjwz_breadcrumb(); ?>
            <h1 class="page-hero__title"><?php echo wp_kses_post( $title ); ?></h1>
            <?php if ( $subtitle ) : ?>
                <p class="page-hero__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/* ─── WhatsApp CTA helper ────────────────────────────────────────────────── */

function jjwz_wa_link( string $text = 'Inquire on WhatsApp', string $class = 'btn btn--primary', string $id = '', string $message = '' ) {
    $wa_number = jjwz_get_option( 'jjwz_whatsapp_number', '919876543210' );
    $wa_mode   = jjwz_get_option( 'jjwz_whatsapp_mode', 'simple' );
    $clean_num = preg_replace( '/[^0-9]/', '', $wa_number );
    $url       = ( $wa_mode === 'simple' ) ? 'https://wa.me/' . $clean_num . ( $message ? '?text=' . rawurlencode( $message ) : '' ) : '#';
    $id_attr   = $id ? ' id="' . esc_attr( $id ) . '"' : '';

    return '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '"' . $id_attr . ' target="_blank" rel="noopener noreferrer">' . esc_html( $text ) . '</a>';
}

/* ─── Stat Counter Items ─────────────────────────────────────────────────── */

function jjwz_stat_item( string $number, string $label, string $suffix = '+' ) {
    return '<div class="stat-item">
        <div class="stat-item__number"><span class="stat-counter" data-target="' . esc_attr( $number ) . '">0</span>' . esc_html( $suffix ) . '</div>
        <div class="stat-item__label">' . esc_html( $label ) . '</div>
    </div>';
}
