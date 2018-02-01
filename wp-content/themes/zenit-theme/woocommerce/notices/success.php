<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

global $product;

$currentProduct = new WC_Product($product->id);

$upsells = $currentProduct->get_upsells();

$currentLang = qtrans_getLanguage();
$langTag     = "[:" . $currentLang . "]";

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="cart-overlay">
		<span class="close-message"></span>
		<div class="inner-top">
			<div class="woocommerce-message">
				<?php echo wp_kses_post( $message ); ?>
			</div>
			<div class="woocommerce-message-fake">
				<a href="javascript:void(0)" class="button keep-shopping"><?php echo ($currentLang === 'fr') ? 'Continuer à magasiner' : 'Continue shopping'; ?></a>
			</div>
		</div>
		<?php if (count($upsells) > 0) : ?>
			<div class="inner-bottom">
				<p class="upsell-title"><?php echo ($currentLang === 'fr') ? 'Vous pourriez aussi aimer' : 'You may also like'; ?></p>
				<div class="upsell-wrapper">
					<ul class="clearfix">
						<?php foreach($upsells as $upsell_prod_id) :
							$_product = new WC_Product_Variable($upsell_prod_id);
							$_product_name_str  = $_product->post->post_title;

							if (strpos($_product_name_str, $langTag) !== false) {
								$_product_name_arr = explode($langTag, $_product_name_str);
								$_product_name  = substr($_product_name_arr[1], 0, strpos($_product_name_arr[1], '[:'));
								$_product_name  = trim($_product_name);
							} else {
								$_product_name = $_product_name_str;
							}

							$_product_url   = get_permalink($upsell_prod_id);
							$_image_source  = get_the_post_thumbnail( $upsell_prod_id, array(150, 150) );
							$_product_price = $_product->get_regular_price();

							// Check if product_price is empty
							// If it's empty it means it has variable prices, use get_price_html instead
							if (empty($_product_price)) {
								$_product_price = $_product->get_price_html();
							} else {
								$_product_price = number_format(floatval($_product_price), 2);
								$_product_price .= get_woocommerce_currency_symbol();
							}
							?>
							<li class="upsell-item">
								<a href="<?php echo $_product_url; ?>">
									<?php echo $_image_source; ?>
									<h4><?php echo $_product_name; ?></h4>
									<p><?php echo $_product_price; ?></p>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
