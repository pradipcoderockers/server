<?php
   /* Plugin Name: Easily Change Admin Color
    Description:  Change admin menu colors and appearances. 
    Version: 2.5
    Author: Kyle Foulks
	License: GPLv2;
    */
	
	
	//set $hide_admin_colors_menu to 'true' to hide the options menu once all colors have been set.
	//set $hide_admin_colors_menu to 'false' to leave the options menu visible. 
	$hide_admin_colors_menu = 'false';
	$menu_id_array = array();

	@include($url.'cam_functions.php');

	function cam_admin_menu(){
			add_options_page( 'Admin Colors', 'Admin Colors', 'manage_options', 'admin_colors', 'cam_display_page');
			add_options_page('Admin Color Codes', 'Admin Color Codes', 'manage_options', 'color_codes', 'cam_display_page');
	}
	add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );

function mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('js/cam_js.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
	
	function cam_admin_init(){
		global $menu;
		
		//remove the submenu page responsible for creating the color codes tab.
		remove_submenu_page('options-general.php', 'color_codes');
		$cam_keys_array = array();
				if($menu){
					foreach($menu as $key=>$v){
						array_push($cam_keys_array,$key);
					}
				}
				
				
				add_settings_field(
    				'cam_WhatToColor?',
				    'What to color?',
				    'sandbox_checkbox_element_callback',
				    'admin_colors',
				    'main-section'
				);

				register_setting('cam_option_group','cam_WhatToColor');
			foreach($cam_keys_array as $key=>$v){
				global $menu_id_array;

					$menu_number = $v;
					
					$menu_title =  $menu[$menu_number][0];
					
					@$menu_id = $menu[$menu_number][5];
					
					if(!empty($menu_title[0])){
						
					register_setting('cam_option_group','cam_'.$menu_id);
					add_settings_field($menu_id,$menu_title,'field_one_callback','admin_colors','main-section',$menu_id);
					$data[$menu_id] = cam_values($menu_id);
					}


					
					//add menu_ids to array
					array_push($menu_id_array, $menu_id);
			}			
				if($menu){
					if (get_option('cam_WhatToColor') == 1) {
						foreach($data as $key=>$value){
					if(!empty($value)){
						if($key !== 'menu-settings' && strtolower($value)  == 'hide' ){
							$writable .= "
								#$key{
									display: none !important;
								}	
							";
						}else{
							$value = trim($value,' #');
								if(preg_match('/^[a-f0-9]{3,6}$/i', $value)){
									@$writable .= "
											#$key a .wp-menu-name{
												background-color: #$value !important;
												color: #fff !important;	
											}
										";
								}else{
									//error message
								}
						}
					}
				}
			};
					}

					if (get_option('cam_WhatToColor') == 2) {
						foreach($data as $key=>$value){
					if(!empty($value)){
						if($key !== 'menu-settings' && strtolower($value)  == 'hide' ){
							$writable .= "
								#$key{
									display: none !important;
								}	
							";
						}else{
							$value = trim($value,' #');
								if(preg_match('/^[a-f0-9]{3,6}$/i', $value)){
									@$writable .= "
											#$key a .wp-menu-name{
												border-left: 5px solid #$value !important;
												color: #fff !important;	
											}
										";
								}else{
									//error message
								}
						}
					}
				}
			};
					

					
			
			//??????
			
			if(is_admin()){
				if(@$_GET['page'] == 'admin_colors'){
					if($_GET['settings-updated'] == 'true' && $_GET['page'] == 'admin_colors'){
						cam_write_to_file($writable);
					};
				};
			};
			add_settings_section( 'main-section', 'Colors', 'main_section_callback', 'admin_colors' );
			
			//create section for the color code tab
			 add_settings_section( 'color-code-section', 'Color Codes', 'color_code_section_callback', 'color_codes' );
			 add_settings_field( 'field_id', 'Color 1', 'field_callback', 'admin_colors', 'color-code-section');
			 global $menu_id_array;
			 
			 
			 foreach($menu_id_array as $id){
				 if($id != ''){
					 $option = get_option('cam_'.$id);
					 if($id != ''){
						 if($option != '' && $option != 'hide'){
						 	create_color_code_fields($option);
						 }
					 };
				 }
			 }
		};
	
	//lets run!
	
	@include($url.'cam_widget.php');
	
	if($hide_admin_colors_menu == 'false'){
		add_action('admin_menu','cam_admin_menu');
		add_action( 'admin_init', 'cam_admin_init' );
	}
		add_action('admin_head','cam_custom_admin_css');
?>