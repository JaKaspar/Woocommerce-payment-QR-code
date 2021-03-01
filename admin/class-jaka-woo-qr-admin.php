<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/JaKaspar
 * @since      1.0.0
 *
 * @package    Jaka_Woo_Qr
 * @subpackage Jaka_Woo_Qr/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Jaka_Woo_Qr
 * @subpackage Jaka_Woo_Qr/admin
 * @author     JaKaspar <j.kaspar.gm@gmail.com>
 */
class Jaka_Woo_Qr_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jaka_Woo_Qr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jaka_Woo_Qr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jaka-woo-qr-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jaka_Woo_Qr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jaka_Woo_Qr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jaka-woo-qr-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function add_settings_link( $links ) {
	    $admin_url = admin_url( add_query_arg( [
	        'page' => 'wc-settings',
	        'tab' => 'checkout',
	        'section' => 'bacs',
	    ], 'admin.php' ) ) . '#woocommerce_bacs_paybysquare';
	    return array_merge(
	        [ 'settings' => '<a href="'. esc_attr( $admin_url ) . '">' . esc_html__( 'Settings', 'jaka-woo-qr' ) . '</a>', ],
	        $links
	        );
	}
	
	public function filter_form_fields( $fields ) {
	    return $fields + [
	        'paybysquare' => [
	            'title' => __( 'Payment QR code settings', 'jaka-woo-qr' ),
	            'type' => 'title',
	            'default' => '',
	        ],
	        'paybysquare_account_prefix' => [
	            'title' => __( 'Account prefix', 'jaka-woo-qr' ),
	            'type' => 'number',
	            'description' => __( 'Prefix of a target account number', 'jaka-woo-qr' ),
	            'desc_tip' => true,
	        ],
	        'paybysquare_account_no' => [
	            'title' => __( 'Account number', 'jaka-woo-qr' ),
	            'type' => 'number',
	            'description' => __( 'Target account number', 'jaka-woo-qr' ),
	            'desc_tip' => true,
	        ],
	        'paybysquare_bank_code' => [
	            'title' => __( 'Bank code', 'jaka-woo-qr' ),
	            'type' => 'number',
	            'description' => __( 'Target account bank code', 'jaka-woo-qr' ),
	            'desc_tip' => true,
	        ],
	        'paybysquare_information' => [
	            'title' => __( 'Checkout information', 'jaka-woo-qr' ),
	            'type' => 'text',
	            'description' => __( 'Text appended to your BACS title, advertising QR code availability', 'jaka-woo-qr' ),
	            'desc_tip' => true,
	        ],
	    ];
	}

}
