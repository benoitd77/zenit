<?php
/**
 * Template Name: Template : Vetements
 */
?>

<section id="product-list" class="container-fluid">
	<?php
	wc_print_notices();

	if (get_field('selected_cat', 8260)) :
		$terms       = get_field('selected_cat', 8260);
		$currentLang = qtrans_getLanguage();
		$langTag     = "[:" . $currentLang . "]";

		foreach($terms as $term): ?>
			<div class="row">
				<div id="<?php echo $term->slug; ?>" class="col-sm-12">
					<?php
						$translations = explode($langTag, $term->description);
						$translation  = substr($translations[1], 0, strpos($translations[1], '[:'));
						$translation  = trim($translation);
					?>

					<h2><?php echo $currentLang === 'fr' ? 'Vêtements Zenit' : 'Zenit\'s Clothing'; ?></h2>
					<?php if($term->description): ?>
						 <p class="description"><?php echo !empty($translation) ? $translation : $term->description; ?></p>
					<?php endif; ?>
				</div>

				<?php $loop = new WP_Query(
					[
						'post_type' => 'product',
						'posts_per_page' => -1,
						'tax_query' => [
							[
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => $term->term_id,
							]
						]
					]
				); ?>

				<?php while ( $loop->have_posts() ) : $loop->the_post();
					$_product = wc_get_product( get_the_ID() ); ?>

					<div class="col-sm-3 col-xs-6 default-product">
						<div class="content-default">
							<div class="section-top">
								<h3><?php echo get_the_title(); ?></h3>

								<p itemprop="price" class="price"><?php echo $product->get_price_html(); ?></p>
							</div>
							<div class="image-product">
								<?php // the_post_thumbnail(); ?>
								<img class="lazy" data-original="<?php echo get_the_post_thumbnail_url($product->ID); ?>">
							</div>

							<div class="hover">
								<div class="cont">
									<?php
										$url =  do_shortcode('[add_to_cart_url id="'.$product->ID.'"]');
										$urlSingle =  get_permalink();
										$id_complete = get_field('link_complete');
										$id_complete = $id_complete[0];
										$urlComplete = do_shortcode('[add_to_cart_url id="'.$id_complete.'"]');
									?>

									<div class="btn-default-prod">
										<button class="btn-acheter" onclick="window.location.href='<?php echo $urlSingle; ?>'">Info</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endwhile; wp_reset_query(); ?>
			</div>
		<?php endforeach;
	endif; ?>
</section>