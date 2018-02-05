<?php
/**
 * Template Name: Template : Team
 */
?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPSql6QXPasYViFiJJPr0KYJjG3kW6uEc"></script>
<?php 

	$loop = new WP_Query( array( 'post_type' => 'team_member', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	$arrTeamMember = array(); 

	$totalPost = $loop->post_count;
	$cpt = 0;
	$noLigne = 1;

	while ( $loop->have_posts() ) : $loop->the_post(); 
	
		array_push($arrTeamMember, get_the_ID());
	
	endwhile; wp_reset_query();

?>


<div class="container-fluid container-team">

	<div class="row row-team">
		<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		
			<?php if($cpt == 0){ ?>
				<div class="row">

			<?php }else{ 
				if($cpt%4 == 0){?>
					</div>
					<div class="row">

					<?php $noLigne++; ?>

					
				<?php }
			} ?>

			<div class="col-sm-3 team-member" data-id="<?php echo get_the_ID(); ?>">
				<div class="content">
					<div class="image">
						<?php //the_post_thumbnail(); ?>
						<img class="lazy" data-original="<?php echo get_the_post_thumbnail_url(); ?>" width="650" height="650">
						<div class="text-content">
						<h3><?php the_title(); ?></h3>
						</div>
					</div>
				</div>
			</div>



			<?php if($cpt == $totalPost-1){ ?>
				</div>
			<?php } ?>

		<?php $cpt++; ?>

		<?php if($cpt %4 == 0  || $cpt == $totalPost) { ?>

				<?php
					

					for($cptLigne = 4; $cptLigne > 0; $cptLigne--){
						$indexTable = $noLigne*4-$cptLigne;
						if($arrTeamMember[$indexTable]){ 
							$currentId = $arrTeamMember[$indexTable];?>
							<?php

								$location = get_field('position_geographique', $currentId);
								

							?>
							<div id="<?php echo $currentId; ?>" class="row row-team fat-team-section" data-id="<?php echo $currentId; ?>" data-lat="<?php echo $location['lat'] ?>" data-lng="<?php echo $location['lng'] ?>">
								<div class="row-height">
								<div class="col-sm-6 col-sm-height background-image-team" style="background-image:url('<?php echo get_field('g_image', $currentId); ?>');">
									
								</div>
								<div class="col-sm-6 col-sm-height col-top right-section">
									<div class="close-team"><span></span><span></span></div>
									<h4><?php echo get_field('complet_name',$currentId); ?></h4>
									<p class="tag-line"><?php echo get_field('tag_line',$currentId); ?></p>
									<div class="content-team-member">
										<?php echo get_field('description',$currentId); ?>
									</div>
									<div class="instagram">
										<img src="<?php echo get_template_directory_uri(); ?>/dist/images/instagram-black.svg" alt="Instagram logo" />
										
										<?php $username = get_field('instagram_username',$currentId); 
											$username = str_replace('@','',$username);
										?>
										<a href="https://www.instagram.com/<?php echo $username ?>" target="_blank"><p><?php echo get_field('instagram_username',$currentId); ?></p></a>
									</div>
									<div id="map-<?php echo $currentId; ?>" class="map"></div>
								</div>
								</div>
							</div>	
						<?php }
					}

				?>

		<?php } ?>
		
		<?php endwhile; wp_reset_query(); ?>
		</div>
		
		<div id="candidature" class="row">
			<div class="col-sm-6 col-sm-push-3">
				<h3><?php echo _e('Formulaire de candidature','zenit'); ?></h3>
				<?php echo do_shortcode('[contact-form-7 id="125" title="Formulaire de candidature"]') ?>
			</div>
		</div>

</div>

