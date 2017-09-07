<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
/**
 * woocommerce_before_single_product hook.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

$currentLang = qtrans_getLanguage();
$currencySymbol = get_woocommerce_currency_symbol();


global $product;

$variations = new WC_Product_Variable( $product->id);
$variables = $variations->get_available_variations();

$recommended_setups = [];

foreach ($variables as $variable) {
	if ($variable['is_recommended']) {
		$recommended_setups[] = $variable;
	}
}

?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="main-panel">

		<div class="recommended-setup">
			<h2><?php echo $currentLang === 'fr' ? 'Configuration recommandée' : 'Recommended Setup'; ?></h2>
			<h3><?php echo $currentLang === 'fr' ? 'Sélectionnez une de nos configurations recommandées ou sélectionnez vos options individuellement plus bas' : 'Choose one of our recommended setup or choose your options individually below'; ?></h3>

			<?php $counter = 1; ?>
			<?php if (!empty($recommended_setups)) : ?>
				<?php foreach ($recommended_setups as $recommended_setup) : ?>

					<div class="board-setup <?php echo $counter == 1 ? 'selected-setup' : ''; ?>"
					     data-trucks="<?php echo $recommended_setup['attributes']['attribute_pa_trucks']; ?>"
					     data-wheels="<?php echo $recommended_setup['attributes']['attribute_pa_roues']; ?>">
						<img src="<?php echo $recommended_setup['image_src']; ?>">
						<h4><?php echo $recommended_setup['variation_description']; ?></h4>
						<span><?php echo $recommended_setup['price_html']; ?></span>
					</div>

					<?php $counter++; ?>

				<?php endforeach; ?>
			<?php else : ?>
				<h4><?php echo $currentLang === 'fr' ? 'Cette planche n\'a pas de configuration recommandée' : 'This Board doesn\'t have any Recommended Setup'; ?></h4>
			<?php endif; ?>

		</div>

		<div id="list-variations" class="variation-container">
			<?php
				// Get product attributes
				$attributes = $product->get_attributes();

				if ( ! $attributes ) {
					echo "No attributes";
				}

				foreach ( $attributes as $attribute ) {

					$attributeName =  $attribute['name'];
					$values = get_terms($attributeName);

					echo "<div class='row'>";

					$term = get_taxonomy($attributeName);


					echo "<h5>".$term->labels->name."</h5>";
					echo "<div class='variation-list'>";
					foreach ( $values as $value ) {

						$metas = get_term_meta($value->term_taxonomy_id);
						$out_of_stock = $metas['out_of_stock'];

						if(!$out_of_stock[0]){
							echo "<div class='variation col-sm-4 col-xs-6' data-name='".__($value->name)."' data-desc='".__($value->description)."' data-slug='".$value->slug."' data-list='".$value->taxonomy."'>";
							// image id is stored as term meta
							echo "<div class='variation-cont'>";
							$image_id = get_term_meta( $value->term_id, 'image', true );

							// image data stored in array, second argument is which image size to retrieve
							$image_data = wp_get_attachment_image_src( $image_id, 'full' );

							// image url is the first item in the array (aka 0)
							$image = $image_data[0];

							if ( ! empty( $image ) ) {
								echo '<img src="' . esc_url( $image ) . '" />';
							}

							echo "<p>";
							_e($value->name);
							echo  "</p>";

							echo "</div></div>";
						}
					}
					echo "</div></div>";
				}
			?>
		</div>
	</div><!-- .main-panel -->

	<div id="recap_variable" class="side-panel">
		<h2><?php echo $currentLang === 'fr' ? 'Sommaire' : 'Summary'; ?></h2>
		<h3><?php echo $product->post->post_title; ?></h3>
		<?php
			/**
			 * woocommerce_single_board_summary hook.
			 *
			 * @hooked woocommerce_template_single_board_add_to_cart - 30
			 * @hooked woocommerce_template_single_board_description - 40
			 */
			do_action( 'woocommerce_single_board_summary' );
		?>
	</div>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product #### -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
