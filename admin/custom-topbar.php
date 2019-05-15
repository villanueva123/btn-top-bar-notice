<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://businesstechninjas.com
 * @since      1.0.0
 *
 * @package    Btn_Fulfillment
 * @subpackage Btn_Fulfillment/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Btn_Fulfillment
 * @subpackage Btn_Fulfillment/includes
 * @author     Augustus <augustus@businesstechninjas.com>
 */
class topbar_alert {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $products = array();


	protected $topbars = false;

	protected $I18n = array();

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	 public static function get_instance() {
		 static $instance = null;
		 if ( is_null( $instance ) ) {
 			$instance = new self;
 			$instance->setup();
 		}
		return $instance;
	}

	/**
	 * Private Contruct Method.
	 *
	 * @since    1.0.0
	 */
	 private function __construct() {}

	/**
		* Define the core functionality of the plugin.
		*
		* Load the dependencies, define the locale, and set the hooks for the admin area
		* @since    1.0.0
		* @access   private
	*/
	public function setup(){
		if ( defined( 'TOPBAR_ALERT_VERSION' ) ) {
			$this->version = TOPBAR_ALERT_VERSION;
		}
		else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'btn-top-bar-notice';
		$this->load_actions();
	}

	/**
	 * Set actions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_actions() {

		if( is_admin() ){
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
			add_action( 'admin_footer', array( $this, 'admin_footer' ) );

		}
		add_action('wp_enqueue_scripts', array( $this, 'frontend_enqueue' ));
		add_action( 'wp_ajax_add_new_topbar', array( $this, 'add_new_topbar' ) );
		add_action( 'wp_ajax_delete_topbar', array( $this, 'delete_topbar' ) );
		add_action( 'wp_ajax_edit_topbar', array( $this, 'edit_topbar' ) );
		add_action( 'wp_footer', array( $this, 'display_alert_box' ) );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'btn-top-bar-notice',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
		$this->I18n['dismiss_notice'] = __( 'Dismiss this notice.', 'btn-top-bar-notice' );
		$this->I18n['topbar_code_title'] = __( 'Tag ID :', 'btn-top-bar-notice' );
	}


	/**
	 * Add Settings Admin Menu
	 *
	 * @since    1.0.0
	 */
  public function admin_menu() {

    add_menu_page(
      __('Topbar Alert Box', 'btn-top-bar-notice'),
	  __('Topbar Alert Box', 'btn-top-bar-notice'),
      "manage_options",
      $this->plugin_name,
      array($this, 'settings_page'),
	   'dashicons-flag',
      62
    );
		//Register Custom Option For Class
		register_setting( 'top-bar-settings-group', 'container_class' );
  }

