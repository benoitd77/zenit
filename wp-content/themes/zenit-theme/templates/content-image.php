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
			<img class="elevate-zoom" src="<?php echo get_sub_field('image_simple'); ?>" alt="<?php echo get_the_title(); ?>" data-zoom-image="<?php echo get_sub_field('image_agrandie'); ?>"/>
		<?php else : ?>
			<img class="lazy" data-original="<?php echo get_sub_field('image_simple'); ?>" alt="<?php echo get_the_title(); ?>" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkI5QkZCMEM3MEVCMzExRTg4RTk1RDYzRjY4RTc3MDc5IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkI5QkZCMEM4MEVCMzExRTg4RTk1RDYzRjY4RTc3MDc5Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QjlCRkIwQzUwRUIzMTFFODhFOTVENjNGNjhFNzcwNzkiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QjlCRkIwQzYwRUIzMTFFODhFOTVENjNGNjhFNzcwNzkiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz438RusAAAAEElEQVR42mL4//8/A0CAAQAI/AL+26JNFgAAAABJRU5ErkJggg=="/>
		<?php endif; ?>
		
		<div class="hidden-desc">
			<?php echo get_sub_field('descriptif'); ?>
		</div>
	</div>
	<span class="clear"></span>
	<span class="bg"></span>
</div>