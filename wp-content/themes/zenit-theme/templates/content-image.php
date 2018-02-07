<?php
	if (!get_sub_field('titre_menu')) {
		$cpt = '';
	}
?>

<div id="<?php echo $cpt; ?>" class="row image">
	<div class="col-sm-12">
		<?php if(get_sub_field('title_simple')) : ?>
			<h3><?php echo get_sub_field('title_simple'); ?></h3>
		<?php endif; ?>

		<?php if(get_sub_field('image_agrandie')) : ?>
			<img class="elevate-zoom lazy" data-original="<?php echo get_sub_field('image_simple'); ?>" alt="<?php echo get_the_title(); ?>" data-zoom-image="<?php echo get_sub_field('image_agrandie'); ?>"/>
		<?php else : ?>
			<img class="lazy" data-original="<?php echo get_sub_field('image_simple'); ?>" alt="<?php echo get_the_title(); ?>" />
		<?php endif; ?>
		
		<div class="hidden-desc">
			<?php echo get_sub_field('descriptif'); ?>
		</div>
	</div>
	<span class="clear"></span>
	<span class="bg"></span>
</div>