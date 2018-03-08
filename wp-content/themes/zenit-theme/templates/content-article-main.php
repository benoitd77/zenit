<?php
//-------------------------------------------------------------------
// Widget: Extrait de l'article principal
//-------------------------------------------------------------------
?>

<div class="main-article">
	<article class="clearfix">
		<div class="main-article-image">
			<a href="<?php the_permalink(); ?>">
				<img class="lazy" data-original="<?php echo get_the_post_thumbnail_url($product->ID, array(600, 360)); ?>" width="600" height="360">
			</a>
		</div>
		<div class="main-article-content">
			<header>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			</header>
			<div class="entry-summary">
				<p><?php echo excerpt(35); ?></p>
			</div>
		</div>
	</article>
</div>