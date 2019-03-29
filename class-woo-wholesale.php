
<?php
if ( !defined ( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WooCommerce_Wholesale')) {
	class WooCommerce_Wholesale {
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts') );
			add_action( 'admin_print_styles', array( $this, 'admin_styles'));
		}

		public function setup() {

		}

		public function admin_scripts() {
			if ( is_admin() ) {
				wp_register_script('vedows-script', VEDOWS_PLUGIN_DIR_PATH .'/assets/js/script.js', array('jquery'), '', false);
				wp_enqueue_script('vedows-script');
			}

		}

		public function admin_styles() {
			if ( is_admin() ) {
				wp_enqueue_style( 'vedows_style', plugins_url('/assets/css/style.css', __FILE__ ) );
			}
		}

	}
}

return new WooCommerce_Wholesale();