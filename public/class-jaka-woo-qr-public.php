<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/JaKaspar
 * @since      1.0.0
 *
 * @package    Jaka_Woo_Qr
 * @subpackage Jaka_Woo_Qr/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Jaka_Woo_Qr
 * @subpackage Jaka_Woo_Qr/public
 * @author     JaKaspar <j.kaspar.gm@gmail.com>
 */
class Jaka_Woo_Qr_Public {

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
	
	protected $bacs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jaka-woo-qr-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jaka-woo-qr-public.js', array( 'jquery' ), $this->version, false );

	}
	
	public function get_bacs() {
	    if ( null === $this->bacs ) {
	        $available = \WC()->payment_gateways->payment_gateways();
	        if ( empty( $available['bacs'] ) ) {
	            trigger_error( 'Paybysquare: BACS payment gateway not available.', E_USER_NOTICE );
	            $this->bacs = false;
	        }
	        else {
	            $this->bacs = $available['bacs'];
	        }
	    }
	    return $this->bacs;
	}
	
	public function thankyou_page_qrcode( $order_id ) {
	    $order = wc_get_order( $order_id );
	    if ( $order ) {
	        $img_data = $this->get_qr_code( $order );
	        if ( $img_data ) {
	            $this->output_qr_code_image( 'data:image/jpg;base64,' . $img_data[1], $img_data[2] );
	        }
	    }
	}
	
	public function onhold_email_qrcode_info( $order, $sent_to_admin = false, $plain_text = false ) {
	    if ( $order && ! $sent_to_admin && ! $plain_text ) {
	        if ( 'bacs' === $order->get_payment_method() && 'on-hold' === $order->get_status() ) {
	            $img_data = $this->get_qr_code( $order );
	            if ( $img_data ) {
	                $this->order = $order;
	                add_action( 'phpmailer_init', [ $this, 'onhold_email_attachments' ] );
	                $this->output_qr_code_image( 'cid:' . $img_data[2] );
	            }
	        }
	    }
	}
	
	public function onhold_email_attachments( $phpmailer ) {
	    $order = $this->order;
	    if ( $order instanceof \WC_Order && 'bacs' === $order->get_payment_method() && 'on-hold' === $order->get_status() ) {
	        $img_data = $this->get_qr_code( $order );
	        if ( $img_data ) {
	            $phpmailer->addEmbeddedImage( $img_data[0], $img_data[2], 'platebni_qr.png', 'base64', 'image/png' );
	            $phpmailer->addAttachment( $img_data[0], 'platebni_qr.png', 'base64', 'image/png' );
	        }
	    }
	}
	
	public function filter_gateway_title( $title, $gateway_id ) {
	    $bacs = $this->get_bacs();
	    if ( 'bacs' === $gateway_id && $bacs && $bacs->get_option( 'paybysquare_information' ) ) {
	        $title .= rtrim( ' ' . ltrim( $bacs->get_option( 'paybysquare_information' ) ) );
	    }
	    return $title;
	}
	
	protected function output_qr_code_image( $src, $id = '' ) {
	    if ( $src ) {
	        echo '<div style="margin: 1em 0 1em">'
	            . '<p>' . __( 'For convenient payment, scan this QR code with your banking app:', 'jaka-woo-qr' ) . '</p>'
	                . '<img id="' . esc_attr( $id ) . '" src="' . esc_attr( $src ) . '" alt="[' . __('Payment QR code') . ']" style="width: 16em; height: auto" />'
	                    . '</div>';
	    }
	}
	
	protected function get_qr_code( \WC_Order $order ) {
	    $bacs = $this->get_bacs();
	    if ( ! $bacs ) {
	        return [];
	    }
	    
	    $account_prefix   = $bacs->get_option( 'paybysquare_account_prefix' );
	    $account_no       = $bacs->get_option( 'paybysquare_account_no' );
	    $bank_code        = $bacs->get_option( 'paybysquare_bank_code' );
	    $wp_upload        = wp_upload_dir();
	    
	    if ( ! empty( $wp_upload['error'] ) ) {
	        trigger_error( 'Paybysquare: Searching for WordPress upload directory failed: ' . $wp_upload['error'], E_USER_NOTICE );
	        return [];
	    }
	    
	    $qrdata = [
	        'total' => $order->get_total(),
	        'currency' => $order->get_currency(),
	        'variable_symbol' => substr( preg_replace( '/[^0-9]+/', '', $order->get_order_number() ), 0, 10 ),
	        'account_prefix' => $account_prefix,
	        'account_no' => $account_no,
	        'bank_code' => $bank_code
	    ];
	    
	    $hash = sha1( json_encode( $qrdata ) );
	    $file = $hash . '.png';
	    $dirpath = $wp_upload['basedir'] . '/payment_qr_codes';
	    $path = $wp_upload['basedir'] . '/payment_qr_codes/' . $file;
	    
	    if (!file_exists($dirpath)) {
	        mkdir($dirpath, 0777, true);
	    }
	    
	    $url = "http://api.paylibo.com/paylibo/generator/czech/image?accountPrefix="
	        . esc_html( $qrdata['account_prefix'] )
	        . "&accountNumber=" . esc_html( $qrdata['account_no'] )
	        . "&bankCode=" . esc_html( $qrdata['bank_code'] )
	        . "&amount=" . esc_html( $qrdata['total'] )
	        . "&currency=" . esc_html( $qrdata['currency'] )
	        . "&vs=" . esc_html( $qrdata['variable_symbol'] ) . "&size=200";
	        
	        $base64img = base64_encode(file_get_contents($url));
	        if ( false === file_put_contents( $path, base64_decode( "$base64img" ), LOCK_EX ) ) {
	            trigger_error( 'Paybysquare: Unable to write QR code into file: ' . $path, E_USER_NOTICE );
	            return [];
	        }
	        
	        return [ $path, $base64img, $hash ];
	}
	
	protected static function sanitize( $value ) {
	    // allow only alphanumeric characters (and uppercase lowercased ones)
	    return preg_replace( '/[^0-9A-Z]+/', '', strtoupper( $value ) );
	}

}
