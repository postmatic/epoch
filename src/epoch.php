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

namespace postmatic\epoch\two;

use postmatic\epoch\two\api\comments;
use postmatic\epoch\two\front\localize;
class epoch {

	protected static $instance;

	protected $options;

	protected $plugin_slug = 'epoch';

	protected $epoch_nonce;


	protected function __construct(){
		$this->add_hooks();
	}

	protected function add_hooks() {
		add_filter( 'comments_template', array( $this, 'initial' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_assets'  ) );
		add_action( 'rest_api_init', array( $this, 'make_api' ) );
		
	}

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
	}

	public function initial(){
		return EPOCH_DIR . '/assets/templates/initial.php';
	}

	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();

		}

		return self::$instance;
	}

	public function get_options(){
		if( null == $this->options ){
			$this->options = get_option( '_epoch_two', array() );
			$this->options = wp_parse_args(  $this->options, $this->default_options() );

		}

		return apply_filters( 'epoch_options', $this->options );


	}

	public function make_api(){
		$api = new comments();
		$api->register_routes();
	}

	protected function default_options(){
		return array(
			'per_page' => 10,
			'order' => 'ASC',
			'before_text' => __( 'Join The Conversation', 'epoch' )
		);
	}

	public function api_namespace(){
		return 'epoch/v2';
	}

	public function api_url(){
		return rest_url( $this->api_namespace() );
	}

	/**
	 * @param $post_id
	 * @param $page
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

	public function get_epoch_nonce(){
		if( null == $this->epoch_nonce ){
			$this->epoch_nonce = wp_create_nonce();
		}

		return $this->epoch_nonce;
	}
	

}
