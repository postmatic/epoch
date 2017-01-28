<?php
if ( ! defined( 'ABSPATH' ) || ! isset( $comments ) || ! is_array( $comments ) ) {
	return;
}
?>

<ol class="comment-list" id="epoch-comment-list">
	<?php
		wp_list_comments( array(
			'style'       => 'ol',
			'short_ping'  => true,
			'avatar_size' => 56,
		), $comments );
	?>
</ol>
