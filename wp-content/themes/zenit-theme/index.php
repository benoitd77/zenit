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
				echo '<div class="secondary-articles">';
			} else {
				// Remaining Articles
				get_template_part( 'templates/content', 'article-default' );
			}

			$counter++;
		}

		echo '</div>';
	?>
</div>

