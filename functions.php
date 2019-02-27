<?php

if(!defined('ABSPATH')) exit;

add_action( 'admin_enqueue_scripts', 'ws_enqueue_script' );

function ws_enqueue_script() {
    wp_register_script('wsscript', WS_PLUGIN_DIR_PATH .'/assets/js/script.js', array('jquery'), '', false);
    wp_enqueue_script('wsscript');
    wp_enqueue_style( 'wsstyle', plugin_dir_url( __FILE__ ) . '/assets/css/style.css' );
}

add_action( 'woocommerce_product_options_general_product_data', 'wholesale_price_field');
function wholesale_price_field() {
	
	global $post;
	$wholesale = get_post_meta( $post->ID, '_wholesale', true);
	$status = get_post_meta( $post->ID, '_wsstatus', true);
	echo $wholesale;

	if ($status == 'enable') {
		woocommerce_wp_checkbox( array(
			'id'	=> '_wsstatus',
			'label'	=> __('Set Wholesale', 'woocommerce'),
			'cbvalue' => 'enable'
		));
		echo '<div style="display: block" id="checkuncheck">';
	}

	elseif ($status == 'disable' || !isset($status) || empty($status) ) {
		woocommerce_wp_checkbox( array(
			'id'	=> '_wsstatus',
			'label'	=> __('Set Wholesale', 'woocommerce')
		));
		echo '<div style="display: none" id="checkuncheck">';
	}

	else {
		echo "value of status error";
	}

	?>
	<script type="text/javascript">

		$(document).ready(function(){
			$("#_wsstatus").change(function(){
				if(this.checked)
					$('#checkuncheck').fadeIn('slow');
				else
					$('#checkuncheck').fadeOut('slow');


			});

		    $(".add-rowWS").click(function(){
		        var markup = '<tr><td><input type="number" name="_wholesale[qty][]" value=""></td><td><input type="text" name="_wholesale[price][]" value=""></td><td><input type="button" class="delete-row" value="X"></td></tr>';

		        $("#inputWS tbody").append(markup);
		    });

		    $("#inputWS").on('click', '.delete-row', function(){
		          $(this).parent().parent().remove();
		       });

		    });
	</script>

	
	
	<table id="inputWS" class="input-ws" cellspacing="0">
		<thead>
			<tr>
				<th>Qty</th>
				<th>Price</th>
				<th><input type="button" class="add-rowWS" value="+"/></th>
			</tr>
		</thead>

		<?php

		if ( !empty($wholesale) && $wholesale != 'null' ) {
			$dec_wholesale = json_decode($wholesale, true);
			$count_ws = count($dec_wholesale['qty']);

			for ( $i = 0; $i < $count_ws; $i++ ) {
				echo '
				<tr>
				<td><input type="number" name="_wholesale[qty][]" value="'.$dec_wholesale['qty'][$i].'"></td>
				<td><input type="text" name="_wholesale[price][]" value="'.$dec_wholesale['price'][$i].'"></td>
				<td><input type="button" class="delete-row" value="X"></td>
				</tr>';
			}
		}

		elseif ( empty($wholesale) || !isset($wholesale) || $wholesale == 'null') {
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
	</div>
	<?php
}

add_action( 'woocommerce_process_product_meta', 'ws_save_field' );
function ws_save_field( $post_id ) {
	$wholesale = $_POST['_wholesale'];
	$status = $_POST['_wsstatus'];
	$count_wholesale = count($wholesale['qty']);
	$wholesale_save = json_encode($wholesale);

	update_post_meta($post_id, '_wholesale', $wholesale_save);
	update_post_meta($post_id, '_wsstatus', $status);
}

add_action( 'woocommerce_before_calculate_totals', 'set_wholesale_pricing');

function set_wholesale_pricing( $wc_cart ) {

	foreach ( $wc_cart->get_cart() as $key => $cart_item) {
		$status = get_post_meta( $cart_item['data']->get_id(), '_wsstatus', true);
		$wholesale = get_post_meta( $cart_item['data']->get_id(), '_wholesale', true);
		$dec_wholesale = json_decode($wholesale, true);

		if ($status == 'enable' && !empty($dec_wholesale)) {

			$count_wholesale = count($dec_wholesale['qty']);
			$price = $cart_item['data']->get_price();
			$qty = $cart_item['quantity'];

			for ( $i = 0; $i < $count_wholesale; $i++ ) {

		      if ($qty >= $dec_wholesale['qty'][$i]) {
		      	$setprice = $dec_wholesale['price'][$i];
		        $cart_item['data']->set_price($setprice);
		      }
			}
		}
	}
}
