<?php

require_once dirname( __FILE__ ) . '/tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'stm_require_plugins' );

function stm_require_plugins() {
	$plugins_path = get_template_directory() . '/inc/tgm/plugins';
	$plugins = array(
		array(
			'name'               => 'STM Post Type',
			'slug'               => 'stm-post-type',
			'source'             => $plugins_path . '/stm-post-type.zip',
			'version'            => '1.3',
			'required'           => true,
			'force_activation'   => true
		),
		array(
			'name'               => 'WPBakery Visual Composer',
			'slug'               => 'js_composer',
			'source'             => $plugins_path . '/js_composer.zip',
			'version'            => '5.2.1',
			'required'           => true,
			'external_url'       => 'http://vc.wpbakery.com',
			'force_activation'   => true
		),
		array(
			'name'               => 'Revolution Slider',
			'slug'               => 'revslider',
			'source'             => $plugins_path . '/revslider.zip',
			'version'            => '5.4.5.1',
			'required'           => false,
			'external_url'       => 'http://www.themepunch.com/revolution/',
			'force_activation'   => true
		),
		array(
            'name'              => 'Breadcrumb NavXT',
            'slug'              => 'breadcrumb-navxt', 
            'required'          => false,
            'force_activation'  => true,
        ),
        array(
            'name'              => 'Contact Form 7',
            'slug'              => 'contact-form-7', 
            'required'          => false,
            'force_activation'  => true,
        ),
        array(
            'name'              => 'Woocommerce',
            'slug'              => 'woocommerce', 
            'required'          => true,
            'force_activation'  => true,
        ),
	);

	tgmpa( $plugins );

}