<?php
/**
 * API routes for getting pages or threads of comments as a rendered string
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */
namespace postmatic\epoch\two\api;



use postmatic\epoch\two\epoch;
use postmatic\epoch\two\thread;

class comments {
	/**
	 * Total number of comments
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	protected $count;

	/**
	 * Total number of pages
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	protected $pages;

	/**
	 * Create routes
	 *
	 * @since 2.0.0
	 */
	public function register_routes(){
		register_rest_route( epoch::get_instance()->api_namespace(), '/comments/(?P<id>[\d]+)', array(
			'methods'  => 'GET',
			'callback'        => array( $this, 'get_item' ),
			'permission_callback' => array( $this, 'get_item_permissions_check' ),
			'args'     => array(
				'nonce' => [
					'type' => 'string'
				],
				'page' => [
					'type' => 'integer',
					'default' => 1,
					'validation_callback' => 'absint'
				],
				'all' => [
					'type' => 'string',
					'default' => false,
				]
			),
		) );

		register_rest_route( epoch::get_instance()->api_namespace(), '/comments/threaded/(?P<id>[\d]+)', array(
			'methods'  => 'GET',
			'callback'        => array( $this, 'get_threaded' ),
			'permission_callback' => array( $this, 'get_item_permissions_check' ),
			'args'     => array(
				'nonce' => [
					'type' => 'string'
				]
			),
		) );
	}

	/**
	 * Permissions check for GET requests -- checks for validity of $_GET[ 'nonce' ] which means that _wpnonce MUST be sent as well.
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return false|int
	 */
	public function get_item_permissions_check( \WP_REST_Request $request ) {
		$valid = wp_verify_nonce( $request[ 'nonce' ] );
		return $valid;

	}

	/**
	 * Get one page of comments, fully rendered HTML
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( \WP_REST_Request $request ){
		$post_id = $request[ 'id'];
		$page = $request[ 'page' ];
		$options = epoch::get_instance()->get_options();
		$this->count = \postmatic\epoch\two\comments::get_comment_count( $request[ 'id'] );

		if( true ==  $request[ 'all' ] ){
			$comments = \postmatic\epoch\two\comments::get_comments( $post_id, $request[ 'page' ], $this->count, 0  );
		}else{
			$comments = \postmatic\epoch\two\comments::get_comments( $post_id, $request[ 'page' ]  );
		}



		$this->pages = ceil( $this->count / $options[ 'per_page' ] );

		ob_start();
		include EPOCH_DIR . '/assets/templates/comment-list.php';
		$template = ob_get_clean();

		$response = new \WP_REST_Response( array( 'template' => $template ) );
		$response->header( 'X-WP-EPOCH-TOTAL-COMMENTS', (int) $this->count  );
		$response->header( 'X-WP-EPOCH-TOTAL-PAGES', (int) $this->pages  );
		$response->header( 'X-WP-EPOCH-NEXT', $this->page_link( $post_id, $page + 1 ) );
		$response->header( 'X-WP-EPOCH-PREVIOUS', $this->page_link( $post_id, $page - 1 ) );
		$response->header( 'X-WP-EPOCH-VERSION', EPOCH_VERSION );
		return $response;
		
	}

	/**
	 * Get a thread of comments, fully rendered HTML
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_threaded( \WP_REST_Request $request ){
		$comment_id = $request[ 'id' ];
		$thread = new thread( $comment_id );
		$thread->collect();
		$comments = $thread->get_comments();
		ob_start();
		include EPOCH_DIR . '/assets/templates/comment-list.php';
		$template = ob_get_clean();

		$response = new \WP_REST_Response( array( 'template' => $template ) );
		$response->header( 'X-WP-EPOCH-VERSION', EPOCH_VERSION );
		return $response;

	}

	/**
	 * Get a pagination link
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id
	 * @param int $page
	 *
	 * @return int|string Link if there is a next/previous page 0 if not.
	 */
	protected function page_link( $post_id, $page ){
		if( 0 == $page || $page > $this->pages ){
			return 0;
		}

		return esc_url_raw( epoch::get_instance()->comment_api_link( $post_id, $page ) );
	}


}
