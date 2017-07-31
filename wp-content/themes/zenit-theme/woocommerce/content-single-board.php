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
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * woocommerce_before_single_product_summary hook.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">
		<h1><?php echo get_the_title(); ?></h1>

		<?php if(get_field('s-titre')){ ?>
			<h4><?php echo get_field('s-titre'); ?></h4>
		<?php } ?>

		<?php if(get_field('recommended_setup')){ ?>
			<p><?php echo get_field('recommended_setup'); ?></p>
		<?php } ?>

		<div id="list-variations" class="container">
			<?php
				global $product;
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
							echo "<div class='variation col-sm-4 col-xs-6' data-slug='".$value->slug."' data-list='".$value->taxonomy."'>";
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

							echo "<p class='small'>";
							_e($value->description);
							echo "</p>";

							echo "</div></div>";
						}
					}
					echo "</div></div>";
				} ?>
			</div>
		<div id="recap_variable">
			<h2><?php _e('Summary','sage'); ?></h2>
			<?php
				/**
				 * woocommerce_single_product_summary hook.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
	</div><!-- .summary -->

	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		//do_action( 'woocommerce_after_single_product_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
