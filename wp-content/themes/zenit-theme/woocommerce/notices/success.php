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

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="cont-woocommerce-message">
		<span class="close-message"></span>
		<div class="woocommerce-message"><?php echo wp_kses_post( $message ); ?></div>
		<div class="cart-items">
			<hr>
			<h5><?php _e('Actuellement dans votre panier : '); ?></h5>
			<?php
			    global $woocommerce;
			    $items = $woocommerce->cart->get_cart();

			        foreach($items as $item => $values) { 
			            $_product = $values['data']->post;
			            $price = get_post_meta($values['product_id'] , '_price', true);
			            echo '<p>';
			            _e($_product->post_title);
			            echo ' x '.$values['quantity'].'</p>';
						//.' = '.$price*$values['quantity'].'$</p>'; 
			        } 
			?>

		</div>
	</div>
<?php endforeach; ?>
