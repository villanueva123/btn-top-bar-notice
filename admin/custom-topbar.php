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
			$this->version = '1.0.1';
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
			add_action( 'admin_footer', array( $this, 'admin_footer' ));


		}
		add_action( 'wp_ajax_add_new_topbar', array( $this, 'add_new_topbar' ) );
		add_action( 'wp_ajax_delete_topbar', array( $this, 'delete_topbar' ) );
		add_action( 'wp_ajax_edit_topbar', array( $this, 'edit_topbar' ) );

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
			false
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
		add_submenu_page( 'btn-top-bar-notice', 'General Setting', 'General Setting',
    'manage_options','general-settings-top-bar',
		array($this, 'top_bar_general_settings'));
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

			 			wp_enqueue_editor();
			 $dependencies = array('wp-util', 'underscore', 'jquery','wp-color-picker','jquery-ui-datepicker');
			 wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ). 'js/settings.js', $dependencies, $this->version, true );
			 wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
		 }


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
			wp_localize_script( $this->plugin_name, 'custom_topbars_data', $json );
		}
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
		$this->to_json['editor_config'] = [
    'tinymce' => [ 'wpautop' => true, plugins => 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
		toolbar1 => 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker'
 ],
    'quicktags' => true,

		];
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
		$msg = __( 'topbar Tag ID : ', 'btn-top-bar-notice' );
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
			'type'		=> 'input',
			'tab'		=> $tab,
			'section'	=> $section,
			
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
			'id'      => $next_id,
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
			'slug'		=> 'start-date-picker',
			'title'		=> __( 'Start Date', 'btn-top-bar-notice' ),
			'desc'		=> __( 'Select Starting Date of the Alert Box.', 'btn-top-bar-notice' ),
			'type'		=> 'datepicker',
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
	 * Register General settings
	 *
	 * @since    1.0.0
	 */

	 function register_top_bar_settings() {
			 //register our settings

		 }
	/**
	 * Adding General Settings
	 *
	 */
	 function top_bar_general_settings(){

		 ?>
		 <form method="post" action="options.php">
        <?php settings_fields( 'top-bar-settings-group' ); ?>
        <?php do_settings_sections( 'top-bar-settings-group' ); ?>
        <table class="form-table">

            <tr valign="top">
            <th scope="row">Container Class</th>
            <td>
              <input type="text" style="width:600px;" name="container_class" class="form-control" value="<?php echo esc_attr( get_option('container_class') ); ?>" /><br>
              <label><i>Add the class container that will display the alert box like this (.container)</i></label>
            </td>
            </tr>
        </table>
        <?php submit_button(); ?>

    </form>
	<?php }


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
