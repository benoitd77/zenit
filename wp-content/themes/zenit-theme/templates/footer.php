<footer class="content-info">
	<div class="container top">
		<h4><?php echo get_field('contact_us'); ?></h4>
		<ul>
			<li><img src="<?php echo get_template_directory_uri(); ?>/dist/images/email.svg" alt="email" /><a href="mailto:phil@zenitboards.com">phil@zenitboards.com</a></li>
			<li><img src="<?php echo get_template_directory_uri(); ?>/dist/images/telphone.svg" alt="phone" /><a href="+tel:15143187332">514 316-7332</a></li>
		</ul>
		
		<ul id="menu-navigation-pied-de-page" class="nav">
			<li><a href="<?php echo get_page_link(43); ?>"><?php echo get_the_title(43); ?></a></li>
			<li><a href="<?php echo get_page_link(45); ?>"><?php echo get_the_title(45); ?></a></li>
			<li><a href="<?php echo get_page_link(47); ?>"><?php echo get_the_title(47); ?></a></li>
		</ul>
	</div>

	<div class="bottom container-fluid">
		<p>Copyright © 2016 Les équipements Zenit Longboard All rights reserved.</p>
		<ul class="social">
			<li><a href="https://www.facebook.com/ZENitCustomBoards/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/dist/images/instagram.svg" alt="instagram" /></a></li>
			<li><a href="https://www.instagram.com/zenit.boards/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/dist/images/facebook-logo.svg" alt="facebook" /></a></li>
		</ul>
		<a id="back-to-top" href="#top-anchor">Back to top</a>
	</div>
	<span class="clear"></span>
</footer>

<div id="video-lightbox" class="hidden-lightbox">
	<img src="<?php echo get_template_directory_uri(); ?>/dist/images/close-button.svg" class="close-videoPanel" alt="Close video panel" />
	<div class="video"></div>
</div>
