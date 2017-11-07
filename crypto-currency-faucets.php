<?php
/*
Plugin Name: Crypto Currency Faucets
Plugin URI: http://shramee.me/
Description: Displays a table showing crypto-currency faucets. Use shortcode [crypto-currency-faucets] to show faucets table on any page.
Author: Shramee
Version: 1.0.0
Author URI: http://shramee.me/
@developer shramee <shramee.srivastav@gmail.com>
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';

/**
 * Crypto Currency Faucets main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class Crypto_Currency_Faucets{

	/** @var Crypto_Currency_Faucets Instance */
	private static $_instance = null;

	/** @var string Token */
	public static $token;

	/** @var string Version */
	public static $version;

	/** @var string Plugin main __FILE__ */
	public static $file;

	/** @var string Plugin directory url */
	public static $url;

	/** @var string Plugin directory path */
	public static $path;

	/** @var Crypto_Currency_Faucets_Admin Instance */
	public $admin;

	/** @var Crypto_Currency_Faucets_Public Instance */
	public $public;

	/** @var array All crypto currencies */
	public static $currencies = [
		'BTC',
		'BCH',
		'LTC',
		'DOGE',
		'BLK',
		'DASH',
		'PPC',
	];

	/**
	 * Return class instance
	 * @return Crypto_Currency_Faucets instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @param string $file __FILE__ of the main plugin
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct( $file ) {

		self::$token   = 'cc-faucets';
		self::$file    = $file;
		self::$url     = plugin_dir_url( $file );
		self::$path    = plugin_dir_path( $file );
		self::$version = '1.0.0';

		$this->_admin(); //Initiate admin
		$this->_public(); //Initiate public

	}

	/**
	 * Initiates admin class and adds admin hooks
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Crypto_Currency_Faucets_Admin::instance();

		//Enqueue admin end JS and CSS
		add_action( 'admin_enqueue_scripts',	array( $this->admin, 'enqueue' ) );
		add_action( 'admin_init',	array( $this->admin, 'admin_init' ) );
		add_action( 'admin_menu',	array( $this->admin, 'admin_menu' ) );
		add_action( 'update_option_crypto_currency_faucets_api',	array( $this->admin, 'clear_cache' ) );
		add_action( 'update_option_crypto_currency_faucets_key',	array( $this->admin, 'clear_cache' ) );
		add_action( 'update_option_crypto_currency_faucets_currencies',	array( $this->admin, 'clear_cache' ) );
	}

	/**
	 * Initiates public class and adds public hooks
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Crypto_Currency_Faucets_Public::instance();

		//Enqueue front end JS and CSS
		add_action( 'wp_enqueue_scripts', 	array( $this->public, 'enqueue' ) );
		add_shortcode( 'crypto-currency-faucets', 	array( $this->public, 'shortcode' ) );

	}
}

/** Intantiating main plugin class */
Crypto_Currency_Faucets::instance( __FILE__ );
