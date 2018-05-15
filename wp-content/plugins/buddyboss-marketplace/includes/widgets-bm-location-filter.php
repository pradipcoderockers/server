<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shop location filter Widget.
 *
 * @author   BuddyBoss
 * @category Widgets
 * @package  WordPress
 * @version  2.3.0
 * @extends  WC_Widget
 */

class BM_Location_Filter extends WP_Widget {
	protected $bm_filter_widget_cssclass;
	protected $bm_filter_widget_description;
	protected $bm_filter_widget_idbase;
	protected $bm_filter_widget_title;
	protected $bm_filter_widget_settings;

	/**
	 * Constructor function.
	 * @since  1.1.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->bm_filter_widget_cssclass = 'buddyboss_widget_location_filter woocommerce widget_layered_nav ';
		$this->bm_filter_widget_description = __( 'Filter products by shop location when viewing product archive.', 'buddyboss-marketplace' );
		$this->bm_filter_widget_idbase = 'buddyboss_widget_location_filter';
		$this->bm_filter_widget_title = __( 'MarketPlace Shop Location Filter', 'buddyboss-marketplace' );

		/* Widget settings. */
		$widget_ops = array(   'classname' => $this->bm_filter_widget_cssclass, 'description' => $this->bm_filter_widget_description );

		/* Widget control settings. */
		$control_ops = array(
            'title' => $this->bm_filter_widget_title,
            'api_key' => '',
            'radius' => '',
        );

		/* Create the widget. */
        parent::__construct( $this->bm_filter_widget_idbase, $this->bm_filter_widget_title, $widget_ops, $control_ops );
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.1.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
		$api_key = apply_filters( 'api_key', $instance['api_key'], $instance, $this->id_base );

        /* Before widget (defined by themes). */
        echo $before_widget;

        /**
         * This is more or less how core-shared-lib works today, but it caches an API by its full URL.
         *
         * The problem is that it doesn't work for an API like the Maps API that you can load additional libraries via a parameter.
         * Plugin 1 loads: https://maps.googleapis.com/maps/api/js?libraries=visualization,geometryWhen(later)
         * Plugin 2 loads: https://maps.googleapis.com/maps/api/js?libraries=places
         *
         * plugin 2 loads, the maps api is already loaded and gives you the error.
         * See http://jsbin.com/zuzata/1/edit.
         *
         * To be 100% honest, the console warning may just be a warning. This may all be moot.
         */
        wp_enqueue_script( 'script-googlemap-api', "https://maps.googleapis.com/maps/api/js?key=$api_key&libraries=places&callback=initAutocomplete" );

        ?>

        <script type="text/javascript">

            var autocomplete;


            // Bias the autocomplete object to the user's geographical location,
            // as supplied by the browser's 'navigator.geolocation' object.
            function geolocate() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var geolocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        var circle = new google.maps.Circle({
                            center: geolocation,
                            radius: position.coords.accuracy
                        });
                        autocomplete.setBounds(circle.getBounds());
                    });
                }
            }

            function initAutocomplete() {
                // Create the autocomplete object, restricting the search to geographical
                // location types.
                autocomplete = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */(document.getElementById('location_search')),
                        {types: ['geocode']});

                // When the user selects an address from the dropdown, populate the address
                // fields in the form.
                autocomplete.addListener('place_changed', fillInAddress);
            }

