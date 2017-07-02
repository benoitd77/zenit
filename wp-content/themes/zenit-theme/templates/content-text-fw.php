<?php
	if(!get_sub_field('titre_menu')){
		$cpt = '';
	}
?>

<div id="<?php echo $cpt; ?>" class="row text-fw">
	<div class="col-sm-12">
		<div class="container">
			<div class="texte">
				<?php echo get_sub_field('contenu'); ?>
			</div>	
		</div>
	</div>
</div>