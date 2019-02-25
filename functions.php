<?php

if(!defined('ABSPATH')) exit;

// add_action( 'admin_enqueue_scripts', 'ws_enqueue_script' );

// function ws_enqueue_script() {
//     wp_enqueue_script('wsscript', plugin_dir_url( __FILE__ ) .'/assets/js/script.js', array('jquery'), '', false);
// }

add_action( 'woocommerce_product_options_general_product_data', 'wholesale_price_field');
function wholesale_price_field() {
	woocommerce_wp_checkbox( array(
		'id'	=> '_status',
		'label'	=> __('Set Wholesale', 'woocommerce')
	));

	// woocommerce_wp_text_input( array(
	// 	'id'	=> '_wholesale1',
	// 	'label'	=> __('Wholesale 1', 'woocommerce'),
	// ));

	// woocommerce_wp_text_input( array(
	// 	'id'	=> '_wholesale2',
	// 	'label'	=> __('Wholesale 2', 'woocommerce'),
	// ));

	?>
	<!-- <a href="" id="testjquery">test</a>
	<a href="" id="#addWS">add</a> -->
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
		$wholesale1qty = get_post_meta( $post->ID, '_wholesale_1_qty', '_wholesale_1_qty', true);
		$wholesale2qty = get_post_meta( $post->ID, '_wholesale_2_qty', '_wholesale_2_qty', true);
		$wholesale3qty = get_post_meta( $post->ID, '_wholesale_3_qty', '_wholesale_3_qty', true);
		$wholesale4qty = get_post_meta( $post->ID, '_wholesale_4_qty', '_wholesale_4_qty', true);

		$wholesale1price = get_post_meta( $post->ID, '_wholesale_1_price', '_wholesale_1_price', true);
		$wholesale2price = get_post_meta( $post->ID, '_wholesale_2_price', '_wholesale_2_price', true);
		$wholesale3price = get_post_meta( $post->ID, '_wholesale_3_price', '_wholesale_3_price', true);
		$wholesale4price = get_post_meta( $post->ID, '_wholesale_4_price', '_wholesale_4_price', true);
		?>

		<tbody>
			<tr>
				<td><input type="number" name="_wholesale_1_qty" id="_wholesale_1_qty" value="<?= $wholesale1qty;?>"></td>
				<td><input type="text" name="_wholesale_1_price" id="_wholesale_1_price" value="<?= $wholesale1price;?>"></td>
			</tr>

			<tr>
				<td><input type="number" name="_wholesale_2_qty" id="_wholesale_2_qty" value="<?= $wholesale2qty;?>"></td>
				<td><input type="text" name="_wholesale_2_price" id="_wholesale_2_price" value="<?= $wholesale2price;?>"></td>
			</tr>

			<tr>
				<td><input type="number" name="_wholesale_3_qty" id="_wholesale_3_qty" value="<?= $wholesale3qty;?>"></td>
				<td><input type="text" name="_wholesale_3_price" id="_wholesale_3_price" value="<?= $wholesale3price;?>"></td>
			</tr>

			<tr>
				<td><input type="number" name="_wholesale_4_qty" id="_wholesale_4_qty" value="<?= $wholesale4qty;?>"></td>
				<td><input type="text" name="_wholesale_4_price" id="_wholesale_4_price" value="<?= $wholesale4price;?>"></td>
			</tr>
		</tbody>
	</table>
	<?php
}

add_action( 'woocommerce_process_product_meta', 'ws_save_field' );
function ws_save_field( $post_id ) {
	$status = $_POST['_status'];
	$wholesale1 = $_POST['_wholesale1'];
	$wholesale2 = $_POST['_wholesale2'];
	$wholesale_1_qty = $_POST['_wholesale_1_qty'];
	$wholesale_2_qty = $_POST['_wholesale_2_qty'];
	$wholesale_3_qty = $_POST['_wholesale_3_qty'];
	$wholesale_4_qty = $_POST['_wholesale_4_qty'];

	$wholesale_1_price = $_POST['_wholesale_1_price'];
	$wholesale_2_price = $_POST['_wholesale_2_price'];
	$wholesale_3_price = $_POST['_wholesale_3_price'];
	$wholesale_4_price = $_POST['_wholesale_4_price'];

	update_post_meta($post_id, '_status', esc_attr($status));
	// update_post_meta($post_id, '_wholesale1', esc_attr($wholesale1));
	// update_post_meta($post_id, '_wholesale2', esc_attr($wholesale2));
	update_post_meta($post_id, '_wholesale_1_qty', esc_attr($wholesale_1_qty));
	update_post_meta($post_id, '_wholesale_2_qty', esc_attr($wholesale_2_qty));
	update_post_meta($post_id, '_wholesale_3_qty', esc_attr($wholesale_3_qty));
	update_post_meta($post_id, '_wholesale_4_qty', esc_attr($wholesale_4_qty));

	update_post_meta($post_id, '_wholesale_1_price', esc_attr($wholesale_1_price));
	update_post_meta($post_id, '_wholesale_2_price', esc_attr($wholesale_2_price));
	update_post_meta($post_id, '_wholesale_3_price', esc_attr($wholesale_3_price));
	update_post_meta($post_id, '_wholesale_4_price', esc_attr($wholesale_4_price));



}

