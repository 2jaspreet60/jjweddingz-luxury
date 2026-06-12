<?php
/**
 * class-video-block.php — Dynamic Video Shortcode [jjwz_video]
 *
 * Implements a high-performance video shortcode for page builder elements
 * and content fields, supporting aspect-ratio containers, cookie-free embeds (YouTube),
 * privacy-enhanced player settings (Vimeo), and lazy-loading local HTML5 players.
 *
 * @package JJWeddingZ_Core
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Video_Block {

    /**
     * Constructor registers the shortcode.
     */
    public function __construct() {
        add_shortcode( 'jjwz_video', [ $this, 'render_video' ] );
    }

    /**
     * Render the shortcode.
     *
     * [jjwz_video type="youtube|vimeo|local" src="URL" poster="URL" aspect="16-9|9-16" autoplay="true|false"]
     */
    public function render_video( $atts ) {
        $a = shortcode_atts( [
            'type'     => 'local',     // local, youtube, vimeo
            'src'      => '',
            'poster'   => '',
            'aspect'   => '16-9',      // 16-9, 9-16
            'autoplay' => 'false',
            'loop'     => 'true',
            'controls' => 'true',
            'mute'     => 'true',
            'class'    => '',
        ], $atts );

        if ( empty( $a['src'] ) ) {
            return '<!-- JJWZ Video Shortcode: Missing src attribute -->';
        }

        $aspect_class = 'ratio-' . $a['aspect'];
        $autoplay     = filter_var( $a['autoplay'], FILTER_VALIDATE_BOOLEAN );
        $loop         = filter_var( $a['loop'], FILTER_VALIDATE_BOOLEAN );
        $controls     = filter_var( $a['controls'], FILTER_VALIDATE_BOOLEAN );
        $mute         = filter_var( $a['mute'], FILTER_VALIDATE_BOOLEAN );
        $extra_class  = esc_attr( $a['class'] );

        ob_start();
        ?>
        <div class="jjwz-video-wrapper <?php echo esc_attr( $aspect_class . ' ' . $extra_class ); ?>">
            <?php if ( $a['type'] === 'youtube' ) :
                // Parse video ID from URL
                preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $a['src'], $match );
                $video_id = ! empty( $match[1] ) ? $match[1] : $a['src'];
                $embed_url = 'https://www.youtube-nocookie.com/embed/' . esc_attr( $video_id ) . '?autoplay=' . ( $autoplay ? '1' : '0' ) . '&mute=' . ( $mute ? '1' : '0' ) . '&loop=' . ( $loop ? '1' : '0' ) . '&playlist=' . esc_attr( $video_id ) . '&controls=' . ( $controls ? '1' : '0' ) . '&rel=0';
                ?>
                <iframe src="about:blank" data-src="<?php echo esc_url( $embed_url ); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="lazy-video" loading="lazy"></iframe>

            <?php elseif ( $a['type'] === 'vimeo' ) :
                // Parse video ID from Vimeo URL
                preg_match( '/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/', $a['src'], $match );
                $video_id = ! empty( $match[3] ) ? $match[3] : $a['src'];
                $embed_url = 'https://player.vimeo.com/video/' . esc_attr( $video_id ) . '?autoplay=' . ( $autoplay ? '1' : '0' ) . '&muted=' . ( $mute ? '1' : '0' ) . '&loop=' . ( $loop ? '1' : '0' ) . '&dnt=1';
                ?>
                <iframe src="about:blank" data-src="<?php echo esc_url( $embed_url ); ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen class="lazy-video" loading="lazy"></iframe>

            <?php else :
                // Local HTML5 video (autoplay requires muted in modern browsers)
                $video_attrs = [];
                if ( $autoplay ) { $video_attrs[] = 'autoplay'; }
                if ( $loop ) { $video_attrs[] = 'loop'; }
                if ( $controls ) { $video_attrs[] = 'controls'; }
                if ( $mute || $autoplay ) { $video_attrs[] = 'muted'; }
                $video_attrs[] = 'playsinline';
                $video_attrs[] = 'preload="none"';
                if ( ! empty( $a['poster'] ) ) { $video_attrs[] = 'poster="' . esc_url( $a['poster'] ) . '"'; }
                ?>
                <video class="lazy-video jjwz-html5-video" <?php echo implode( ' ', $video_attrs ); ?>>
                    <source data-src="<?php echo esc_url( $a['src'] ); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
