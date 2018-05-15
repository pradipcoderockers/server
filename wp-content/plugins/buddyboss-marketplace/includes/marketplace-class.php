<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BuddyBoss_BM_BP_Component' ) ):

	/**
	 *
	 * MarketPlace BuddyPress Component
	 * ***********************************
	 */
	class BuddyBoss_BM_BP_Component extends BP_Component {

		public $id = 'orders';

		/**
		 * INITIALIZE CLASS
		 *
		 * @since MarketPlace 1.0.0
		 */
		public function __construct() {
			parent::start(
				'bm', __( 'BM', 'buddyboss-marketplace' ), dirname( __FILE__ )
			);
		}

	} //End of class BuddyBoss_BM_BP_Component

endif;
