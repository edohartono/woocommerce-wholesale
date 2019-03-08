<?php

if(!defined('ABSPATH')) exit;



if ( is_admin() ) {
add_action( 'admin_enqueue_scripts', 'ws_enqueue_script' );
function ws_enqueue_script() {
    wp_register_script('wsscript', WS_PLUGIN_DIR_PATH .'/assets/js/script.js', array('jquery'), '', false);
    wp_enqueue_script('wsscript');
}


add_action ( 'admin_print_styles', 'ws_enqueue_style' );
function ws_enqueue_style(){
    wp_enqueue_style( 'wsstyle', plugins_url('/assets/css/style.css', __FILE__ ) );
}
}

if ( !function_exists('wcws_admin_submenu' ) ) {

	add_action( 'admin_menu', 'wcws_admin_submenu' );

	function wcws_admin_submenu() {
		add_submenu_page( 'woocommerce', 'Wholesale', 'Wholesale', 'manage_options', 'wholesale', 'wcws_admin_submenu_callback');
	}
}

if ( !function_exists( 'wcws_admin_submenu_callback' ) ) {
	function wcws_admin_submenu_callback() {
		$html = '<h2>WooCommerce Wholesale</h2>';
		echo $html;
	}
}

add_action( 'woocommerce_product_options_general_product_data', 'wholesale_price_field');
function wholesale_price_field() {
	
	global $post;
	$wholesale = get_post_meta( $post->ID, '_wholesale', true);
	$status = get_post_meta( $post->ID, '_wsstatus', true);

	if ($status == 'yes') {
		woocommerce_wp_checkbox( array(
			'id'	=> '_wsstatus',
			'label'	=> __('Set Wholesale', 'woocommerce'),
			'cbvalue' => 'yes'
		));
		echo '<div style="display: block" id="checkuncheck">';
	}

	elseif ($status == 'no' || !isset($status) || empty($status) ) {
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
				$html .= '<td><input type="number" name="_wholesale[qty][]" value="'.$dec_wholesale['qty'][$i].'"></td>';
				$html .= '<td><input type="text" name="_wholesale[price][]" value="'.$dec_wholesale['price'][$i].'"></td>';
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
	if ( is_admin() && !defined( 'DOING_AJAX' ) )
		return;

	foreach ( $wc_cart->get_cart() as $key => $cart_item) {
		$status = get_post_meta( $cart_item['data']->get_id(), '_wsstatus', true);
		$wholesale = get_post_meta( $cart_item['data']->get_id(), '_wholesale', true);
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

add_filter( 'woocommerce_cart_item_price', 'woocommerce_cart_item_price_filter', 10, 3 );
function woocommerce_cart_item_price_filter( $price, $cart_item, $cart_item_key ) {

	$status = get_post_meta( $cart_item['data']->get_id(), '_wsstatus', true);
	$wholesale = get_post_meta( $cart_item['data']->get_id(), '_wholesale', true);
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





add_action( 'woocommerce_before_add_to_cart_button', 'wholesale_single_loop' );

function wholesale_single_loop() {

        global $post;
        $status = get_post_meta($post->ID, '_wsstatus', true);
        
		if ( is_single() && isset($status) && $status == 'yes' ) {
        	$wholesale = json_decode(get_post_meta($post->ID, '_wholesale', true), true);
        	$wholesale_count = count($wholesale['qty']);
        	?>

        	<style>
				.wholesale-loop {
					border-radius: 5px;
					width: 90%;
					margin: 7vh auto;
					box-shadow: 0 0 9px 0 #DCDCDC;

				}

				.wholesale-loop tr th {
					text-align: center;
					background-color: #D0D0D0;
				}

				.wholesale-loop tr td {
					text-align: center;
				}
			</style>
        	<?php

        	$html = "<table class='wholesale-loop'><tr><th>Qty</th><th>Price</th></tr>";

        		for ( $i = 0; $i < $wholesale_count; $i++ ) {
					if (!empty($wholesale['qty'][$i+1])){
						$html .= "<tr><td>".$wholesale['qty'][$i]." - ".($wholesale['qty'][$i+1]-1)."</td>";
						$html .= "<td>".wc_price($wholesale['price'][$i])."</td></tr>";
					}

					else {
						$html .= "<tr><td> >= ".$wholesale['qty'][$i]."</td>";
						$html .= "<td>".wc_price($wholesale['price'][$i])."</td></tr>";
					}
				}

        	$html.= "</table>";

        }
        echo $html;
}