//            //Set current country name on page load in location filter widget
//            //e.g Canada
//            function setCurrentCounty() {
//                if (navigator.geolocation) {
//                    navigator.geolocation.getCurrentPosition(function(position) {
//
//                        var geolocation = {
//                            lat: position.coords.latitude,
//                            lng: position.coords.longitude
//                        };
//
//                        var geocoder = new google.maps.Geocoder();
//                        geocoder.geocode({'latLng': geolocation }, function(results, status) {
//                            if (status == google.maps.GeocoderStatus.OK) {
//                                if (results[0]) {
//                                    var loc = getCountry(results);
//                                    jQuery('#user_country_link').text( loc.long_name );
//                                    jQuery('#user_country_link').data( 'short_name', loc.short_name );
//                                    jQuery("input[name=user_country_long]").val( loc.long_name );
//                                    jQuery("input[name=user_country_short]").val( loc.short_name );
//                                    jQuery("#country_short").val( loc.short_name );
//                                    jQuery("#country_long").val( loc.long_name );
//                                    jQuery('#user_country_link').parents('li').show();
//                                }
//                            }
//                        });
//
//                    });
//                }
//            }
//
//            function getCountry(results)
//            {
//                for (var i = 0; i < results[0].address_components.length; i++)
//                {
//                    var shortname = results[0].address_components[i].short_name;
//                    var longname = results[0].address_components[i].long_name;
//                    var type = results[0].address_components[i].types;
//                    if (type.indexOf("country") != -1)
//                    {
//                      return results[0].address_components[i];
//                    }
//                }
//
//            }

            function fillInAddress() {
                //Get the place details from the autocomplete object.
                var place = autocomplete.getPlace();

                document.getElementById('formatted_address').value = place.formatted_address;
                document.getElementById('lat').value = place.geometry.location.lat();
                document.getElementById('lng').value = place.geometry.location.lng();
            }

            function replaceQueryParam(param, newval, search) {
                var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
                var query = search.replace(regex, "$1").replace(/&$/, '');

                return (query.length > 2 ? query + "" : "?") + (newval ? param + "=" + newval : '');
            }

            //Do location filter
            function doFilter() {

                var url = clearFilterQueries();

                if( false == /[?]/.test( url ) ) {
                    url += '?' + $('#shop_location_filter_form').serialize();
                } else {
                    url += '&' + $('#shop_location_filter_form').serialize();
                }

                console.log(url);

               window.location = url;
            }

            //Reset query string param for location search
            function clearFilterQueries() {

                <?php
                global $wp;
                if ( false == get_option( 'permalink_structure' ) ) {
                    $form_action = remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
                } else {
                    $form_action = preg_replace( '%\/page/[0-9]+%', '', add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) ) );
                } ?>

                var str = '<?php echo $form_action; ?>';

                str = replaceQueryParam('location', false, str);
                str = replaceQueryParam('sref', false, str);
                str = replaceQueryParam('lat', false, str);
                str = replaceQueryParam('lng', false, str);
                str = replaceQueryParam('formatted_address', false, str);


                return str;
            }

            (function($){

                $(document).on('click', '.location-link', function(e) {

                    var $elm = $(this);
                    e.preventDefault();

                    if ( 'anywhere' == $elm.data('location') ) {
                        window.location = clearFilterQueries();
                    } else if( 'user_country' == $elm.data('location') ) {
                        jQuery('#sref').val('user_country');
                        doFilter();
                    } else if( 'user_query' == $elm.data('location') ) {
                        jQuery('#sref').val('user_query');
                        doFilter();
                    } else {
                        jQuery('#sref').val('user_query');
                        doFilter();
                    }

                });

                $(document).on( 'click', 'a.change-location-query', function(e) {

                    e.preventDefault();

                    var $elm = $(this);
                    $form = $('#shop_location_filter_form');

                    $filter_links = $form.find('ul');
                    $filter_form = $form.find('.location_search_wrapper');

                    if( $filter_links.is(':visible') ) {
                        $filter_links.hide();
                        $filter_form.slideDown();
                        $filter_form.show();
                        $elm.text('<?php _e( 'Cancel', 'buddyboss-marketplace' ) ?>');
                    } else if( $filter_form.is(':visible') ) {
                        $filter_form.hide();
                        $filter_links.slideDown();
                        $filter_links.show();
                        $elm.text('<?php _e( 'Choose a custom location', 'buddyboss-marketplace' ) ?>');
                    }

                });

            })(jQuery);
        </script>

        <?php
        /* Display the widget title if one was input (before and after defined by themes). */
        if ( $title ) { echo $before_title . $title . $after_title; }

        /* Widget content. */
        // Add actions for plugins/themes to hook onto.
        do_action( $this->bm_filter_widget_cssclass . '_top' );

//		if ( 0 < intval( $instance['course_category'] ) ) {
        $this->load_component( $instance );
