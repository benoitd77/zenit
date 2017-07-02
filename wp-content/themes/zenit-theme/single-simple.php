<?php while (have_posts()) : the_post(); ?>

<?php
	if(get_field('texte_en_blanc') && get_field('texte_en_blanc') == 1){
		$classHeader = "class='white'";
	}else{
		$classHeader="";
	}
?>

<script language="javascript">

function adjustBottomSales() {
	var boardOnly = document.getElementById('container-boardonly');
	var complete  = document.getElementById('container-complete');
	var boardOnlyImg = null;
	var completeImg  = null;

	if (boardOnly != null && complete != null) {
		boardOnlyImg = boardOnly.getElementsByTagName('img')[0];
		completeImg  = complete.getElementsByTagName('img')[0];		
	}

	if (boardOnlyImg != null && completeImg != null) {
		boardOnlyImg.style.marginTop = (completeImg.height - boardOnlyImg.height) + "px";
	}
}

setTimeout(function(){
	adjustBottomSales();
}, 1000);

setTimeout(function(){
	adjustBottomSales();
}, 5000);

if(window.addEventListener) {
    window.addEventListener('resize', function() {
        adjustBottomSales();
    }, true);
}
	
</script>
	
<div id="header-product" <?php echo $classHeader; ?>>
<?php 
wc_print_notices(); ?>
	<div id="slider-container">
		<div class="slider-single slider">
			<?php

			// check if the repeater field has rows of data
			if( have_rows('visionneuse_haut') ):

			 	// loop through the rows of data
			    while ( have_rows('visionneuse_haut') ) : the_row(); ?>
					<div class="cont-img">
			        
			        <img src="<?php echo get_sub_field('image'); ?>" alt="<?php echo get_the_title(); ?>" />

			        </div>

			    <?php endwhile;

			endif;

			?>
		</div>
	</div>

	<?php
		if(get_field('specification_en_noir') && get_field('specification_en_noir') == 1){
			$classStats = "class='black'";
		}else{
			$classStats="";
		}

	?>

	
	<div id="stats" class="stats-single"  style="background-image:url('<?php echo get_field('background-image'); ?>')">
		<h1><?php echo get_the_title(); ?></h1>
		<?php if(get_field('s-titre')){ ?>
			<h3><?php echo get_field('s-titre'); ?></h3>
		<?php } ?>
		<div class="description"><?php echo get_the_content(); ?></div>
		<?php $url =  do_shortcode('[add_to_cart_url id="'.$product->ID.'"]');  ?>
		<?php $_product = wc_get_product( get_the_id() ); ?>
		
		<?php if($_product->is_in_stock()){ ?>
			<a href="<?php echo $url; ?>" class="button"><?php echo get_field('button_add_to_cart'); ?></a>
		<?php }else{ ?>
			<a href="#" class="button disabled"><?php echo _e('Out of stock','zenit'); ?></a>
		<?php } ?>
		<ul id="specs" <?php echo $classStats; ?>>
			<?php
				// check if the repeater field has rows of data
				if( have_rows('specifications') ):
					// loop through the rows of data
					$i = 0;
					while ( have_rows('specifications') ) : the_row(); ?>
						<li>
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

							<span class="title-meter" data-id="<?php echo $i; ?>" ><?php echo get_sub_field('title'); ?></span>
							<!--<span class="description-meter" data-id-desc="<?php //echo $i; ?>"><?php// echo get_sub_field('desc'); ?></span>-->
						</li>
						<span class="description-meter" data-id-desc="<?php echo $i; ?>"><?php echo get_sub_field('desc'); ?></span>
						<?php $i++; ?>
				<?php endwhile; endif; ?>
		</ul>
	</div>	
</div>
	
	

<?php $cpt = 0; ?>
<div id="page-builder" class="container-fluid">
		<?php

		// check if the repeater field has rows of data
		if( have_rows('section') ):

		 	// loop through the rows of data
		    while ( have_rows('section') ) : the_row();

		        
		        $type = get_sub_field('type');
		        
		        if($type == 'Image' ){
		        	set_query_var( 'cpt', $cpt );
		        	get_template_part('templates/content-image');
		        } 

		        if($type == 'image_texte' ){
		        	set_query_var( 'cpt', $cpt );
		        	get_template_part('templates/content-image-fw');
		        }

		        if($type == 'video' ){
		        	set_query_var( 'cpt', $cpt );
		        	get_template_part('templates/content-video');
		        }

		        if($type == 'text-fw'){
		        	set_query_var( 'cpt', $cpt );
		        	get_template_part('templates/content-text-fw');
		        }

		        if(get_sub_field('titre_menu')){
		        	$cpt++;
		        }

		        

		    endwhile;

		else :

		    // no rows found

		endif;

		?>
	</div>

