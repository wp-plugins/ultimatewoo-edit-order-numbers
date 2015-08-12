<?php
/**
 * Plugin Name: WooCommerce Edit Order Numbers
 * Plugin URI: https://www.ultimatewoo.com/
 * Description: Manually edit WooCommerce order numbers.
 * Version: 1.0
 * Author: UltimateWoo
 * Author URI: http://www.ultimatewoo.com/
 *
 * License: GPL 2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

 /*
	Copyright 2015  UltimateWoo, mail@ultimatewoo.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	Permission is hereby granted, free of charge, to any person obtaining a copy of this
	software and associated documentation files (the "Software"), to deal in the Software
	without restriction, including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
	to whom the Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all copies or
	substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

if ( ! class_exists( 'WooCommerce_Edit_Order_Numbers' ) ) :

class WooCommerce_Edit_Order_Numbers {

	public function __construct() {

		$this->constants();
		$this->includes();
		$this->hooks();
	}

	/**
	 *	Define plugin constants
	 */
	public function constants() {

		if ( ! defined( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_DIR' ) )
			define( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		if ( ! defined( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_URL' ) )
			define( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		if ( ! defined( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_FILE' ) )
			define( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_FILE', __FILE__ );

		if ( ! defined( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_BASENAME' ) )
			define( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

		if ( ! defined( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_ULTIMATEWOO_URL' ) )
			define( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_ULTIMATEWOO_URL', 'https://www.ultimatewoo.com' );

		if ( ! defined( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_VERSION' ) )
			define( 'WOOCOMMERCE_EDIT_ORDER_NUMBERS_VERSION', 1.0 );
	}

	/**
	 *	Include files
	 */
	public function includes() {

		foreach ( glob( WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_DIR . '/includes/*.php' ) as $file )
			include_once $file;
	}

	/**
	 *	Start
	 */
	public function hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueues' ) );

		add_filter( 'woocommerce_order_number', array( $this, 'filter_order_number' ), 10, 2 );
	}

	/**
	 *	Enqueue assets
	 */
	public function admin_enqueues() {

		$screen = $GLOBALS['current_screen'];

		// Only enqueue on shop_order table list and for users with permission
		if ( $screen->base !== 'edit' || $screen->post_type !== 'shop_order' ) return;
		if ( ! current_user_can( apply_filters( 'edit_order_numbers_permissions', 'manage_options' ) ) ) return;

		wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', array(), '4.4.0' );	
		wp_enqueue_script( 'change-order-numbers', WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_URL . 'assets/js/ajax.js', array( 'jquery' ), WOOCOMMERCE_EDIT_ORDER_NUMBERS_VERSION, true );
		wp_localize_script( 'change-order-numbers', 'change_order_numbers', array(
			'change_order_numbers_nonce' => wp_create_nonce( 'change_order_numbers_nonce' ),
		));
	}

	/**
	 *	Filter the order number
	 */
	public function filter_order_number( $order_number, $order ) {

		if ( $custom_order_number = get_post_meta( $order->id, '_order_number', true ) )
			$order_number = $custom_order_number;

		return $order_number;
	}
}

endif;

/**
 *	Main function
 *	@return object WooCommerce_Edit_Order_Numbers instance
 */
function WooCommerce_Edit_Order_Numbers() {
	return new WooCommerce_Edit_Order_Numbers;
}

WooCommerce_Edit_Order_Numbers();