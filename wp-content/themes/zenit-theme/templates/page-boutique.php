<?php
/**
 * Template Name: Template : Boutique
 */
?>


<section id="boards-list" class="container-fluid"> 
<?php 
wc_print_notices(); ?>
<?php 

	$terms = get_field('selected_cat');
	$terms2 = get_field('categorie_presente');

	var_dump($terms); 
	var_dump($terms2); 

	foreach($terms as $term){ ?>
		<div class="row">
			<div class="col-sm-12">
				<h2><?php echo $term->name; ?></h2>
				<p class="description"><?php echo $term->description; ?></p>
			</div>
			<?php $loop = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			) ) ); ?>
			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<?php $_product = wc_get_product( get_the_ID() ); ?>
				<div class="col-sm-4 board">
						
						<div class="content-board">
							<a href="<?php echo get_permalink(); ?>">
							<div class="section-top">
								<h3><?php echo get_the_title(); ?></h3>
								<p class="price"><?php echo $_product->get_price(); ?>$</p>
							</div>
							<div class="image-board">
								<? the_post_thumbnail(); ?>
							</div>
							</a>
							<div class="hover">
								<ul class="spec-top">
									<?php

										// check if the repeater field has rows of data
										if( have_rows('specifications') ):

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
								<div class="bts-boards">
								<?php $url =  do_shortcode('[add_to_cart_url id="'.$product->ID.'"]'); 
										$urlSingle =  get_permalink();
								?>
									<button onclick="window.location.href='<?php echo $urlSingle; ?>'"><?php echo _e('Info','sage'); ?></button>
									<button onclick="window.location.href='<?php echo $url; ?>'"><?php echo get_field('buy_button'); ?></button>
									
									
								</div>
							</div>
						</div>

				</div>
			<?php endwhile; wp_reset_query(); ?>
		</div>
	<?php }
	
?>

</section>