<?php
/**
 * Main plugin class
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */

namespace postmatic\epoch\two;

use postmatic\epoch\two\admin\sanitation;
use postmatic\epoch\two\admin\save;
use postmatic\epoch\two\admin\screen;
use postmatic\epoch\two\api\comments;
use postmatic\epoch\two\front\localize;
class epoch {

	/**
	 * Class instance
	 *
	 * @since 2.0.0
	 *
	 * @var epoch
	 */
	protected static $instance;

	/**
	 * The options
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Plugin slug
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $plugin_slug = 'epoch';

	/**
	 * Nonce
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $epoch_nonce;

	/**
	 * Key for our option
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $option_key = '_epoch_two';

	/**
	 * Epoch constructor.
	 *
	 * @since 2.0.0
	 */
	protected function __construct(){
		$this->add_hooks();
	}

	/**
	 * Add hooks for this class
	 *
	 * @since 2.0.0
	 */
	protected function add_hooks() {
		add_action( 'rest_api_init', array( $this, 'make_api' ) );

		//sanitization for settings
		$sanitation = new sanitation( $this->option_key, array_keys( $this->default_options() ) );
		add_filter( 'pre_update_option_' . $this->option_key, array( $sanitation, 'apply' ) );

		if( is_admin() ) {
			//admin screen
			$screen = new screen( $this->plugin_slug );
			add_action( 'admin_menu', array( $screen, 'add_screen' ) );
			add_action( 'admin_enqueue_scripts', array( $screen, 'register_scripts' ), 10 );
			add_action( 'admin_enqueue_scripts', array( $screen, 'enqueue_scripts' ), 25  );

			//AJAX save for settings
			$save = new save( $this->option_key, array_keys( $this->default_options() ) );
			add_action( 'wp_ajax_epoch_settings', array( $save, 'save_settings' ) );
		}else{
			add_filter( 'comments_template', array( $this, 'initial' ), 100 );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_assets'  ) );
		}
		
		
	}

	/**
	 * Load assets in front
	 *
	 * @uses "wp_enqueue_scripts"
	 *
	 * @since 2.0.0
	 */
	public function front_assets(){
		wp_register_style( $this->plugin_slug, EPOCH_URL . 'assets/css/epoch.css' );
		wp_register_script( $this->plugin_slug, EPOCH_URL . 'assets/js/epoch.js', array( 'jquery', 'underscore' ), EPOCH_VERSION  );
		$post = get_post();
		if( ! is_object( $post ) ){
			return;
		}

		$vars = new localize( $post );
		$vars = $vars->get_vars();

		wp_enqueue_style( $this->plugin_slug );
		wp_enqueue_script( $this->plugin_slug );
		wp_localize_script( $this->plugin_slug, 'EpochFront', $vars );
		$options = $this->get_options();

	}

	/**
	 * Load our custom comment area template
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function initial(){
		return EPOCH_DIR . '/assets/templates/initial.php';
	}

	/**
	 * Get class instance
	 *
	 * @since 2.0.0
	 *
	 * @return \postmatic\epoch\two\epoch
	 */
	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();

		}

		return self::$instance;
	}

	/**
	 * Get options
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_options(){
		if( null == $this->options ){
			$this->options = get_option( $this->option_key, array() );
			$this->options = wp_parse_args(  $this->options, $this->default_options() );

		}

		return $this->options;


	}

	/**
	 * Load custom REST API endpoints
	 *
	 * @uses "rest_api_init"
	 *
	 * @since 2.0.0
	 */
	public function make_api(){
		$api = new comments();
		$api->register_routes();
	}

	/**
	 * The default options
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function default_options(){
		return array(
			'per_page' => 10,
			'order' => 'ASC',
			'before_text' => esc_html__( 'Join The Conversation', 'epoch' ),
			'infinity_scroll' => false
		);
	}

	/**
	 * Namespace for custom routes
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function api_namespace(){
		return 'epoch/v2';
	}

	/**
	 * Base URL for the custom routes
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function api_url(){
		return rest_url( $this->api_namespace() );
	}

	/**
	 * Create a link to our comments route
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id
	 * @param int $page
	 *
	 * @return string
	 */
	public function comment_api_link( $post_id, $page = 1 ) {
		$args = array(
			'page' => $page,
			'nonce' => $this->get_epoch_nonce(),
			'_wpnonce' => wp_create_nonce( 'wp_rest' )
		);

		return add_query_arg( $args, sprintf( '%s/comments/%d', $this->api_url(), $post_id ) );
	}

	/**
	 * Get our nonce
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_epoch_nonce(){
		if( null == $this->epoch_nonce ){
			$this->epoch_nonce = wp_create_nonce();
		}

		return $this->epoch_nonce;
	}
	

}
