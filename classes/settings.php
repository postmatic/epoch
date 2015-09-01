<?php
/**
 * Epoch Setting.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Postmatic
 */
namespace postmatic\epoch;

/**
 * Settings class
 * @package Epoch
 * @author  Postmatic
 */
class settings extends core{


	/**
	 * Constructor for class
	 *
	 * @since 0.0.1
	 */
	public function __construct(){

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );
		// save config
		add_action( 'wp_ajax_epoch_save_config', array( $this, 'save_config') );
		

	}

	/**
	 * Saves a config
	 *
	 * @uses "wp_ajax_epoch_save_config" hook
	 *
	 * @since 0.0.1
	 */
	public function save_config(){
		if( ! current_user_can( 'manage_options' ) ) {
			status_header( '500' );
			die();

		}

		if( empty( $_POST[ 'epoch-setup' ] ) || ! wp_verify_nonce( $_POST[ 'epoch-setup' ], 'epoch' ) ){
			if( empty( $_POST['config'] ) ){
				return;
			}
		}

		if( !empty( $_POST[ 'epoch-setup' ] ) && empty( $_POST[ 'config' ] ) ){
			$config = stripslashes_deep( $_POST['config'] );

			options::update( $config );

			wp_redirect( '?page=epoch&updated=true' );
			exit;
		}

		if( !empty( $_POST['config'] ) ){

			$config = json_decode( stripslashes_deep( $_POST['config'] ), true );

			if(	wp_verify_nonce( $config['epoch-setup'], 'epoch' ) ){
				options::update( $config );
				wp_send_json_success( $config );
			}

		}

		// nope
		wp_send_json_error( $config );

	}

	/**
	 * Add options page
	 *
	 * @since 0.0.1
	 *
	 * @uses "admin_menu" hook
	 */
	public function add_settings_pages(){
		// This page will be under "Settings"
		
	
			$this->plugin_screen_hook_suffix['epoch'] =  add_submenu_page( 'options-general.php', __( 'Epoch', $this->plugin_slug ), __( 'Epoch', $this->plugin_slug ), 'manage_options', 'epoch', array( $this, 'create_admin_page' ) );
			add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix['epoch'], array( $this, 'enqueue_admin_stylescripts' ) );

	}

	/**
	 * Options page callback
	 *
	 * @since 0.0.1
	 */
	public function create_admin_page(){
		// Set class property        
		$screen = get_current_screen();
		$base = array_search($screen->id, $this->plugin_screen_hook_suffix);
			
		// include main template
		include EPOCH_PATH .'includes/edit.php';

		// php based script include
		if( file_exists( EPOCH_PATH .'assets/js/inline-scripts.php' ) ){
			echo "<script type=\"text/javascript\">\r\n";
				include EPOCH_PATH .'assets/js/inline-scripts.php';
			echo "</script>\r\n";
		}

	}


}

