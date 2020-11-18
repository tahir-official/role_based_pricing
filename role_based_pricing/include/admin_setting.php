<?php

add_action( 'woocommerce_product_options_pricing', 'add_custom_field_product_options_pricing' );
function add_custom_field_product_options_pricing() {
    global $product_object;

    echo '</div><div class="options_group">';

    $_cutom_meta_key_check = $product_object->get_meta('_cutom_meta_key_check');

    woocommerce_wp_checkbox( array( // Checkbox.
        'id'            => '_cutom_meta_key_check',
        'label'         => __( 'Specific product price', 'woocommerce' ),
        'value'         => empty($_cutom_meta_key_check) ? 'no' : $_cutom_meta_key_check,
        'description'   => __( 'Specific product price would be visible for all or for the members (Customer or Distributor) only.', 'woocommerce' ),
    ) );



    
    $customer_price = $product_object->get_meta('customer_price');
  
	woocommerce_wp_text_input(array(
	    'id' => 'customer_price',
	    'label' => __( 'Customer Price', 'textdomain' ),
	    'data_type' => 'price', 
	    'value'       =>$customer_price,
	  ));

	$distributor_price = $product_object->get_meta('distributor_price');
  
	woocommerce_wp_text_input(array(
	    'id' => 'distributor_price',
	    'label' => __( 'Distributor Price', 'textdomain' ),
	    'data_type' => 'price', 
	    'value'       =>$distributor_price,
	));

}


add_action( 'woocommerce_admin_process_product_object', 'save_custom_field_product_options_pricing' );
function save_custom_field_product_options_pricing( $product ) {
    $product->update_meta_data( '_cutom_meta_key_check', isset($_POST['_cutom_meta_key_check']) ? 'yes' : 'no' );

    $product->update_meta_data( 'customer_price', $_POST['customer_price'] );

    $product->update_meta_data( 'distributor_price', $_POST['distributor_price'] );
}

add_filter( 'woocommerce_loop_add_to_cart_link', 'ts_replace_add_to_cart_button', 10, 2 );
function ts_replace_add_to_cart_button( $button, $product ) {
	global $wp;
	$product_id=$product->get_id();
	$_cutom_meta_key_check = get_post_meta($product_id, '_cutom_meta_key_check', true );
	if($_cutom_meta_key_check=='yes'){
		if ( is_user_logged_in() ) {
		    $button_text = __("Add To Cart", "woocommerce");
		    $button_link = $product->get_permalink();
		    $button = '<button type="submit" name="add-to-cart" value="'.$product_id.'" class="single_add_to_cart_button button alt">Add to cart</button>';
		} else {
		    $button_text = __("Login to view price", "woocommerce");
			$button_link = $product->get_permalink();
			$button = '<a class="button product_type_simple add_to_cart_button ajax_add_to_cart" href="' .home_url().'/my-account/?callback='.home_url($wp->request) . '">' . $button_text . '</a>';
		}
		
		
	}else{
		$button_text = __("Add To Cart", "woocommerce");
		$button_link = $product->get_permalink();
		$button = '<button type="submit" name="add-to-cart" value="'.$product_id.'" class="single_add_to_cart_button button alt">Add to cart</button>';
	}
	return $button;
	

}


function custom_product_button(){
	global $product;

   
    $button_text = __( "Login to view price", "woocommerce" );
    $button_link = $product->get_permalink();
    
  
    echo '<a class="button product_type_simple add_to_cart_button ajax_add_to_cart" href="' .home_url().'/my-account/?callback='.$button_link.'">' . $button_text . '</a>';
}

add_action( 'woocommerce_single_product_summary', 'replace_single_add_to_cart_button', 1 );
function replace_single_add_to_cart_button() {
    global $product;
    $product_id=$product->get_id();
	$_cutom_meta_key_check = get_post_meta($product_id, '_cutom_meta_key_check', true );
	if($_cutom_meta_key_check=='yes'){
		if (!is_user_logged_in() ) {
		    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            add_action( 'woocommerce_single_product_summary', 'custom_product_button', 30 );
		} 
		
		
	}
	
}
add_filter('woocommerce_login_redirect', 'wc_login_redirect'); 

function wc_login_redirect( $redirect_to ) {

   $redirect_to = $_REQUEST['callback'];
   return $redirect_to;

}

	
add_filter('woocommerce_get_price', 'woocommerce_change_price_by_addition', 10, 2);
 
function woocommerce_change_price_by_addition($price, $product) {
     

	 if (is_user_logged_in() ) {

	 	
	 	$product_id=$product->get_id();
	 	$_cutom_meta_key_check = get_post_meta($product_id, '_cutom_meta_key_check', true );
	 	if($_cutom_meta_key_check=='yes'){
	 		$user = wp_get_current_user();
				if (in_array( 'customer', (array) $user->roles ) ) {
					$customer_price = get_post_meta($product_id, 'customer_price', true );
				    $price = $customer_price;
				}else if(in_array( 'distributor', (array) $user->roles ) ){
					$distributor_price = get_post_meta($product_id, 'distributor_price', true );
					$price = $distributor_price;
				}
	 		
	 	}
        

	 }
	
    
	
	
    return  $price;
}


add_action( 'woocommerce_thankyou', 'misha_poll_form', 9 );
 
function misha_poll_form( $order_id ) {
 
 $user = wp_get_current_user();
 if(in_array( 'distributor', (array) $user->roles ) ){
 	echo '<div class="newslatter_form">
		<h4>Enter Some Information</h4>
	   <form method="post"  id="thankyou_form" action="'.esc_url( admin_url('admin-post.php') ).'">
	                 
	                
	                  <div class="form-group">
	                   
	                    <input type="text" name="order_number" id="order_number" value="" class="form-control" placeholder="Order Number" required="">
	                  </div>
	                  <div class="form-group">
	                    
	                    <input type="date" name="expecting_date" id="expecting_date" class="form-control"  required="">
	                  </div>
	                  <div class="form-group">
	                    
	                    <textarea  name="special_notes" class="form-control" placeholder="Special Notes"  ></textarea>
	                  </div>
	                  <input type="hidden" name="action" value="send_to_admin" />
	                  <input type="hidden" name="order_id" value="' . $order_id . '" />
	                  <div class="form-group">
	                    <button type="submit" class="btn btn_theme btn-lg btn-block" name="submitBtnLogin" id="submitBtnLogin">Submit</button>
	                  </div>
		  </form>
		</div>';
	
 }
 	
 
}

function send_to_admin_submit() {

    global $wpdb;
    print_r($_POST);
    $user_info = get_userdata(1);
    $admin_name = $user_info->display_name;
    $admin_email = $user_info->user_email;

    $to = $admin_email;
	$subject = '#'.$_POST['order_id'].' order detail';
	$body = 'hello '.$admin_name.', you have received new order request by distributor. Please check detail in below.<br>';
	$body .='Order Number: '.$_POST['order_number'].'<br>';
	$body .='Expecting Date of Order: '.$_POST['expecting_date'].'<br>';
	$body .='Special Notes: '.$_POST['special_notes'].'<br>';
	$headers = array('Content-Type: text/html; charset=UTF-8');
	 
	wp_mail( $to, $subject, $body, $headers );

	$url=home_url().'/my-account/orders/';
	wp_redirect( $url );
    exit;

}

// Use your hidden "action" field value when adding the actions
add_action( 'admin_post_nopriv_send_to_admin', 'send_to_admin_submit' );
add_action( 'admin_post_send_to_admin', 'send_to_admin_submit' );





