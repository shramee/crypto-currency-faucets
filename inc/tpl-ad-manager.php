<?php
/**
 *
 */

?>
<div class="wrap">
	<h2>Ad manager</h2>
	<?php settings_errors() ?>
	<form action="options.php" method="post">
		<?php
		do_settings_sections( 'crypto_currency_ad_manager' );
		settings_fields( 'crypto_currency_ad_manager' );

		for ( $i = 1; $i <= 5; $i++ ) {
			$snippet = Crypto_Currency_Faucets::ad_data( $i, 'snippet' );
			$name = Crypto_Currency_Faucets::ad_data( $i, 'name' );
			$source = Crypto_Currency_Faucets::ad_data( $i, 'source' );
			?>
			<div class="ad-man-shortcode-section about-wrap" style="<?php echo $i === 1 || $snippet ? '' : 'display:none' ?>">
				<div class="two-col">
					<div class="col">
						<input type="text" name="crypto_currency_ad_data[<?php echo $i ?>][name]" placeholder="Ad Name"
						value="<?php echo $name ?>">
						<input type="text" name="crypto_currency_ad_data[<?php echo $i ?>][source]" placeholder="Ad Source"
						value="<?php echo $source ?>">
					</div>
					<div class="col">
						<textarea name="crypto_currency_ad_data[<?php echo $i ?>][snippet]" placeholder="Ad Snippet Input"><?php echo $snippet ?></textarea>
					</div>
				</div>

				<input type="text" readonly="readonly" onClick="this.select()"
					placeholder="Shortcode will appear here after saving" value="<?php if ( $snippet ) {
					echo "[crypto-currency-ad id='$i' name='$name' source='$source']";
				} ?>">
			</div>
			<?php
		}
		?>

		<p class="ad-man-add-more">
			<a
				href="javascript:void(0)" class="button"
				onclick="jQuery(this).closest('p').siblings('.ad-man-shortcode-section[style*=\'display:none\']:first').slideDown()">Add More</a>
		</p>
		<?php
		submit_button();
		?>
	</form>
</div>
<?php
?>



