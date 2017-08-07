<?php
/**
 * Template Name: Template : Boards
 */
?>

<section id="boards-list" class="container-fluid">
	<?php
	wc_print_notices();

	if (get_field('selec_board-cat', 303)) :
		$terms = get_field('selec_board-cat', 303);
		$currentLang = qtrans_getLanguage();
		$langTag = "[:" . $currentLang . "]";
	
		foreach($terms as $term) : ?>
			<div class="row brd">
				<div id="<?php echo $term->slug; ?>" class="col-sm-12 board-category">
					<?php
						$translations = explode($langTag, $term->description);
						$translation  = substr($translations[1], 0, strpos($translations[1], '[:'));
						$translation  = trim($translation);
					?>

					<h2><?php echo $term->name; ?></h2>
					<?php if ($term->description) : ?>
						<p class="description"><?php echo !empty($translation) ? $translation : $term->description; ?></p>
					<?php endif; ?>
				</div>
				<?php
					$loop = new WP_Query(
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
					);
				?>
				<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
					<?php $_product = wc_get_product( get_the_ID() ); ?>
					<div class="col-sm-3 col-xs-6 board">
						<div class="content-board">

							<div class="section-top">
								<h3><?php echo get_the_title(); ?></h3>

								<p itemprop="price" class="price"><?php echo $product->get_price_html(); ?></p>
							</div>
							<div class="image-board">
								<?php the_post_thumbnail(); ?>
							</div>

							<div class="hover">
								<div class="cont">
									<ul class="spec-top">
										<?php
											// check if the repeater field has rows of data
											if( have_rows('specifications') ) :
											    // loop through the rows of data
											    while ( have_rows('specifications') ) : the_row(); ?>
													<li><span><?php echo get_sub_field('title'); ?> : </span>

														<?php $outOfTen = get_sub_field('out_of_ten'); 	?>
														<ul class="meter single out-of-<?php echo $outOfTen; ?>">
															<li></li>
															<li></li>
															<li></li>
															<li></li>
															<li></li>
															<li></li>
															<li></li>
															<li></li>
															<li></li>
															<li></li>
														</ul>
											        </li>
											    <?php endwhile;
											else :
											    // no rows found
											endif;
										?>
									</ul>

									<ul class="spec-bottom">
										<li><?php echo get_field('longueur_titre'); ?><br/><span><?php echo get_field('longueur'); ?></span></li>
										<li><?php echo get_field('largeur_titre'); ?><br/><span><?php echo get_field('largeur'); ?></span></li>
										<li><?php echo get_field('wheelbase_titre'); ?><br/><span><?php echo get_field('wheelbase'); ?></span></li>
									</ul>

									<?php
										$url =  do_shortcode('[add_to_cart_url id="'.$product->ID.'"]');
										$urlSingle =  get_permalink();
										$id_complete = get_field('link_complete');
										$id_complete = $id_complete[0];
										$urlComplete = do_shortcode('[add_to_cart_url id="'.$id_complete.'"]');
									?>

									<div class="btn-boards">
										<button class="btn-acheter" onclick="window.location.href='<?php echo $urlSingle; ?>'"><?php echo _e('Info','sage'); ?></button>
										<button class="btn-acheter" onclick="window.location.href='<?php echo $urlSingle . "#bottom-sale-section"; ?>'"><?php echo get_field('buy_button'); ?></button>
										<div class="pop-up-acheter" style="display:none;">
											<ul>
												<li><a href="<?php echo $urlSingle; ?>"><button><?php echo _e('Board only','sage'); ?></button></a></li>
												<li><a href="<?php echo $urlComplete; ?>"><button><?php echo _e('Complete','sage'); ?></button></a></li>
											</ul>
										</div>
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