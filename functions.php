<?php

if(!defined('ABSPATH')) exit;


function vedows_wholesale_price_field() {
	
	global $post;
	$wholesale = get_post_meta( $post->ID, '_vedows_wholesale', true);
	$status = get_post_meta( $post->ID, '_vedows_status', true);

	if ($status == 'yes') {
		woocommerce_wp_checkbox( array(
			'id'	=> '_vedows_status',
			'label'	=> __('Set Wholesale', 'woocommerce'),
			'cbvalue' => 'yes'
		));
		echo '<div style="display: block" id="checkuncheck">';
	}

	elseif ($status == 'no' || !isset($status) || empty($status) ) {
		woocommerce_wp_checkbox( array(
			'id'	=> '_vedows_status',
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
			$("#_vedows_status").change(function(){
				if(this.checked)
					$('#checkuncheck').fadeIn('slow');
				else
					$('#checkuncheck').fadeOut('slow');


			});

		    $(".add-rowWS").click(function(){
		        var markup = '<tr><td><input type="number" name="_vedows_wholesale[qty][]" value=""></td><td><input type="text" name="_vedows_wholesale[price][]" value=""></td><td><input type="button" class="delete-row" value="X"></td></tr>';

		        $("#inputWS tbody").append(markup);
		    });

		    $("#inputWS").on('click', '.delete-row', function(){
		          $(this).parent().parent().remove();
		       });

		    });
	</script>

	
	<?php	
	$html = '<table id="inputWS" class="input-ws" cellspacing="0">';
	$html .= '<thead>';
	$html .= '<tr>';
	$html .= '<th>Qty</th>';
	$html .= '<th>Price</th>';
	$html .= '<th><input type="button" class="add-rowWS" value="+"/></th>';
	$html .= '</tr>';
	$html .= '</thead>';

		

		if ( !empty($wholesale) && $wholesale != 'null' ) {
			$dec_wholesale = json_decode($wholesale, true);
			$count_ws = count($dec_wholesale['qty']);

			for ( $i = 0; $i < $count_ws; $i++ ) {
				$html .= '<tr>';
				$html .= '<td><input type="number" name="_vedows_wholesale[qty][]" value="'.$dec_wholesale['qty'][$i].'"></td>';
				$html .= '<td><input type="text" name="_vedows_wholesale[price][]" value="'.$dec_wholesale['price'][$i].'"></td>';
				$html .= '<td><input type="button" class="delete-row" value="X"></td>';
				$html .= '</tr>';
			}
		}

		elseif ( empty($wholesale) || !isset($wholesale) || $wholesale == 'null') {

				$html .= '<tr>';
				$html .= '<td><input type="number" name="_wholesale[qty][]" value=""></td>';
				$html .= '<td><input type="text" name="_wholesale[price][]" value=""></td>';
				$html .= '</tr>';
		}

		 else {
			echo "wholesale error";
		}
		
	$html .= '</table>';
	$html .= '</div>';

	echo $html;
}

function vedows_save_field( $post_id ) {
	$wholesale = $_POST['_vedows_wholesale'];
	$status = $_POST['_vedows_status'];
	$count_wholesale = count($wholesale['qty']);
	$wholesale_save = json_encode($wholesale);

	update_post_meta($post_id, '_vedows_wholesale', $wholesale_save);
	update_post_meta($post_id, '_vedows_status', $status);
}

function vedows_set_wholesale_pricing( $wc_cart ) {
	if ( is_admin() && !defined( 'DOING_AJAX' ) )
		return;

	foreach ( $wc_cart->get_cart() as $key => $cart_item) {
		$status = get_post_meta( $cart_item['data']->get_id(), '_vedows_status', true);
		$wholesale = get_post_meta( $cart_item['data']->get_id(), '_vedows_wholesale', true);
		$dec_wholesale = json_decode($wholesale, true);

		if ($status == 'yes' && !empty($dec_wholesale)) {

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


function vedows_woocommerce_cart_item_price_filter( $price, $cart_item, $cart_item_key ) {

	$status = get_post_meta( $cart_item['data']->get_id(), '_vedows_status', true);
	$wholesale = get_post_meta( $cart_item['data']->get_id(), '_vedows_wholesale', true);
	$price = $cart_item['data']->get_price();
	$dec_wholesale = json_decode($wholesale, true);

	if ( $status == 'yes' && !empty($dec_wholesale) ) {
		$qty = $cart_item['quantity'];
		$count_wholesale = count($dec_wholesale['qty']);

		for ( $i=0; $i < $count_wholesale; $i++ ) {
			if ( $qty >= $dec_wholesale['qty'][$i] ) {
				$wholesale_price = $dec_wholesale['price'][$i];
			}
		}
	} else {
		$wholesale_price = $price;
	}


    return $wholesale_price;
}
