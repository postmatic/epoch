<?php
/**
 * Create admin screen
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */
namespace postmatic\epoch\two\admin;


class screen {

	/**
	 * Plugin slug
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * screen constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param $plugin_slug
	 */
	public function __construct( $plugin_slug ) {
		$this->plugin_slug = $plugin_slug;
		
	}

	/**
	 * Add our settings sub menu item
	 *
	 * @uses "admin_menu"
	 *
	 * @since 2.0.0
	 */
	public function add_screen(){
		add_options_page(
			'Epoch by Postmatic',
			'Epoch by Postmatic',
			'manage_options',
			$this->plugin_slug,
			array( $this, 'screen' )
		);
	}

	/**
	 * Callback for rendering screen
	 *
	 * @since 2.0.0
	 */
	public function screen(){
		include EPOCH_DIR . 'assets/templates/admin-screen.php';
	}

	/**
	 * Register scripts for our admin page
	 *
	 * @uses "admin_enqueue_script"
	 *
	 * @since 2.0.0
	 */
	public function register_scripts(){
		wp_register_style( $this->plugin_slug . '-admin', EPOCH_URL . 'assets/css/epoch-admin.css' );
		wp_register_script( $this->plugin_slug . '-admin', EPOCH_URL . 'assets/js/epoch-admin.js', array( 'jquery', 'underscore' ), EPOCH_VERSION  );

	}

	/**
	 * Load scripts/styles on our admin screen only
	 *
	 * @uses "admin_enqueue_script"
	 *
	 * @since 2.0.0
	 *
	 * @param string $hook
	 */
	public function enqueue_scripts( $hook ){
		if ( 'settings_page_epoch' == $hook ) {
			wp_enqueue_style( $this->plugin_slug . '-admin', EPOCH_URL . 'assets/css/epoch-admin.css' );
			wp_enqueue_script( $this->plugin_slug . '-admin', EPOCH_URL . 'assets/js/epoch-admin.js', array(
				'jquery',
				'underscore'
			), EPOCH_VERSION );
			wp_localize_script(  $this->plugin_slug . '-admin', 'EpochAdmin', $this->localize() );
		}
	}

	/**
	 * Values to be localized
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function localize(){
		return array();
	}

}
