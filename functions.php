<?php

if(!defined('ABSPATH')) exit;

add_action( 'wp_enqueue_scripts', 'ws_enqueue_script' );

function ws_enqueue_script() {
    wp_register_script('wsscript', WS_PLUGIN_DIR_PATH .'/assets/js/script.js', array('jquery'), '', false);
    wp_enqueue_script('wsscript');
}

add_action( 'woocommerce_product_options_general_product_data', 'wholesale_price_field');
function wholesale_price_field() {
	woocommerce_wp_checkbox( array(
		'id'	=> '_status',
		'label'	=> __('Set Wholesale', 'woocommerce')
	));

	?>
	<table id="inputWS">
		<thead>
			<tr>
				<th>Qty</th>
				<th>Price</th>
				<th></th>
			</tr>
		</thead>

		<?php
		global $post;
		$wholesale = get_post_meta( $post->ID, '_wholesale', true);

		if (!empty($wholesale) ) {
			$dec_wholesale = json_decode($wholesale, true);
			$count_ws = count($dec_wholesale['qty']);

			for ( $i = 0; $i < $count_ws; $i++ ) {
				echo '
				<tr>
				<td><input type="number" name="_wholesale[qty][]" value="'.$dec_wholesale['qty'][$i].'"></td>
				<td><input type="text" name="_wholesale[price][]" value="'.$dec_wholesale['price'][$i].'"></td>
				</tr>';
			}
		}

		elseif ( empty($wholesale) || !isset($wholesale)) {
			echo '
			<tr>
			<td><input type="number" name="_wholesale[qty][]" value=""></td>
			<td><input type="text" name="_wholesale[price][]" value=""></td>
			</tr>
			';
		}

		 else {
			echo "wholesale error";
		}
		
		?>
	</table>
	<?php
}

add_action( 'woocommerce_process_product_meta', 'ws_save_field' );
function ws_save_field( $post_id ) {
	$status = $_POST['_status'];
	$wholesale = $_POST['_wholesale'];
	$count_wholesale = count($wholesale['qty']);
	$wholesale_save = json_encode($wholesale);

	update_post_meta($post_id, '_wholesale', $wholesale_save);
	update_post_meta($post_id, '_status', esc_attr($status));
}

add_action( 'woocommerce_before_calculate_totals', 'set_wholesale_pricing');

function set_wholesale_pricing( $wc_cart ) {

	foreach ( $wc_cart->get_cart() as $key => $cart_item) {

		$wholesale = get_post_meta( $cart_item['data']->get_id(), '_wholesale', true);
		$dec_wholesale = json_decode($wholesale);
		$count_wholesale = count($wholesale['qty']);

		$price = $cart_item['data']->get_price();
		$qty = $cart_item['quantity'];
		//Dinamic Pseudo code

		
		for ( $i = 0; $i < $count_wholesale, $i++ ) {
			$ws_qty = $dec_wholesale['qty'][$i];
			if ($qty >= $ws_qty && $qty < $ws_qty+1) {
				$setprice = $dec_wholesale['price'][$i];
				$cart_item['data']->set_price($setprice);
			}
		}
	}

}