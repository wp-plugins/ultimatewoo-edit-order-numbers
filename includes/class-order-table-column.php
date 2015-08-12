<?php

/**
 *	Add a column to the WooCommerce Orders admin table
 *	@package WooCommerce Edit Order Numbers
 *	@author UltimateWoo
 */

if ( ! class_exists( 'WooCommerce_Edit_Order_Numbers_Table_Column' ) ):

class WooCommerce_Edit_Order_Numbers_Table_Column {

	public function __construct() {

		$this->hooks();
	}

	/**
	 *	Start
	 */
	public function hooks() {

		add_action( 'admin_init', array( $this, 'authorization' ) );

		add_filter( 'manage_edit-shop_order_columns', array( $this, 'column_header' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'column_data' ) );
	}

	/**
	 *	Make sure user is authorized to view the setting
	 */
	public function authorization() {

		// Add columns for users with permission to edit
		if ( ! current_user_can( apply_filters( 'edit_order_numbers_permissions', 'manage_options' ) ) ) exit;
	}

	/**
	 *	Remove default columns, add our column then add defaults back
	 */
	public function column_header( $columns ) {

		$new_columns = ( is_array( $columns ) ) ? $columns : array();

		unset( $new_columns['order_items'] );
		unset( $new_columns['shipping_address'] );
		unset( $new_columns['customer_message'] );
		unset( $new_columns['order_notes'] );
		unset( $new_columns['order_date'] );
		unset( $new_columns['order_total'] );
		unset( $new_columns['order_actions'] );

		$new_columns['edit_order_number'] = 'Edit Order #';
		$new_columns['order_items'] = $columns['order_items'];
		$new_columns['shipping_address'] = $columns['shipping_address'];
		$new_columns['customer_message'] = $columns['customer_message'];
		$new_columns['order_notes'] = $columns['order_notes'];
		$new_columns['order_date'] = $columns['order_date'];
		$new_columns['order_total'] = $columns['order_total'];
		$new_columns['order_actions'] = $columns['order_actions'];

		return $new_columns;
	}

	/**
	 *	Add input to the column
	 */
	public function column_data( $column ) {

		global $post;

		if ( $column == 'edit_order_number' ) {

			$data = get_post_meta( $post->ID );

			if ( isset( $data['_order_number'] ) )
				$order_id = $data['_order_number'][0];

			else $order_id = $post->ID;

			printf( '<input id="uw_change_order_number_input_%s" type="text" value="%s" style="width: 90px;">', $post->ID, esc_attr( $order_id ) );

			printf( '<i class="uw_change_order_number_submit fa fa-refresh fa-2x" style="cursor: pointer; position: relative; top: 5px;" data-current-order-number="%1$s" data-order-post-id="%2$s"></i><span class="uw_change_order_number_submit_processing_%2$s" style="display: none;">Processing...</span>', esc_attr( $order_id ), $post->ID );

			printf( '<p id="uw_change_order_number_result_%s" style="display: none;"></p>', $post->ID );
		}
	}
}

endif;

new WooCommerce_Edit_Order_Numbers_Table_Column;