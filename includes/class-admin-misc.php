<?php
/**
 *	Misc admin - Plugin meta link and alert
 *	@package WooCommerce Edit Order Numbers
 *	@author UltimateWoo
 */

if ( ! class_exists( 'WooCommerce_Edit_Order_Numbers_Admin_Misc' ) ):

class WooCommerce_Edit_Order_Numbers_Admin_Misc {

	private $current_user;

	public function __construct() {

		$this->hooks();
	}

	/**
	 *	Start
	 */
	public function hooks() {

		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_links' ), 10, 2 );

		add_action( 'admin_notices', array( $this, 'admin_notice' ) );

		add_action( 'admin_init', array( $this, 'dismiss_admin_notice' ) );
	}

	/**
	 *	Add plugin row links
	 */
	public function plugin_row_links( $links, $file ) {

		if ( is_plugin_active( 'ultimatewoo-pro/ultimatewoo-pro.php'  ) )
			return $links;

		if ( $file == WOOCOMMERCE_EDIT_ORDER_NUMBERS_PLUGIN_BASENAME ) {

			$uw_links = array( '<a href="https://www.ultimatewoo.com/" target="_blank">Get UltimateWoo!</a>' );

			$links = array_merge( $links, $uw_links );
		}

		return $links;
	}

	/**
	 *	Admin notice for UltimateWoo
	 */
	public function admin_notice() {

		$screen_id = get_current_screen()->id;

		// Don't show to UltimateWoo users or users who have dismissed
		if ( is_plugin_active( 'ultimatewoo-pro/ultimatewoo-pro.php' ) || 1 === intval( get_user_meta( get_current_user_id(), 'uw_eon_dismissed_admin_alert', true ) ) )
			return;

		echo '<div class="update-nag">';

		_e( 'Thanks for installing WooCommerce Edit Order Numbers! For more WooCommerce goodness, check out the ' );

		printf( '<a href="%s" target="_blank">%s</a> | <a href="%s">%s</a>', esc_url( WOOCOMMERCE_EDIT_ORDER_NUMBERS_ULTIMATEWOO_URL ), __( 'most powerful WooCommerce plugin yet.' ), esc_url( wp_nonce_url( '?uw_eon_ultimatewoo_alert=1', 'uw_eon_ultimatewoo_alert' ) ), __( 'Dismiss', 'ultimatewoo' ) );

		echo '</div>';
	}

	/**
	 *	Allow the admin notice to be dismissed
	 */
	public function dismiss_admin_notice() {

		if ( isset( $_GET['uw_eon_ultimatewoo_alert'] ) && intval( $_GET['uw_eon_ultimatewoo_alert'] ) === 1 ) {

			if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'uw_eon_ultimatewoo_alert' ) )
				wp_die( 'Dismiss failed.' );

			update_user_meta( get_current_user_id(), 'uw_eon_dismissed_admin_alert', 1 );
		}
	}
}

endif;

new WooCommerce_Edit_Order_Numbers_Admin_Misc;