//		} // End If Statement

        // Add actions for plugins/themes to hook onto.
        do_action( $this->bm_filter_widget_cssclass . '_bottom' );

        /* After widget (defined by themes). */
        echo $after_widget;

	} // End widget()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since  1.1.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
    public function form( $instance ) {

        $instance = wp_parse_args( (array) $instance, array(
            'title'     => __( 'Shop Location', 'buddyboss-marketplace' ),
            'radius'    => '1000',
            'api_key'   => ''
        ) );

        ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'buddyboss-marketplace' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"  value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" />
		</p>

        <!-- Search radius -->
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>"><?php _e( 'Radius (in miles):', 'buddyboss-marketplace' ); ?></label>
            <input type="number" name="<?php echo esc_attr( $this->get_field_name( 'radius' ) ); ?>"  value="<?php echo esc_attr( $instance['radius'] ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'radius' ) ); ?>" />
        </p>

        <!-- Google place API key -->
        <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'api_key' ) ); ?>"><?php _e( 'Google API Key:', 'buddyboss-marketplace' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'api_key' ) ); ?>"  value="<?php echo esc_attr( $instance['api_key'] ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'api_key' ) ); ?>" />
		</p>

        <p><?php printf( "The Place Autocomplete service is part of the Google Places API Web Service and shares an <a href='%s'>API key</a>", 'https://developers.google.com/places/web-service/get-api-key' ); ?></p>

    <?php
	} // End form()

	/**
	 * Load the output.
	 * @param  array $instance.
	 * @since  1.1.0
	 * @return void
	 */
	protected function load_component ( $instance ) {
        global $wp;

        //location data prefilled and preselected stuff
//        $location = ! empty( $_REQUEST['location'] ) ? $_REQUEST['location'] : '';
//        $location_arr = explode(', ', $location );
//        $location_arr = array_slice( $location_arr, -3, 3, true );
//        $location = implode( ', ', $location_arr );

//        $city_short = ! empty( $_REQUEST['city'][0] ) ? $_REQUEST['city'][0] : '';
//        $city_long = ! empty( $_REQUEST['city'][1] ) ? $_REQUEST['city'][1] : '';
//        $state_short = ! empty( $_REQUEST['state'][0] ) ? $_REQUEST['state'][0] : '';
//        $state_long = ! empty( $_REQUEST['state'][1] ) ? $_REQUEST['state'][1] : '';
//        $country_short = ! empty( $_REQUEST['country'][0] ) ? $_REQUEST['country'][0] : '';
//        $country_long = ! empty( $_REQUEST['country'][1] ) ? $_REQUEST['country'][1] : '';
//        $location_search = ! empty( $_REQUEST['location_search'] ) ? $_REQUEST['location_search'] : '';
        $formatted_address = ! empty( $_REQUEST['formatted_address'] ) ? $_REQUEST['formatted_address'] : '';
//        $user_country_short = ! empty( $_REQUEST['user_country_short'] ) ? $_REQUEST['user_country_short'] : '';
//        $user_country_long = ! empty( $_REQUEST['user_country_long'] ) ? $_REQUEST['user_country_long'] : '';
        $sref = ! empty( $_REQUEST['sref'] ) ? $_REQUEST['sref'] : '';
        $lat = ! empty( $_REQUEST['lat'] ) ? $_REQUEST['lat'] : '';
        $lng = ! empty( $_REQUEST['lng'] ) ? $_REQUEST['lng'] : '';

        ?>
        <form method="get" action="" id="shop_location_filter_form">

            <ul>
                <!-- location anywhere -->
                <li class="<?php echo empty( $formatted_address ) ? 'chosen' : ''; ?>">
                    <a class="location-link" data-location="anywhere"><?php _e('Anywhere', 'buddyboss-marketplace' ) ?></a>
                </li>

                <!-- location user country -->
                <li style="display: none;" class="<?php echo ( ! empty( $sref ) && 'user_country' == $sref ) ? 'chosen' : '' ?>" >
                    <a data-location="user_country" class="location-link" id="user_country_link"><?php echo ! empty( $user_country_long ) ? $user_country_long : ''  ?></a>
                </li>

                <?php if ( ! empty( $formatted_address ) ): ?>
                    <li class="<?php echo ( ( ! empty( $sref ) && 'user_query' == $sref ) || isset( $_REQUEST['location_submit'] )) ? 'chosen' : '' ?>">
                        <a data-location="user_query" class="location-link" ><?php echo $formatted_address; ?></a>
                    </li>
                <?php endif; ?>
            </ul>

			<div class="location_search_wrapper" style="display: none;">
				<div class="location_name">
                <input type="hidden" id="sref" name="sref" />
                <input type="hidden" id="formatted_address" name="formatted_address" value="<?php echo $formatted_address ?>" />
                <input type="hidden" id="lat" name="lat" value="<?php echo $lat ?>"/>
                <input type="hidden" id="lng" name="lng" value="<?php echo $lng ?>"/>
                <input type="text" placeholder="<?php _e( 'Enter location', 'buddyboss-marketplace' ) ?>" onFocus="geolocate()" id="location_search" name="location" value="<?php echo $formatted_address ?>" />
                <button type="button" name="location_submit" class="button filter-button location-link"><?php  echo '>' ?></button>
				</div>
			</div>

            <a class="change-location-query"><?php _e( 'Choose a custom location', 'buddyboss-marketplace' ) ?></a>
		</form>

        <?php

	} // End load_component()
} // End Class
?>