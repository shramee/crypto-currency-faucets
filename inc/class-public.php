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
	 * Renders shortcode on frontend
	 *
	 * @param $attr
	 */
	public function shortcode( $attr ) {
		$faucets    = $this->get_faucets();
		$currencies = $this->get_currencies( $faucets );

		if ( count( $currencies ) > 1 ) {
			$current = $this->shortcode_tabs( $currencies );
			$currencies = $current ? $currencies = [ $current => $currencies[ $current ] ] : $currencies;
		}
		?>
		<table id="crypto-currency-faucets">
			<tr>
				<th>Faucet name</th>
				<th>Currency</th>
				<th>Reward</th>
				<th>Visit</th>
			</tr>
			<?php foreach ( $faucets as $f ) {
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
			} ?>
		</table>
		<?php
	}

	/**
	 * Adds front end stylesheet and js
	 * @return array
	 */
	public function get_faucets() {
		$faucets = get_transient( 'cc-faucets' );

		if ( ! $faucets ) {
			$faucets = [];
			$resp    = wp_remote_get( 'https://faucetlist.me/api' );
			if ( is_array( $resp ) ) {
				$faucets = json_decode( $resp['body'] );
				set_transient( 'cc-faucets', $faucets, DAY_IN_SECONDS );
			}
		}

		return $faucets;
	}

	/**
	 * Get currencies from array of faucets
	 *
	 * @param array $faucets Faucets to get currencies of
	 *
	 * @return array
	 */
	public function get_currencies( $faucets ) {

		$currencies = [];

		foreach ( $faucets as $f ) {
			$currencies[ $f->currency ] = empty( $currencies[ $f->currency ] ) ? 1 : ++$currencies[ $f->currency ];
		}

		return $currencies;
	}

	/**
	 * Shows all currency tabs
	 * @param array $currencies All currencies to show
	 * @return string Current currency
	 */
	protected function shortcode_tabs( $currencies ) {
		$current = filter_input( INPUT_GET, 'faucets-currency' );
		?>
		<div class="cc-faucets-tabs">
			<a class="cc-faucets-tab <?php echo ! $current ? 'current' : '' ?>" href="?faucets-currency">ALL
				<span class="cc-faucet-badge"><?php echo array_sum( $currencies ); ?></span></a>
			<?php
			foreach ( $currencies as $currency => $num_f ) {
				$url = '?faucets-currency=' . urlencode( $currency );
					?>
					<a href="<?php echo $url ?>" class="cc-faucets-tab <?php echo $currency === $current ? 'current' : '' ?>">
						<?php echo $currency ?></a>
					<?php
			}
			?>
		</div>
		<?php

		return $current;
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