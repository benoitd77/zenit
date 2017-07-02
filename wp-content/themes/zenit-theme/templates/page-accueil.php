<?php
/**
 * Template Name: Template : Accueil
 */
?>
<style type="text/css">
	body.home form{
		display: table;
	    margin-left: auto;
	    margin-right: auto;
	    position: relative;
	}
	body.home form input{
		border-radius: 5px;
	    border: 2px solid #fff;
	    background-color: transparent;
	    width: 179px;
	    text-align: center;
	    color:white;
	}
	body.home form input[type=submit] {
	    width: 100px;
	    background-color: white;
	    color: black;
	    border-radius: 5px;
	    font-weight: 800;
	    margin-left: 30px;
	    float: none;
	    border-color: transparent;
	    -webkit-transition:  500ms ease-in-out ;
	    -moz-transition:  500ms ease-in-out ;
	    -o-transition:  500ms ease-in-out ;
	    transition: 500ms ease-in-out ;

	}
	body.home form input[type=submit]:hover {
	    width: 100px;
	    background-color: black;
	    color: white;
	    border-radius: 5px;
	    font-weight: 800;
	    margin-left: 30px;
	    float: none;
	    border-color: transparent;

	}


::-webkit-input-placeholder {
   color: white;
}

:-moz-placeholder { /* Firefox 18- */
   color: white;  
}

::-moz-placeholder {  /* Firefox 19+ */
   color: white;  
}

:-ms-input-placeholder {  
   color: white;  
}

</style>

<section id="splash">
	<div class="slider-fullscreen">
	
	<?php

		// check if the repeater field has rows of data
		if( have_rows('slider_home') ):

		 	// loop through the rows of data
		    while ( have_rows('slider_home') ) : the_row();
			?>
				<div class="slide" style="background-image:url('<?php echo get_sub_field('background_image'); ?>')">
					<div class="content">
			<?php
			        // display a sub field value
			        echo "<h2 class='wht'>".get_sub_field('title')."</h2>";
			        echo "<div class='description'>".get_sub_field('description')."</div>";
			    

			    	if(get_sub_field('lien_du_bouton') && get_sub_field('label_button')){
			    		echo "<a class='bouton' href='".get_sub_field('lien_du_bouton')."'>".get_sub_field('label_button')."</a>";
			    	}
		    	?>
		    		</div>
		    	</div>
		    <?php
		    
		    endwhile;

		endif;

		?>
		</div>
	</div>
</section>
<section id="info-home" class="row">
		<div class="col-sm-6 col-sm-push-3">
			<h2><?php echo get_field('info_titre') ?></h2>
			<p><?php echo get_field('info_texte') ?></p>
		</div>
</section>

<section id="instagram">
	<div class="content-instagram">
		<img src="<?php echo get_template_directory_uri(); ?>/dist/images/instagram-black.svg" alt="Instagram logo" />
		<h3>#Zenitboards</h3>
		<p><?php echo get_field('share_your_story'); ?></p>
	</div>
	<div class="row instagram">	
		<!-- LightWidget WIDGET --><script src="//lightwidget.com/widgets/lightwidget.js"></script><iframe src="//lightwidget.com/widgets/050248ab087c5647a774f82d55a929a3.html" scrolling="no" allowtransparency="true" class="lightwidget-widget" style="width: 100%; border: 0; overflow: hidden;"></iframe>
	</div>
</section>
<section id="newsletter" style="background-image:url('<?php echo get_field('image_newsletter'); ?>')" class="row">
	<div class="container">
		<h3 class="wht"><?php echo get_field('newsletter_heading'); ?></h3>
		<?php echo get_field('newsletter'); ?>
		<form>
			<input type="text" placeholder="<?php echo get_field('newsletter_email_field'); ?>" />
			<input type="submit" value="<?php echo get_field('newsletter_send_button'); ?>" />
		</form>
	</div>
</section>