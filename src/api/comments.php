<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace postmatic\epoch\two\api;



use postmatic\epoch\two\epoch;
use postmatic\epoch\two\thread;

class comments {

	protected $count;


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

	public function get_item_permissions_check( \WP_REST_Request $request ) {
		$valid = wp_verify_nonce( $request[ 'nonce' ] );
		return apply_filters( 'epoch_api_request_alloed', $valid  );

	}
	
	public function get_item( \WP_REST_Request $request ){
		$post_id = $request[ 'id'];
		$page = $request[ 'page' ];
		$comments = \postmatic\epoch\two\comments::get_comments( $post_id, $request[ 'page' ]  );
		$this->count = \postmatic\epoch\two\comments::get_comment_count( $request[ 'id'] );

		ob_start();
		include EPOCH_DIR . '/assets/templates/comment-list.php';
		$template = ob_get_clean();

		$response = new \WP_REST_Response( array( 'template' => $template ) );
		$response->header( 'X-WP-EPOCH-TOTAL-COMMENTS', $this->count  );
		$response->header( 'X-WP-EPOCH-NEXT', $this->page_link( $post_id, $page + 1 ) );
		$response->header( 'X-WP-EPOCH-PREVIOUS', $this->page_link( $post_id, $page - 1 ) );
		$response->header( 'X-WP-EPOCH-VERSION', EPOCH_VERSION );
		return $response;
		
	}

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

	protected function page_link( $post_id, $page ){
		if( 0 == $page || $page > $this->count ){
			return 0;
		}

		return epoch::get_instance()->comment_api_link( $post_id, $page );
	}


}
