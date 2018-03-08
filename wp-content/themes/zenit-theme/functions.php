<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */

$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php' // Theme customizer
];

remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_filter('the_title', 'wptexturize');

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}

add_filter('acf/settings/google_api_key', function () {
    return 'AIzaSyDMvoc6a8Mhv7JAyrM7fVGrYjPHr9yKQiQ';
});

add_filter('woocommerce_show_variation_price',      function() { return TRUE;});

function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

add_filter( 'woocommerce_add_error', function( $message ) {
    $message = str_replace("is a required field","est un champ requis",$message );
    $message = str_replace("Billing","",$message );
    return $message;
});


function new_excerpt_length($len) {
	return 40;
}
add_filter('excerpt_length', 'new_excerpt_length');

function custom_read_more() {
	return '... <a class="read-more" href="'.get_permalink(get_the_ID()).'">more&nbsp;&raquo;</a>';
}
function excerpt($limit) {
	$excerpt = get_the_excerpt();
	$parts = explode('&hellip;', $excerpt);
	$theExcerpt = $parts[0];

	return wp_trim_words($theExcerpt, $limit, custom_read_more());
}

// stop wp removing div tags
function ikreativ_tiny_mce_fix( $init )
{
    // html elements being stripped
    $init['extended_valid_elements'] = 'div[*], article[*]';

    // don't remove line breaks
    $init['remove_linebreaks'] = false;

    // convert newline characters to BR
    $init['convert_newlines_to_brs'] = true;

    // don't remove redundant BR
    $init['remove_redundant_brs'] = false;

    // pass back to wordpress
    return $init;
}
add_filter('tiny_mce_before_init', 'ikreativ_tiny_mce_fix');

unset($file, $filepath);

add_image_size( 'custom-size', 480, 640, false );
add_image_size( 'custom-size500', 500, 500, false );
add_image_size( 'custom-size540', 540, 720, false );
add_image_size( 'custom-size375', 375, 500, false );

// Blog post image sizes
add_image_size( 'custom-size600x360', 600, 360, false );
add_image_size( 'custom-size375x225', 375, 225, false );
