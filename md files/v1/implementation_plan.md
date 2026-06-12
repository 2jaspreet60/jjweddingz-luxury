# Implementation Plan - JJ WeddingZ Photography Luxury WordPress Framework

This plan details the technical specifications and modifications required to build a premium, luxury photography website for **JJ WeddingZ Photography** (domain: `jjweddingz.com`). 

We will rename the existing codebases to `jjw-luxury` (theme) and `jjw-core` (plugin), implement the missing custom post types (CPTs) and taxonomies, expand the administrative Global Settings panel and Watermark controls, create a fully dynamic homepage with all 11 requested sections, and ensure absolute compliance with security and speed standards.

---

## User Review Required

> [!IMPORTANT]
> - **Workspace Configuration**: Since there is no local WordPress installation (PHP, web servers, or Docker are not active on this system), we will place the final source files directly under our active workspace directory `/Users/apple/Documents/jjweddingz/website with all/`. We will structure them under `jjw-luxury` (theme) and `jjw-core` (plugin) subfolders.
> - **Advanced Custom Fields Integration**: We will support both ACF Pro (options pages, repeaters, galleries) and a graceful PHP fallback. If ACF is active, all dynamic values will map to ACF custom fields. If not active, the custom Options Panel in the plugin will serve as the database source.
> - **Social Media Repeater**: For the social media links without ACF Pro, we will build a JavaScript-driven dynamic repeater table inside the custom option panel that serializes rows as a JSON string to `wp_options`.

---

## Proposed Changes

We will copy the base files from `jjweddingz-theme` and `jjweddingz-core` to the workspace, renaming them, and then proceed with the following specific enhancements.

### 1. Framework Naming and Bootstrap

#### [NEW] [jjw-core.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-core/jjw-core.php)
- Serves as the plugin entry bootstrap file.
- Register headers: `Plugin Name: JJW Core`, `Text Domain: jjw-core`.
- Register the autoloader mapping class namespaces to the `includes/` folder.
- Instantiate all modules (Image Processor, Options Panel, SEO Schema, CRM Forms, and Custom Post Types).

#### [NEW] [style.css](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/style.css)
- Theme entry style file.
- Register headers: `Theme Name: JJW Luxury`, `Text Domain: jjw-luxury`.
- Set styling variables matching the requested **Luxury Editorial Style**:
  - Headings: `Cormorant Garamond`
  - Body: `Montserrat`
  - Colors: Gold (`#C8A46A`), Black (`#111111`), Ivory (`#F8F5F0`), White (`#FFFFFF`).

---

### 2. Custom Post Types & Taxonomies (Plugin Module)

#### [NEW] Custom Post Type Classes
We will register CPTs and taxonomies dynamically under `jjw-core/includes/`:
- **`class-cpt-films.php`**: Registers `jjwz_film` CPT (slug: `films`) with support for title, thumbnail, editor, excerpt.
- **`class-cpt-packages.php`**: Registers `jjwz_package` CPT (slug: `packages`).
- **`class-cpt-testimonials.php`**: Registers `jjwz_testimonial` CPT (slug: `testimonials`).
- **`class-cpt-team.php`**: Registers `jjwz_team` CPT (slug: `team`).
- **`class-cpt-locations.php`**: Registers `jjwz_location` CPT (slug: `locations`) for landing page cities.

We will register the requested taxonomies and link them:
1. **Categories** (`jjwz_portfolio_cat`): Applied to Portfolios and Films.
   - Initial terms: Wedding, Pre Wedding, Maternity, Newborn, Baby, Cake Smash, Birthday, Anniversary, Family, Films.
2. **Session Types** (`jjwz_session_type`): Applied to Portfolios.
   - Initial terms: Studio, Outdoor, Lifestyle, Fine Art, Luxury, Traditional.
3. **Themes** (`jjwz_theme_type`): Applied to Portfolios.
   - Initial terms: Luxury, Modern, Fine Art, Classic, Minimal, Outdoor.
4. **Locations** (`jjwz_location_tax`): Applied to Portfolios and Locations.
   - Initial terms: Amritsar, Delhi, Ludhiana, Jalandhar, Mohali, Chandigarh, Patiala, Bathinda.

---

### 3. Global Settings Panel & Watermark System (Plugin Module)

#### [MODIFY] [class-options-panel.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-core/includes/class-options-panel.php)
- We will expand this class to support the custom WordPress settings page under the menu "JJ WeddingZ".
- Add tabs and fields:
  - **Branding**: Logo, Dark Logo, Light Logo, Mobile Logo, Favicon (using WordPress Media Uploader JS for custom file selection).
  - **Contact**: Primary Phone, Secondary Phone, Primary WhatsApp, Secondary WhatsApp, Email, Support Email.
  - **Branches**:
    - *Amritsar*: Address, Phone, WhatsApp, Email, Google Maps URL.
    - *Delhi NCR*: Address, Phone, WhatsApp, Email, Google Maps URL.
  - **Social Media Repeater**: Dynamically add, sort, enable/disable entries with X, Instagram, Pinterest, YouTube, Facebook, Flickr, LinkedIn, Threads, etc. Saved as a JSON serialized option.
  - **Watermark Settings**:
    - Enable Watermark (Checkbox)
    - Watermark Text (Text input, defaults to `© JJ WeddingZ Photography`)
    - Watermark Opacity (Select: 10% to 90%, defaults to `15%`)
    - Watermark Position (Select: `bottom-right`, `bottom-left`, `top-right`, `top-left`, `center`)

