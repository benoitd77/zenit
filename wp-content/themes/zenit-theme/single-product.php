<?php

$_product = wc_get_product( get_the_ID() );
$category = wp_get_post_terms( get_the_ID(), 'product_cat' );

$catSlug  = '';

for ( $i= 0; $i < count($category); $i++ ) {
	$catSlug  = $category[$i]->slug;
	if ($catSlug === 'board') {
		break;
	}
}

if ($catSlug === 'board') :
	/*---------------------------------*/
	// PAGE BOARD
	/*---------------------------------*/

	$col2Layout = false;

	if (have_rows('section')) :
		$cpt = 0;
		$col2Layout = true;	?>

		<div id="fixed-menu-single-product">
			<ul class="menu-single-product">
				<li><a href="#stats"><?php _e('Stats','zenit'); ?></a></li>
				<?php while ( have_rows('section') ) : the_row();
					if(get_sub_field('titre_menu')){
						echo "<li><a href='#$cpt'>".get_sub_field('titre_menu')."</a></li>";
						$cpt++;
					}
				endwhile; ?>
				<li><a href="#bottom-sales-section"><?php echo get_field('buy_button'); ?></a></li>
			</ul>
		</div>
	<?php endif; ?>

	<div id="single-prod" class="single-product<?php echo $col2Layout ? ' col2-layout' : ''; ?>">
		<?php
			if ($_product->is_type('simple')) :
				// Page board (view)
				get_template_part( 'single-simple-board' );
			else :
				// page personnaliser ?>
				<div class="variable-product clearfix">
					<?php get_template_part('single-variable-board'); ?>
				</div>
			<?php endif;
		?>
	</div>
<?php else :
	/*---------------------------------*/
	// PAGE DEFAULT
	/*---------------------------------*/ ?>
	<div id="single-prod" class="single-product">
		<div class="variable-product clearfix">
			<?php get_template_part('single-default'); ?>
		</div>
	</div>
<?php endif; ?>