add_action( 'woocommerce_before_calculate_totals', 'set_wholesale_pricing');

function set_wholesale_pricing( $wc_cart ) {

	foreach ( $wc_cart->get_cart() as $key => $cart_item) {

		// $wholesale1 = get_post_meta( $cart_item['data']->get_id(), '_wholesale1', true);
		// $wholesale2 = get_post_meta( $cart_item['data']->get_id(), '_wholesale2', true);
		$wholesale1qty = get_post_meta( $cart_item['data']->get_id(), '_wholesale_1_qty', true);
		$wholesale2qty = get_post_meta( $cart_item['data']->get_id(), '_wholesale_2_qty', true);
		$wholesale3qty = get_post_meta( $cart_item['data']->get_id(), '_wholesale_3_qty', true);
		$wholesale4qty = get_post_meta( $cart_item['data']->get_id(), '_wholesale_4_qty', true);

		$wholesale1price = get_post_meta( $cart_item['data']->get_id(), '_wholesale_1_price', true);
		$wholesale2price = get_post_meta( $cart_item['data']->get_id(), '_wholesale_2_price', true);
		$wholesale3price = get_post_meta( $cart_item['data']->get_id(), '_wholesale_3_price', true);
		$wholesale4price = get_post_meta( $cart_item['data']->get_id(), '_wholesale_4_price', true);
		$price = $cart_item['data']->get_price();
		$qty = $cart_item['quantity'];
		// if($qty >= 3 && $qty < 5) {
		// 	$setprice = $wholesale1;
		// }
		// elseif($qty > 5 && $qty <=10) {
		// 	$setprice = $wholesale2;
		// }

		// else {
		// 	echo "wholesale price error";
		// }

		

		//Dinamic Pseudo code

		//jika qty > 3 and qty < 6 (4,5)
		if ($qty >= $wholesale1qty && $qty < $wholesale2qty) {
			$setprice = $wholesale1price;
			$cart_item['data']->set_price( $setprice);
		}
		//jika qty >= 6 and qty < 10 (6-9)
		elseif ($qty >= $wholesale2qty && $qty < $wholesale3qty) {
			$setprice = $wholesale2price;
			$cart_item['data']->set_price( $setprice);
		}
		//jika qty > wholesale3qty, set harga wholesale3price
		elseif ($qty >= $wholesale3qty && $qty < $wholesale4qty) {
			$setprice = $wholesale3price;
			$cart_item['data']->set_price( $setprice);
		}

		elseif ($qty >= $wholesale4qty) {
			$setprice = $wholesale4price;
			$cart_item['data']->set_price( $setprice);
		}

		else {
			echo "harga tidak ditemukan";
		}

		

		//jika qty > wholesale4qty, set harga wholesale4price

		//jika not found, tampilkan error
	}

}

// add_action( 'woocommerce_before_calculate_totals', 'IG_recalculate_price',5,1 );

// function IG_recalculate_price( $cart_object ) {

//     if ( is_admin() && ! defined( 'DOING_AJAX' ) )
//     return;
//     $quantity = 0;


//     foreach ( $cart_object->get_cart() as $key => $value ) {
//             // count q.ty
//             $quantity += $value['quantity'];
//          // delta q.ty  
//         if( $quantity > 3 ) {
//            // get price by custom field but you can use a simple var
//             $newprice = get_post_meta( $value['data']->get_id(), '_wholesale1', true);
//             $value['data']->set_price( $newprice );
//            // reset q.ty for check every item in the cart
//             $quantity = 0;

//                     }else{

//             $newprice = get_post_meta( $value['data']->get_id(), '_wholesale1', true);
//             $value['data']->set_price( $newprice );
//             $quantity = 0;

//             }
//         }
//     }