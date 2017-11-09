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
		add_menu_page(
			'Crypto Currency Faucets',
			'Crypto Currency',
			'manage_options',
			'crypto_currency_faucets',
			[ $this, 'admin_page' ],
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTI1Ij48cGF0aCBmaWxsPSJibGFjayIgZD0iTTkwIDkwLjJjMCA0LjQtMy41IDgtOCA4LTQuNCAwLTgtMy42LTgtOCAwLTQuOCA0LjctMTAuMyA2LjctMTQgMS0yIDEtMyAxLjQtM3MuNSAxLjIgMS41IDMuM2MyIDMuNyA2LjcgOSA2LjcgMTMuN3pNODIgMzlINTguM2MtMyAwLTQuNC0yLTQuNC00LjhWMjEuOGgtNHYtOC40YzEuNS0uNyAyLjctMS44IDMuNC0zIDUuNi40IDEwIDMuMyAxNSAzLjMgMy40IDAgNi0yLjcgNi02IDAtMy41LTIuNi02LjItNi02LjItNC43IDAtOS40IDIuMy0xNS4yIDMtMS40LTIuMi00LjYtNC04LjMtNC00IDAtNyAyLTguNSA0LjQtNS42LS43LTEwLTMuNi0xNS0zLjYtMy40IDAtNi4yIDIuNy02LjIgNiAwIDMuNSAyLjggNi4yIDYuMiA2LjIgNC42IDAgOS40LTIuMiAxNS0zIDEgMSAyIDIgMy40IDIuOHY4LjNoLTQuN1YzNGMwIDMtLjcgNS00LjUgNUgxMC41djE4aDIxLjhzNiAxLjIgMTIuMyAxLjJDNTEgNTguMiA1NyA1NyA1NyA1N2g2LjhjMi43IDAgNy41LS4yIDcuNSAyLjZsLjQgMTAuNmMzLjggMS43IDE2LjMgMS4yIDE5LS4zbC4yLTIzLjVjMC01LTMtNy43LTktNy43eiIvPjwvc3ZnPgo='
		);

		add_submenu_page(
			'crypto_currency_faucets',
			'Getting Started',
			'Getting Started',
			'manage_options',
			'crypto_currency_faucets_getting_started',
			[ $this, 'admin_page_getting_started' ]
		);

		add_submenu_page(
			'crypto_currency_faucets',
			'Ad manager',
			'Ad manager',
			'manage_options',
			'crypto_currency_faucets_ad_manager',
			[ $this, 'admin_page_ad_manager' ]
		);
	}

	/**
	 * Renders the admin page
	 */
	public function admin_page() {
		?>
		<div class="wrap">
			<h2>Crypto Currency Faucets</h2>
			<?php settings_errors() ?>
			<p>Use shortcode <code>[crypto-currency-faucets]</code> to show faucets table on any page.</p>
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
	 * Adds front end stylesheet and js
	 * @return array
	 */
	public function getting_started_content() {
		$content = get_transient( 'cc-faucets-getting-started' );

		if ( ! $content ) {
			$resp    = wp_remote_get( 'http://axiomcrypto.org/SatoshisToolBox/wp-json/wp/v2/crypto-plugin/4' );
			if ( is_array( $resp ) ) {
				$resp = json_decode( $resp['body'] );
				$content = $resp->content->rendered;
				set_transient( 'cc-faucets-getting-started', $content, DAY_IN_SECONDS );
			}
		}

		return $content;
	}

	/**
	 * Renders the admin page
	 */
	public function admin_page_getting_started() {

		?>
		<div class="wrap">
			<h2>Crypto Currency Faucets</h2>
			<?php
			settings_errors();
			echo $this->getting_started_content();

	}

	/**
	 * Renders the admin page
	 */
	public function admin_page_ad_manager() {
		?>
		<div class="wrap">
			<h2>Ad manager</h2>
			<?php settings_errors() ?>
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
		$currencies     = get_option( 'crypto_currency_faucets_currencies', Crypto_Currency_Faucets::$currencies );
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
					foreach ( Crypto_Currency_Faucets::$currencies as $cur ) {
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

	/**
	 * Clear cache when options are updated
	 * @action update_option_crypto_currency_faucets_api
	 * @action update_option_crypto_currency_faucets_key
	 * @action update_option_crypto_currency_faucets_currencies
	 */
	public function clear_cache() {
		delete_transient( 'cc-faucets-faucethub.io' );
		delete_transient( 'cc-faucets-faucetlist.me' );
	}
}