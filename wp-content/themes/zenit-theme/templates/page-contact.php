<?php
/**
 * Template Name: Template : Contact
 */
?>
<style>

	body.page-template-page-contact{
		padding-top: 105px;
	}
	.page-contact{
		padding-top: 50px;
	}
	.page-contact .row:first-child{
		margin-bottom:25px;
	}
	.page-contact .gauche , .page-contact .droite{
		color: #959595;
	}
	.page-contact .gauche{
		padding-left: 25px;
	}


	.container-fluid h2{
		padding-left: 25px;
    	font-size: 40px;
	}

	.contact-form input,.contact-form text-field{
		margin-bottom: 5px;
		margin-top: 5px;
	}
	input[type=text],input[type=email],textarea {
   		border-radius: 3px 3px 8px 8px;
	    border-color: transparent;
	    background-color: #ababab;
	    width: 100%;
	}
	textarea{
		margin-top: 5px;
	}
	input[type=submit]{
		    width: 100px;
		    background-color: #292929;
		    color: gainsboro;
		    border-radius: 5px;
		    font-weight: 800;
		    float: right;
		    border-color:transparent;
	}
	main form{
		width: 90%;
		margin-bottom: 70px;
	}




</style>

<div class="container-fluid page-contact">
	
	<div class="row">
		<div class="col-sm-6">
			<h2><?php echo get_field('titre_contact_us'); ?></h2>
			<div class="contact-form">
				<?php 
					if (qtrans_getLanguage() === 'fr') {
						echo do_shortcode('[contact-form-7 id="83" title="Formulaire de contact fr"]');
					} else {
						echo do_shortcode('[contact-form-7 id="8154" title="Formulaire de contact en"]');
					}
				?>
			</div>
		</div>
		<div class="col-sm-3 gauche">
				<h2><?php echo get_field('titre_find_us'); ?></h2>
				<p><?php echo get_field('section_gauche'); ?></p>
		</div>
		<div class="col-sm-3 droite">
			<h2><?= get_field('titre_opening_hours'); ?></h2>
			<p><?php echo get_field('section_droite'); ?></p>
		</div>

	</div>
	<div class="row row-map">
		<section id="map" class="col-sm-12">

		</section>
	</div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPSql6QXPasYViFiJJPr0KYJjG3kW6uEc"></script>
	<script>
			<?php
				$location = get_field('location');
			  	$lat = $location['lat'];
			  	$lng = $location['lng'];
			?>

			var myLatLng = {lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?>};
			var roadAtlasStyles=[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}];

		    var mapOptions = {
			   zoom: 14,
                    center: myLatLng,
                    scrollwheel: false,
                    navigationControl: false,
                    mapTypeControl: false,
                    scaleControl: false,
                    draggable: false,
                    disableDefaultUI: true
			}



			var map = new google.maps.Map(document.getElementById("map"), mapOptions);

			var mapZenit = new google.maps.StyledMapType(roadAtlasStyles);

			map.mapTypes.set('map-zenit', mapZenit);
			  map.setMapTypeId('map-zenit');

	
			var marker = new google.maps.Marker({
     			position: new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $lng; ?>),
      			map: map,
     			title: '<?php the_title(); ?>',
     			//icon: '<?php echo get_template_directory_uri(); ?>/dist/images/marker.svg'
  			});

  			
			
	</script>