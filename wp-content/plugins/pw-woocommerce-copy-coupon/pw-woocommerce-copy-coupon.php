<?php
/**
Plugin Name: PW WooCommerce Copy Coupon
Plugin URI: https://wordpress.org/plugins/pw-woocommerce-exclude-free-shipping
Description: Adds a Copy button to WooCommerce coupons.
Version: 1.0
Author: Pimwick, LLC
Author URI: https://pimwick.com
*/

/*
Copyright (C) 2017 Pimwick, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Verify this isn't called directly.
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

if ( ! class_exists( 'PW_Copy_Coupon' ) ) :

final class PW_Copy_Coupon {

    public function __construct() {

        if ( is_admin() ) {
            add_action( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
            add_action( 'admin_action_copy_coupon', array( $this, 'copy_coupon' ) );
            add_action( 'post_submitbox_start', array( $this, 'post_submitbox_start' ) );
        }
    }

    public function post_row_actions( $actions, $post ) {

        if ( $post->post_type == 'shop_coupon' && !isset( $actions['copy'] ) ) {
            if ( current_user_can( apply_filters( 'woocommerce_copy_coupon_capability', 'manage_woocommerce' ) ) ) {
                $actions['copy'] = '<a href="' . wp_nonce_url( admin_url( 'edit.php?post_type=shop_coupon&action=copy_coupon&post=' . $post->ID ), 'woocommerce-copy-shop_coupon_' . $post->ID ) . '" title="' . esc_attr__( 'Make a copy of this Coupon', 'pimwick' ) . '" rel="permalink">' .  __( 'Copy', 'pimwick' ) . '</a>';
            }
        }

        return $actions;
    }

    public function copy_coupon() {

        if ( ! current_user_can( apply_filters( 'woocommerce_copy_coupon_capability', 'manage_woocommerce' ) ) ) {
            wp_die( __( 'Not authorized!', 'pimwick' ) );
        }

        if ( empty( $_REQUEST['post'] ) ) {
            wp_die( __( 'No Coupon to copy has been supplied!', 'pimwick' ) );
        }

        // Get the original page
        $post_id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

        check_admin_referer( 'woocommerce-copy-shop_coupon_' . $post_id );

        // Copy the page and insert it
        $post = get_post( $post_id );
        if ( !empty( $post ) ) {

            $new_id = $this->duplicate_coupon( $post );

            // Redirect to the edit screen for the new draft page
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
            exit;

        } else {
            wp_die( __( 'Coupon creation failed, could not find original Coupon:', 'pimwick' ) . ' ' . $post_id );
        }
    }

    public function post_submitbox_start() {
        global $post;

        if ( ! current_user_can( apply_filters( 'woocommerce_copy_coupon_capability', 'manage_woocommerce' ) ) ) {
            return;
        }

        if ( ! is_object( $post ) ) {
            return;
        }

        if ( $post->post_type != 'shop_coupon' ) {
            return;
        }

        if ( isset( $_GET['post'] ) ) {
            $notify_url = wp_nonce_url( admin_url( "edit.php?post_type=shop_coupon&action=copy_coupon&post=" . absint( $_GET['post'] ) ), 'woocommerce-copy-shop_coupon_' . absint( $_GET['post'] ) );
            ?>
            <div id="duplicate-action"><a class="submitduplicate duplication" href="<?php echo esc_url( $notify_url ); ?>"><?php _e( 'Copy Coupon', 'pimwick' ); ?></a></div>
            <?php
        }
    }

    private function duplicate_coupon( $post ) {
        global $wpdb;

        $new_post_author    = wp_get_current_user();
        $new_post_date      = current_time( 'mysql' );
        $new_post_date_gmt  = get_gmt_from_date( $new_post_date );


        $post_parent = $post->post_parent;
        $post_status = 'draft';
        $post_title  = $post->post_title . ' ' . __( '(Copy)', 'woocommerce' );

        // Insert the new template in the post table
        $wpdb->insert(
            $wpdb->posts,
            array(
                'post_author'               => $new_post_author->ID,
                'post_date'                 => $new_post_date,
                'post_date_gmt'             => $new_post_date_gmt,
                'post_content'              => $post->post_content,
                'post_content_filtered'     => $post->post_content_filtered,
                'post_title'                => $post_title,
                'post_excerpt'              => $post->post_excerpt,
                'post_status'               => $post_status,
                'post_type'                 => $post->post_type,
                'comment_status'            => $post->comment_status,
                'ping_status'               => $post->ping_status,
                'post_password'             => $post->post_password,
                'to_ping'                   => $post->to_ping,
                'pinged'                    => $post->pinged,
                'post_modified'             => $new_post_date,
                'post_modified_gmt'         => $new_post_date_gmt,
                'post_parent'               => $post_parent,
                'menu_order'                => $post->menu_order,
                'post_mime_type'            => $post->post_mime_type
            )
        );

        $old_coupon_id = $post->ID;
        $new_coupon_id = $wpdb->insert_id;

        // Copy the meta information
        $this->duplicate_post_meta( $old_coupon_id, $new_coupon_id );

        // Clear cache
        clean_post_cache( $new_coupon_id );

        return $new_coupon_id;
    }

    private function duplicate_post_meta( $id, $new_id ) {
        global $wpdb;

        $sql     = $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", absint( $id ) );

        $post_meta = $wpdb->get_results( $sql );

        if ( sizeof( $post_meta ) ) {
            $sql_query_sel = array();
            $sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

            foreach ( $post_meta as $post_meta_row ) {
                $sql_query_sel[] = $wpdb->prepare( "SELECT %d, %s, %s", $new_id, $post_meta_row->meta_key, $post_meta_row->meta_value );
            }

            $sql_query .= implode( " UNION ALL ", $sql_query_sel );
            $wpdb->query( $sql_query );
        }
    }
}

new PW_Copy_Coupon();

endif;

?>