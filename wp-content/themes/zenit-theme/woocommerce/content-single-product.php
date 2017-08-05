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

	global $product;
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * woocommerce_before_single_product_summary hook.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		//do_action( 'woocommerce_before_single_product_summary' );
	?>

	<?php
		get_template_part( 'single-product-image' );
	?>

	<div class="summary entry-summary">
		<h1><?php echo get_the_title(); ?></h1>

		<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

			<p class="price"><?php echo $product->get_price_html(); ?></p>

			<meta itemprop="price" content="<?php echo esc_attr( $product->get_display_price() ); ?>" />
			<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
			<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

		</div>

		<div id="list-variations" class="container">
			<?php
			global $product;

			// Get product attributes
			$attributes = $product->get_attributes();

			if (!empty($attributes) && $attributes) :
				foreach ( $attributes as $attribute ) :
					$attributeName =  $attribute['name'];
					$values = get_terms($attributeName);
					$term = get_taxonomy($attributeName);
					?>

					<div class="row">
						<div class="variation-list clearfix">
							<?php foreach ( $values as $value ) :
								$metas = get_term_meta($value->term_taxonomy_id);
								$out_of_stock = $metas['out_of_stock'];

								if(!$out_of_stock[0]): ?>
									<div class="variation variation-size" data-slug="<?php echo $value->slug; ?>" data-list="<?php echo $value->taxonomy; ?>">
										<!--image id is stored as term meta-->
										<div class="variation-cont">
											<?php
											$image_id = get_term_meta( $value->term_id, 'image', true );

											// image data stored in array, second argument is which image size to retrieve
											$image_data = wp_get_attachment_image_src( $image_id, 'full' );

											// image url is the first item in the array (aka 0)
											$image = $image_data[0];

											if ( ! empty( $image ) ) : ?>
												<img src="<?php echo esc_url( $image ); ?>">
											<?php endif; ?>

											<p><?php _e($value->name); ?></p>

											<p class="small"><?php _e($value->description); ?></p>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach;
			endif; ?>
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

		<div class="prod-desc">
			<p><?php echo $product->post->post_content; ?></p>
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
