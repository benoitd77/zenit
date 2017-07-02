<?php

defined('ABSPATH') or die();

class WJECF_Debug_CLI extends WP_CLI_Command {

	/**
	 * Display some debugging information
	 */
	public function debug() {
		WP_CLI::log( sprintf("WJECF Version: %s", WJECF()->version ) );

		$coupon = WJECF_WC()->get_coupon('auto-cheapest-item');

		$args = array(
		    'posts_per_page'   => -1,
		    'orderby'          => 'title',
		    'order'            => 'asc',
		    'post_type'        => 'shop_coupon',
		    'post_status'      => 'publish',
		);		    
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$coupon = WJECF_WC()->get_coupon( $post->ID );
			$this->execute_test_for_coupon( $coupon );
		}

		$args = array(
		    'posts_per_page'   => -1,
		    'orderby'          => 'title',
		    'order'            => 'asc',
		    'post_type'        => array( 'product', 'product_variation' ),
		    'post_status'      => 'publish',
		);	
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$product = wc_get_product( $post->ID );
			$this->execute_test_for_product( $product );
		}

	}

	protected function execute_test_for_coupon( $coupon ) {
		//WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->coupon_custom_fields ) ) );
		//WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->get_meta_data() ) ) );

		$meta_keys = array_keys( $coupon->coupon_custom_fields );
		$meta_keys[] = '__non_existing__';

		//WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->coupon_custom_fields ) ) );
		//WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->get_meta_data() ) ) );

		$wrap_leg = WJECF_WC()->wrap( $coupon, false ); $wrap_leg->use_wc27 = false;
		$wrap_new = WJECF_WC()->wrap( $coupon, false ); $wrap_new->use_wc27 = true;

		$results = array();
		$results['new'] = $wrap_new->get_id();
		$results['legacy'] = $wrap_leg->get_id();
		$results['old'] = $coupon->id;		
		$this->assert_same( $results, sprintf('Same coupon id %s', current( $results ) ) );

		foreach( $meta_keys as $meta_key ) {
			for($i=1; $i>=0; $i--) {
				$single = $i>0;

				$results = array();
				$results['new'] = $wrap_new->get_meta( $meta_key, $single );
				$results['legacy'] = $wrap_leg->get_meta( $meta_key, $single );
				$results['old'] = get_post_meta( $coupon->id, $meta_key, $single );
				$this->assert_same( $results, sprintf('%s: Same value %s', $meta_key, $single ? 'single' : 'multi' ) );

			}
		}
	}

	protected function execute_test_for_product( $product ) {
		$wrap_leg = WJECF_WC()->wrap( $product, false ); $wrap_leg->use_wc27 = false;
		$wrap_new = WJECF_WC()->wrap( $product, false ); $wrap_new->use_wc27 = true;

		if ($product instanceof WC_Product_Variation) {
			$results = array();
			$results['new'] = $wrap_new->get_product_or_variation_id();
			$results['legacy'] = $wrap_leg->get_product_or_variation_id();
			$results['old'] = $product->variation_id;		
			$this->assert_same( $results, sprintf('Same variation id %s', current( $results ) ) );

			$results = array();
			$results['new'] = $wrap_new->get_variable_product_id();
			$results['legacy'] = $wrap_leg->get_variable_product_id();
			$results['old'] = $wrap_leg->get_variable_product_id();
			$this->assert_same( $results, sprintf('Same variable product (parent) id %s', current( $results ) ) );			
		} else {
			$results = array();
			$results['new'] = $wrap_new->get_id();
			$results['legacy'] = $wrap_leg->get_id();
			$results['old'] = $product->id;
			$this->assert_same( $results, sprintf('Same product id %s', current( $results ) ) );
		}



	}	

	protected function assert_same( $results, $test_description ) {
		$success = true;
		foreach( $results as $result ) {
			if ( isset( $prev_result ) && $result !== $prev_result ) {
				$success = false;
				break;
			}
			$prev_result = $result;
		}

		if ( $success ) {
			WP_CLI::success( $test_description );
		} else {
			foreach( $results as $key => $result ) {
				WP_CLI::log( sprintf("%s : %s", $key, $this->dd( $result ) ) );
			}
			WP_CLI::error( $test_description );
		}
	}

	protected function dd( $variable ) {
		return print_r( $variable, true );
	}


//	/**
//	 * Display expiry information for the given product.
//	 * 
// 	 * ## OPTIONS
//	 *
//	 * <product_id>...
//	 * : The product ID.
//	 * 
//	 */
//	public function product( $args ) {
//	}
	
}