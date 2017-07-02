<?php

/*
Plugin Name: WP All Import - Listable Add-On
Plugin URI: http://www.wpallimport.com/
Description: Supporting imports into the Listable theme.
Version: 1.0.2
Author: Soflyy
*/


include "rapid-addon.php";

add_action( 'pmxi_saved_post', 'listable_addon_set_author', 10, 1 );
add_action( 'pmxi_before_xml_import', 'listable_addon_que_scripts', 10, 1 );

$listable_addon = new RapidAddon( 'Listable Add-On', 'listable_addon' );
$listable_addon->disable_default_images();
$listable_addon->import_images( 'listable_addon_listing_gallery', 'Gallery Images' );
$listable_addon->add_field( '_company_tagline', 'Company Tagline', 'text' );
$listable_addon->add_field( '_job_location', 'Location', 'text', null, 'Leave this blank if location is not important' );
$listable_addon->add_field( '_job_hours', 'Hours', 'textarea', null, 'Mon - Fri 09:00 - 23:00&lt;br /&gt;Sat - Sun 17:00 - 23:00' );
$listable_addon->add_field( '_company_phone', 'Phone:', 'text', null, 'e.g +42-898-4364' );
$listable_addon->add_field( '_company_website', 'Company Website', 'text' );
$listable_addon->add_field( '_company_twitter', 'Company Twitter', 'text' );
$listable_addon->add_field( '_filled', 'Filled', 'radio', 
    array(
        '0' => 'No',
        '1' => 'Yes'
    ),
    'Filled listings will no longer accept applications.'
);
$listable_addon->add_field( '_featured', 'Featured Listing', 'radio', 
    array(
        '0' => 'No',
        '1' => 'Yes'
    ),
    'Featured listings will be sticky during searches, and can be styled differently.'
);
$listable_addon->add_field( '_job_expires', 'Listing Expiry Date', 'text', null, 'Import date in any strtotime compatible format.');
$listable_addon->add_field( '_post_author', 'Posted by', 'text', null, 'Enter the ID of the user, or leave blank if submitted by a guest' );
$listable_addon->set_import_function( 'listable_addon_import' );

$listable_addon->admin_notice(
    'The Listable Add-On requires WP All Import <a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=listable" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="http://themeforest.net/item/listable-a-friendly-directory-wordpress-theme/13398377">Listable</a> theme.',
    array( 
        "themes" => array( "Listable" ),
) );

$listable_addon->run( array(
        "themes" => array( "Listable" ),
        'post_types' => array( 'job_listing' ) 
) );

function listable_addon_listing_gallery( $post_id, $attachment_id, $image_filepath, $import_options ) {
        $images_array = array(); // Image IDs array
        $images_urls = array(); // Image URLs array
        if ( $current_images = get_post_meta( $post_id, 'main_image', true ) ) { // Get current images, if any.
            $current_images = explode( ",", $current_images );
             foreach ( $current_images as $image ) {
                $images_array[$image] = $image;
                $images_urls[] = wp_get_attachment_url( $image );
            }
        }
        $images_array[$attachment_id] = $attachment_id;
        $images_urls[] = wp_get_attachment_url( $attachment_id );
        $final_images = implode( ",", $images_array );
        update_post_meta( $post_id, 'main_image', $final_images ); // Add image IDs
        update_post_meta( $post_id, '_main_image', $images_urls ); // Add image URLs
}

function listable_addon_import( $post_id, $data, $import_options, $article ) {
    
    global $listable_addon;

    // build fields array
    $fields = array(
        '_company_tagline',
        '_job_location',
        '_job_hours',
        '_company_phone',
        '_company_website',
        '_company_twitter',
        '_filled',
        '_featured',
        '_job_expires'
    );

    // update everything in fields arrays
    foreach ( $fields as $field ) {

        if ( empty( $article['ID'] ) or $listable_addon->can_update_meta( $field, $import_options ) ) {
                update_post_meta( $post_id, $field, $data[$field] );
            }
        }

            // clear image fields to override import settings
    $fields = array(
        'main_image',
        '_main_image'
    );
    if ( empty( $article['ID'] ) or $listable_addon->can_update_image( $import_options ) ) {
        foreach ($fields as $field) {
            delete_post_meta($post_id, $field);
        }
    }

     // update listing expiration date
    $field = '_job_expires';
    $date = $data[$field];
    $date = strtotime( $date );

     if ( empty( $article['ID'] ) or $listable_addon->can_update_meta( $field, $import_options ) ) {
        if( !empty( $date ) ) {
            $date = date( 'Y-m-d', $date );
            update_post_meta( $post_id, $field, $date );
        }
    }

    // This meta field is used in the listable_addon_set_author function.
    $field = '_post_author';

    if ( empty( $article['ID'] ) or $listable_addon->can_update_meta( $field, $import_options ) ) {
        update_post_meta( $post_id, '_post_author', $data[$field] );
        update_post_meta( $post_id, '_post_author_can_update', 'yes' );
    } else {
        update_post_meta( $post_id, '_post_author_can_update', 'no' );
    }
}

function listable_addon_que_scripts( $import_id ) {
    if ( !wp_script_is( 'google-maps' ) ) {
        if ( '' == listable_get_option( 'mapbox_token', '' ) ) {
            wp_deregister_script('google-maps');
            wp_enqueue_script( 'google-maps', '//maps.google.com/maps/api/js?v=3.exp&amp;libraries=places' . $google_maps_key, array(), '3.22', true );
            $listable_scripts_deps[] = 'google-maps';
        } elseif ( wp_script_is( 'google-maps' ) || listable_using_facetwp() ) {
            wp_deregister_script('google-maps');
            wp_enqueue_script( 'google-maps', '//maps.google.com/maps/api/js?v=3.exp&amp;libraries=places' . $google_maps_key, array(), '3.22', false );
            $listable_scripts_deps[] = 'google-maps';
        }
    }
}

function listable_addon_set_author( $post_id ) {
    // Set the author to Guest "0" if the field is empty, otherwise check if the user ID exists and then set the author.
    global $listable_addon;
    $can_update = get_post_meta( $post_id, '_post_author_can_update', true );
    $new_author = get_post_meta( $post_id, '_post_author', true );

    if ( empty( $new_author ) ) { $new_author == 0; }

    if ( $can_update == 'yes' ) {
        if ( listable_addon_user_exists( $post_id, $new_author ) ) {
                $update_array = array(
                    'ID'           => $post_id,
                    'post_author'   => $new_author
                );
                wp_update_post( $update_array );
                $listable_addon->log( "- Author updated according to 'Posted By' setting" );
        }
    } else {

        $listable_addon->log( "- Author failed to update." );

    }
}

function listable_addon_user_exists( $post_id, $user_id = null ) {
    // Make sure a user exists before we change the author.
    if ( $user_id == null ) {
        update_post_meta( $post_id, '_post_author', 0 );
        return true;
    } else {
        global $wpdb;
        $data = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users WHERE ID = %d", $user_id ) );
        return empty( $data ) || 1 > $data ? false : true;
    }
}