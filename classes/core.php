<?php
/**
 * Epoch.
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 Postmatic
 */
namespace postmatic\epoch;
use postmatic\epoch\front\api;
use postmatic\epoch\front\api_route;
use postmatic\epoch\front\layout;
use postmatic\epoch\front\vars;

/**
 * Main plugin class.
 *
 * @package Epoch
 * @author  Postmatic
 */
class core {

	/**
	 * The slug for this plugin
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'epoch';

	/**
	 * Holds class instance
	 *
	 * @since 0.0.1
	 *
	 * @var      object|\postmatic\epoch\core
	 */
	protected static $instance = null;

	/**
	 * Holds the option screen prefix
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 0.0.1
	 *
	 * @access private
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );

		

		//load settings class in admin
		if ( is_admin() ) {
			new settings();
		}else{
			new layout();
			add_action( 'wp_enqueue_scripts', array( $this, 'front_stylescripts' ) );
			add_filter( 'comments_template', array( '\postmatic\epoch\front\layout', 'initial' ), 100 );
			add_action( 'wp_footer', array( $this, 'print_template' ) );
			add_action( 'wp_footer', array( $this, 'print_modals' ) );

			new api_route();

		}

	}


	/**
	 * Return an instance of this class.
	 *
	 * @since 0.0.1
	 *
	 * @return    object|\postmatic\epoch\core    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( EPOCH_PATH ) . '/languages');

	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 0.0.1
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		if( !is_object( $screen ) ){
			return;
		}

		
		
		if( false !== strpos( $screen->base, 'epoch' ) ){

			wp_enqueue_style( 'epoch-core-style', EPOCH_URL . '/assets/css/styles.css' );
			wp_enqueue_style( 'epoch-baldrick-modals', EPOCH_URL . '/assets/css/modals.css' );
			wp_enqueue_script( 'epoch-wp-baldrick', EPOCH_URL . '/assets/js/wp-baldrick-full.js', array( 'jquery' ) , false, true );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_style( 'epoch-codemirror-style', EPOCH_URL . '/assets/css/codemirror.css' );
			wp_enqueue_script( 'epoch-codemirror-script', EPOCH_URL . '/assets/js/codemirror.js', array( 'jquery' ) , false );
			wp_enqueue_script( 'epoch-core-script', EPOCH_URL . '/assets/js/scripts.js', array( 'epoch-wp-baldrick' ) , false );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );			
		
		}


	}

	/**
	 * Enqueue Scripts and style in front-end
	 *
	 * @uses "wp_enqueue_scripts" hook
	 *
	 * @since 0.0.1
	 */
	public function front_stylescripts() {
		$version = EPOCH_VER;
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = rand();
		}

		$options = options::get();
		if ( is_array( $options ) && isset( $options['options'] ) && isset( $options['options' ][ 'theme' ] ) ) {
			$theme = $options[ 'options' ][ 'theme' ];
			if ( ! in_array( $theme, array( 'light', 'dark' ) ) ) {
				$theme = 'light';
			}

		} else {
			$theme = 'light';
		}

		wp_enqueue_script( 'handlebars', EPOCH_URL . '/assets/js/front/handlebars.js', false, '3.0.3' );
		wp_enqueue_script( 'epoch-handlebars-helpers', EPOCH_URL . '/assets/js/front/helpers.js', array( 'handlebars' ), $version );
		wp_enqueue_script( 'simplemodal', EPOCH_URL . 'assets/js/front/simplemodal.min.js', array( 'jquery' ), '1.4.4' );
		wp_enqueue_script( 'epoch', EPOCH_URL . '/assets/js/front/epoch.js', array( 'jquery', 'handlebars', 'simplemodal' ), $version, true );
		wp_enqueue_style( "epoch-{$theme}", EPOCH_URL . "/assets/css/front/{$theme}.css",false, $version );

		if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		//localize variables we need in JS
		$vars = $this->prepare_data_to_be_localized();
		wp_localize_script( 'epoch', 'epoch_vars', $vars );

		//localize translation strings we need in JS
		$vars = $this->translation_strings();
		wp_localize_script( 'epoch', 'epoch_translation', $vars );
	}

	/**
	 * Outputs the Handlebars template for comments in the footer
	 *
	 * @uses "wp_footer" hook
	 *
	 * @since 0.0.1
	 */
	public function print_template() {
		echo layout::comments_template();

	}

	/**
	 * Prepare data to be localized into script (that isn't for translation)
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function prepare_data_to_be_localized() {
		$vars = array(
			'wrap_class' => esc_attr( vars::$wrap_class ),
			'form_id' => esc_attr( vars::$form_id ),
			'submit_id' => esc_attr( vars::$submit_id ),
			'nonce' => esc_attr( vars::make_nonce() ),
			'form_wrap' => esc_attr( vars::$form_wrap ),
			'comments_wrap' => esc_attr( vars::$comments_wrap ),
			'comments_template_id' => esc_attr( vars::$comments_template_id ),
			'comment_form_spinner_id' => esc_attr( vars::$comment_form_spinner_id ),
			'comments_area_spinner_id' => esc_attr( vars::$comments_area_spinner_id ),
			'api_url' => esc_url( vars::api_url( false ) ),
			'submit_api_url' => esc_url( vars::api_url( true ) ),
			'depth' => absint( get_option( 'thread_comments_depth', 5 ) )

		);

		if ( true != get_option( 'thread_comments' ) ) {
			$vars[ 'depth' ] = 1;
		}

		$vars[ 'epoch_options' ] = $this->prepare_epoch_options();

		global $post;
		if ( is_object( $post ) ) {
			$vars[ 'post_id' ] = $post->ID;
		}

		return $vars;

	}

	/**
	 * Gets Epoch's saved options and prepares them to be localized into the script.
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function prepare_epoch_options() {
		$_options = options::get();
		if ( is_array( $_options ) && isset( $_options['options'] ) ) {
			$options = $_options['options'];
		} else {
			$options = array();
		}

		$defaults = array(
			'theme'       => 'light',
			'threaded'    => false,
			'before_text' => false,
			'interval'    => 15
		);

		$options = wp_parse_args( $options, $defaults );

		$_interval = absint( $options[ 'interval' ] ) * 1000;
		if ( 0 === $_interval || $_interval > 15000 ) {
			$options[ 'interval' ] = 15000;
		}else{
			$options[ 'interval' ] = $_interval;
		}

		return $options;

	}

	/**
	 * Holds translation strings for use in front-end
	 *
	 * @since 0.0.2
	 *
	 * @return array
	 */
	protected function translation_strings() {
		return array(
			'awaiting_moderation' => __( 'Your comment is awaiting moderation.', 'epoch' ),
			'comment_link_title' => __( 'Link to comment' ),
			'reply' => __( 'Reply', 'epoch' ),
			'reply_link_title' => __( 'Reply To This Comment', 'epoch' ),
			'author_url_link_title' => __( 'Link to comment author\'s website', 'epoch' )
		);
	}

	/**
	 * Print modal content in footer hidden
	 *
	 * @uses "wp_footer"
	 *
	 * @since 0.0.2
	 */
	public function print_modals() {
		printf( '<div class="epoch-modal" style="display: none;" id="epoch-success">%s</div>', __( 'Comment Submitted Successfully', 'epoch' ) );
		printf( '<div class="epoch-modal" style="display: none;" id="epoch-failure">%s</div>', __( 'There was an error submitting your comment.', 'epoch' ) );
		printf( '<div class="epoch-modal" style="display: none;" id="epoch-new-comment">%s</div>', __( 'A New Comment Available', 'epoch' ) );
	}


}
