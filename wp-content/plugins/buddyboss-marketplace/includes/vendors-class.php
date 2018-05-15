<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( 'BuddyBoss_BM_Vendors' ) ):

    class BuddyBoss_BM_Vendors {

        /**
         * Array of all vendor
         * @var array
         */
        public $all_vendors = array();


        public $vendor_args = array (
            'fields'            => 'ID',
            'role' 				=> 'vendor',
            'meta_key' 			=> 'pv_shop_slug',
            'meta_value'   		=> '',
            'meta_compare' 		=> '>',
            'query_id'          => 'vendors_with_products'
        );

        public $search_term;

        public $orderby = 'registered';

        public $order = 'DESC';

        public $per_page;

        /**
         * Constructor
         */
        private function __construct() {
//            add_action( 'pre_user_query', function( $qv ){
//                var_dump( $qv );
//            });
        }

        /**
         *	vendors_with_products - Get vendors with products pubilc or private
         *	@param array $query
         */
        public function vendors_with_products( $query ) {

            global $wpdb;

            // $post_count = $products ? ' AND post_count  > 0 ' : '';

            if ( isset( $query->query_vars['query_id'] ) && 'vendors_with_products' == $query->query_vars['query_id'] ) {
                $query->query_from = $query->query_from . ' LEFT OUTER JOIN (
	                SELECT post_author, COUNT(*) as post_count
	                FROM '.$wpdb->prefix.'posts
	                WHERE post_type = "product" AND (post_status = "publish" OR post_status = "private")
	                GROUP BY post_author
	            ) p ON ('.$wpdb->prefix.'users.ID = p.post_author)';
                $query->query_where = $query->query_where . ' AND post_count  > 0 ' ;
            }
        }

        /**
         * Vendors ids as string
         * @return string
         */
        public function bm_get_vendor_users_string() {
            return $theExcludeString = implode(",", $this->all_vendors);
        }

        /**
         * Object Instance.
         */
        public static function instance() {
            static $instance = null;

            if ( null === $instance ) {
                $instance = new BuddyBoss_BM_Vendors();
                $instance->setup_actions();
            }

            return $instance;
        }

        /**
         * Main actions
         */
        private function setup_actions() {
            // Shop Index Filter
			add_filter( 'bp_ajax_querystring', array( $this, 'bm_index_search_results' ), 20, 2 );
            add_action( 'init', array( $this, 'index_search_filter' ) );
            add_action( 'init', array( $this, 'index_sort_filter' ) );

        }

        /**
         * Get Search Results
         * @param $request
         * @return array
         */
        public function bm_stores_search ($request, $subject) {

            $results = array();

            foreach ( $this->all_vendors as $vendor_id ) {
                if($subject == 'shops') {
                    $shop_name = WCV_Vendors::get_vendor_shop_name($vendor_id);
                    if (strpos(strtolower($shop_name), strtolower($request)) !== false) {
                        $results[] = $vendor_id;
                    }
                } else {
                    $vendor_name = bm_get_user_displayname($vendor_id);
                    if (strpos(strtolower($vendor_name), strtolower($request)) !== false) {
                        $results[] = $vendor_id;
                    }
                }
            }

            return $results;
        }


        /**
         * Filter after search
         * @param bool|false $qs
         * @param bool|false $object
         * @return bool
         */
        public function bm_index_search_results($qs=false, $object=false) {
            if ($object != 'members')  return $qs;

            if (isset ($_GET['bm_index_search']))
                $request = $_GET['bm_index_search'];

            if (isset ($_GET['subject'])) {
                $subject = $_GET['subject'];
            } else {
                $subject = 'shops';
            }

            if (empty ($request))  return $qs;

            $users = $this->bm_stores_search ($request, $subject);

                $args = wp_parse_args ($qs);

                if (isset ($args['include']))
                {
                    $included = explode (',', $args['include']);
                    $users = array_intersect ($users, $included);
                    if (count ($users) == 0)  $users = array (0);
                }

                $users = apply_filters ('bps_filter_members', $users);
                $args['include'] = implode (',', $users);
                $qs = build_query ($args);


            return $qs;
        }

        /**
         * Searching
         *
         */
        public function index_search_filter() {

            if ( isset( $_GET['bm_index_search'] ) )
                $this->search_term = $_GET['bm_index_search'];

            if ( empty( $this->search_term ) ) return;

            if ( isset( $_GET['subject'] ) ) {
                $subject = $_GET['subject'];
            } else {
                $subject = 'shops';
            }

            if ( $subject == 'shops' ) {
                $this->vendor_args['meta_key']      = 'pv_shop_name';
                $this->vendor_args['meta_value']    = $this->search_term;
                $this->vendor_args['meta_compare']  = 'LIKE';
            } else {
                $this->vendor_args['search'] = '*'. $this->search_term. '*';
            }
        }

        /**
         * Sorting
         *
         */
        public function index_sort_filter() {

            if ( isset( $_GET['storeorder'] ) && 'name' == $_GET['storeorder'] ) {
                $this->orderby                  = 'meta_value';
                $this->order                    = 'ASC';
                $this->vendor_args['meta_key']  = 'pv_shop_name';
            }

            if ( isset( $_GET['sorder'] ) && 'name' == $_GET['sorder'] ) {
                $this->orderby  = 'name';
                $this->order    = 'ASC';
            }
        }

        /**
         * Get all vendors
         *
         * @return array
         */
        public function get_all_vendors() {

            // Hook into the user query to modify the query to return users that have at least one product
            add_action( 'pre_user_query', array( $this, 'vendors_with_products') );

            $vendor_args = wp_parse_args(
                array(
                    'count_total' => false
                ), $this->vendor_args );

            $vendor_query = New WP_User_Query( $vendor_args );

            return $vendor_query->get_results();
        }

        /**
         * Get paged vendors
         *
         * @return array
         */
        public function get_paged_vendors() {

            // Hook into the user query to modify the query to return users that have at least one product
            add_action( 'pre_user_query', array( $this, 'vendors_with_products') );

            $this->per_page = get_option('posts_per_page');
            $paged          = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
            $offset         = ( $paged - 1 ) * $this->per_page;

            $vendor_args = $this->vendor_args;

            $vendor_args['offset']  = $offset;
            $vendor_args['number']  = $this->per_page;
            $vendor_args['orderby'] = $this->orderby;
            $vendor_args['order']   = $this->order;

            $vendor_query = New WP_User_Query( $vendor_args );

            return  $vendor_query->get_results();
        }

    }

endif;