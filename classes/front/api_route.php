<?php
/**
 * Routes request to internal API
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */
namespace postmatic\epoch\front;


class api_route {

	/**
	 * Nonce key for requests
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	public $api_nonce_key;

	/**
	 * Constructor for class
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'do_api' ), 99 );
		$this->api_nonce_key = vars::$nonce_field;
	}

	/**
	 * Route and respond to request if valid
	 *
	 * @since 0.0.1
	 */
	public function do_api(  ) {
		if (
			! isset( $_REQUEST[ $this->api_nonce_key ] )
			|| ! $_REQUEST[ 'action' ]
			|| ! $this->verify_nonce()

		) {
			return;
		}else {
			$action = strip_tags( trim($_REQUEST[ 'action' ] ) );
			$response = self::route( $action );
			if ( ! $response ) {
				wp_send_json_error();
			}else{
				wp_send_json_success( $response );
			}

		}
		
	}

	/**
	 * Die handler filter
	 *
	 * @since 0.2.4
	 *
	 * @return array
	 */
	public function return_die_handler() {
		return array( $this, 'die_handler' );

	}

	/**
	 * Handle wp_die() calls
	 *
	 * @since 0.2.4
	 *
	 * @param string $message Message to send.
	 * @param string $title Title for message
	 * @param array $args Arguments from request
	 */
	public function die_handler( $message, $title, $args ) {
		status_header( $args['response'] );
		wp_send_json_error( compact( 'message' ) );
		die();

	}


	/**
	 * Route request to callback in api_process class
	 *
	 * @since 0.0.1
	 *
	 * @param string $action Action to take (IE name of method in api_process class.
	 *
	 * @return bool|array
	 */
	protected function route( $action ) {

		if ( method_exists( '\postmatic\epoch\front\api_process', $action )  ) {

			add_filter( 'wp_die_handler', array( $this, 'return_die_handler' ) );

			$data = $this->data( $action );

			$response = \postmatic\epoch\front\api_process::$action( $data );
			return $response;
		}else{
			return false;
		}

	}

	/**
	 * Get data from request and sanitize.
	 *
	 * NOTE: submit_comment intentionally skips sanitization (for now)
	 *
	 * @since 0.0.1
	 *
	 * @param string $action Current action.
	 *
	 * @return array
	 */
	protected function data( $action ) {
		$data = array();

		if( isset( $_GET[ 'epochModal' ] ) && $_GET[ 'epochModal' ] ) {
			return $_GET;
		}

		if ( 'submit_comment' !== $action ) {
			$data_fields = $this->get_fields( $action );
			if (
				isset( $_POST[ 'i' ] )
				&& is_array( $_POST[ 'i'] )
			) {
				foreach( $_POST[ 'i'] as $id ) {
					$data[ 'ignore' ][] = absint( $id );
				}

			}

			foreach( $data_fields as $field => $cb ) {

				if ( isset( $_POST[ $field ] ) ) {
					$data[ $field ] = call_user_func( $cb, $_POST[ $field ] );
				}else{
					$data[ $field ] = null;
				}
			}
		}else{
			$data = $_POST;
		}

		return $data;
	}

	/**
	 * Fields to get from a GET request and how to sanatize them
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @param string $action Current action.
	 *
	 * @return array
	 */
	protected function get_fields( $action ) {
		$fields = array(
			'postID' => 'absint',
			'commentsPage' => 'absint',
			vars::$nonce_field => 'strip_tags',
			'highest' => 'absint'
		);


		if ( 'comments_open' == $action ) {
			$fields = array(
				'postID' => 'absint'
			);

		}

		if ( 'get_comment' == $action ) {
			$fields[ 'commentID' ] = 'absint';
		}

		return $fields;
	}

	/**
	 * Verify nonce
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @return false|int
	 */
	protected function verify_nonce() {

		if( isset( $_POST[ 'postID' ] ) ) {
			$id = $_POST[ 'postID' ];
		}elseif( isset( $_POST[  'comment_post_ID' ] ) ) {
			$id = $_POST[  'comment_post_ID' ];
		}else{
			return false;
		}

		$valid =  wp_verify_nonce( $_REQUEST[ $this->api_nonce_key ], $this->api_nonce_key . (int) $id );

		return $valid;

	}


}
