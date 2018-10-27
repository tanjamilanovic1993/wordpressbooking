<?php

function tm_enqueue(){
	wp_register_style( 'bootstrap', plugins_url( '/assets/bootstrap.min.css', BOOKING_PLUGIN_URL) );
	wp_enqueue_style( 'bootstrap' );

	wp_register_style( 'tm_calendar_css', plugins_url( '/assets/calendarcss.css', BOOKING_PLUGIN_URL) );
	wp_enqueue_style( 'tm_calendar_css' );


	wp_register_style( 'tm_main_css', plugins_url( '/assets/booking-main.css', BOOKING_PLUGIN_URL) );
	wp_enqueue_style( 'tm_main_css' );

	wp_register_script( 'bootstrap', plugins_url( '/assets/bootstrap.min.js',BOOKING_PLUGIN_URL), array( 'jquery' ), '1.0.0', true );
	wp_enqueue_script( 'bootstrap' );

	wp_register_script( 'tm_popper', "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" );
	wp_enqueue_script( 'tm_popper' );

	wp_register_script( 'tm_main_js', plugins_url( '/assets/booking-main.js', BOOKING_PLUGIN_URL), array( 'jquery' ), '1.0.0', true );
	wp_localize_script( 'tm_main_js', 'bookingObj', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'homeUrl' => home_url('/')
	) );
	wp_enqueue_script( 'tm_main_js' );

}
