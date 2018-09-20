<?php
/*
Plugin Name: Clean Booking
Description: Bla
Version:     1.0
Author:      Tatjana Milanovic
Text Domain: booking
*/
define( 'BOOKING_PLUGIN_URL', __FILE__ ); // definisemo radi lakseg koriscenj(cuvamo url od naseg plugina preko __FILE__ prom koja uvek ima vr. url fajla u kom je iskoriscena)


if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly

include( 'shortcodes.php' );
include( 'functions.php' );
include( 'enqueue.php' );


add_action('init', 'tm_service_custom_post');
add_action('init', 'tm_booking_custom_post');
add_action( 'wp_enqueue_scripts', 'tm_enqueue', 100 );
add_action('wp_ajax_nopriv_tm_add_new_booking_ajax', 'tm_add_new_booking_ajax');
add_action('wp_ajax_tm_add_new_booking_ajax', 'tm_add_new_booking_ajax');
add_action('wp_ajax_nopriv_tm_the_timeslots_ajax', 'tm_the_timeslots_ajax');
add_action('wp_ajax_tm_the_timeslots_ajax', 'tm_the_timeslots_ajax');
add_action('wp_ajax_nopriv_tm_the_calendar_ajax', 'tm_the_calendar_ajax');
add_action('wp_ajax_tm_the_calendar_ajax', 'tm_the_calendar_ajax');
add_action('wp_footer', 'tm_the_booking_modal');


add_shortcode('clean-booking', 'booking_content');

