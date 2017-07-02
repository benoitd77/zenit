<script language="javascript">

function adjustColHeight() {
	var colRight = document.getElementById('single-prod');
	var colMenu  = document.getElementById('fixed-menu-single-product');
	var colRightHeight = 0;

	if (colRight != null && colMenu != null) {
		colMenu.style.height = colRight.offsetHeight + "px";
	}
}

for (var i = 500; i <= 3500; i+=1000) {
	setTimeout(function(){
		adjustColHeight();
	}, i);
}

if(window.addEventListener) {
    window.addEventListener('resize', function() {
        adjustColHeight();
    }, true);

    window.addEventListener("load", function() {
        console.log("All resources finished loading!");
    });
}
	
</script>
<style>
	body.single-product .sale-section .board {
		margin-left: 5% !important;
		margin-right: 5% !important;
		width: 90%;
	}

	body.single-product .image-board {
        width: 100%;
    }

	body.single-product .image-board img {
		max-height: initial;
		width: 100%;
	}

	#fixed-menu-single-product {
		position: initial !important;
		height: 1000px;
		width: 190px;
		float: left;
		box-shadow: none;
		border-right: 2px solid #e6e6e6;
	}

	#fixed-menu-single-product ul {
		position: fixed;
	}

	div.single-product.col2-layout {
		width: calc(100% - 190px);
	    float: right;
	}

	#bottom-sales-section {
		background-color: #e6e6e6 !important;
		padding: 30px !important;
		position: relative;
	}

	#bottom-sales-section .outer-anchor {
		position: absolute;
		top: -80px;
	}

	#bottom-sales-section .row > div {
		background-color: #ffffff;
		padding: 40px 0 50px;
	}

	@media (min-width: 768px) {
		#bottom-sales-section .row > div {
		    width: 49%;
		    margin-left: 2%;
		    padding: 2.5% 0 2.5%;
		}

		#bottom-sales-section .row > div:first-child {
			margin-left: 0;
		}
	}

	@media (max-width: 925px) {
		body.single-product .sale-section p {
			font-size: 15px;
		}
	}

	@media (max-width: 810px) {
		body.single-product .sale-section p {
			font-size: 14px;
		}
	}

	@media (max-width: 767px) {
		body.single-product .sale-section p {
			font-size: 18px;
		}
		
		div.single-product,
		div.single-product.col2-layout {
			width: 100%;
			float: none;
		}

		#bottom-sales-section .row > div:first-child {
			margin-bottom: 20px;
		}
	}
</style>

<?php

$col2Layout = false;	
	
if (have_rows('section')) {
    $cpt = 0;
    $col2Layout = true;
    ?>
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
		    <li><a href="#bottom-sales-section"><?php echo get_field('buy_button'); ?></a></li>
	    </ul>
	</div>
<?php }	?>

<div id="single-prod" class="single-product<?php echo $col2Layout ? ' col2-layout' : ''; ?>">
<?php

	$_product = wc_get_product( get_the_ID() );

	if($_product->is_type('simple')){
		get_template_part('single-simple');
	}else{
		if($_product->is_type('variable')){
			?>
			<div class="variable-product">
				<?php get_template_part('single-variable'); ?>
			</div>
			<?php
		}else{
			
			get_template_part('single-simple');
		}
		
	}
?>
</div>