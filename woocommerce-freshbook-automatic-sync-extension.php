<?php
/**
 * Plugin Name: WooCommerce Freshbook Automatic Sync Extension
 * Version: 1.0.0
 * Plugin URI: https://blog.vilourenco.com.br
 * Description: A plugin to run automatically, by a cron, the sync with your Freshbook account.
 * Author: Vinícius Lourenço
 * Author URI: https://blog.vilourenco.com.br
 * Requires at least: 4.4.0
 * Tested up to: 4.8.0
 *
 * Text Domain: wfase
 * Domain Path: /languages
 *
 * @package WordPress
 * @author Vinícius Lourenço
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Woo_Freshbook_Auto_Sync' ) ) {

	/**
	 * Main Class.
	 */
	class Woo_Freshbook_Auto_Sync {

		/**
	    * Plugin version.
		*
		* @var string
		*/
		const VERSION = '1.0.0';

		/**
	    * Plugins Needed version.
		*
		* @var string
		*/
		private $plugins_needed = array();


		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Return an instance of this class.
		 *
		 * @return object single instance of this class.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			if ( ! $this->check_plugins() ) {
				add_action( 'admin_notices', array( $this, 'wfase_fallback_notice' ) );
			} else {
				$this->includes();
			}
		}

		/**
		 * Check if we have all the plugins that we need!
		 */
		public function check_plugins() {
			$passed = true;
			if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$this->plugins_needed[] = 'WooCommerce';
				$passed = false;
			}

			if ( ! in_array( 'woocommerce-freshbooks/woocommerce-freshbooks.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$this->plugins_needed[] = 'WooCommerce FreshBooks';
				$passed = false;
			}
			return $passed;
		}

		/**
		 * Method to includes our dependencies.
		 *
		 */
		public function includes() {
			include_once 'includes/class-wfase-woo-freshbooks-sync-payments.php';
		}

		/**
		 * Fallback notice.
		 *
		 * We need some plugins to work, and if any isn't active we'll show you!
		 */
		public function wfase_fallback_notice() {
			echo '<div class="error"><p><strong>';
			echo sprintf( __( 'WooCommerce Freshbook Automatic Sync Extension need the following(s) plugin(s) to work:', 'wfase' ) );
			echo '</strong></p>';
			echo '<ul></u>';
			foreach ( $this->plugins_needed as $plugin ) {
				echo '<ul>' . $plugin . '</ul>';
			}
		 	echo '</div>';
		}
	}
}

/**
* Initialize the plugin.
*/
add_action( 'plugins_loaded', array( 'Woo_Freshbook_Auto_Sync', 'get_instance' ) );
