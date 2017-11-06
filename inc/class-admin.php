<?php

/**
 * Crypto Currency Faucets Admin class
 */
class Crypto_Currency_Faucets_Admin {

	/** @var Crypto_Currency_Faucets_Admin Instance */
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
	 * Constructor function.
	 * @access  private
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->token   = Crypto_Currency_Faucets::$token;
		$this->url     = Crypto_Currency_Faucets::$url;
		$this->path    = Crypto_Currency_Faucets::$path;
		$this->version = Crypto_Currency_Faucets::$version;
	} // End instance()

	/**
	 * Main Crypto Currency Faucets Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return Crypto_Currency_Faucets_Admin instance
	 * @since  1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		$token = $this->token;
		$url   = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/admin.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/admin.js', array( 'jquery' ) );
	}

	public function admin_menu() {
		add_options_page(
			'Crypto Currency Faucets',
			'Crypto Currency Faucets',
			'manage_options',
			'crypto_currency_faucets',
			[ $this, 'admin_page' ]
		);
	}

	/**
	 * Renders the admin page
	 */
	public function admin_page() {
		?>
		<div class="wrap">
			<h2>Crypto Currency Faucets</h2>
			<form action="options.php" method="post">
				<?php
				do_settings_sections( 'crypto_currency_faucets' );
				settings_fields( 'crypto_currency_faucets' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Initiates admin settings
	 */
	public function admin_init() {
		register_setting( 'crypto_currency_faucets', 'crypto_currency_faucets_api' );
		register_setting( 'crypto_currency_faucets', 'crypto_currency_faucets_key' );
		register_setting( 'crypto_currency_faucets', 'crypto_currency_faucets_currencies' );
		add_settings_section(
			'crypto_currency_faucets_section',
			'',
			[ $this, 'crypto_currency_faucets_section' ],
			'crypto_currency_faucets'
		);
	}

	public function crypto_currency_faucets_section() {
		$api            = get_option( 'crypto_currency_faucets_api' );
		$key            = get_option( 'crypto_currency_faucets_key' );
		$all_currencies = [
			'BTC',
			'BCH',
			'LTC',
			'DOGE',
			'BLK',
			'DASH',
			'PPC',
		];
		$currencies     = get_option( 'crypto_currency_faucets_currencies', $all_currencies );
		$currencies     = $currencies ? $currencies : [];
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="crypto_currency_faucets_api">Faucets API</label></th>
				<td>
					<select name="crypto_currency_faucets_api" id="crypto_currency_faucets_api">
						<option value="faucetlist.me" <?php selected( $api, 'faucetlist.me' ) ?>>faucetlist.me</option>
						<option value="faucethub.io" <?php selected( $api, 'faucethub.io' ) ?>>faucethub.io</option>
					</select>
				</td>
			</tr>
			<tr id="crypto_currency_faucets_key_row" style="display:none;">
				<th scope="row"><label for="crypto_currency_faucets_key">Faucets API Key</label></th>
				<td><input name="crypto_currency_faucets_key" type="text" id="crypto_currency_faucets_key"
									 value="<?php echo $key ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label>Currencies</label></th>
				<td>
					<?php
					foreach ( $all_currencies as $cur ) {
						?>
						<label>
							<input name="crypto_currency_faucets_currencies[]" type="checkbox"
										 value="<?php echo $cur ?>" <?php checked( in_array( $cur, $currencies ) ) ?>>
							<?php echo $cur ?>
						</label><br>
						<?php
					}
					?>
				</td>
			</tr>
		</table>
		<?php
	}
}