#### [MODIFY] [class-image-processor.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-core/includes/class-image-processor.php)
- Read settings dynamically:
  - If watermark is disabled, skip watermark processing.
  - Apply configured text, opacity (converted to GD alpha index 0-127), and position coordinates (calculating bounding boxes for GD and composite offsets for Imagick).
- Maintain lazy loading and automatic conversion to compressed WebP (78% quality, max width 2560px).

---

### 4. Dynamic Field Definitions (Theme Module)

#### [MODIFY] [acf-fields.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/inc/acf-fields.php)
- Register field groups for all post types:
  - **Portfolio Fields**: Gallery (image list), Video URL, Short Description, Full Story, Location (taxonomy), Session Type (taxonomy), Theme (taxonomy), Venue, Photographer, Featured (true/false), Display Order (number).
  - **Films Fields**: YouTube URL, Vimeo URL, Description, Featured (true/false).
  - **Package Fields**: Price, Description, Features (textarea), Album Included (true/false), Delivery Timeline, Featured Package (true/false).
  - **FAQ Fields**: Question, Answer, Category (select), Display Order.
  - **Team Fields**: Designation, Photo, Bio, Instagram, Email, Phone, Experience.
  - **Testimonial Fields**: Service, Location, Rating (1-5 stars), Review, Photo, Video URL.
  - **Location Fields**: Hero Image, Description, SEO Content, Google Map embed URL, Featured (true/false).
- Register the "Global Settings" Options Page fields to match the options stored in `wp_options` so both ACF Pro and the native fields align.

---

### 5. Frontend Templates & Homepage Sections (Theme Module)

#### [MODIFY] [front-page.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-luxury/front-page.php)
We will rewrite the homepage template to implement all 11 sections completely dynamically:
1. **Hero Video**: Displays loop video/YouTube ID or cover image. CTAs link to availability check and portfolio.
2. **Services**: Queries the 10 service descriptions and icons.
3. **Featured Portfolio**: Masonry grid showing items marked as `Featured` in portfolio posts.
4. **Featured Films**: Slider of films CPT items marked as `Featured`, displaying YouTube/Vimeo embeds in a fluid lightbox.
5. **About Founder**: Editorial column layout with portrait, founder signature, experience, and visual authenticity promise.
6. **Why Choose Us**: Technical redundant safety features (dual card recording) and identity protection details.
7. **Ventures**: Highlights secondary portals like *The Baby StudioZ*.
8. **Testimonials**: Star rating review carousel pulling from Testimonials CPT.
9. **Date Availability Form**: Secure form that routes inquiries directly into the custom CRM table and triggers notification alerts.
10. **Latest Blogs**: The 3 most recent articles showing reading time.
11. **Contact CTA**: Editorial luxury footer banner with branch information and dynamic phone/WhatsApp buttons.

#### [NEW] CPT Detail and Archive Templates
- **`single-jjwz_portfolio.php`**: Fine-art editorial story layout showing large grid gallery, story description, location, venue, and camera credentials.
- **`single-jjwz_location.php`**: Showcase page for cities, linking portfolio images tagged with this location automatically.
- **`archive-jjwz_film.php`**: Premium grid layout of cinema films.
- **`archive-jjwz_package.php`**: Columns comparing package pricing, delivery timelines, and standard features.

---

### 6. SEO & Cloudflare Compliance

#### [MODIFY] [class-seo-schema.php](file:///Users/apple/Documents/jjweddingz/website%20with%20all/jjw-core/includes/class-seo-schema.php)
- Replace all hardcoded phone numbers, URLs, addresses, and geo coordinates in `render_dual_branch_schema()` with options values fetched dynamically from the Global Settings panel database.
- breadcrumbs list schema will output Rank Math or Yoast data natively, falling back to a clean list.

---

## Verification Plan

### Automated Tests & Quality Review
- Run PHP syntax check on all files: `find . -name "*.php" -exec php -l {} \;` (if PHP is available or via standard syntax lint checks).
- Run CSS validation to verify standard layout alignment and color properties.

### Manual Verification
- We will inspect the generated files to ensure that no hardcoded contact info exists.
- We will confirm that form endpoints use WP nonce validation and input sanitization (`sanitize_text_field`, `wp_filter_kses`).
- We will double-check that all functions checking settings use `jjwz_get_option()` which handles database, custom setting page options, and ACF field options interchangeably.
