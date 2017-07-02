<?php
/**
 * Template Name: Template : À propos
 */
?>
<div id="page-builder" class="container-fluid">
		<?php

		// check if the repeater field has rows of data
		if( have_rows('section') ):

		 	// loop through the rows of data
		    while ( have_rows('section') ) : the_row();

		        
		        $type = get_sub_field('type');
		        
		        if($type == 'Image' ){
		        	get_template_part('templates/content-image');
		        } 

		        if($type == 'image_texte' ){
		        	get_template_part('templates/content-image-fw');
		        }

		        if($type == 'video' ){
		        	get_template_part('templates/content-video');
		        }

		        if($type == 'text-fw'){
		        	get_template_part('templates/content-text-fw');
		        }

		    endwhile;

		else :

		    // no rows found

		endif;

		?>
	</div>