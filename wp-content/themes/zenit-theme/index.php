<?php
//-------------------------------------------------------------------
// Page de la liste des articles
//-------------------------------------------------------------------
?>

<div id="blog" class="container-fluid">
	<?php
		$counter = 0;
		while (have_posts()) {
			the_post();

			if ($counter === 0) {
				// Most Recent Article
				get_template_part( 'templates/content', 'article-main' );
			} else {
				// Remaining Articles
				if ($counter % 3 === 0) : ?>
					<div class="article-last">
						<?php get_template_part( 'templates/content', 'article-default' ); ?>
					</div> <?php
				else:
					get_template_part( 'templates/content', 'article-default' );
				endif;
			}

			$counter++;
		}
	?>
</div>

