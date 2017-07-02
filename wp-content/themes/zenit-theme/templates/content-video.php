<?php
	if(!get_sub_field('titre_menu')){
		$cpt = '';
	}
?>
<div id="<?php echo $cpt; ?>" class="row video">
	<div class="col-sm-12 videoTrigger" data-id="<?php echo get_sub_field('video',false,false); ?>" style="background-image:url('<?php echo get_sub_field('bg_image'); ?>')">
		<div class="texte">
			<img src="<?php echo get_template_directory_uri(); ?>/dist/images/play-button.svg" class="play-video" alt="Play video" />
			<?php if(get_sub_field('title_simple')){ ?>
				<p><?php echo get_sub_field('title_simple'); ?></p>
			<?php } ?>

		</div>	
	</div>
</div>