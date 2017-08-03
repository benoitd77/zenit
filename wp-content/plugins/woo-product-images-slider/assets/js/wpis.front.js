jQuery(document).ready(function()
{
	if(jQuery('.wpis-slider-for').length > 0)
	{
		if(object_name.wpis_arrow == 'true'){ 
			var slider_arrow = true;
		}else{
			var slider_arrow = false;
		}			
		
		if(object_name.wpis_carrow == 'true'){ 
			var slider_carrow = true;
		}else{
			var slider_carrow = false;
		}		
		
		if(object_name.wpis_popup != 'true'){ 
			jQuery('a.wpis-popup').remove();
		}
		
		if(object_name.wpis_autoplay == 'true'){ 
			var slider_autoplay = true;
		}else{
			var slider_autoplay = false;
		}
		
		jQuery('.wpis-slider-for').slick({
			fade: true,
			autoplay : slider_autoplay,
			arrows: slider_arrow,
			slidesToShow: 1,
			infinite:false,
			slidesToScroll: 1,
			asNavFor: '.wpis-slider-nav'
		});
		
		jQuery('.wpis-slider-nav').slick({
			dots: false,
			arrows: slider_carrow,
			centerMode: false,
			focusOnSelect: true,
			infinite:false,
			slidesToShow: 4,
			slidesToScroll: 1,
			asNavFor: '.wpis-slider-for'
		});
		
		if(object_name.wpis_zoom == 'true'){
			jQuery('.wpis-slider-for .slick-slide').zoom();
		}
		jQuery('.wpis-slider-for .slick-track').addClass('woocommerce-product-gallery__image single-product-main-image');
		jQuery('.wpis-slider-nav .slick-track').addClass('flex-control-nav');
		jQuery('.wpis-slider-nav .slick-track li img').removeAttr('srcset');
		
		jQuery('.variations select').change(function(){
			jQuery('.wpis-slider-nav').slick('slickGoTo', 0);
			window.setTimeout( function() {
				if(object_name.wpis_zoom == 'true'){
					jQuery('.wpis-slider-for .slick-track .slick-current').zoom();
				}
			}, 20 );
		});
	}
});