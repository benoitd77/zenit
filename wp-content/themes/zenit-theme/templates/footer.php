<footer class="content-info">
	<div class="container top">
		<h4><?php echo get_field('contact_us'); ?></h4>
		<ul>
			<li><img src="<?php echo get_template_directory_uri(); ?>/dist/images/email.svg" alt="email" /><a href="mailto:phil@zenitboards.com">phil@zenitboards.com</a></li>
			<li><img src="<?php echo get_template_directory_uri(); ?>/dist/images/telphone.svg" alt="phone" /><a href="+tel:15143187332">514 316-7332</a></li>
		</ul>

		<?php
			if (has_nav_menu('footer_nav')) :
				wp_nav_menu(['theme_location' => 'footer_nav', 'menu_class' => 'nav']);
			endif;
		?>
	</div>

	<div class="bottom container-fluid">
		<p>Copyright © 2016 Les équipements Zenit Longboard All rights reserved.</p>
		<ul class="social">
			<li><a href="https://www.instagram.com/zenit.boards/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/dist/images/instagram.svg" alt="instagram" /></a></li>
			<li><a href="https://www.facebook.com/ZENitCustomBoards/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/dist/images/facebook-logo.svg" alt="facebook" /></a></li>
		</ul>
		<a id="back-to-top" href="#top-anchor">Back to top</a>
	</div>
	<span class="clear"></span>
</footer>

<div id="video-lightbox" class="hidden-lightbox">
	<img src="<?php echo get_template_directory_uri(); ?>/dist/images/close-button.svg" class="close-videoPanel" alt="Close video panel" />
	<div class="video"></div>
</div>
