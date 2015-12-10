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
use postmatic\epoch\front\api_paths;
use postmatic\epoch\front\api_route;
use postmatic\epoch\front\end_points;
use postmatic\epoch\front\layout;
use postmatic\epoch\front\prewrite_comment_count;
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

		//register scripts/styles used in both front-end and back-end
		add_action( 'wp_enqueue_scripts', array( $this, 'register_common' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_common' ), 5 );

		//load settings class in admin
		if ( is_admin() ) {
			new settings();
			new end_points();
			
		}elseif( ! defined( 'DOING_AJAX' ) && ! defined( 'EPOCH_API' ) ){

			//boot API
			new end_points();
			new api_route();

			add_action( 'template_redirect', array( $this, 'need_epoch'), 9 );
			add_action( 'template_redirect', array( $this, 'boot_epoch_front_comment' ) );


		}

		//flush permalinks if not on an API call and hasn't been done this version.
		add_action( 'init', function() {
			if( ! isset( $_REQUEST[ vars::$nonce_field ] ) && EPOCH_VER != get_option( 'epoch_ver' ) ) {
				epoch_fix_rewrites();
			}
		});


		if ( EPOCH_ALT_COUNT_CHECK_MODE ){
			new prewrite_comment_count();

		}


	}

	/**
	 * Load Epoch's front-end
	 *
	 * @deprecated 1.0.8
	 *
	 * @uses "parse_query" action (since we need a is_singular() check)
	 *
	 * @since 0.0.8
	 */
	public function boot_epoch_front( $query ) {
		_deprecated_function( __FUNCTION__, '1.0.8', '\postmatic\epoch\core\epoch_front' );
		if ( false != $query->is_singular ) {
			$options = options::get_display_options();
			if ( 'none' == $options[ 'theme' ] ) {
				vars::$wrap_id = 'comments';
			}

			//add_action( 'wp_enqueue_scripts', array( $this, 'front_stylescripts' ) );
			add_filter( 'comments_template', array( '\postmatic\epoch\front\layout', 'initial' ), 100 );
			add_action( 'epoch_iframe_footer', array( $this, 'print_template' ), 9 );
			add_action( 'wp_footer', array( $this, 'print_template' ) );
			add_filter( 'the_content', array( '\postmatic\epoch\front\layout', 'width_sniffer' ), 100 );

		}
		
	}

	/**
	 * Check if a post needs Epoch
	 *
	 * @since 1.0.8
	 *
	 * @uses "template_redirect"
	 *
	 * @return bool
	 */
	public function need_epoch() {
		$post = get_post( get_queried_object_id() );
		if( ! is_object( $post ) ) {
			return false;
		}

		$comments_open = comments_open( $post->ID );
		$comment_count = get_comment_count( $post->ID );
		$approved = false;
		if( ! empty( $comment_count ) ) {
			$approved = $comment_count[ 'approved' ];
		}

		if( $approved || $comments_open ) {
			$this->epoch_front();
		}

	}

	/**
	 * Add hooks and filters for Epoch's front-end
	 *
	 * @since 1.0.
	 */
	protected function epoch_front() {
		$options = options::get_display_options();
		if ( 'none' == $options[ 'theme' ] ) {
			vars::$wrap_id = 'comments';
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'front_stylescripts' ) );
		add_filter( 'comments_template', array( '\postmatic\epoch\front\layout', 'initial' ), 100 );
		add_action( 'epoch_iframe_footer', array( $this, 'print_template' ), 9 );
		add_action( 'wp_footer', array( $this, 'print_template' ) );
		add_filter( 'the_content', array( '\postmatic\epoch\front\layout', 'width_sniffer' ), 100 );
	}

	/**
	 * Load Epoch's front-end template for iFrame Mode
	 *
	 * @since 0.0.8
	 */
	public function boot_epoch_front_comment( $template ) {
		$options = options::get();
		if( 'iframe' != $options['options'][ 'theme' ] ) {
			return $template;
		}

		global $wp_query;
		if ( ! isset( $wp_query->query_vars['epoch'] ) || ! is_singular() ){
			return $template;
		}

		$this->front_stylescripts();
		add_filter( 'show_admin_bar', '__return_false' );

		include EPOCH_PATH . 'includes/templates/comment-template.php';
		exit;

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
			wp_enqueue_style( 'epoch-baldrick-modals' );
			wp_enqueue_script( 'epoch-wp-baldrick' );
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

		//set random version and unminified if SCRIPT DEBUG
		$version = EPOCH_VER;
		$min = true;
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = rand();
			$min = false;
		}

		//determine theme
		$options = options::get_display_options();

		$theme = $options[ 'theme' ];
		if ( ! in_array( $theme, array( 'light', 'dark', 'none', 'iframe' ) ) ) {
			$theme = 'light';
		}


		//visibility API
		wp_enqueue_script( 'visibility', '//cdnjs.cloudflare.com/ajax/libs/visibility.js/1.2.1/visibility.min.js', array('jquery'), $version, true );

		//handlebars
		wp_enqueue_script( 'epoch-handlebars', EPOCH_URL . "/assets/js/front/handlebars.js", array('jquery'), $version, true );

		//load unminified if !SCRIPT_DEBUG
		if ( ! $min ) {
			//our handlebars helpers
			wp_enqueue_script( 'epoch-handlebars-helpers', EPOCH_URL . '/assets/js/front/helpers.js', array( 'epoch-handlebars' ), $version, true  );

			//main script
			wp_enqueue_script( 'epoch', EPOCH_URL . "/assets/js/front/epoch.js", array( 'jquery', 'visibility' ), $version, true );
		}

		//main scripts and styles
		wp_enqueue_script( 'epoch', EPOCH_URL . "/assets/js/front/epoch{$suffix}.js", array( 'jquery', 'epoch-handlebars' ), $version, true );
		if ( 'none' != $theme ) {
			wp_enqueue_style( "epoch-{$theme}", EPOCH_URL . "/assets/css/front/{$theme}{$suffix}.css", $version, true  );
		}



		//make sure we have the comment reply JS from WordPress core.
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
			'api_url' => esc_url( api_paths::api_url( false ) ),
			'submit_api_url' => esc_url( api_paths::api_url( true ) ),
			'alt_comment_count' => null,
			'depth' => absint( get_option( 'thread_comments_depth', 5 ) ),
			'nonce' => vars::make_nonce(),
		);

		/**
		 * Turn live update mode on and off.
		 *
		 * If this filter is set to false, comments from other users will not live update.
		 *
		 * @since 1.0.1
		 */
		$vars[ 'live_mode' ] = (bool) apply_filters( 'epoch_live_mode', true );

		//add all properties from vars class
		$props = get_class_vars( "\\postmatic\\epoch\\front\\vars" );
		foreach( $props as $var => $value ) {
			$vars[ $var ] = esc_attr( $value );
		}

		//reset comment depth to 1 if threaded comments are disabled.
		if ( true != get_option( 'thread_comments' ) ) {
			$vars[ 'depth' ] = 1;
		}

		//add the options
		$vars[ 'epoch_options' ] = $this->prepare_epoch_options();

		global $post;
		if ( is_object( $post ) ) {
			$vars[ 'alt_comment_count'] = esc_url( api_paths::comment_count_alt_check_url( $post->ID ) );
			$vars[ 'post_id' ] = $post->ID;
		}

		$user = get_user_by( 'id', get_current_user_id() );
		if ( is_object( $user ) ) {

			$vars[ 'user' ] = array(
				'author_avatar' => get_avatar( get_current_user_id() ),
				'comment_author_url' => $user->user_url,
				'comment_author' => $user->display_name
			);
		}else{
			$vars[ 'user' ] = array(
				'author_avatar' => '',
				'comment_author_url' => '',
				'comment_author' => '',
			);
		}

		$vars[ 'empty_avatar' ] = esc_html( get_avatar(0) );

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

		$options = options::get_display_options();

		$_interval = absint( $options[ 'interval' ] ) * 1000;


		if ( 15000 > $_interval ) {
			$options[ 'interval' ] = 15000;
		}else{
			$options[ 'interval' ] = $_interval;
		}

		/**
		 * Ovveride comment check interval
		 *
		 * Note: This runs after the check to force it to be less than 15000ms. Use with caution.
		 *
		 * @since 1.0.10
		 *
		 * @param int $interval Interval IN MILLISECONDS
		 */
		$options[ 'interval' ] = apply_filters( 'epoch_comment_check_interval', $options[ 'interval' ] );

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
			'author_url_link_title' => __( 'Link to comment author\'s website', 'epoch' ),
			'is_required' => __( 'is required', 'epoch' ),
			'pending' => __( 'Comment Pending', 'epoch' ),
			'comment_rejected' => __(
				'Your comment was not accepted, please check that everything is filled out correctly.',
				'epoch'
			),
		);
	}


	/**
	 * Register scripts shared between front-end and back-end
	 *
	 * @uses "wp_enqueue_scripts" action
	 *
	 * @since 0.0.5
	 */
	public function register_common() {
		wp_register_style( 'epoch-baldrick-modals', EPOCH_URL . '/assets/css/modals.css' );
		wp_register_script( 'epoch-wp-baldrick', EPOCH_URL . '/assets/js/wp-baldrick-full.js', array( 'jquery' ) , false, true );
	}

}
