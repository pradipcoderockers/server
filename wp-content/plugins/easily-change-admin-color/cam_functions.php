<?php
//function to display the admin colors page.
	function cam_display_page(){?>
        <div class="wrap">
            <div id="cam_left">
            <form style="float: left;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<a href="http://twitter.com/kylefoulks" target="_blank" id="twitter"></a>
<a href="https://www.fiverr.com/kylefoulks" target="_blank" id="fiverr"></a>
<br style="clear: both;"/>
    <?php  
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'menu_options';  ?>

<h2 class="nav-tab-wrapper">  
    <a href="?page=admin_colors&tab=menu_options" class="nav-tab <?php echo $active_tab == 'menu_options' ? 'nav-tab-active' : ''; ?>">Menu Options</a>  
    <a href="?page=admin_colors&tab=color_codes" class="nav-tab <?php echo $active_tab == 'color_codes' ? 'nav-tab-active' : ''; ?>">Color Codes</a>  
     <a href="?page=admin_colors&tab=coming_soon" class="nav-tab <?php echo $active_tab == 'coming_soon' ? 'nav-tab-active' : ''; ?>">What's New?</a>  
</h2> 
                
                    <form action="options.php" method="POST">
                       
                        <?php 
							 if( $active_tab == 'menu_options' ) {  
							 	echo '<h3>Menu Options</h3>';
								
								settings_fields( 'cam_option_group' );  
								do_settings_sections( 'admin_colors' );  
							}elseif ($active_tab == 'coming_soon'){
								echo '<h3>What\'s New in 2.5?</h3>';
								echo '1. Users are no longer required to color an entire menu item. For a cleaner design select "border" under the "What to color" header. (As requested by master412160)';



								echo '<h3>What\'s New in 2.0?</h3>';
								echo '1. New tabbed browsing allows for easy navigation between option pages.<br>';
								echo '2. Color Codes! Tell your users what your menu colors actually mean by an easy to use interface!<br>';
								echo '3. Twitter logo updated to match new Wordpress 1.8 color scheme<br>';
								echo '4. Small squares beside input fields on the "Menu Options" tab. Now you can see what your colors are inline with the field.<br>';
								echo '5. Orange preset color scheme.<br>';
								echo '6. Yellow preset color scheme.';
								echo '<br>';
								echo '<h3>Coming Soon</h3>';
								
								echo '1. A contact form for suggestions<br>';
								echo '2. Ability to select border width when "border" option is selected.<br><br>';
								
								echo 'This is my first plugin and I am improving it as I think of new ideas. However, if you have a feature that you would like to see feel free to contact me. You can contact me directly at kylerfoulks@gmail.com. Any and all suggestions are appreciated. I am also currently working on a portfolio website. I should have it released shortly. As soon as it is released, I will release a plugin update that links to it. If you like this plugin, pass it around to your friends!';
									
							}else { 
								settings_fields( 'cam_code_group' );  
								do_settings_sections( 'color_codes' );  
							} // end if/else  
							  
							if($active_tab != 'coming_soon'){
								submit_button();
							}  
						
						
						?>
                        
                        
                    </form>
             </div>
       			<?php if($active_tab == 'menu_options'){?>
             <div id="cam_right">
             	<div id="cam_scheme">
                	<h2>Preset Colors</h2>
                    <?php include($url.'cam_color_scheme.php');?>
                    <?php include($url.'inc/schemes.inc.php');?>
                </div>
                
                <div id="cam_color_picker">
                	<h2>Color Picker</h2>
                    <input type="text" value="#bada55" class="my-color-field" />
                </div>
             </div>
             <?php };?>
         </div><?php
			cam_values($menu_id);
		};

//displays help text for the main section	
	function main_section_callback(){
			echo 'Change the hex value of the fields below to change the admin menu colors.  The `#` is optional.  Also, if you want to hide a menu item, enter `hide`. <br><br>';
	};
	
//dispays the help text for the color codes section. 
	function color_code_section_callback(){
		echo 'Enter descriptions for specific colors below. You must save your colors on the "Menu Options" tab before they will appear here';
	}
$color_code_values = array();
//this function generates all of the form fields based on the menu id variable
function field_one_callback($menu_id){
		$setting = esc_attr( get_option( 'cam_'.$menu_id ) );
		//add color to an array for use in the color code widget
		global $color_code_values;
		$color_code_values[] = $setting;
		
		$sanitized_color = str_replace('#','',$setting);
    	echo "<input style='float:left;' type='text' name='cam_$menu_id' value='$setting' /> <div style='margin-left: 10px;background-color: #$sanitized_color;float: left;height: 27px; width: 27px; '></div>";
	};

//loads the custom css to the admin head hook	
function cam_custom_admin_css() {
		$plug_url = WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/';
		$plugin_dir = wp_upload_dir();
		
		echo '<link rel="stylesheet" type="text/css" href="'.$plug_url.'css/style.css">';
			$css_version = rand(0,100);
			echo '<link rel="stylesheet" type="text/css" href="'.$plugin_dir['baseurl'].'/cam_style.css?version='.$css_version.'">';
	}

//begins formatting data for insertion into the css file	
function cam_values($menu_id){
		$value = get_option('cam_'.$menu_id);
		return $value;
	}

//write all of the fields to the CSS file.  	
function cam_write_to_file($writable){
		$plugin_dir = wp_upload_dir();
		$our_css = "/cam_style.css";
		
		$writable_file = $plugin_dir['basedir'].$our_css;
		
		file_put_contents($writable_file,$writable);
	}


function create_color_code_fields($color){
	$new_color = str_replace('#','',$color);
	register_setting('cam_code_group','cam_code_'.$new_color);
	add_settings_field( 'cam_code_'.$new_color, '<span style="display: inline-block; width: 200px; background-color:#'.$new_color.'; color: #fff;">#'.$new_color.'</span>', 'field_callback', 'color_codes','color-code-section',$new_color);	
}
function field_callback($new_color){
	
	if($new_color != ''){
		$field_value = get_option('cam_code_'.$new_color);
	echo "<input type='text' id='cam_code_$new_color' name='cam_code_$new_color' value='$field_value'>";
	}
}

function sandbox_checkbox_element_callback() {
 
    ?>
        <input type="radio" name="cam_WhatToColor" value="1" <?php checked(1, get_option('cam_WhatToColor'), true); ?>>Background <br>
        <input type="radio" name="cam_WhatToColor" value="2" <?php checked(2, get_option('cam_WhatToColor'), true); ?>>Border Only
   <?php
 
}
function border_or_bg(){
	echo 'hello';
}
?>