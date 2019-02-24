<?php

if(!defined('ABSPATH')) exit;

add_action( 'woocommerce_product_options_general_product_data', 'wholesale_price_field');
function wholesale_price_field() {
	woocommerce_wp_checkbox( array(
		'id'	=> '_status',
		'label'	=> __('Set Wholesale', 'woocommerce')
	));
}

add_action( 'woocommerce_process_product_meta', 'ws_save_field' );
function ws_save_field( $post_id ) {
	$status = $_POST['_status'];
	update_post_meta($post_id, '_status', esc_attr($status));
}