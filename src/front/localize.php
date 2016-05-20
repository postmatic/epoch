<?php
/**
 * Prepare strings to be localized
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */

namespace postmatic\epoch\two\front;

use postmatic\epoch\two\epoch;

class localize {

	/**
	 * Post object
	 *
	 * @since 2.0.0
	 *
	 * @var \WP_Post
	 */
	protected $post;

	/**
	 * localize constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post
	 */
	public function __construct( \WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * Get all the stuff to localize
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_vars() {
		return array_merge( $this->translation_strings(), $this->data() );
	}

	/**
	 * The translation strings to localize
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function translation_strings() {
		return array(
			'awaiting_moderation'   => esc_html__( 'Your comment is awaiting moderation.', 'epoch' ),
			'comment_link_title'    => esc_html__( 'Link to comment',  'epoch' ),
			'reply'                 => esc_html__( 'Reply', 'epoch' ),
			'reply_link_title'      => esc_html__( 'Reply To This Comment', 'epoch' ),
			'author_url_link_title' => esc_html__( 'Link to comment author\'s website', 'epoch' ),
			'is_required'           => esc_html__( 'is required', 'epoch' ),
			'pending'               => esc_html__( 'Comment Pending', 'epoch' ),
			'comment_rejected'      => esc_html__(
				'Your comment was not accepted, please check that everything is filled out correctly.',
				'epoch'
			),
		);
	}

	/**
	 * Data to localize that is not translation strings
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function data() {

		$data = array(
			'post'          => $this->post->ID,
			'api'           => esc_url_raw( epoch::get_instance()->api_url() ),
			'comments_core' => esc_url_raw( rest_url( 'wp/v2/comments' ) ),
			'first_url'     => esc_url_raw( epoch::get_instance()->comment_api_link( $this->post->ID, 1 ) ),
			'_wpnonce'      => wp_create_nonce( 'wp_rest' ),
			'user_email'    => 0,
			'nonce'         => epoch::get_instance()->get_epoch_nonce()
		);

		if ( 0 !== get_current_user_id() ) {
			$user                   = get_user_by( 'ID', get_current_user_id() );
			$data[ 'user_email' ]   = $user->user_email;
			$data[ 'user_url' ]     = $user->user_email;
			$data[ 'display_name' ] = $user->display_name;
		}

		return $data;

	}

}
