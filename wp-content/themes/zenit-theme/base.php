<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

$_product = wc_get_product( get_the_ID() );
$category = wp_get_post_terms( get_the_ID(), 'product_cat' );

$pageClass = 'page-default';
$catSlug  = '';

for ( $i= 0; $i < count($category); $i++ ) {
	$catSlug  = $category[$i]->slug;
	if ($catSlug === 'board') {
		$pageClass = 'page-board';
	}
}

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<?php get_template_part('templates/head'); ?>
<body id="<?php echo $pageClass; ?>" <?php body_class(); ?>>
	<!--[if IE]>
	<div class="alert alert-warning">
		<?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
	</div>
	<![endif]-->
	<?php
		do_action('get_header');
		get_template_part('templates/header');
	?>
	<div class="wrap container-fluid" role="document">
		<div class="content row">
			<main class="main">
				<?php
					include Wrapper\template_path();
				?>
			</main><!-- /.main -->
		</div><!-- /.content -->
	</div><!-- /.wrap -->
	<?php
		do_action('get_footer');
		get_template_part('templates/footer');
		wp_footer();
	?>
</body>
</html>