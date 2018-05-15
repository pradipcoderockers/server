<?php

/**
 * @package WordPress
 * @subpackage BuddyBoss MarketPlace
 */
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly


if ( ! function_exists( 'bm_user_products' ) ) {
    function bm_user_products() {
        add_action( 'bp_template_content', 'bm_template_products' );
        bp_core_load_template( apply_filters( 'bm_user_products', 'members/single/plugins' ) );
    }
}

if ( ! function_exists( 'bm_template_products' ) ) {
    function bm_template_products() {
        bm_load_template('bm-products-page');
    }
}

if ( ! function_exists( 'bm_user_favorites' ) ) {
    function bm_user_favorites() {
        add_action( 'bp_template_content', 'bm_template_favorites' );
        bp_core_load_template( apply_filters( 'bm_user_favorites', 'members/single/plugins' ) );
    }
}

if ( ! function_exists( 'bm_template_favorites' ) ) {
    function bm_template_favorites() {
        bm_load_template('bm-favorites-page');
    }
}

if ( ! function_exists( 'bm_user_shops' ) ) {
    function bm_user_shops() {
        add_action( 'bp_template_content', 'bm_template_shops' );
        bp_core_load_template( apply_filters( 'bm_user_shops', 'members/single/plugins' ) );
    }
}

if ( ! function_exists( 'bm_template_shops' ) ) {
    function bm_template_shops() {
        bm_load_template('bm-favorite-shops-page');
    }
}

/**
 * Screen function to display the purchase history
 *
 * Template can be changed via the <code> bm_template_member_history</code>
 * filter hook. Note that template files can also be copied to the current theme.
 *
 * @since 	1.0
 * @uses	bp_core_load_template()
 * @uses	apply_filters()
 */
if ( ! function_exists( 'bm_screen_history' ) ) {
    function bm_screen_history()
    {
        add_action('bp_template_content', 'bm_template_history');
        bp_core_load_template(apply_filters('bm_template_member_history', 'members/single/plugins'));
    }
}

if ( ! function_exists( 'bm_template_history' ) ) {
    function bm_template_history() {
        bm_load_template('shop/member/history');
    }
}

/**
 * Screen function for tracking an order
 *
 * Template can be changed via the <code> bm_template_member_track_order</code>
 * filter hook. Note that template files can also be copied to the current theme.
 *
 * @since 	1.0
 * @uses	bp_core_load_template()
 * @uses	apply_filters()
 */

if ( ! function_exists( 'bm_screen_track_order' ) ) {
    function bm_screen_track_order()
    {
        add_action('bp_template_content', 'bm_template_track_order');
        bp_core_load_template(apply_filters('bm_template_member_track_order', 'members/single/plugins'));
    }
}

if ( ! function_exists( 'bm_template_track_order' ) ) {
    function bm_template_track_order() {
        bm_load_template('shop/member/track');
    }
}

/**
 * Register BuddyBoss Menu Page
 */
if ( !function_exists( 'register_buddyboss_menu_page' ) ) {

    function register_buddyboss_menu_page() {
        // Set position with odd number to avoid confict with other plugin/theme.
        add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', buddyboss_bm()->assets_url . '/images/logo.svg', 61.000129 );

        // To remove empty parent menu item.
        add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
        remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
    }

    add_action( 'admin_menu', 'register_buddyboss_menu_page' );
}

if ( ! function_exists( 'bm_load_template' ) ) {
    function bm_load_template($template)
    {
        $template .= '.php';
        if (file_exists(STYLESHEETPATH . '/bb-marketplace/' . $template))
            include_once(STYLESHEETPATH . '/bb-marketplace/' . $template);
        else if (file_exists(TEMPLATEPATH . '/bb-marketplace/' . $template))
            include_once(TEMPLATEPATH . '/bb-marketplace/' . $template);
        else {
            $template_dir = apply_filters('bm_load_template', buddyboss_bm()->templates_dir);
            include_once trailingslashit($template_dir) . $template;
        }
    }
}

if ( ! function_exists( 'bm_check_template' ) ) {
    function bm_check_template($template)
    {
        if ( strpos( $template, '.php' ) == false) {
            $template .= '.php';
        }

        if (file_exists(STYLESHEETPATH . '/bb-marketplace/' . $template))
            $path = STYLESHEETPATH . '/bb-marketplace/' . $template;
        else if (file_exists(TEMPLATEPATH . '/bb-marketplace/' . $template))
            $path = TEMPLATEPATH . '/bb-marketplace/' . $template;
        else {
            $template_dir = apply_filters('bm_check_template', buddyboss_bm()->templates_dir);
            $path = trailingslashit($template_dir) . $template;
        }
        return $path;
    }
}

/**
 * Output the tracked order
 *
 * @since 	1.0.8
 */
function  bm_output_tracking_order() {
    global $current_order;

    if( $current_order instanceof WC_Order ) :
        do_action( 'woocommerce_track_order', $current_order->id );
        echo '<h3>'. __( 'Your Order', 'buddyboss-marketplace' ) .'<h3>';

        wc_get_template( 'order/tracking.php', array(
            'order' => $current_order
        ) );
    endif;
}
add_action( 'bm_after_track_body', 'bm_output_tracking_order' );

function bm_my_recent_orders_shortcode( $atts ) {

    $current_page    = bp_action_variable(0);
    $current_page    = empty( $current_page ) ? 1 : absint( $current_page );
    $customer_orders = wc_get_orders( apply_filters( 'woocommerce_my_account_my_orders_query', array( 'customer' => get_current_user_id(), 'page' => $current_page, 'paginate' => true ) ) );

    return wc_get_template('myaccount/orders.php',
        array(
            'current_page' => absint( $current_page ),
            'customer_orders' => $customer_orders,
            'has_orders' => 0 < $customer_orders->total,
        )
    );


}
add_shortcode( 'bm_my_recent_orders', 'bm_my_recent_orders_shortcode' );

