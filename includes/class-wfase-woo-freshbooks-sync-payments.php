<?php
/**
 * FreshBooks Sync Payments
 *
 * @category  Class
 * @package   Vinícius Lourenço
 * @author    Vinícius Lourenço
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://blog.vilourenco.com.br/
 */

if ( ! defined( 'ABSPATH' ) ) { exit;
}

/**
 * Class to manage role to upload files on orders.
 */
class Woo_Fresh_Auto_Sync_Payments {

	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'admin_init', array( $this, 'set_cron_hooks') );
		add_action( 'wfase_freshbook_sync_payment_hook', array( $this, 'wfase_sync_invoices' ) );
	}

	public function set_cron_hooks() {
		if ( ! wp_next_scheduled( 'wfase_freshbook_sync_payment_hook' ) ) {
			wp_schedule_event( time(), 'hourly', 'wfase_freshbook_sync_payment_hook' );
		}
	}

	public function wfase_sync_invoices() {
		$args = array(
			'numberposts' => -1,
			'meta_key'  => '_wc_freshbooks_invoice_status',
			'meta_value' => 'sent',
			'post_type' => 'shop_order',
			'post_status' => 'wc-pending',
		);
		$unpaid_orders = get_posts( $args );
		foreach( $unpaid_orders as $unpaid_order ) {
			$order = new WC_FreshBooks_Order( $unpaid_order->ID );
			$order->update_invoice_from_order();
			$order->set_status('completed');
			$order->save();
		}
	}

}
return new Woo_Fresh_Auto_Sync_Payments();
