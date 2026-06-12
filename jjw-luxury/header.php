<?php
/**
 * header.php — JJ WeddingZ Photography
 * Global site header with dynamic ACF options data.
 *
 * @package JJWeddingZ
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<?php // Critical CSS + preloads injected here by jjwz_preload_resources() and jjwz_inline_critical_css() ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#jjwz-main-content" class="skip-to-content">Skip to content</a>

<?php
// Dynamic header data
$phone        = jjwz_get_option( 'jjw_primary_phone',      '+91 98765 43210' );
$wa_number    = jjwz_get_option( 'jjw_primary_whatsapp',   '919876543210' );
$wa_mode      = jjwz_get_option( 'jjwz_whatsapp_mode',     'simple' );
$wa_link      = ( $wa_mode === 'simple' ) ? 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $wa_number ) : '#';

$logo_url     = jjwz_get_option( 'jjw_logo', '' );
$logo_light   = jjwz_get_option( 'jjw_logo_light', '' );

// Try to grab Instagram link from our serialized social media settings
$socials_raw  = jjwz_get_option( 'jjw_social_media', '[]' );
$socials      = json_decode( $socials_raw, true ) ?: [];
$instagram_url = '';
foreach ( $socials as $s ) {
    if ( strtolower( $s['name'] ) === 'instagram' && ! empty( $s['enabled'] ) && $s['enabled'] !== '0' ) {
        $instagram_url = $s['url'];
        break;
    }
}
?>

<!-- ═══════════════════════════════════════════
     TOP BAR
     ═══════════════════════════════════════════ -->
<div class="jjwz-topbar" role="banner" aria-label="Contact information">
    <div class="container flex-between">
        <div class="topbar__left">
            <span class="topbar__tagline">International Luxury Wedding Photography — Delhi NCR &amp; Amritsar</span>
        </div>
        <div class="topbar__right">
            <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"
               class="topbar__link topbar__link--phone" id="header-phone-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.1 1.14 2 2 0 012.11 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z"></path></svg>
                <?php echo esc_html( $phone ); ?>
            </a>
            <a href="<?php echo esc_url( $wa_link ); ?>"
               class="topbar__link topbar__link--wa" target="_blank" rel="noopener noreferrer"
               id="header-wa-link" aria-label="Chat on WhatsApp">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12.003 2a9.987 9.987 0 00-8.59 15.04L2 22l5.115-1.34A9.993 9.993 0 0012.003 22c5.514 0 10-4.486 10-10s-4.486-10-9.997-10zm0 18.212a8.184 8.184 0 01-4.17-1.146l-.3-.178-3.09.81.824-3.01-.195-.31A8.198 8.198 0 013.802 12c0-4.517 3.676-8.195 8.2-8.195 4.517 0 8.196 3.678 8.196 8.195 0 4.52-3.68 8.212-8.196 8.212z"/></svg>
                WhatsApp
            </a>
            <?php if ( $instagram_url ) : ?>
            <a href="<?php echo esc_url( $instagram_url ); ?>" class="topbar__link topbar__social" target="_blank" rel="noopener" aria-label="Instagram">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     PRIMARY HEADER
     ═══════════════════════════════════════════ -->
<header class="jjwz-header" id="jjwz-header" role="navigation" aria-label="Primary navigation">
    <div class="container flex-between header__inner">

        <!-- Logo -->
        <div class="header__logo">
            <?php if ( ! empty( $logo_url ) ) : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home">
                    <img src="<?php echo esc_url( $logo_url ); ?>" class="custom-logo" alt="<?php bloginfo( 'name' ); ?>">
                </a>
            <?php elseif ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__site-name" rel="home">
                    <span class="header__logo-primary">JJ WeddingZ</span>
                    <span class="header__logo-secondary">Photography</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Primary Navigation -->
        <nav class="header__nav" id="header-nav" aria-label="Main Menu">
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_class'     => 'nav__list',
                'container'      => false,
                'walker'         => new JJWZ_Nav_Walker(),
                'fallback_cb'    => 'jjwz_nav_fallback',
            ] );
            ?>
        </nav>

        <!-- Header CTA -->
        <div class="header__actions">
            <a href="<?php echo esc_url( $wa_link ); ?>"
               class="btn btn--primary header__cta"
               id="header-cta-wa" target="_blank" rel="noopener noreferrer">
                Inquire Now
            </a>

            <!-- Mobile Hamburger -->
            <button class="hamburger" id="mobile-menu-toggle"
                    aria-expanded="false" aria-controls="header-nav"
                    aria-label="Toggle mobile menu">
                <span class="hamburger__line"></span>
                <span class="hamburger__line"></span>
                <span class="hamburger__line"></span>
            </button>
        </div>

    </div>
</header>

<main id="jjwz-main-content" tabindex="-1">
