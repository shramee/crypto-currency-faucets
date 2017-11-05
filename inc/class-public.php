<?php

/**
 * Crypto Currency Faucets public class
 */
class Crypto_Currency_Faucets_Public{

	/** @var Crypto_Currency_Faucets_Public Instance */
	private static $_instance = null;

	/* @var string $token Plugin token */
	public $token;

	/* @var string $url Plugin root dir url */
	public $url;

	/* @var string $path Plugin root dir path */
	public $path;

	/* @var string $version Plugin version */
	public $version;

	/**
	 * Crypto Currency Faucets public class instance
	 * @return Crypto_Currency_Faucets_Public instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   =   Crypto_Currency_Faucets::$token;
		$this->url     =   Crypto_Currency_Faucets::$url;
		$this->path    =   Crypto_Currency_Faucets::$path;
		$this->version =   Crypto_Currency_Faucets::$version;
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front.js', array( 'jquery' ) );
	}
}