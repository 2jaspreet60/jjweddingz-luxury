<?php
/**
 * class-image-processor.php — WebP Conversion + Dynamic Watermark Engine
 *
 * Hooks into wp_handle_upload to:
 * 1. Resize uploaded images to max 2560px on longest edge
 * 2. Convert to WebP at 78% quality
 * 3. Apply custom watermark text based on settings
 *
 * @package JJW_Core
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JJWZ_Image_Processor {

    /** Max longest-edge dimension (px) */
    const MAX_DIM = 2560;

    /** WebP compression quality (0–100) */
    const WEBP_QUALITY = 78;

    public function __construct() {
        add_filter( 'wp_handle_upload', [ $this, 'process_upload' ], 10, 2 );
        add_action( 'add_attachment', [ $this, 'process_attachment_on_add' ] );
    }

    /* ─── MAIN ENTRY POINT — wp_handle_upload filter ─────────────────────── */

    public function process_upload( array $upload, string $context ): array {
        if ( ! isset( $upload['type'] ) || strpos( $upload['type'], 'image/' ) !== 0 ) {
            return $upload;
        }

        $skip_types = [ 'image/svg+xml', 'image/gif', 'image/x-icon', 'image/vnd.microsoft.icon' ];
        if ( in_array( $upload['type'], $skip_types, true ) ) {
            return $upload;
        }

        $source_path = $upload['file'];
        if ( ! file_exists( $source_path ) ) { return $upload; }

        // Detect library and process
        if ( extension_loaded( 'gd' ) ) {
            $webp_path = $this->process_with_gd( $source_path, $upload['type'] );
        } elseif ( extension_loaded( 'imagick' ) ) {
            $webp_path = $this->process_with_imagick( $source_path );
        } else {
            error_log( 'JJWZ Image Processor: Neither GD nor Imagick extension is loaded.' );
            return $upload;
        }

        if ( $webp_path && file_exists( $webp_path ) ) {
            $upload['file'] = $webp_path;
            $upload['url']  = str_replace(
                wp_basename( $source_path ),
                wp_basename( $webp_path ),
                $upload['url']
            );
            $upload['type'] = 'image/webp';

            if ( $source_path !== $webp_path ) {
                @unlink( $source_path );
            }
        }

        return $upload;
    }

    /* ─── GD PROCESSING ──────────────────────────────────────────────────── */

    private function process_with_gd( string $source_path, string $mime_type ): ?string {
        $img = match( $mime_type ) {
            'image/jpeg' => @imagecreatefromjpeg( $source_path ),
            'image/png'  => @imagecreatefrompng( $source_path ),
            'image/webp' => @imagecreatefromwebp( $source_path ),
            default      => null,
        };

        if ( ! $img ) { return null; }

        // Fix EXIF orientation for JPEG
        if ( $mime_type === 'image/jpeg' && function_exists( 'exif_read_data' ) ) {
            $exif = @exif_read_data( $source_path );
            if ( ! empty( $exif['Orientation'] ) ) {
                $img = $this->fix_orientation_gd( $img, $exif['Orientation'] );
            }
        }

        // Resize
        $img = $this->resize_gd( $img, self::MAX_DIM );

        // Apply dynamic watermark
        $img = $this->apply_watermark_gd( $img );

        // Save as WebP
        $webp_path = preg_replace( '/\.(jpe?g|png|webp|bmp|tiff?)$/i', '.webp', $source_path );
        $saved = imagewebp( $img, $webp_path, self::WEBP_QUALITY );
        imagedestroy( $img );

        return $saved ? $webp_path : null;
    }

    private function resize_gd( \GdImage $img, int $max ): \GdImage {
        $w = imagesx( $img );
        $h = imagesy( $img );
        $longest = max( $w, $h );

        if ( $longest <= $max ) { return $img; }

        $ratio = $max / $longest;
        $new_w = (int) round( $w * $ratio );
        $new_h = (int) round( $h * $ratio );

        $resized = imagecreatetruecolor( $new_w, $new_h );

        imagealphablending( $resized, false );
        imagesavealpha( $resized, true );
        $transparent = imagecolorallocatealpha( $resized, 0, 0, 0, 127 );
        imagefilledrectangle( $resized, 0, 0, $new_w, $new_h, $transparent );
        imagealphablending( $resized, true );

        imagecopyresampled( $resized, $img, 0, 0, 0, 0, $new_w, $new_h, $w, $h );
        imagedestroy( $img );

        return $resized;
    }

    private function apply_watermark_gd( \GdImage $img ): \GdImage {
        $enable = get_option( 'jjw_watermark_enable', '0' );
        if ( ! $enable ) { return $img; }

        $text     = get_option( 'jjw_watermark_text', '© JJ WeddingZ Photography' );
        $opacity  = floatval( get_option( 'jjw_watermark_opacity', '0.15' ) );
        $position = get_option( 'jjw_watermark_position', 'bottom-right' );

        $w = imagesx( $img );
        $h = imagesy( $img );

        $font_size = max( 10, (int) ( $w * 0.018 ) );
        $margin    = (int) ( $w * 0.012 );
        $font_path = JJWZ_CORE_DIR . 'assets/fonts/Inter-Regular.ttf';

        if ( function_exists( 'imagettfbbox' ) && file_exists( $font_path ) ) {
            $bbox   = imagettfbbox( $font_size, 0, $font_path, $text );
            $text_w = abs( $bbox[4] - $bbox[0] );
            $text_h = abs( $bbox[5] - $bbox[1] );

            // Calculate coordinates based on position
            switch ( $position ) {
                case 'bottom-left':
                    $x = $margin;
                    $y = $h - $margin;
                    break;
                case 'top-right':
                    $x = $w - $text_w - $margin;
                    $y = $margin + $text_h;
                    break;
                case 'top-left':
                    $x = $margin;
                    $y = $margin + $text_h;
                    break;
                case 'center':
                    $x = ( $w - $text_w ) / 2;
                    $y = ( $h + $text_h ) / 2;
                    break;
                case 'bottom-right':
                default:
                    $x = $w - $text_w - $margin;
                    $y = $h - $margin;
                    break;
            }

            $alpha        = (int) round( 127 * ( 1 - $opacity ) );
            $wm_color     = imagecolorallocatealpha( $img, 255, 255, 255, $alpha );
            $shadow_color = imagecolorallocatealpha( $img, 0, 0, 0, $alpha );

            // Draw text shadow
            imagettftext( $img, $font_size, 0, $x + 1, $y + 1, $shadow_color, $font_path, $text );
            imagettftext( $img, $font_size, 0, $x,     $y,     $wm_color,    $font_path, $text );

        } else {
            $font   = 3;
            $char_w = imagefontwidth( $font );
            $char_h = imagefontheight( $font );
            $text_w = strlen( $text ) * $char_w;

            switch ( $position ) {
                case 'bottom-left':
                    $x = $margin;
                    $y = $h - $char_h - $margin;
                    break;
                case 'top-right':
                    $x = $w - $text_w - $margin;
                    $y = $margin;
                    break;
                case 'top-left':
                    $x = $margin;
                    $y = $margin;
                    break;
                case 'center':
                    $x = ( $w - $text_w ) / 2;
                    $y = ( $h - $char_h ) / 2;
                    break;
                case 'bottom-right':
                default:
                    $x = $w - $text_w - $margin;
                    $y = $h - $char_h - $margin;
                    break;
            }

            $alpha    = (int) round( 127 * ( 1 - $opacity ) );
            $wm_color = imagecolorallocatealpha( $img, 255, 255, 255, $alpha );
            imagestring( $img, $font, $x, $y, $text, $wm_color );
        }

        return $img;
    }

    private function fix_orientation_gd( \GdImage $img, int $orientation ): \GdImage {
        return match( $orientation ) {
            3       => imagerotate( $img, 180, 0 ),
            6       => imagerotate( $img, -90, 0 ),
            8       => imagerotate( $img, 90, 0 ),
            default => $img,
        };
    }

    /* ─── IMAGICK PROCESSING ─────────────────────────────────────────────── */

    private function process_with_imagick( string $source_path ): ?string {
        try {
            $imagick = new \Imagick( $source_path );
            $imagick->setImageFormat( 'webp' );
            $imagick->autoOrientImage();
            $imagick->stripImage();

            $w       = $imagick->getImageWidth();
            $h       = $imagick->getImageHeight();
            $longest = max( $w, $h );

            if ( $longest > self::MAX_DIM ) {
                $ratio = self::MAX_DIM / $longest;
                $imagick->resizeImage(
                    (int) round( $w * $ratio ),
                    (int) round( $h * $ratio ),
                    \Imagick::FILTER_LANCZOS,
                    1
                );
            }

            $imagick->setImageCompressionQuality( self::WEBP_QUALITY );

            $this->apply_watermark_imagick( $imagick );

            $webp_path = preg_replace( '/\.(jpe?g|png|webp|bmp|tiff?)$/i', '.webp', $source_path );
            $imagick->writeImage( $webp_path );
            $imagick->destroy();

            return $webp_path;

        } catch ( \Exception $e ) {
            error_log( 'JJWZ Imagick error: ' . $e->getMessage() );
            return null;
        }
    }

    private function apply_watermark_imagick( \Imagick $img ): void {
        $enable = get_option( 'jjw_watermark_enable', '0' );
        if ( ! $enable ) { return; }

        $text     = get_option( 'jjw_watermark_text', '© JJ WeddingZ Photography' );
        $opacity  = floatval( get_option( 'jjw_watermark_opacity', '0.15' ) );
        $position = get_option( 'jjw_watermark_position', 'bottom-right' );

        try {
            $draw = new \ImagickDraw();
            $draw->setFont( 'Helvetica' );
            $draw->setFontSize( max( 14, (int) ( $img->getImageWidth() * 0.018 ) ) );
            $draw->setFillColor( new \ImagickPixel( 'rgba(255,255,255,' . $opacity . ')' ) );

            $metrics = $img->queryFontMetrics( $draw, $text );
            $text_w  = (int) $metrics['textWidth'];
            $text_h  = (int) $metrics['textHeight'];
            $margin  = (int) ( $img->getImageWidth() * 0.012 );

            $img_w = $img->getImageWidth();
            $img_h = $img->getImageHeight();

            switch ( $position ) {
                case 'bottom-left':
                    $x = $margin;
                    $y = $img_h - $margin;
                    break;
                case 'top-right':
                    $x = $img_w - $text_w - $margin;
                    $y = $margin + $text_h;
                    break;
                case 'top-left':
                    $x = $margin;
                    $y = $margin + $text_h;
                    break;
                case 'center':
                    $x = ( $img_w - $text_w ) / 2;
                    $y = ( $img_h + $text_h ) / 2;
                    break;
                case 'bottom-right':
                default:
                    $x = $img_w - $text_w - $margin;
                    $y = $img_h - $margin;
                    break;
            }

            $img->annotateImage( $draw, $x, $y, 0, $text );
            $draw->destroy();
        } catch ( \Exception $e ) {
            error_log( 'JJWZ Watermark (Imagick) error: ' . $e->getMessage() );
        }
    }

    /* ─── FALLBACK: process on add_attachment ────────────────────────────── */

    public function process_attachment_on_add( int $attachment_id ): void {
        $mime = get_post_mime_type( $attachment_id );
        if ( $mime === 'image/webp' ) { return; }

        $file = get_attached_file( $attachment_id );
        if ( ! $file || ! file_exists( $file ) ) { return; }

        $fake_upload = [
            'file' => $file,
            'url'  => wp_get_attachment_url( $attachment_id ),
            'type' => $mime,
        ];

        $processed = $this->process_upload( $fake_upload, 'upload' );

        if ( $processed['file'] !== $file ) {
            update_attached_file( $attachment_id, $processed['file'] );
            wp_update_post( [
                'ID'             => $attachment_id,
                'post_mime_type' => 'image/webp',
            ] );
        }
    }
}
