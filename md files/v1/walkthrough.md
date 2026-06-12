# Walkthrough — JJ WeddingZ Photography WordPress Framework

This document outlines the technical updates and additions successfully implemented during the first sprint of development for the **JJ WeddingZ Photography** premium luxury framework.

---

## 1. Accomplishments & Key Refactors

### 🏢 Dynamic Studio Branches Repeater & Schema
- **Implementation**: Fully replaced the original static Amritsar and Delhi studio variables in `front-page.php`, `page-contact.php`, and `class-seo-schema.php` with a dynamic JSON decoder parsing the `jjw_branches` setting.
- **Structured Data**: Re-engineered `class-seo-schema.php` to dynamically generate a `PhotographyStore` schema block in a graph array for each office entered via the custom settings panel, rather than hardcoding DLF Gurugram and Ranjit Avenue coordinates.

### 🏠 Dynamic Homepage Rewrite (`front-page.php`)
- Redesigned the home page template to dynamically loop through all CPT entities and options, including:
  1. **Hero Video Banner**: Media source switcher (local file/YouTube ID/poster image), eyebrows, title, subtitle, and dynamic WhatsApp inquiry trigger.
  2. **Stats Counter Band**: Dynamic years of experience, weddings count, branch locations count (`count($branches)`), and customer satisfaction percentage.
  3. **Value Proposition Block**: Displays philosophy text and a "100% Identity Retained" trust badge.
  4. **Dynamic Branch Hub Router**: Loops through all branch options to output elegant cards.
  5. **Services milestones selection**: Queries `jjwz_service` CPT items ordered by `svc_display_order`.
  6. **Featured Portfolio Masonry**: Incorporates standard masonry grid helper.
  7. **Featured Films Showcase**: Displays cinematic highlights matching CPT `jjwz_film` (integrates YouTube and Vimeo ID parsers and poster-first play button overlays).
  8. **About Founder**: Rich editorial section profiling Jaspreet Singh with dynamic bios, portraits, and signature blocks.
  9. **Why Choose Us Promises**: A premium card grid demonstrating technical and safety standards.
  10. **Testimonials Carousel**: Queries CPT `jjwz_testimonial` dynamically.
  11. **Date Availability Booking Form**: Features a clean lead capture form with honeypot fields that integrate directly into the custom database CRM table.

### 🖼️ Core Theme Templates Refactoring
- **Centralized Helper Integration**: Replaced all direct calls to `get_field` with the centralized helper function `jjwz_get_option( $key, $fallback, $post_id )` inside:
  - `page-about.php` (Headline, intro, founder details, bio)
  - `page-services.php` (Page header, service blocks repeater)
  - `page-contact.php` (Dynamic branch details)
  - `front-page.php` (All global site configuration and home post fields)
- **Custom Single Templates Created**:
  - `single-jjwz_portfolio.php`: Renders editorial narratives, full meta cards (Venue, photographer, editor, client name, city, album details, and status), video highlights, and responsive image grids.
  - `single-jjwz_service.php`: Renders icon headers, full editorial descriptions, and list highlights.
  - `single-jjwz_location.php`: Renders studio location descriptions, direct Google Maps frames, and regional portfolios.
- **Custom Archive Templates Created**:
  - `archive-jjwz_film.php`: Displays video cards with play overlays that initialize iframe embeds on click.
  - `archive-jjwz_package.php`: Renders clear investment option tables, feature bullet points, and availability buttons.

---

## 2. File Modifiations

Here is a summary of the files updated and created in the workspace:

| Target Component | File Path | Action | Description |
| :--- | :--- | :--- | :--- |
| **Plugin** | [class-seo-schema.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-core/includes/class-seo-schema.php) | **MODIFY** | Updated `render_dual_branch_schema()` to loop through dynamic branch listings |
| **Theme** | [front-page.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/front-page.php) | **MODIFY** | Rewrote homepage template to implement 11 dynamic editorial sections |
| **Theme** | [page-about.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/page-about.php) | **MODIFY** | Updated fields to use helper function `jjwz_get_option()` |
| **Theme** | [page-services.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/page-services.php) | **MODIFY** | Refactored main page headings and blocks to use option helper |
| **Theme** | [page-contact.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/page-contact.php) | **MODIFY** | Refactored branch cards to load branches dynamically from repeater settings |
| **Theme** | [single-jjwz_portfolio.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/single-jjwz_portfolio.php) | **NEW** | Created single template for Portfolio CPT entries |
| **Theme** | [single-jjwz_service.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/single-jjwz_service.php) | **NEW** | Created single template for Service CPT entries |
| **Theme** | [single-jjwz_location.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/single-jjwz_location.php) | **NEW** | Created single template for Location CPT entries |
| **Theme** | [archive-jjwz_film.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/archive-jjwz_film.php) | **NEW** | Created archive grid template for Films CPT |
| **Theme** | [archive-jjwz_package.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/archive-jjwz_package.php) | **NEW** | Created archive investment template for Packages CPT |

---

## 3. Verification Details

- **Templates Check**: Verified that the homepage (`front-page.php`), contact page, and single templates parse both global DB values and fallback custom options gracefully.
- **WP Coding Standards**: Output escaping (`esc_html`, `esc_url`, `esc_attr`, and `wp_kses_post`) has been strictly applied across all print paths.
- **Database Safety**: Contact form handles honeypot validation (`jjwz_honey`), rate limits requests to 3 per IP per hour, and utilizes localized security nonces.
