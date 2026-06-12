<?php
/**
 * comments.php — Post Comments Template
 *
 * Renders comments list and comment form with custom markup for luxury design.
 *
 * @package JJWeddingZ
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// If post is password protected, return early
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title" style="font-family: var(--font-display); font-size: var(--text-2xl); margin-bottom: var(--sp-xl);">
            <?php
            $comments_number = get_comments_number();
            if ( 1 === $comments_number ) {
                printf( esc_html__( 'One Comment', 'jjweddingz' ) );
            } else {
                printf(
                    /* translators: %s: comment count number */
                    esc_html( _n( '%s Comment', '%s Comments', $comments_number, 'jjweddingz' ) ),
                    number_format_i18n( $comments_number )
                );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( [
                'avatar_size' => 50,
                'style'       => 'ol',
                'short_ping'  => true,
                'reply_text'  => esc_html__( 'Reply', 'jjweddingz' ),
            ] );
            ?>
        </ol>

        <?php
        the_comments_navigation( [
            'prev_text' => esc_html__( '← Older Comments', 'jjweddingz' ),
            'next_text' => esc_html__( 'Newer Comments →', 'jjweddingz' ),
        ] );
        ?>

        <?php if ( ! comments_open() ) : ?>
            <p class="no-comments" style="color: var(--clr-mist); font-size: var(--text-sm); font-style: italic;">
                <?php esc_html_e( 'Comments are closed.', 'jjweddingz' ); ?>
            </p>
        <?php endif; ?>

    <?php endif; // have_comments() ?>

    <?php
    // Customise comment form inputs and layout
    $commenter = wp_get_current_commenter();
    $req       = get_option( 'require_name_email' );
    $html_req  = $req ? " required='required'" : '';

    comment_form( [
        'title_reply'        => esc_html__( 'Leave a Reply', 'jjweddingz' ),
        'title_reply_to'     => esc_html__( 'Leave a Reply to %s', 'jjweddingz' ),
        'cancel_reply_link'  => esc_html__( 'Cancel Reply', 'jjweddingz' ),
        'label_submit'       => esc_html__( 'Post Comment', 'jjweddingz' ),
        'class_submit'       => 'btn btn--primary',
        'submit_button'      => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
        'submit_field'       => '<div class="form-submit" style="grid-column: span 2; margin-top: var(--sp-md);">%1$s %2$s</div>',
        'class_form'         => 'comment-form',
        'comment_field'      => '
            <div class="form-group comment-form-comment">
                <label for="comment" class="form-label">' . esc_html__( 'Comment', 'jjweddingz' ) . ' <span class="text-gold">*</span></label>
                <textarea id="comment" name="comment" cols="45" rows="8" required class="form-control" placeholder="' . esc_attr__( 'Share your thoughts on this story...', 'jjweddingz' ) . '"></textarea>
            </div>',
        'fields'             => [
            'author' => '
                <div class="form-group comment-form-author">
                    <label for="author" class="form-label">' . esc_html__( 'Name', 'jjweddingz' ) . ( $req ? ' <span class="text-gold">*</span>' : '' ) . '</label>
                    <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $html_req . ' class="form-control" placeholder="Your Name">
                </div>',
            'email'  => '
                <div class="form-group comment-form-email">
                    <label for="email" class="form-label">' . esc_html__( 'Email', 'jjweddingz' ) . ( $req ? ' <span class="text-gold">*</span>' : '' ) . '</label>
                    <input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . $html_req . ' class="form-control" placeholder="your@email.com">
                </div>',
        ],
    ] );
    ?>

</div><!-- #comments -->
