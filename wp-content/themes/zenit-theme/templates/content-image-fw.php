<?php
	if(!get_sub_field('titre_menu')){
		$cpt = '';
	}
?>

<div id="<?php echo $cpt; ?>" class="row image-fw">
	<div class="col-sm-12" data-bottom-top="background-position: 50% 0px;" data-top-bottom="background-position: 50% -430px;"  style="background-image:url('<?php echo get_sub_field('bg_image'); ?>')">
		<div class="container">
			<div class="texte">
				<?php echo get_sub_field('contenu'); ?>
			</div>	
		</div>
	</div>
</div>