add_filter( 'woocommerce_get_endpoint_url', 'bm_my_recent_orders_pagination_url', 10, 4 );

function bm_my_recent_orders_pagination_url( $url, $endpoint, $value, $permalink  ) {

    if ( 'orders' == $endpoint ) {
        $user_domain = ( ! empty( $displayed_user_id ) ) ? bp_displayed_user_domain() : bp_loggedin_user_domain();
        $url = trailingslashit( $user_domain . 'orders/history/' . $value );
    }

    return $url;
}

add_action( 'wcv_pro_store_settings_saved', 'bm_update_user_location', 10, 1 );
add_action( 'wcv_save_pending_vendor',      'bm_update_user_location', 10, 1 );

/**
 * Save vendor's store latitude and longitude in database
 *
 * @param $vendor_id
 * @return bool
 */
function bm_update_user_location( $vendor_id ) {
    global $wpdb;

    $address1 	= get_user_meta( $vendor_id, '_wcv_store_address1', 	true );
    $address2 	= get_user_meta( $vendor_id, '_wcv_store_address2', 	true );
    $city	 	= get_user_meta( $vendor_id, '_wcv_store_city', 		true );
    $state	 	= get_user_meta( $vendor_id, '_wcv_store_state',		true );
    $country	= get_user_meta( $vendor_id, '_wcv_store_country', 	    true );
    $postcode	= get_user_meta( $vendor_id, '_wcv_store_postcode', 	true );

    $address =  $address1 .' ' .$address2 .' '. $city .' '. $state .' '. $country. ' '. $postcode;

    $all_settings   = get_option('widget_buddyboss_widget_location_filter');
    end($all_settings);
    $_settings      = prev($all_settings);

    $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . rawurlencode($address) . '&key=' . $_settings['api_key'];

    if ( !$response = wp_remote_get( $url ) ) {
        return false;
    }
    if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
        return false;
    }
    if ( '' === $response_body = wp_remote_retrieve_body( $response ) ) {
        return false;
    }

    $data =json_decode( $response_body, true );

    if ( isset( $data['results'][0] ) ) {

        $lat = $data['results'][0]['geometry']['location']['lat'];
        $lng = $data['results'][0]['geometry']['location']['lng'];

        $sql = "INSERT INTO {$wpdb->prefix}bm_user_location (user_id, lat, lng) VALUES (%d,  %f, %f) ON DUPLICATE KEY UPDATE lat = %f, lng = %f";
        $sql = $wpdb->prepare( $sql, $vendor_id, $lat, $lng, $lat, $lng );
        $wpdb->query($sql);

        return true;
    }
}

add_action( 'woocommerce_product_query', 'bm_shop_by_location', 10, 1 );

/**
 *
 * @param $query
 * @return mixed
 */
function bm_shop_by_location( $query ) {
    global $wpdb;

    if( !isset( $_GET['formatted_address'] ) || empty( $_GET['lat'] ) || empty( $_GET['lng'] ) )
        return;

    if ( is_admin() || !$query->is_main_query() ) {
        return;
    }

    $all_settings   = get_option('widget_buddyboss_widget_location_filter');
    $_settings      = reset($all_settings);

    $u_lat  = $_GET['lat'];
    $u_lng  = $_GET['lng'];
    $radius = isset( $_settings['radius'] ) ? $_settings['radius'] : '1000';


    // Source: https://stackoverflow.com/questions/29553895/querying-mysql-for-latitude-and-longitude-coordinates-that-are-within-a-given-mi
    // Spherical Law of Cosines Formula
    $query_vendor = "SELECT user_id  FROM {$wpdb->prefix}bm_user_location WHERE ( 3959 * acos( cos( radians({$u_lat}) ) * cos( radians( lat ) ) 
                    * cos( radians( lng ) - radians({$u_lng}) ) + sin( radians({$u_lat}) ) * sin(radians(lat)) ) )  < {$radius}";

    $vendors_in = $wpdb->get_col( $query_vendor );

    if ( ! empty( $vendors_in ) ) {
        $query->set('author__in', $vendors_in );
    } else {
        $query->set('author__in', array(0) );
    }
}

/**
 * Add a new column, for favorite count, in the product list table.
 * @param array $columns
 * @return array
 */
function bm_wcv_product_table_columns( $columns ){
    if( !empty( $columns ) ){
        $new_columns = array();
        
        $new_column_number = 5;
        $i = 1;
        
        foreach( $columns as $k=>$l ){
            if( $i == $new_column_number ){
                $new_columns['favorites'] = '<i class="fa fa-heart"></i>';
            }
            
            $new_columns[$k] = $l;
            $i++;
        }
        
        $columns = $new_columns;
    }
    return $columns;
}
add_filter( 'wcv_product_table_columns', 'bm_wcv_product_table_columns' );

/**
 * Show the data for the newly added column above.
 * 
 * @param array $rows
 * @return array
 */
function bm_wcv_product_table_rows( $rows ){
    if( !empty( $rows ) ){
        $products_favorited_count = get_option('products_favorited_count');
        
        foreach( $rows as $row ){
            $row->favorites = isset( $products_favorited_count[$row->ID] ) ? $products_favorited_count[$row->ID] : 0;
        }
    }
    return $rows;
}
add_filter( 'wcv_product_table_rows', 'bm_wcv_product_table_rows' );