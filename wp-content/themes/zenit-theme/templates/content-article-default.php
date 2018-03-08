<?php
//-------------------------------------------------------------------
// Widget: Extrait d'un article
//-------------------------------------------------------------------
?>

<div class="default-article">
	<article>
		<div class="article-image">
			<a href="<?php the_permalink(); ?>">
				<img class="lazy" data-original="<?php echo get_the_post_thumbnail_url($product->ID, array(375, 225)); ?>" width="375" height="225">
			</a>
		</div>
		<div class="article-content">
			<header>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			</header>
			<div class="entry-summary">
				<p><?php echo excerpt(25); ?></p>
			</div>
		</div>
	</article>
</div>