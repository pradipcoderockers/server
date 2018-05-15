<?php
	
	require_once(get_template_directory() . '/admin/admin.php');
	
	$inc_path = get_template_directory() . '/inc';
	
	$widgets_path = get_template_directory() . '/inc/widgets';
	define('STM_DOMAIN', 'stm_domain');
	
		// Theme setups
		
		// Custom code and theme main setups
		require_once( $inc_path . '/setup.php' );
		
		// Enqueue scripts and styles for theme
		require_once( $inc_path . '/scripts_styles.php' );
		
		// Customizer opt
		require_once ( $inc_path . '/redux-framework/admin-init.php' );
		//require_once( $inc_path . '/customizer/setup.php' );
		
		// Required plugins for theme
		require_once( $inc_path . '/tgm/tgm-plugin-registration.php' );
		
		// Visual composer custom modules
		if ( defined( 'WPB_VC_VERSION' ) ) {
			require_once( $inc_path . '/visual_composer.php' );
		}

		// Custom code for any outputs modifying
		require_once( $inc_path . '/payment.php' );
		require_once( $inc_path . '/custom.php' );
		
		// Custom code for woocommerce modifying
		if( class_exists( 'WooCommerce' ) ) {
			require_once( $inc_path . '/woocommerce_setups.php' );
		    
		    // Custom Woo widget
		    require_once( $widgets_path . '/woo_popular_courses.php' );
		}
		
		// Mailchimp widget
		require_once( $widgets_path . '/mailchimp.php' );
		require_once( $widgets_path . '/contacts.php' );
		require_once( $widgets_path . '/pages.php' );
		require_once( $widgets_path . '/socials.php' );
		require_once( $widgets_path . '/recent_posts.php' );
		require_once( $widgets_path . '/working_hours.php' );
			
		// Less compiler only in stm dev area
		$stm_uri = str_replace('www.', '',$_SERVER['HTTP_HOST'] );
		
		if(preg_match('/.stm/', $stm_uri)) {
			require_once( $inc_path . '/less/lessc.connect.php' );
		}
		
	function stm_cli_after_import() {
		$locations = get_theme_mod('nav_menu_locations');
		$menus  = wp_get_nav_menus();
		if(!empty($menus))
		{
			foreach($menus as $menu)
			{
				if(is_object($menu) && $menu->name == 'Primary menu')
				{
					$locations['primary'] = $menu->term_id;
				}
				if(is_object($menu) && $menu->name == 'Footer menu')
				{
					$locations['secondary'] = $menu->term_id;
				}
			}
		}

		set_theme_mod('nav_menu_locations', $locations);

		update_option( 'show_on_front', 'page' );

		$front_page = get_page_by_title( 'Front Page' );
		if ( isset( $front_page->ID ) ) {
			update_option( 'page_on_front', $front_page->ID );
		}
		$blog_page = get_page_by_title( 'Blog' );
		if ( isset( $blog_page->ID ) ) {
			update_option( 'page_for_posts', $blog_page->ID );
		}
		$shop_page = get_page_by_title( 'All courses' );
		if ( isset( $shop_page->ID ) ) {
			update_option( 'woocommerce_shop_page_id', $shop_page->ID );
			update_option( 'shop_single_image_size[width]', 840 );
			update_option( 'shop_single_image_size[height]', 400 );
			update_option( 'shop_thumbnail_image_size[width]', 150 );
			update_option( 'shop_thumbnail_image_size[height]', 75 );
		}

		if ( class_exists( 'RevSlider' ) ) {
			$main_slider = get_template_directory() . '/inc/redux-framework/redux-extensions/extensions/wbc_importer/demo-data/demo/rev_slider_full_screen_slider.zip';

			if ( file_exists( $main_slider ) ) {
				$slider = new RevSlider();
				$slider->importSliderFromPost( true, true, $main_slider );
			}
		}
	}

	add_action('stm_cli_after_import_done', 'stm_cli_after_import', 10, 2);
	function add_login_logout_register_menu( $items, $args ) {
	 if ( $args->theme_location != 'primary' ) {
	 return $items;
	 }
	 
	 if ( is_user_logged_in() ) {
	 $items .= '<li><a href="' . get_home_url() . '/dashboard">' . __( 'Dashboard' ) . '</a><ul class="sub-menu"><li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2206"><a href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li><li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2206"><a href="' . get_home_url() . '/account-2">' . __( 'Account' ) . '</a></li></ul></li>';
	 } else {
	 $items .= '<li><a href="' . get_home_url() . '/login">' . __( 'Login In' ) . '</a></li>';
	 }
	 
	 return $items;
	}
	 
	add_filter( 'wp_nav_menu_items', 'add_login_logout_register_menu', 199, 2 );
	
	
  
  function redirect_admin( $redirect_to, $request, $user ){

    //is there a user to check?

    if ( isset( $user->roles ) && is_array( $user->roles ) ) {

        //check for admins
        if ( in_array( 'vendor', $user->roles ) ) {

            $redirect_to = home_url().'/restaurant-owner-dashboard/'; // Your redirect URL
        }
        else
			{
				$redirect_to =  home_url().'/server-iq-profile';
			}
    }

    return $redirect_to;
}

add_filter( 'login_redirect', 'redirect_admin', 10, 3 );
