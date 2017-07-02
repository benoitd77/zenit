<?php

abstract class Abstract_WJECF_Plugin {

//Override these functions in the WJECF plugin

    public function init_hook() {}
    public function init_admin_hook() {}

    /**
     * Asserts that all dependencies are respected. If not an Exception is thrown. Override this function for extra assertions (e.g. minimum plugin versions)
     * @return void
     */
    public function assert_dependencies() {
		foreach( $this->get_plugin_dependencies() as $dependency ) {
			if ( ! isset( $this->plugins[ $dependency ] ) ) {
				throw new Exception( sprintf( 'Missing dependency %s', $dependency) );
			}
		}

		if ( ! empty ( $this->plugin_data['required_version_wjecf'] ) ) {
			$this->assert_wjecf_version( $this->plugin_data['required_version_wjecf'] );
		}
    }

    /**
     * Assert minimum WJECF version number
     * @param string $required_version 
     * @return void
     */
    protected function assert_wjecf_version( $required_version ) {
		if ( version_compare( WJECF()->version, $required_version, '<' ) ) {
			throw new Exception( sprintf( __( 'WooCommerce Extended Coupon Features version %s is required. You have version %s', 'woocommerce-jos-autocoupon' ), $required_version, WJECF()->version ) );
		}    	
    }

//

	/**
	 * Log a message (for debugging)
	 *
	 * @param string $message The message to log
	 *
	 */
	protected function log ( $message ) {
		WJECF()->log( $message, 1 );
	}

	private $plugin_data = array();

	/**
	 *  Information about the WJECF plugin
	 * @param string|null $key The data to look up. Will return an array with all data when omitted
	 * @return mixed
	 */
	protected function get_plugin_data( $key = null ) {
		$default_data = array(
			'description' => '',
			'can_be_disabled' => false,
			'dependencies' => array(),
			'minimal_wjecf_version' => ''
		);
		$plugin_data = array_merge( $default_data, $this->plugin_data );
		if ( $key === null ) { 
			return $plugin_data;
		}
		return $plugin_data[$key];
	}

	/**
	 *  Set information about the WJECF plugin
	 * @param array $plugin_data The data for this plugin
	 * @return void
	 */
	protected function set_plugin_data( $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

    /**
     *  Get the description if this WJECF plugin.
     * @return string
     */
	public function get_plugin_description() {
		return $this->get_plugin_data( 'description' );
	}

    /**
     *  Get the class name of this WJECF plugin.
     * @return string
     */
	public function get_plugin_class_name() {
		return get_class( $this );
	}

    public function get_plugin_dependencies() {
        return $this->get_plugin_data( 'dependencies' );
    }

    public function plugin_is_enabled() {
        return true;
    }

}