<?php



/* WooCommerce Hooks */
add_action( 'woocommerce_product_options_general_product_data', 'vedows_wholesale_price_field');
add_action( 'woocommerce_process_product_meta', 'vedows_save_field' );
add_action( 'woocommerce_before_calculate_totals', 'vedows_set_wholesale_pricing');
add_filter( 'woocommerce_cart_item_price', 'vedows_woocommerce_cart_item_price_filter', 10, 3 );

