<?php
/**
 * Template Name: Template : Accueil
 */
?>

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

		<?php
			// Display the Newsletter form
			the_content();
		?>
	</div>
</section>