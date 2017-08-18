<?php
/**
 * Template Name: Template : Vidéo
 */
?>
<div id="list-videos" class="container-fluid">
	<?php
	// check if the repeater field has rows of data
	if( have_rows('liste_videos') ):
		$currentLang = qtrans_getLanguage();
		$seeBoardTxt = $currentLang === 'fr' ? 'Voir la planche' : 'See the Board';
	 	// loop through the rows of data
	    while ( have_rows('liste_videos') ) : the_row(); ?>
			<div class="row">
		        <div class="col-sm-12 video videoTrigger" data-id="<?php echo get_sub_field('video',false,false); ?>">
		            <div class="background-image" style="background-image:url('<?php echo get_sub_field('placeholder_image'); ?>')"></div>
		            <div class="container-content">
		                <div class="content">
		                    <img src="<?php echo get_template_directory_uri(); ?>/dist/images/play-button.svg" class="play-video" alt="Play video" />
		                    <h2><?php the_sub_field('titre'); ?></h2>
		                    <h3><?php the_sub_field('s-titre'); ?></h3>
					        <a href="<?php echo get_permalink(get_sub_field('board_utilise_dans_le_video')); ?>" class="seebrdtxt"><?php echo $seeBoardTxt; ?></a>
		                </div>
		            </div>
		        </div>
		    </div>
	    <?php endwhile;
	endif;
	?>
</div>