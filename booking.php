<?php
/*
Plugin Name: Clean Booking
Description: Plugin that allows you to add booking form to your site, it is specialised for the cleaning industry (End of tenancy cleaning).
Version:     1.0
Author:      Tatjana Milanovic
Text Domain: booking
*/


//Set up
//Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

//Defining const with plugin url
define( 'BOOKING_PLUGIN_URL', __FILE__ );

//Includes
include( 'shortcodes.php' );
include( 'functions.php' );
include( 'enqueue.php' );
include( 'acf-booking.php');

//Hooks
add_action('init', 'tm_service_custom_post');
add_action('init', 'tm_booking_custom_post');
add_action('wp_enqueue_scripts', 'tm_enqueue', 100);
remove_action('future_tm_booking', '_future_tm_booking_hook');
add_filter('wp_insert_post_data', 'tm_do_not_set_posts_to_future');
add_action('admin_init', 'tm_acf_booking');
add_action('wp_ajax_nopriv_tm_the_calendar_ajax', 'tm_the_calendar_ajax');
add_action('wp_ajax_tm_the_calendar_ajax', 'tm_the_calendar_ajax');
add_action('wp_ajax_nopriv_tm_the_timeslots_ajax', 'tm_the_timeslots_ajax');
add_action('wp_ajax_tm_the_timeslots_ajax', 'tm_the_timeslots_ajax');
add_action('wp_ajax_nopriv_tm_add_new_booking_ajax', 'tm_add_new_booking_ajax');
add_action('wp_ajax_tm_add_new_booking_ajax', 'tm_add_new_booking_ajax');
add_action('wp_footer', 'tm_the_booking_modal');

//Shortcode
add_shortcode('clean-booking', 'tm_booking_content');

