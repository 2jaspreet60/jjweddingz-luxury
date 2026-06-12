<?php
/**
 * elementor-dynamic-tags.php — Custom Dynamic Tags for Elementor
 *
 * Provides dynamic tags in Elementor for JJ WeddingZ options.
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Elementor_Option_Tag extends \Elementor\Core\DynamicTags\Tag {

    /**
     * Unique tag name.
     */
    public function get_name() {
        return 'jjwz-option-tag';
    }

    /**
     * User-visible title.
     */
    public function get_title() {
        return esc_html__( 'JJ Options Field', 'jjweddingz' );
    }

    /**
     * Group category in tag dropdown (e.g., site, post, archive).
     */
    public function get_group() {
        return 'site';
    }

    /**
     * Categories where this tag is available.
     */
    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
        ];
    }

    /**
     * Dynamic tag settings controls.
     */
    protected function register_controls() {
        $this->add_control(
            'option_key',
            [
                'label'   => esc_html__( 'Option Field', 'jjweddingz' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'jjwz_header_phone'     => esc_html__( 'Header Phone', 'jjweddingz' ),
                    'jjwz_footer_phone'     => esc_html__( 'Footer Phone', 'jjweddingz' ),
                    'jjwz_whatsapp_number'  => esc_html__( 'WhatsApp Number', 'jjweddingz' ),
                    'jjwz_copyright_text'   => esc_html__( 'Copyright Text', 'jjweddingz' ),
                    'jjwz_social_instagram' => esc_html__( 'Instagram URL', 'jjweddingz' ),
                    'jjwz_social_facebook'  => esc_html__( 'Facebook URL', 'jjweddingz' ),
                    'jjwz_social_youtube'   => esc_html__( 'YouTube URL', 'jjweddingz' ),
                ],
                'default' => 'jjwz_whatsapp_number',
            ]
        );
    }

    /**
     * Render the tag output on the front-end.
     */
    public function render() {
        $key = $this->get_settings( 'option_key' );
        if ( ! $key ) { return; }

        $value = jjwz_get_option( $key );
        echo esc_html( $value );
    }
}

// Register the dynamic tag
\Elementor\Plugin::$instance->dynamic_tags->register( new JJWZ_Elementor_Option_Tag() );