<div id="bottom-sales-section" class="container-fluid sale-section">
	<div id="bottom-sale-section" class="outer-anchor"></div>
	<div class="row">
	<div id="container-boardonly" class="col-sm-6">
		<div class="board">
			<?php $_product = wc_get_product( get_the_ID() ); ?>
			<p><?php echo get_the_title(); ?> (<?php echo get_field('board_only'); ?>)</p>
			<p itemprop="price" class="price"><?php echo $_product->get_price_html(); ?></p>
			<?php $arrVis = get_field('visionneuse_haut'); ?>
			<div class="image-board">
				<?php $url = do_shortcode('[add_to_cart_url id="'.$_product->ID.'"]');  ?>
				<img src="<?php echo $arrVis[0]['image']; ?>" />
				<?php if($_product->is_in_stock()){ ?>
					<a href="<?php echo $url; ?>" class="button"><?php echo get_field('button_add_to_cart'); ?></a>
				<?php }else{ ?>
					<a href="#" class="button disabled"><?php echo _e('Out of stock','zenit'); ?></a>
				<?php } ?>
			</div>
		</div>
	</div>
	<div id="container-complete" class="col-sm-6">
		<?php if(get_field('link_complete')){ ?>
		<?php $id_complete = get_field('link_complete'); 
			$id_complete = $id_complete[0];
		?>
		<div class="board">
			<?php $_product = wc_get_product( $id_complete ); ?>
			<p><?php echo get_the_title( $id_complete ); ?></p>
			<p itemprop="price" class="price"><?php echo $_product->get_price_html(); ?></p>
			<div class="image-board">
				<?php echo get_the_post_thumbnail( $id_complete ); ?>
				<?php $url = do_shortcode('[add_to_cart_url id="'.$id_complete.'"]');  ?>
				<?php if($_product->is_in_stock()){ ?>
					<a href="<?php echo $url; ?>" class="button"><?php echo get_field('button_customize'); ?></a>
				<?php }else{ ?>
					<a href="#" class="button disabled"><?php echo _e('Out of stock','zenit'); ?></a>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
	</div>
</div>

<?php /*
		
		// check if the repeater field has rows of data
		if( have_rows('section') ){ ?>
		<?php $cpt = 0; ?>
		<div id="fixed-menu-single-product">
			<ul>
			<li><a href="#stats"><?php _e('Stats','zenit'); ?></a></li>
		   <?php while ( have_rows('section') ) : the_row();
		       
		        if(get_sub_field('titre_menu')){
		        	echo "<li><a href='#$cpt'>".get_sub_field('titre_menu')."</a></li>";
		        	$cpt++;
		        }	        

		    endwhile;
		    ?>
		    <li><a href="#bottom-sale-section"><?php echo get_field('buy_button'); ?></a></li>
		    </ul>
			</div>
		<?php }	*/ ?>

<div class="container-fluid bottom-section">
	<div class="row">
		<div class="col-sm-4">
			<img src="<?php echo get_field('1_icone','option'); ?>" alt="Icone 1"/>
			<h5><?php echo get_field('1_titre','option'); ?></h5>
			<p><?php echo get_field('1_description','option'); ?></p>
		</div>
		<div class="col-sm-4">
			<img src="<?php echo get_field('2_icone','option'); ?>" alt="Icone 2"/>
			<h5><?php echo get_field('2_titre','option'); ?></h5>
			<p><?php echo get_field('2_description','option'); ?></p>
		</div>
		<div class="col-sm-4">
			<img src="<?php echo get_field('3_icone','option'); ?>" alt="Icone 3"/>
			<h5><?php echo get_field('3_titre','option'); ?></h5>
			<p><?php echo get_field('3_description','option'); ?></p>
		</div>
	</div>
</div>	
<?php endwhile; wp_reset_query(); ?>