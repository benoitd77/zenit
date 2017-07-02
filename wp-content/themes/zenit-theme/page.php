<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
 	<div class="container">
 		<div class="row">
 			<div class="col-sm-12">
 				<h1><?php echo get_the_title(); ?></h1>
 			</div>
 		</div>
 		<div class="row">
 			<div class="col-sm-12">
 				<?php get_template_part('templates/content', 'page'); ?>
 			</div>
 		</div>
 		<br/><br/>
 	</div>
  
<?php endwhile; ?>