	/**
	 * Admin Enqueue
	 *
	 * @since    1.0.0
	 */
	 public function admin_enqueue( $hook ){
		 if( $hook === 'toplevel_page_' . $this->plugin_name ) {
			 // https://github.com/woocommerce/selectWoo
			 wp_register_script('selectWoo', plugin_dir_url( __FILE__ ) . 'js/selectWoo.full.min.js', ['jquery'], '1.0.4');
			 wp_enqueue_style('selectWoo', plugin_dir_url( __FILE__ ) . 'css/selectWoo.css', false, '5.7.2');
			 wp_enqueue_script('selectWoo' );

			 $dependencies = array('wp-util', 'underscore', 'jquery','wp-color-picker','jquery-ui-datepicker');
			 wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ). 'js/settings.js', $dependencies, $this->version, true );
			 wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
		 }
	 }

	 /**
	  * Front Enqueue
	  *
	  * @since    1.0.0
	  */
	  public function frontend_enqueue(){
	 		 wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ctb_frontend.css', array(), $this->version, 'all' );
	  }



	/**
	 * Admin Footer
	 *
	 * @since    1.0.0
	 */
	public function admin_footer(){
		if( !empty($this->to_json) ){
			#Include Underscore Tmpls
			require plugin_dir_path( __FILE__ ) . 'views/admin-templates.php';
			require plugin_dir_path( __FILE__ ) . 'views/form-elements.php';
			$json = $this->to_json;
			wp_localize_script( $this->plugin_name, 'elite_topbars_data', $json );
		}
	}

	/**
		* SVG Icon
		*
		* @since  	1.0.0
		* @access 	public
	*/
	function svg_icon( $fill = 'black', $decode = false ) {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">';
		$svg .= '<path fill="'.$fill.'" d="M617.74 88.14c-88.59 7.23-136.5 11.98-144.55 14.25-6.61 1.86-19.41 7.23-28.5 11.98-16.11 8.05-22.51 14.25-225.5 217.04C104.17 446.22 10 541 10 542.24c0 1.03 85.08 87.15 188.95 191.02L387.9 922.21l208.57-208.57c186.48-186.47 221.37-222.61 220.76-229.43 0-1.03-11.77-7.64-25.81-14.66l-25.61-12.6-3.92 6.4c-2.27 3.72-87.14 89.62-188.54 191.02l-184.41 184.2-148.27-148.26L92.6 542.24l187.3-187.09c173.05-172.64 188.54-187.51 199.9-192.46 11.77-4.96 19.2-5.78 144.35-15.9 126.38-10.33 132.16-10.53 140.83-7.23 4.75 1.86 12.18 7.02 16.11 11.36 12.18 13.63 12.6 16.93 7.64 80.54-6.2 79.5-7.23 71.04 10.74 87.97 14.87 14.46 34.49 29.74 38 29.74 2.07 0 14.04-155.08 13.42-175.94-.62-25.4-13.01-52.25-32.63-69.59-12.39-10.95-35.11-22.3-48.12-23.96-5.16-.62-11.77-1.45-14.66-1.86-2.89-.42-64.84 4.33-137.74 10.32z"/>';
		$svg .= '<path fill="'.$fill.'" d="M889.09 187.67c-.62.83-1.45 15.28-2.27 32.42-.83 16.93-1.86 34.69-2.48 39.24-1.03 7.43 0 9.71 8.88 21.89 35.52 48.32 45.43 102.01 21.27 116.05-15.49 9.29-44.6 5.37-75.79-9.71-41.09-20.24-81.78-58.44-105.94-99.54l-7.85-13.63 3.72-6.4c20.03-34.28-3.51-77.65-42.13-77.85-17.96 0-27.67 3.72-38.82 14.66-20.86 20.65-20.65 52.87.41 73.72 3.51 3.51 8.05 7.02 10.33 7.64 2.27.83 7.02 7.43 11.36 16.52 13.63 28.29 31.18 52.25 59.68 80.54 51.63 51.42 102.43 78.06 155.08 80.95 35.31 2.06 59.47-6.4 79.3-27.26 39.03-41.09 34.07-111.31-12.8-182.34-20.03-30.34-58.85-72.26-61.95-66.9zM431.27 351.85c-21.68 7.43-40.68 23.34-50.39 42.54-9.91 19-4.54 47.08 16.73 89l13.01 25.81-19 19c-10.53 10.53-19.82 19.2-20.65 19.2-2.89 0-9.91-16.31-9.91-23.33 0-4.13 2.48-10.74 6.4-16.93l6.4-9.91-29.32-29.12-29.12-29.12-6.19 7.23c-13.01 15.07-22.51 42.33-22.51 63.81 0 20.65 13.22 50.59 30.77 70.42l10.11 11.35-13.63 14.04-13.84 13.84 6.81 6.61 6.61 6.81 13.42-13.42c10.12-9.91 14.25-12.8 16.31-11.36 1.45 1.24 8.26 6.61 14.87 11.98 15.28 12.39 37.17 23.13 53.9 26.23 16.31 3.3 35.93.21 53.69-8.47 14.66-7.02 34.9-27.46 41.92-42.33 12.39-26.64 4.75-60.92-24.57-109.03l-8.67-14.46 15.69-15.49c15.07-15.08 15.9-15.49 18.79-11.56 1.65 2.27 4.13 7.64 5.37 11.98 2.27 7.23 1.86 9.09-2.48 17.35l-4.96 9.09 26.43 26.23 26.22 26.23 6.2-7.23c3.51-3.92 9.09-13.22 12.8-20.65 6.2-12.39 6.61-14.87 6.61-35.11 0-20.86-.21-22.3-7.64-37.38-4.33-8.67-11.98-20.44-17.14-26.23-5.16-5.58-9.29-11.15-9.29-12.18 0-1.03 4.54-6.4 10.33-11.98l10.33-10.12-6.61-6.81-6.61-6.81-10.53 10.33c-5.78 5.58-11.15 10.33-12.18 10.33-1.03 0-5.37-3.3-9.71-7.23-10.12-9.09-32.63-20.44-48.74-24.57-17.12-4.57-30.13-4.16-46.03 1.42zm49.35 66.28c12.6 6.61 12.39 8.47-2.06 23.13-7.23 7.23-13.84 13.22-14.87 13.22-2.69 0-9.71-16.93-9.71-23.33 0-7.23 8.26-15.9 15.49-15.9 2.89-.01 7.85 1.23 11.15 2.88zm-53.69 119.16c5.99 14.66 5.78 23.96-1.24 31.18-7.23 7.64-18.38 7.85-30.98.62-4.75-2.89-8.88-5.99-8.88-6.81 0-1.65 33.45-35.52 35.31-35.52.63-.01 3.31 4.74 5.79 10.53z"/>';
		$svg .= '</svg>';
		return ( $decode ) ? 'data:image/svg+xml;base64,' . base64_encode($svg) : $svg;
	}

	/**
		* Settings Page
		*
		* @since  	1.0.0
		* @access 	public
	*/
	function settings_page(){

		$this->to_json['I18n'] = $this->I18n;
		$this->to_json['ajax_url'] = admin_url( 'admin-ajax.php' );
		$this->topbars = get_option( $this->plugin_name, array() );
		$this->to_json['topbars'] = $this->topbars;
		$this->to_json['next_id'] = $this->next_id($this->topbars);
		$this->to_json['settings_tabs'] = $this->settings_tabs();
		$this->to_json['form_config'] = $this->form_config();

		#TMPLs
		require plugin_dir_path( __FILE__ ) . 'views/admin-page.php';

	}

	/**
	 * Update topbar
	*/
	public function update_topbars( $topbar, $action ){

		if( $action === 'add' ){
			$this->topbars = get_option( $this->plugin_name, array() );
			$this->topbars[] = $topbar;
		}
		else if( $action === 'edited' ){
			$this->topbars = $topbar;
		}
		update_option( $this->plugin_name, $this->topbars );
		$this->topbars = get_option( $this->plugin_name, array() );

		return $this->topbars;
	}


	/**
	 * Ajax Add New topbar
	 *
	 * @since    1.0.0
	 */
	public function add_new_topbar(){

		$validate = $this->validate_topbar( $_POST );
		$error = $validate['error'];
		$topbar = ($error) ? false : $validate['topbar'];

		$type = ($error) ? 'error' : 'updated';
		$msg = ($error) ? __( 'topbar not added.', 'btn-top-bar-notice' ) : __( 'topbar has been added', 'btn-top-bar-notice' );
		$next_id = 0;
		if( !$error ){
			$updated_topbars = $this->update_topbars( $topbar, 'add' );
			$next_id = $this->next_id($updated_topbars);
		}
		$return = array(
			'action'	=> $_POST['action'],
			'topbar'		=> $topbar,
			'error' 	=> $error,
			'next_id' 	=> $next_id,
			'notice'	=> array(
				'type'				=> $type,
				'id'				=> 'btn-output',
				'content'			=> $msg,
				'dismissable'		=> $this->I18n['dismiss_notice']
			),
		);
		wp_send_json_success($return);
	}

	/**
	 * Ajax Edit New topbar
	 *
	 * @since    1.0.0
	 */
	public function edit_topbar(){
		$validate = $this->validate_topbar($_POST);
		$topbar_id = $_POST['id'];
		$css_id = $_POST['css_id'];
		$error = $validate['error'];
		$new_topbar = ($error) ? false : $validate['topbar'];
		$type = ($error) ? 'error' : 'updated';
		$msg = ($error) ? __( 'topbar not updated.', 'btn-top-bar-notice' ) : __( 'Top Bar Alert Box has been updated', 'btn-top-bar-notice' );
		if( !$error ){
			$topbars = get_option( $this->plugin_name, array() );
			if( !empty($topbars) ){
				foreach ($topbars as $p => $topbar) {
					if( $topbar_id === $topbar['id'] ){
						$topbars[$p] = $new_topbar;
					}
				}
			}
			$updated_topbars = $this->update_topbars( $topbars, 'edited' );
		}
		$return = array(
			'action'	=> $_POST['action'],
			'error' 	=> $error,
			'topbar'		=> $new_topbar,
			'css_id'	=> $css_id,
			'notice'	=> array(
				'type'				=> $type,
				'id'				=> 'btn-output',
				'content'			=> $msg,
				'dismissable'		=> $this->I18n['dismiss_notice']
			),
		);
		wp_send_json_success($return);
	}


	/**
	 * Delete topbar
	 *
	 * @since    1.0.0
	 */
	public function delete_topbar(){
		$topbar_id = $_POST['id'];
		$topbar_code = $_POST['topbar_code'];
		$topbars = get_option( $this->plugin_name, array() );
		$new_topbars = $topbars;
		$deleted = false;
		if( !empty($topbars) ){
			foreach ($topbars as $p => $topbar) {
				if( $topbar_id == $topbar['id'] ){
					unset( $new_topbars[$p] );
					$deleted = true;
				}
			}
		}
		$error = true;
		$type = 'error';
		$msg = __( 'topbar Code : ', 'btn-top-bar-notice' );
		$msg .= $topbar_code;
		if( $topbars != $new_topbars ){
			$type = 'updated';
			$error = false;
			$updated_topbars = $this->update_topbars( $new_topbars, 'edited' );
		}
		$msg .= ( $error ) ? __(  'has not been deleted.', 'btn-top-bar-notice' ) : __( ' has been deleted', 'btn-top-bar-notice' );
		$return = array(
			'action'	=> $_POST['action'],
			'error' 	=> $error,
			'css_id' 	=> $_POST['css_id'],
			'notice'	=> array(
				'type'				=> $type,
				'id'				=> 'btn-output',
				'content'			=> $msg,
				'dismissable'		=> $this->I18n['dismiss_notice']
			)
		);
		wp_send_json_success($return);
	}

	/**
	 * Validate topbar
	 *
	 * @since    1.0.0
	 */
	public function validate_topbar( $post ){

		#Include Validation
		$this->maybe_include_validation();

		#Generate New Array
		$new_topbar = array();
		$settings = $this->form_config();
		$error = array();
		foreach ($settings as $s => $setting) {
			$type = $setting['type'];
			if( $type != 'section' ){
				$slug = $setting['slug'];
				$value = ( isset($post[$slug]) ) ? $post[$slug] : '';
				#Sanitize
				$do_sanitize = ( isset($setting['sanitize']) ) ? $setting['sanitize'] : false;
				if( $do_sanitize && function_exists($do_sanitize) ){
					$value = call_user_func($do_sanitize, $value);
				}
				#Maybe Validate
				$do_validate = ( isset($setting['validate']) ) ? $setting['validate'] : false;
				if( $do_validate && function_exists($do_validate) ){
					$validate = call_user_func($do_validate, $value);
					if( !$validate ){
						$error[$s] = $setting;
					}
					else{
						$value = $validate;
					}
				}
				$new_topbar[$slug] = $value;
			}
		}
		return array(
			'error' => ( empty($error) ) ? false : $error,
			'topbar'	=> $new_topbar
		);
	}


	/**
	 * Setting Tabs Configuration
	 *
	 * @since    1.0.0
	 */
	public function settings_tabs(){
		return array(
			array(
				'slug'	=> 'main',
				'label'	=> __( 'Conditional Top Bar Settings', 'btn-top-bar-notice' ),
				'icon'	=> 'dashicons dashicons-admin-generic'
			),
		);
	}

	/**
	 * Settings Configuration
	 *
	 * @since    1.0.0
	 */
	public function form_config(){
	    $settings = array();
	    $tab = 'main';
	    $section = 'main';
	    $settings[] = array(
			'slug'	=> $section,
			'type'	=> 'section',
			'tab'	=> $tab
		);
		$settings[] = array(
			'slug'		=> 'id',
			'type'		=> 'hidden',
			'tab'		=> $tab,
			'section'	=> $section,
		);
	    $settings[] = array(
			'slug'		=> 'tag_id',
			'title'		=> __( 'Tag ID', 'btn-top-bar-notice' ),
			'desc'		=> __( 'Add the Tag Id here', 'btn-top-bar-notice' ),
			'type'		=> 'number',
			'tab'		=> $tab,
			'section'	=> $section,
			'validate'	=> 'ctb_validate_not_empty',
			'sanitize'	=> 'ctb_sanitize_slug'
		);
		$settings[] = array(
			'slug'		=> 'active',
			'title'		=> __( 'Activate', 'btn-top-bar-notice' ),
			'desc'		=> __( 'status', 'btn-top-bar-notice' ),
			'type'		=> 'switch',
			'default'	=> 1,
			'tab'		=> $tab,
			'section'	=> $section,
		);

		$settings[] = array(
			'slug'		=> 'content',
			'title'		=> __( 'Content', 'btn-top-bar-notice' ),
			'desc'		=> __( 'Add the content of the Alert Box here.', 'btn-top-bar-notice' ),
			'type'		=> 'textarea',
			'tab'		=> $tab,
			'section'	=> $section,
		);
		$settings[] = array(
			'slug'		=> 'cotent-color',
			'title'		=> __( 'Text Color', 'btn-top-bar-notice' ),
			'desc'		=> __( 'Select the text color of the Alert Box.', 'btn-top-bar-notice' ),
			'type'		=> 'colorpicker',
			'tab'		=> $tab,
			'section'	=> $section,
		);
		$settings[] = array(
			'slug'		=> 'color-picker',
			'title'		=> __( 'Background Color', 'btn-top-bar-notice' ),
			'desc'		=> __( 'Select the background color of the Alert Box.', 'btn-top-bar-notice' ),
			'type'		=> 'colorpicker',
			'tab'		=> $tab,
			'section'	=> $section,
		);
		$settings[] = array(
			'slug'		=> 'date-picker',
			'title'		=> __( 'End Date', 'btn-top-bar-notice' ),
			'desc'		=> __( 'Select Ending Date of the Alert Box.', 'btn-top-bar-notice' ),
			'type'		=> 'datepicker',
			'tab'		=> $tab,
			'section'	=> $section,
		);

    	return $settings;
  	}


	/**
	 * Get The ID For the next topbar
	 * Necessary for editing and saving in options only
	*/
	public function next_id( $topbars ){
		$next_id = 1;
		if( !empty($topbars) ){
			$numbers = wp_list_pluck( $topbars, 'id' );
			$max = max($numbers);
			$next_id = $max + 1;
		}
		return $next_id;
 	}


	/**
	 * Add Wp Footer in frontend
	 *
	 * @since    1.0.0
	 */
	public function display_alert_box() {
		$topbars = get_option( $this->plugin_name, array() );
		echo "<script>
						( function( $ ) {";

		foreach ($topbars as $s => $setting) {
			$tag_id = $setting['tag_id'];
			$message = $setting['content'];
			$content_color = $setting['cotent-color'];
			$bg = $setting['color-picker'];
			$current_date = strtotime( date("Y-m-d") );
			$end_date = strtotime($setting['date-picker']);
			$container = get_option('container_class');

			if($setting['active'] == 1){
			$condition = do_shortcode('[memb_has_any_tag tagid="'.$tag_id.'"]');
			 if($condition == 'Yes'){
				 if($current_date < $end_date || $setting['date-picker'] ==''){
					 $html = '<div class="alert-box-top-bar" id="alert-box'.$tag_id .'" style="background-color:'.$bg.';">'.$message.'</div>';
					 echo "$('".$container."').prepend('".$html."');";
					 echo '$("#alert-box'.$tag_id.' p,#alert-box'.$tag_id.' a,#alert-box'.$tag_id.'").css("color","'.$content_color.'");';
				 }

				}
			}

		}
		echo "} ( jQuery ) );
						</script>";


}

	/**
	 * Include Validation functions
	 *
	 * @since    1.0.0
	 */
	function maybe_include_validation(){
		if( !function_exists('ctb_validate_not_empty') ){
			#Validation
			require plugin_dir_path( __FILE__ ) . 'validation.php';
		}
	}
}
