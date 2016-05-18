<?php

namespace postmatic\epoch\two\front;

use postmatic\epoch\two\epoch;

class localize {

	/**
	 * @var \WP_Post
	 */
	protected $post;

	public function __construct( \WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * @return array
	 */
	public function get_vars(){
		return array_merge( $this->translation_strings(), $this->data() );
	}

	protected function translation_strings() {
		return array(
			'awaiting_moderation' => __( 'Your comment is awaiting moderation.', 'epoch' ),
			'comment_link_title' => __( 'Link to comment' ),
			'reply' => __( 'Reply', 'epoch' ),
			'reply_link_title' => __( 'Reply To This Comment', 'epoch' ),
			'author_url_link_title' => __( 'Link to comment author\'s website', 'epoch' ),
			'is_required' => __( 'is required', 'epoch' ),
			'pending' => __( 'Comment Pending', 'epoch' ),
			'comment_rejected' => __(
				'Your comment was not accepted, please check that everything is filled out correctly.',
				'epoch'
			),
		);
	}


	protected function data(){

		return array(
			'post' => $this->post->ID,
			'api' => esc_url_raw( epoch::get_instance()->api_url() ),
			'first_url' => esc_url_raw( epoch::get_instance()->comment_api_link( $this->post->ID, 1 ) )
		);

	}

}
