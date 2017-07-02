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