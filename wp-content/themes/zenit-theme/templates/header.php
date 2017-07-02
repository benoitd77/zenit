<?php global $woocommerce; ?>

<header id="top-anchor" class="banner">
	<div class="container-fluid">
		<nav class="nav-primary">
			<?php
				if (has_nav_menu('primary_nav')) :
					wp_nav_menu(['theme_location' => 'primary_nav', 'menu_class' => 'nav']);
				endif;
			?>

			<a href="<?php echo get_home_url(); ?>" id="logo">
				<?php include('logo-svg.php'); ?>
			</a>

			<div class="transactionnal">
				<ul>
					<li><?php qtranxf_generateLanguageSelectCode(array('type' => 'text', 'hide-title' => true)); ?></li>
					<li class="currency-changer"><?php echo do_shortcode("[woocs width='45px']"); ?></li>
					<li>
						<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>">
							<img src="<?php echo get_template_directory_uri(); ?>/dist/images/cart.svg" class="cart-icon" alt="<?php _e('cart page','zenit') ?>"/>
						</a>
					</li>
				</ul>
			</div>

			<span class="clear"></span>

			<div id="btMenu">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</nav>
	</div>
</header>

<ul id="social-fixed" class="visually-hidden">
	<li><a href="https://www.facebook.com/ZENitCustomBoards/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/dist/images/facebook-logo-black.svg" alt="facebook" /></a></li>
	<li><a href="https://www.instagram.com/zenit.boards/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/dist/images/instagram-black.svg" alt="instagram" /></a></li>
</ul>

<script src="<?php echo get_template_directory_uri(); ?>/assets/scripts/elevateZoom.js"></script>