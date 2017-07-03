<?php

$col2Layout = false;	
	
if (have_rows('section')) {
    $cpt = 0;
    $col2Layout = true;
    ?>
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
<?php }	?>

<div id="single-prod" class="single-product<?php echo $col2Layout ? ' col2-layout' : ''; ?>">
<?php
	$_product = wc_get_product( get_the_ID() );

	if ($_product->is_type('simple')) {
		get_template_part('single-simple');
	} else {
		if ($_product->is_type('variable')) {
			?>
				<div class="variable-product">
					<?php get_template_part('single-variable'); ?>
				</div>
			<?php
		} else {
			get_template_part('single-simple');
		}
	}
?>
</div>