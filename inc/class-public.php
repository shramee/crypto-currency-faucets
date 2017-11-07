<?php

/**
 * Crypto Currency Faucets public class
 */
class Crypto_Currency_Faucets_Public {

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
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   = Crypto_Currency_Faucets::$token;
		$this->url     = Crypto_Currency_Faucets::$url;
		$this->path    = Crypto_Currency_Faucets::$path;
		$this->version = Crypto_Currency_Faucets::$version;
	}

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
	 * Adds front end stylesheet and js
	 * @return array
	 */
	public function faucetlist_me_faucets() {
		$faucets = get_transient( 'cc-faucets-faucethub.io' );

		if ( ! $faucets ) {
			$faucets = [];
			$resp    = wp_remote_get( 'https://faucetlist.me/api' );
			if ( is_array( $resp ) ) {
				$faucets = json_decode( $resp['body'] );
				set_transient( 'cc-faucets-faucethub.io', $faucets, DAY_IN_SECONDS );
			}
		}

		return $faucets;
	}

	/**
	 * Get faucets from faucetlist.me
	 * @return string|array
	 */
	public function faucethub_io_faucets() {
		$faucets = get_transient( 'cc-faucets-faucetlist.me' );

		if ( ! $faucets ) {
			$currencies = get_option( 'crypto_currency_faucets_currencies', Crypto_Currency_Faucets::$currencies );
			$key        = get_option( 'crypto_currency_faucets_key' );
			$faucets    = [];
			if ( ! $key ) {
				return 'Error: Key required for faucetlist.me API.';
			}
			$resp = wp_remote_post( 'https://faucethub.io/api/listv1/faucetlist', [
				'body' => array( 'api_key' => $key, ),
			] );
			if ( is_array( $resp ) ) {
				$faucets_data = json_decode( $resp['body'], 'array' );
				if ( $faucets_data['message'] == 'OK' ) {
					$this->faucethub_io_extract_faucets( $faucets, $faucets_data['list_data'], 'premium', $currencies );
					$this->faucethub_io_extract_faucets( $faucets, $faucets_data['list_data'], 'normal', $currencies );
				}
				set_transient( 'cc-faucets-faucetlist.me', $faucets, DAY_IN_SECONDS );
			}
		}

		return $faucets;
	}

	/**
	 * Extract faucets from faucetlist.me API response
	 *
	 * @param array &$faucets Faucets array (by reference)
	 * @param array $list_data $response['list_data']
	 * @param string $key Key in $list_data array
	 * @param array $currencies Currencies to grab faucets for
	 */
	public function faucethub_io_extract_faucets( &$faucets, $list_data, $key, $currencies ) {
		if ( ! empty ( $list_data[ $key ] ) ) {
			foreach ( $list_data[ $key ] as $currency => $c_faucets ) {
				if ( in_array( $currency, $currencies ) ) {
					foreach ( $c_faucets as $f ) {

						$faucet = new stdClass();

						$faucet->name     = $f['name'];
						$faucet->currency = $f['currency'];
						$faucet->reward   = $f['reward'];
						$faucet->url      = $f['url'];

						$faucets[] = $faucet;
					}
				}
			}
		}
	}

	/**
	 * Renders shortcode on frontend
	 */
	public function shortcode() {
		$faucets    = $this->get_faucets();
		$currencies = $this->get_currencies( $faucets );
		ob_start();
		?>
		<div id="crypto-currency-faucets">
			<?php
			if ( count( $currencies ) > 1 ) {
				$current    = $this->shortcode_select( $currencies ); // Output tabs as well
				$currencies = $current ? $currencies = [ $current => $currencies[ $current ] ] : $currencies;
			}
			?>
			<table id="crypto-currency-faucets-table">
				<tr>
					<th>Faucet name</th>
					<th>Currency</th>
					<th>Reward</th>
					<th>Visit</th>
				</tr>
				<?php $this->shortcode_faucets_rows( $faucets, $currencies ) ?>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Gets faucets from API selected by admin
	 * @return string|array Error message or Faucets [ stdObj( name, currency, reward, url ) ]
	 */
	public function get_faucets() {
		$provider = get_option( 'crypto_currency_faucets_api', 'faucetlist.me' );

		// faucetlist_me_faucets and faucethub_io_faucets
		$method = str_replace( '.', '_', $provider ) . '_faucets';

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}

		return [];
	}

	/**
	 * Get currencies from array of faucets
	 *
	 * @param array $faucets Faucets to get currencies of
	 *
	 * @return array
	 */
	public function get_currencies( $faucets ) {

		$currencies = get_option( 'crypto_currency_faucets_currencies', Crypto_Currency_Faucets::$currencies );

		$curr_items = [];

		foreach ( $faucets as $f ) {
			if ( in_array( $f->currency, $currencies ) ) {
				$curr_items[ $f->currency ] = empty( $curr_items[ $f->currency ] ) ? 1 : ++ $curr_items[ $f->currency ];
			}
		}


		return $curr_items;
	}

	/**
	 * Shows all currency tabs
	 *
	 * @param array $currencies All currencies to show
	 *
	 * @return string Current currency
	 */
	protected function shortcode_select( $currencies ) {
		$current = filter_input( INPUT_GET, 'faucets-currency' );
		?>
		<div class="cc-faucets-select">
			<label>
				Select Your Crypto
				<select id="cc-faucets-select" onchange="window.location = '?faucets-currency=' + this.value">
					<?php
					foreach ( $currencies as $currency => $num_f ) {
						if ( ! $current ) {
							$current = $currency;
						}
						?>
						<option value="<?php echo urlencode( $currency ) ?>" <?php selected( $currency, $current ) ?>>
							<?php echo $currency ?></option>
						<?php
					}
					?>
				</select>
			</label>
		</div>
		<?php
		return $current;
	}

	/**
	 * Outputs table rows for each faucet
	 *
	 * @param array $faucets
	 * @param array $currencies
	 */
	public function shortcode_faucets_rows( $faucets, $currencies ) {
		foreach ( $faucets as $f ) {
			if ( isset( $currencies[ $f->currency ] ) ) {
				?>
				<tr>
					<td class="cc-faucet-name"><?php echo $f->name ?></td>
					<td class="cc-faucet-currency"><?php echo $f->currency ?></td>
					<td class="cc-faucet-reward"><?php echo $f->reward ?></td>
					<td class="cc-faucet-url"><a href="<?php echo $f->url ?>">Visit</a></td>
				</tr>
				<?php
			}
		}
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		$token = $this->token;
		$url   = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front.js', array( 'jquery' ) );
	}
}