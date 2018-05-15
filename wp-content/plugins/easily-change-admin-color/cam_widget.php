<?php
	/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function color_code_widget() {

	wp_add_dashboard_widget(
                 'color_code_widget',         // Widget slug.
                 'Admin Menu Color Codes',         // Title.
                 'color_code_widget_function' // Display function.
        );	
}
add_action( 'wp_dashboard_setup', 'color_code_widget' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function color_code_widget_function() {

	echo 'These options are set on the "Color Codes" tab in the "Admin Colors" menu.';
	global $menu_id_array;
			 
			 $used_color_array = array();
			 foreach($menu_id_array as $id){
				 if($id != ''){
					 $option = get_option('cam_'.$id);
					 if($option != ''){
						 $sanitize_options = str_replace('#','',$option);
						 
						 if(!in_array($sanitize_options,$used_color_array)){
							 $code_desc = get_option('cam_code_'.$sanitize_options);
							 echo '<div style="padding-left: 10px; padding-top: 5px;color: #fff;width: 405px;margin-bottom: 10px; height:25px; background-color: #'.$sanitize_options.';">';
							 	if($code_desc != ''){
									echo $code_desc;
								}else{
									echo '(No Description Has Been Set)';	
								}
							 echo '</div>';
							 $used_color_array[] = $sanitize_options;
						 }
					 };
				 }
			 }
} 
	


?>