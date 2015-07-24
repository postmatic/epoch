<?php
/**
 * Add hooks for internal API.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2015 Transitive, Inc.
 */

namespace postmatic\epoch\front;


class end_points {

	/**
	 * Constructor for this class.
	 *
	 * Adds our API endpoint and hooks it in at template_redirect
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
	}

	/**
	 * Add endpoints for the API
	 *
	 * @uses 0.0.6
	 *
	 * @uses "init" action
	 */
	public function add_endpoints() {

		//add "action" as a rewrite tag
		add_rewrite_tag( '%action%', '^[a-z0-9_\-]+$' );

		//add the endpoint
		$endpoint = vars::$endpoint;
		add_rewrite_rule( "{$endpoint}/^[a-z0-9_\-]+$/?", 'index.php?action=$matches[1]', 'top' );
		// add template endpoint
		add_rewrite_endpoint( 'epoch', EP_PERMALINK | EP_PAGES );
	}
}
