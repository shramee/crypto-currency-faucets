/**
 * Plugin front end scripts
 *
 * @package Crypto_Currency_Faucets
 * @version 1.0.0
 */
jQuery(function ($) {
	var
		$apiVal = $( '#crypto_currency_faucets_api' ),
		$apiKeyRo = $( '#crypto_currency_faucets_key_row' ),
		apiKeyHideShow = function () {
			if ( 'faucethub.io' === $apiVal.val() ) {
				$apiKeyRo.slideDown();
			} else {
				$apiKeyRo.slideUp();
			}
		};
	$apiVal.change( apiKeyHideShow );
	apiKeyHideShow();
});