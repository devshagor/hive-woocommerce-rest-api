<?php
/**
 * Plugin Name:       hive-woocommerce-rest-api
 * Description:       hive-woocommerce-rest-api system using the latest version of WordPress
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            devshagor
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hive-woocommerce-rest-api
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// domain/wp-json/wc/v3/custom 
function get_custom_fallback( $request ) {
    $args     = array ( 
        'post_type' => 'shop_order', 
        'posts_per_page' => -1,
        'post_status' => 'wc-completed'
    );
    $products = get_posts( $args ); 

    $order_info = [];

    foreach($products as $product) {
        $order = wc_get_order( $product->ID );
       
        $order_info[$order->get_id()]  = [
            'email' => $order->get_billing_email(),
            'name' => $order->get_billing_first_name(),
            'phone' => $order->get_billing_phone()
        ];
    }

    $order_details = array(
        'custom' => $products,
        "request"=> $request->get_params(),
        "order_details" => $order_info
    );

    return new WP_REST_Response($order_details);
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wc/v3', 'custom', array(
        'methods' => array('GET'), // array( 'GET', 'POST', 'PUT', )       
        'callback' => 'get_custom_fallback',
        'permission_callback' => '__return_true'
    ));
});
