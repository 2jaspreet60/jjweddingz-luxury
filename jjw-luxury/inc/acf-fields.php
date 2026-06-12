<?php
/**
 * inc/acf-fields.php — ACF Field Group Registration
 *
 * Registers all ACF field groups via PHP. Falls back gracefully 
 * if ACF is not active — fields are retrieved from wp_options via jjwz_get_option().
 *
 * @package JJW_Luxury
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'acf/init', 'jjwz_register_acf_fields' );

function jjwz_register_acf_fields() {

    if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }

    /* ─────────────────────────────────────────────────────────────────────
       1. OPTIONS PAGE (ACF Pro)
       ───────────────────────────────────────────────────────────────────── */
    if ( function_exists( 'acf_add_options_page' ) ) {
        acf_add_options_page( [
            'page_title'  => 'JJ WeddingZ Settings',
            'menu_title'  => 'JJ WeddingZ',
            'menu_slug'   => 'jjwz-options',
            'capability'  => 'manage_options',
            'icon_url'    => 'dashicons-camera',
            'redirect'    => false,
        ] );
    }

    /* ─────────────────────────────────────────────────────────────────────
       2. GLOBAL OPTIONS FIELD GROUP
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'      => 'group_jjwz_global',
        'title'    => 'JJ WeddingZ Global Options',
        'fields'   => [
            /* — Branding — */
            [ 'key' => 'field_jjw_logo',        'label' => 'Logo URL',          'name' => 'jjw_logo',        'type' => 'text' ],
            [ 'key' => 'field_jjw_logo_dark',   'label' => 'Dark Logo URL',     'name' => 'jjw_logo_dark',   'type' => 'text' ],
            [ 'key' => 'field_jjw_logo_light',  'label' => 'Light Logo URL',    'name' => 'jjw_logo_light',  'type' => 'text' ],
            [ 'key' => 'field_jjw_logo_mobile', 'label' => 'Mobile Logo URL',   'name' => 'jjw_logo_mobile', 'type' => 'text' ],
            [ 'key' => 'field_jjw_favicon',     'label' => 'Favicon URL',       'name' => 'jjw_favicon',     'type' => 'text' ],

            /* — Contacts — */
            [ 'key' => 'field_jjw_primary_phone',      'label' => 'Primary Phone',        'name' => 'jjw_primary_phone',      'type' => 'text' ],
            [ 'key' => 'field_jjw_secondary_phone',    'label' => 'Secondary Phone',      'name' => 'jjw_secondary_phone',    'type' => 'text' ],
            [ 'key' => 'field_jjw_primary_whatsapp',   'label' => 'Primary WhatsApp',     'name' => 'jjw_primary_whatsapp',   'type' => 'text' ],
            [ 'key' => 'field_jjw_secondary_whatsapp', 'label' => 'Secondary WhatsApp',   'name' => 'jjw_secondary_whatsapp', 'type' => 'text' ],
            [ 'key' => 'field_jjw_email',              'label' => 'Primary Email',        'name' => 'jjw_email',              'type' => 'email' ],
            [ 'key' => 'field_jjw_support_email',      'label' => 'Support Email',        'name' => 'jjw_support_email',      'type' => 'email' ],
            [ 'key' => 'field_jjwz_copyright_text',    'label' => 'Footer Copyright',     'name' => 'jjwz_copyright_text',    'type' => 'text' ],

            /* — Watermark Settings — */
            [ 'key' => 'field_jjw_watermark_enable',   'label' => 'Enable Watermarking',  'name' => 'jjw_watermark_enable',   'type' => 'true_false', 'ui' => 1 ],
            [ 'key' => 'field_jjw_watermark_text',     'label' => 'Watermark Text',       'name' => 'jjw_watermark_text',     'type' => 'text', 'default_value' => '© JJ WeddingZ Photography' ],
            [ 'key' => 'field_jjw_watermark_opacity',  'label' => 'Watermark Opacity',    'name' => 'jjw_watermark_opacity',  'type' => 'select', 'choices' => [ '0.05' => '5%', '0.10' => '10%', '0.15' => '15%', '0.25' => '25%', '0.40' => '40%', '0.60' => '60%' ], 'default_value' => '0.15' ],
            [ 'key' => 'field_jjw_watermark_position', 'label' => 'Watermark Position',   'name' => 'jjw_watermark_position', 'type' => 'select', 'choices' => [ 'bottom-right' => 'Bottom Right', 'bottom-left' => 'Bottom Left', 'top-right' => 'Top Right', 'top-left' => 'Top Left', 'center' => 'Center' ], 'default_value' => 'bottom-right' ],

            /* — WhatsApp Config — */
            [ 'key' => 'field_jjwz_whatsapp_mode',     'label' => 'WhatsApp Mode',        'name' => 'jjwz_whatsapp_mode',     'type' => 'select', 'choices' => [ 'simple' => 'Simple wa.me Link', 'api' => 'Business API Endpoint' ], 'default_value' => 'simple' ],
            [ 'key' => 'field_jjwz_whatsapp_number',   'label' => 'WhatsApp Number',      'name' => 'jjwz_whatsapp_number',   'type' => 'text', 'conditional_logic' => [ [ [ 'field' => 'field_jjwz_whatsapp_mode', 'operator' => '==', 'value' => 'simple' ] ] ] ],
            [ 'key' => 'field_jjwz_wa_api_endpoint',   'label' => 'API Endpoint',         'name' => 'jjwz_wa_api_endpoint',   'type' => 'url', 'conditional_logic' => [ [ [ 'field' => 'field_jjwz_whatsapp_mode', 'operator' => '==', 'value' => 'api' ] ] ] ],
            [ 'key' => 'field_jjwz_wa_bearer_token',   'label' => 'Bearer Auth Token',    'name' => 'jjwz_wa_bearer_token',   'type' => 'password', 'conditional_logic' => [ [ [ 'field' => 'field_jjwz_whatsapp_mode', 'operator' => '==', 'value' => 'api' ] ] ] ],
            [ 'key' => 'field_jjwz_wa_json_payload',   'label' => 'JSON Payload Template','name' => 'jjwz_wa_json_payload',   'type' => 'textarea', 'conditional_logic' => [ [ [ 'field' => 'field_jjwz_whatsapp_mode', 'operator' => '==', 'value' => 'api' ] ] ] ],
        ],
        'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'jjwz-options' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       3. SERVICES POST TYPE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_service',
        'title'  => 'Service Fields',
        'fields' => [
            [ 'key' => 'field_svc_icon',         'label' => 'Icon Emoji',            'name' => 'svc_icon',         'type' => 'text', 'placeholder' => '💍' ],
            [ 'key' => 'field_svc_hero_image',   'label' => 'Hero Image',            'name' => 'svc_hero_image',   'type' => 'image', 'return_format' => 'array' ],
            [ 'key' => 'field_svc_gallery',      'label' => 'Service Gallery',       'name' => 'svc_gallery',      'type' => 'gallery', 'return_format' => 'array' ],
            [ 'key' => 'field_svc_short_desc',   'label' => 'Short Description',     'name' => 'svc_short_desc',   'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_svc_seo_content',  'label' => 'Full SEO Content',      'name' => 'svc_seo_content',  'type' => 'wysiwyg' ],
            [ 'key' => 'field_svc_pricing_title','label' => 'Pricing Section Title', 'name' => 'svc_pricing_title', 'type' => 'text', 'placeholder' => 'Exclusive Packages' ],
            [ 'key' => 'field_svc_pricing_desc', 'label' => 'Pricing Section Desc',  'name' => 'svc_pricing_desc',  'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_svc_faqs',         'label' => 'Map Related FAQs',      'name' => 'svc_faqs',         'type' => 'post_object', 'post_type' => [ 'jjwz_faq' ], 'allow_null' => 1, 'multiple' => 1, 'return_format' => 'id' ],
            [ 'key' => 'field_svc_featured',     'label' => 'Featured on Home',      'name' => 'svc_featured',     'type' => 'true_false', 'ui' => 1 ],
            [ 'key' => 'field_svc_display_order','label' => 'Display Order',         'name' => 'svc_display_order','type' => 'number', 'default_value' => 0 ],
            [ 'key' => 'field_svc_seo_title',    'label' => 'Service SEO Title',     'name' => 'svc_seo_title',    'type' => 'text' ],
            [ 'key' => 'field_svc_seo_desc',     'label' => 'Service SEO Meta Desc', 'name' => 'svc_seo_desc',     'type' => 'textarea', 'rows' => 3 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_service' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       4. PORTFOLIO POST TYPE FIELDS (EXTENDED)
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_portfolio',
        'title'  => 'Portfolio Details',
        'fields' => [
            [ 'key' => 'field_portfolio_gallery',      'label' => 'Story Gallery',       'name' => 'portfolio_gallery',      'type' => 'gallery', 'return_format' => 'array' ],
            [ 'key' => 'field_portfolio_video_url',    'label' => 'Video Link URL',      'name' => 'portfolio_video_url',    'type' => 'url' ],
            [ 'key' => 'field_portfolio_short_desc',   'label' => 'Short Description',   'name' => 'portfolio_short_desc',   'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_portfolio_full_story',   'label' => 'Full Editorial Story','name' => 'portfolio_full_story',   'type' => 'wysiwyg' ],
            [ 'key' => 'field_portfolio_venue',        'label' => 'Venue Name',          'name' => 'portfolio_venue',        'type' => 'text' ],
            [ 'key' => 'field_portfolio_photographer', 'label' => 'Photographer Creds',  'name' => 'portfolio_photographer', 'type' => 'text' ],
            [ 'key' => 'field_portfolio_editor',       'label' => 'Editor Name',         'name' => 'portfolio_editor',       'type' => 'text' ],
            [ 'key' => 'field_portfolio_shoot_date',   'label' => 'Shoot Date',          'name' => 'portfolio_shoot_date',   'type' => 'date_picker', 'return_format' => 'Y-m-d' ],
            [ 'key' => 'field_portfolio_client_name',  'label' => 'Client Names',        'name' => 'portfolio_client_name',  'type' => 'text' ],
            [ 'key' => 'field_portfolio_city',         'label' => 'City Location',       'name' => 'portfolio_city',         'type' => 'text' ],
            [ 'key' => 'field_portfolio_album_included','label' => 'Album Included',      'name' => 'portfolio_album_included','type' => 'true_false', 'ui' => 1 ],
            [ 'key' => 'field_portfolio_video_included','label' => 'Video Included',      'name' => 'portfolio_video_included','type' => 'true_false', 'ui' => 1 ],
            [ 'key' => 'field_portfolio_status',       'label' => 'Production Status',   'name' => 'portfolio_status',       'type' => 'select', 'choices' => [ 'scheduled' => 'Scheduled', 'shot' => 'Shot', 'edited' => 'Edited', 'delivered' => 'Delivered' ], 'default_value' => 'delivered' ],
            [ 'key' => 'field_portfolio_featured',     'label' => 'Featured Story',      'name' => 'portfolio_featured',     'type' => 'true_false', 'ui' => 1 ],
            [ 'key' => 'field_portfolio_display_order','label' => 'Display Order',       'name' => 'portfolio_display_order','type' => 'number', 'default_value' => 0 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_portfolio' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       5. FILMS POST TYPE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_film',
        'title'  => 'Film Details',
        'fields' => [
            [ 'key' => 'field_film_youtube_url', 'label' => 'YouTube Video Link',   'name' => 'film_youtube_url', 'type' => 'url' ],
            [ 'key' => 'field_film_vimeo_url',   'label' => 'Vimeo Video Link',     'name' => 'film_vimeo_url',   'type' => 'url' ],
            [ 'key' => 'field_film_description', 'label' => 'Film Description',     'name' => 'film_description', 'type' => 'textarea', 'rows' => 4 ],
            [ 'key' => 'field_film_featured',    'label' => 'Featured Film',        'name' => 'film_featured',    'type' => 'true_false', 'ui' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_film' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       6. PACKAGE POST TYPE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_package',
        'title'  => 'Package Details',
        'fields' => [
            [ 'key' => 'field_package_category',          'label' => 'Package Category',     'name' => 'package_category',          'type' => 'select', 'choices' => [ 'wedding' => 'Wedding', 'pre-wedding' => 'Pre-Wedding', 'maternity' => 'Maternity', 'newborn' => 'Newborn', 'baby' => 'Baby Shoot', 'cake-smash' => 'Cake Smash', 'birthday' => 'Birthday', 'anniversary' => 'Anniversary', 'family' => 'Family' ] ],
            [ 'key' => 'field_package_price',             'label' => 'Price Label',          'name' => 'package_price',             'type' => 'text', 'placeholder' => '₹75,000' ],
            [ 'key' => 'field_package_description',       'label' => 'Package Description',  'name' => 'package_description',       'type' => 'textarea', 'rows' => 4 ],
            [ 'key' => 'field_package_features',          'label' => 'Features list',        'name' => 'package_features',          'type' => 'textarea', 'placeholder' => '1 dynamic item per line' ],
            [ 'key' => 'field_package_album_included',     'label' => 'Physical Album Include','name' => 'package_album_included',     'type' => 'true_false', 'ui' => 1 ],
            [ 'key' => 'field_package_delivery_timeline', 'label' => 'Delivery Timeline',    'name' => 'package_delivery_timeline', 'type' => 'text', 'placeholder' => '6-8 weeks' ],
            [ 'key' => 'field_package_featured',          'label' => 'Featured Package',     'name' => 'package_featured',          'type' => 'true_false', 'ui' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_package' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       7. TEAM POST TYPE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_team',
        'title'  => 'Team Member Details',
        'fields' => [
            [ 'key' => 'field_team_designation', 'label' => 'Designation',     'name' => 'team_designation', 'type' => 'text', 'placeholder' => 'Lead Cinematographer' ],
            [ 'key' => 'field_team_bio',         'label' => 'Full Bio',        'name' => 'team_bio',         'type' => 'wysiwyg' ],
            [ 'key' => 'field_team_instagram',   'label' => 'Instagram Link',  'name' => 'team_instagram',   'type' => 'url' ],
            [ 'key' => 'field_team_email',       'label' => 'Contact Email',   'name' => 'team_email',       'type' => 'email' ],
            [ 'key' => 'field_team_phone',       'label' => 'Contact Phone',   'name' => 'team_phone',       'type' => 'text' ],
            [ 'key' => 'field_team_experience',  'label' => 'Years Experience','name' => 'team_experience',  'type' => 'text', 'placeholder' => '6 Years' ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_team' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       8. TESTIMONIAL POST TYPE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_testimonial',
        'title'  => 'Testimonial Details',
        'fields' => [
            [ 'key' => 'field_testimonial_service',   'label' => 'Service Name',    'name' => 'testimonial_service',   'type' => 'text', 'placeholder' => 'Wedding Photography' ],
            [ 'key' => 'field_testimonial_location',  'label' => 'Couple Location', 'name' => 'testimonial_location',  'type' => 'text', 'placeholder' => 'Delhi' ],
            [ 'key' => 'field_testimonial_rating',    'label' => 'Rating (Stars)',  'name' => 'testimonial_rating',    'type' => 'select', 'choices' => [ '1' => '1 Star', '2' => '2 Stars', '3' => '3 Stars', '4' => '4 Stars', '5' => '5 Stars' ], 'default_value' => '5' ],
            [ 'key' => 'field_testimonial_review',    'label' => 'Review Narrative','name' => 'testimonial_review',    'type' => 'textarea', 'rows' => 5 ],
            [ 'key' => 'field_testimonial_video_url', 'label' => 'Video Review URL','name' => 'testimonial_video_url', 'type' => 'url' ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_testimonial' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       9. LOCATIONS POST TYPE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_location',
        'title'  => 'Location Hub Details',
        'fields' => [
            [ 'key' => 'field_location_hero_image',  'label' => 'Hero Banner Image',   'name' => 'location_hero_image',  'type' => 'image', 'return_format' => 'array' ],
            [ 'key' => 'field_location_description', 'label' => 'Studio Description',  'name' => 'location_description', 'type' => 'wysiwyg' ],
            [ 'key' => 'field_location_seo_content',  'label' => 'SEO Editorial Text',  'name' => 'location_seo_content',  'type' => 'wysiwyg' ],
            [ 'key' => 'field_location_google_map',  'label' => 'Google Maps Embed iframe','name' => 'location_google_map',  'type' => 'textarea' ],
            [ 'key' => 'field_location_featured',    'label' => 'Featured Location',   'name' => 'location_featured',    'type' => 'true_false', 'ui' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_location' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       10. HOMEPAGE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_homepage',
        'title'  => 'Homepage Content',
        'fields' => [
            [ 'key' => 'field_jjwz_hero_headline',    'label' => 'Hero Headline',           'name' => 'jjwz_hero_headline',    'type' => 'text' ],
            [ 'key' => 'field_jjwz_hero_subheadline', 'label' => 'Hero Sub-headline',       'name' => 'jjwz_hero_subheadline', 'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_jjwz_hero_bg_type',     'label' => 'Hero Background Type',    'name' => 'jjwz_hero_bg_type',     'type' => 'select', 'choices' => [ 'image' => 'Image', 'video' => 'HTML5 Video', 'youtube' => 'YouTube Video' ], 'default_value' => 'image' ],
            [ 'key' => 'field_jjwz_hero_bg_image',    'label' => 'Hero Background Image',   'name' => 'jjwz_hero_bg_image',    'type' => 'image', 'return_format' => 'array', 'conditional_logic' => [ [ [ 'field' => 'field_jjwz_hero_bg_type', 'operator' => '==', 'value' => 'image' ] ] ] ],
            [ 'key' => 'field_jjwz_hero_bg_video',    'label' => 'Hero Background Video',   'name' => 'jjwz_hero_bg_video',    'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => [ [ [ 'field' => 'field_jjwz_hero_bg_type', 'operator' => '==', 'value' => 'video' ] ] ] ],
            [ 'key' => 'field_jjwz_hero_youtube_id',  'label' => 'Hero YouTube Video ID',   'name' => 'jjwz_hero_youtube_id',  'type' => 'text', 'conditional_logic' => [ [ [ 'field' => 'field_jjwz_hero_bg_type', 'operator' => '==', 'value' => 'youtube' ] ] ] ],
            [ 'key' => 'field_jjwz_value_prop',       'label' => 'Value Proposition Text',  'name' => 'jjwz_value_prop',       'type' => 'wysiwyg' ],
        ],
        'location' => [ [ [ 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       11. FAQ POST TYPE FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_faq',
        'title'  => 'FAQ Content',
        'fields' => [
            [ 'key' => 'field_faq_question', 'label' => 'FAQ Question', 'name' => 'faq_question', 'type' => 'text', 'required' => 1 ],
            [ 'key' => 'field_faq_answer',   'label' => 'FAQ Answer',   'name' => 'faq_answer',   'type' => 'wysiwyg', 'required' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_faq' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       12. CLIENT GALLERY FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_gallery_page',
        'title'  => 'Client Gallery Settings',
        'fields' => [
            [ 'key' => 'field_gallery_access_key',   'label' => 'Gallery Access Key',  'name' => 'gallery_access_key',   'type' => 'text', 'required' => 1 ],
            [ 'key' => 'field_gallery_client_name',  'label' => 'Client Name',         'name' => 'gallery_client_name',  'type' => 'text' ],
            [ 'key' => 'field_gallery_event_date',   'label' => 'Event Date',          'name' => 'gallery_event_date',   'type' => 'date_picker', 'return_format' => 'F j, Y' ],
            [ 'key' => 'field_gallery_images',       'label' => 'Gallery Images',      'name' => 'gallery_images',       'type' => 'gallery', 'return_format' => 'array' ],
            [ 'key' => 'field_gallery_enable_dl',    'label' => 'Enable Downloads',    'name' => 'gallery_enable_dl',    'type' => 'true_false', 'default_value' => 1, 'ui' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'jjwz_gallery' ] ] ],
    ] );

    /* ─────────────────────────────────────────────────────────────────────
       13. BLOG POST SEO FIELDS
       ───────────────────────────────────────────────────────────────────── */
    acf_add_local_field_group( [
        'key'    => 'group_jjwz_blog',
        'title'  => 'SEO & Blog Meta',
        'fields' => [
            [ 'key' => 'field_blog_focus_keyword', 'label' => 'Focus Keyword',       'name' => 'blog_focus_keyword', 'type' => 'text' ],
            [ 'key' => 'field_blog_reading_time',  'label' => 'Custom Reading Time', 'name' => 'blog_reading_time',  'type' => 'text' ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ] ] ],
    ] );
}
