<?php

/* MAKE SCHEDULED POSTS REGULAR */
remove_action('future_post', '_future_post_hook');
add_filter('wp_insert_post_data', 'do_not_set_posts_to_future');
function do_not_set_posts_to_future($data) {
	if($data['post_status'] == 'future' && $data['post_type'] == 'tm_booking') {
		$data['post_status'] = 'publish';
	}

	return $data;
}


/* ADD CUSTOM POST */
function tm_service_custom_post() {
	register_post_type( 'tm_services', array(
		'labels'             => array(
			'name'              => __( 'Services', 'booking' ),
			'singular_name'     => __( 'Service', 'booking' ),
			'add_new'           => _x( 'Add New', 'Service', 'booking' ),
			'add_new_item'      => __( 'Add New Service', 'booking' ),
			'edit_item'         => __( 'Edit Service', 'booking' ),
			'new_item'          => __( 'New Service', 'booking' ),
			'all_items'         => __( 'All Services', 'booking' ),
			'view_item'         => __( 'View Service', 'booking' ),
			'parent_item_colon' => '',
			'menu_name'         => __( 'Services', 'booking' )
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'revisions' ),
		//'rewrite'            => array('slug' => 'custom'),
	) );
}

function tm_booking_custom_post() {
	register_post_type( 'tm_booking', array(
		'labels'             => array(
			'name'              => __( 'Bookings', 'booking' ),
			'singular_name'     => __( 'Booking', 'booking' ),
			'add_new'           => _x( 'Add New', 'Booking', 'booking' ),
			'add_new_item'      => __( 'Add New Booking', 'booking' ),
			'edit_item'         => __( 'Edit Booking', 'booking' ),
			'new_item'          => __( 'New Booking', 'booking' ),
			'all_items'         => __( 'All Bookings', 'booking' ),
			'view_item'         => __( 'View Booking', 'booking' ),
			'parent_item_colon' => '',
			'menu_name'         => __( 'Bookings', 'booking' )
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'supports'           => array( 'title', 'editor', 'author', 'revisions' ),
		//'rewrite'            => array('slug' => 'custom'),
	) );
}


function tm_add_new_booking( $booking_title, $email, $phone, $text_content, $date_time, $property_details, $key , $additional_details) {
	// Create post object
	$my_post = array(
		'post_type'    => 'tm_booking',
		'post_title'   => wp_strip_all_tags( $booking_title ),
		'post_content' => $text_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
        'post_date'    => $date_time,

	);

// Insert the post into the database
	$post_id = wp_insert_post( $my_post );
	update_post_meta( $post_id, 'customer_email', $email);
	update_post_meta( $post_id, 'phone_number', $phone);
	update_post_meta( $post_id, 'address', $property_details['address']);
	update_post_meta( $post_id, 'house_no', $property_details['house_no']);
	update_post_meta( $post_id, 'postcode', $property_details['postcode']);
	update_post_meta( $post_id, 'city', $property_details['city']);
	update_post_meta($post_id, 'key_pick_up', $key);

	update_post_meta($post_id, 'key_address', $additional_details['key-address']);
	update_post_meta($post_id, 'key_houseno', $additional_details['key-houseno']);
	update_post_meta($post_id, 'key_postcode', $additional_details['key-postcode']);
	update_post_meta($post_id, 'key_city', $additional_details['key-city']);
	update_post_meta($post_id, 'additional_instructions', $additional_details['instructions']);





}


function tm_add_new_booking_ajax() {
	$first_name   = trim( sanitize_text_field( $_POST['first_name'] ) );
	$last_name    = trim( sanitize_text_field( $_POST['last_name'] ) );
	$email        = trim(sanitize_text_field($_POST['email']));
	$phone        = $_POST['phone'];
	$date_time    = trim( sanitize_text_field( $_POST['date'] ) ) . " " . trim( sanitize_text_field( $_POST['time'] ) ) ;
    $date_time    = date('Y-m-d H:i:s', strtotime($date_time));

	$text_content = trim( $_POST['text_content'] );

	$property_details = $_POST['property_details'];
	$property_details['address']    = trim(sanitize_text_field($property_details['address']));
	$property_details['house_no']   = $property_details['houseno'];
	$property_details['postcode']   = trim(sanitize_text_field($property_details['postcode']));
	$property_details['city']       = trim(sanitize_text_field($property_details['city']));

	$key = $_POST['key'];

	$additional_details = $_POST['additional_details'];
	$additional_details['key-address']    = trim(sanitize_text_field($additional_details['keyAddress']));
	$additional_details['key-houseno']    = trim(sanitize_text_field($additional_details['keyHouseno']));
	$additional_details['key-postcode']    = trim(sanitize_text_field($additional_details['keyPostcode']));
	$additional_details['key-city']    = trim(sanitize_text_field($additional_details['keyCity']));
	$additional_details['instructions']    = trim(sanitize_text_field($additional_details['instructions']));


	$booking_title = $first_name . " " . $last_name;


	tm_add_new_booking( $booking_title, $email, $phone, $text_content, $date_time, $property_details, $key, $additional_details);
	wp_die();
}


function tm_the_calendar( $day = '', $month = '', $year = '' ) {
	if ( $month == '' ) {
		$month = date( 'n' );
	}
	if ( $year == '' ) {
		$year = date( 'Y' );
	}

	$prev_month = $month - 1;
	if ( $prev_month <= 0 ) {
		$prev_month = 12;
	}

	$cur_month = date( 'n' );
	$cur_year  = date( 'Y' );
	$cur_day   = 0;
	if ( $month == $cur_month && $year == $cur_year ) {
		$cur_day = date( 'j' );
		$day = $cur_day;
	}
	else{
	    $day = 1;
    }

	$day_count      = cal_days_in_month( CAL_GREGORIAN, $month, $year );
	$day_count_prev = cal_days_in_month( CAL_GREGORIAN, $prev_month, $year );
	$offset         = date( 'N', strtotime( '1-' . $month . '-' . $year ) ) - 1;
	$onset          = 7 - ( $offset + $day_count ) % 7;
	if ( $onset == 7 ) {
		$onset = 0;
	}
	$onset_counter = 1;

	$dowMap = array( 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su' );

	?>


    <div class="calendar" data-month="<?php echo $month; ?>" data-year="<?php echo $year; ?>">
        <div class="month">
            <ul>
                <li class="change prev<?php if($month <= $cur_month  && $year <= $cur_year){echo ' disabled';} ?>">&#10094;</li>
                <li class="change next">&#10095;</li>
                <li>
					<?php echo date( 'F', mktime( 0, 0, 0, $month, 10 ) ); ?><br>
                    <span style="font-size:18px"><?php echo $year; ?></span>
                </li>
            </ul>
        </div>
        <ul class="weekdays">
			<?php for ( $i = 0; $i < 7; $i ++ ) { ?>
                <li>
					<?php echo $dowMap[ $i ]; ?>
                </li>
			<?php } ?>
        </ul>

        <ul class="days">
			<?php for ( $d = 1 - $offset; $d <= $day_count + $onset; $d ++ ) {
				if ( $d >= 1 && $d <= $day_count ) {
					?>
                    <li id="day-<?php echo $d; ?>" class="<?php
                    if($d < $cur_day){
                        echo 'blank';
                    }
                    else {
						echo 'active';
					}
					if ( $d == $cur_day ) {
						echo ' current';
					}
					if ( $d == $day ) {
						echo ' selected';
					}
					?>">
                        <span><?php echo $d; ?></span>
                    </li>
				<?php } else if ( $d < 1) { ?>
                    <li class="blank">
                        <span><?php echo $day_count_prev - $offset + 1; ?></span>
                    </li>
					<?php $offset --;
				} else if ( $d > $day_count ) { ?>
                    <li class="blank">
                        <span><?php echo $onset_counter; ?></span>
                    </li>
					<?php $onset_counter ++;
				}
			} ?>
        </ul>
    </div>
	<?php

}


function tm_the_calendar_ajax(){
    $day  =  "";
	$month  = sanitize_text_field( $_POST['month'] );
	$year  = sanitize_text_field( $_POST['year'] );

	tm_the_calendar($day, $month, $year);
	wp_die();
}

function tm_the_timeslots($day = '', $month = '', $year = '') {
	if ( $month == '' ) {
		$month = date( 'n' );
	}
	if ( $year == '' ) {
		$year = date( 'Y' );
	}
	if ( $day == '' ) {
		$day = date( 'j' );
	}

	$time_slot_query = new WP_Query( array(
		'post_type' => 'tm_booking',
		'posts_per_page' => - 1,
		'date_query' => array(
			array(
				'year'  => $year,
				'month' => $month,
				'day'   => $day,
			),
		),
	) ); ?>

	<?php $time_arr  = []; ?>

    <?php while ( $time_slot_query->have_posts() ) { ?>
		<?php $time_slot_query->the_post(); ?>
        <?php  array_push($time_arr, (int)get_the_time("H")); ?>
	<?php } ?>
	<?php wp_reset_query(); ?>


    <div class="card align-middle">
        <div class="card-header text-center">
           <span class="timeslot-header" data-day="<?php echo $day; ?>"><?php echo date('jS', mktime( 0, 0, 0, $month, $day )) . " of " . date( 'F', mktime( 0, 0, 0, $month, 10 ) ); ?></span>
        </div>
        <ul class="list-group myshadow">
            <?php for ($i=8; $i<16; $i++){ ?>
                <li class="list-group-item d-flex justify-content-between align-items-center<?php if(in_array($i, $time_arr)) { echo ' not-available'; } ?>">
                    <?php echo $i; ?>:00 - <?php echo $i+1; ?>:00
                </li>
            <?php } ?>
        </ul>
    </div>
<?php }

function tm_the_timeslots_ajax(){
	$day  = sanitize_text_field( $_POST['day'] );
	$month  = sanitize_text_field( $_POST['month'] );
	$year  = sanitize_text_field( $_POST['year'] );

	tm_the_timeslots($day, $month, $year);
	wp_die();
}



function tm_the_booking_modal() { ?>
    <?php $service_query = new WP_Query( array(
		'post_type' => 'tm_services',
		'posts_per_page' => - 1,
	) ); ?>

    <div class="modal fade" id="bookingModal" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <button id="closeb" type="button" class="close-modal btn btn-highlight" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <div class="modal-body">
                    <div id="booking-form">
                        <div id="crumbs">
                            <ul class="nav nav-tabs" id="nav-tab" role="tablist">
                                <li>
                                    <a id="nav-home-tab" onclick="StageCrumbs1()" data-toggle="tab" href="#nav-date"
                                       role="tab"
                                       aria-controls="nav-home" aria-selected="true"><span class="d-block d-sm-none" aria-hidden="true" style="font-weight: bold;">1</span><span class="d-none d-sm-block" aria-hidden="true">Date & Time</span></a>
                                </li>
                                <li>
                                    <a id="nav-service-tab" onclick="StageCrumbs2()" data-toggle="tab"
                                       href="#nav-service"
                                       role="tab"
                                       aria-controls="nav-service" aria-selected="false"><span
                                                class="d-block d-sm-none justify-content-center" aria-hidden="true"
                                                style="font-weight: bold;">2</span> <span class="d-none d-sm-block">Service Details</span></a>
                                </li>
                                <li>
                                    <a id="nav-contact-tab" class="disabled" onclick="StageCrumbs3()" data-toggle="tab"
                                       href="#nav-contact"
                                       role="tab"
                                       aria-controls="nav-contact" aria-selected="false"><span
                                                class="d-block d-sm-none justify-content-center" aria-hidden="true"
                                                style="font-weight: bold;">3</span> <span class="d-none d-sm-block">Finish Booking</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-date" role="tabpanel"
                                 aria-labelledby="nav-home-tab">
                                <div class="row mt-1 ml-1 mr-1">
                                    <div class="col-lg-8 mb-4">
                                        <h4 class="mb-3">Choose a Date</h4>
                                        <div class="calendar-container">
                                            <?php tm_the_calendar(); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mb-3 mt-1">

                                        <h4 class="mb-3">Choose a Time Slot</h4>
                                        <div class="timeslots-container">
	                                    <?php tm_the_timeslots();?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mr-1">
                                    <div class="col-8">
                                    </div>
                                    <div class="col-4">
                                        <button id="next1" class="btn btn-outline-primary btn-next btn-lg float-right"
                                                type="button"
                                                onclick="OpenService2()"
                                                data-toggle="tooltip" data-animation="true" data-placement="left"
                                                data-html="true" title="Continue to Service Details"> Next

                                            <!--data-template='<div class="tooltip" role="tooltip"><div class="tooltip-inner"></div></div>'-->
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-service" role="tabpanel"
                                 aria-labelledby="nav-service-tab">
                                <div class="row mt-1 ml-1 mr-1 summContainer">
                                    <div class="col-lg-8 mb-4 customize-service">
                                        <h4 class="mb-3">Customize your Service</h4>
                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <h5 class="d-flex">
                                                    <span class="text-muted">Tell us about your place?</span>
                                                </h5>

												<?php while ( $service_query->have_posts() ) { ?>
													<?php $service_query->the_post(); ?>

													<?php global $post; ?>

                                                    <button id="<?php echo $post->post_name . 'b'; ?>"
                                                            class="btn mainb propertyb" type="button"
                                                            aria-controls="<?php the_title(); ?>">
														<?php the_title(); ?>
                                                    </button>

												<?php } ?>
												<?php wp_reset_query(); ?>



												<?php while ( $service_query->have_posts() ) { ?>
													<?php $service_query->the_post(); ?>

													<?php global $post; ?>

													<?php if ( get_field( 'p_show' ) ) {
														?>
                                                        <div id="<?php echo $post->post_name . '-buttons'; ?>"
                                                             class="property-buttons">
                                                            <div class="card card-body mt-2">
                                                                <span class="fa fa-refresh align-self-end text-secondary reset"
                                                                      aria-hidden="true"></span>
                                                                <div class="row">
                                                                    <div class="col-12 col-lg-6 col-xl-12">
                                                                        <div class="btn-toolbar" role="toolbar"
                                                                             aria-label="Toolbar buttons for property addons l1">
                                                                            <div  class="p1 btn-group btn-group-sm mr-2 mb-1"
                                                                                 role="group"
                                                                                 aria-label="bedroom filter">
                                                                                <button type="button"
                                                                                        class="btn counter">-
                                                                                </button>
                                                                                <button class="btn service-label"><span class="min1">1</span> Bedroom
                                                                                </button>
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">+
                                                                                </button>
                                                                            </div>
                                                                            <div class="p2 btn-group btn-group-sm mr-2 mb-1"
                                                                                 role="group"
                                                                                 aria-label="bathroom filter">
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">-
                                                                                </button>
                                                                                <button class="btn service-label"><span class="min1">1</span> Bathroom
                                                                                </button>
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">+
                                                                                </button>
                                                                            </div>
                                                                            <div class="p3 btn-group btn-group-sm"
                                                                                 role="group"
                                                                                 aria-label="floor filter">
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">-
                                                                                </button>
                                                                                <button class="btn service-label"><span class="min1">1</span> Floor
                                                                                </button>
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">+
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6 col-xl-12">
                                                                        <div class="btn-toolbar" role="toolbar"
                                                                             aria-label="Toolbar buttons for property addons l2">
                                                                            <div class="p4 btn-group btn-group-sm mr-2 mb-1"
                                                                                 role="group"
                                                                                 aria-label="bedroom filter">
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">-
                                                                                </button>
                                                                                <button class="btn service-label"><span>0</span> Toilet
                                                                                </button>
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">+
                                                                                </button>
                                                                            </div>
                                                                            <div class="p5 btn-group btn-group-sm mr-2 mb-1"
                                                                                 role="group"
                                                                                 aria-label="bathroom filter">
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">-
                                                                                </button>
                                                                                <button class="btn service-label"><span>0</span> Utility
                                                                                    room
                                                                                </button>
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">+
                                                                                </button>
                                                                            </div>
                                                                            <div class="p6 btn-group btn-group-sm mr-2 mb-1"
                                                                                 role="group"
                                                                                 aria-label="floor filter">
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">-
                                                                                </button>
                                                                                <button class="btn service-label"><span>0</span> Office
                                                                                </button>
                                                                                <button type="button"
                                                                                        class="btn btn-secondary counter">+
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
														<?php
													}
													?>


												<?php } ?>
												<?php wp_reset_query(); ?>

                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <h5 class="d-flex">
                                                    <span class="text-muted">How would you like your carpets to be cleaned?</span>
                                                </h5>
                                                <button id="noc" class="btn mainb carpetb" type="button"
                                                        onclick="CarpetsHide();"
                                                        aria-controls="No Carpets">
                                                    No Carpets
                                                </button>
                                                <button id="hoov" class="btn mainb carpetb" type="button" aria-expanded="false"
                                                        onclick="CarpetsHide();"
                                                        aria-controls="Hoovered">
                                                    Hoovered
                                                </button>
                                                <button id="steamc" class="btn mainb carpetb" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#Cbuttons" aria-expanded="false"
                                                        aria-controls="Steam Cleaned">
                                                    Steam Cleaned
                                                </button>
                                                <div class="collapse" id="Cbuttons">
                                                    <div class="card card-body mt-2">
                                                        <span class="fa fa-refresh align-self-end text-secondary reset" aria-hidden="true"></span>
                                                        <div class="row">
                                                            <div class="col-12 col-lg-6 col-xl-12">
                                                                <div class="btn-toolbar mb-2" role="toolbar"
                                                                     aria-label="Toolbar buttons for carpet addons">
                                                                    <div  class="c1 btn-group btn-group-sm mr-2"
                                                                          role="group"
                                                                          aria-label="carpets filter">
                                                                        <button type="button" class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Carpet</button>
                                                                        <button type="button" class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div  class="c2 btn-group btn-group-sm mr-2"
                                                                          role="group"
                                                                          aria-label="rugs filter">
                                                                        <button type="button" class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Rug</button>
                                                                        <button type="button" class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <h5 class="d-flex">
                                                    <span class="text-muted">Would you like any upholstery to be cleaned?</span>
                                                </h5>
                                                <button id="upholsteryb" class="btn mainb" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#Ubuttons" aria-expanded="false"
                                                        aria-controls="Upholstery">
                                                    Upholstery
                                                </button>
                                                <div class="collapse" id="Ubuttons">
                                                    <div class="card card-body mt-2">
                                                        <span class="fa fa-refresh align-self-end text-secondary reset"
                                                              aria-hidden="true"></span>
                                                        <div class="row">
                                                            <div class="col-12 col-lg-6 col-xl-12">
                                                                <div class="btn-toolbar" role="toolbar"
                                                                     aria-label="Toolbar buttons for property addons l1">
                                                                    <div class="u1 btn-group btn-group-sm mr-2 mb-1"
                                                                         role="group"
                                                                         aria-label="bedroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Single Mattress
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="u2 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="bathroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Double Mattress
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="u3 btn-group btn-group-sm"
                                                                         role="group"
                                                                         aria-label="floor filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Sofa</button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-lg-6 col-xl-12">
                                                                <div class="btn-toolbar" role="toolbar"
                                                                     aria-label="Toolbar buttons for property addons l2">
                                                                    <div class="u4 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="bedroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Double l.curtain
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="u5 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="bathroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> 1/2 Curtain
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="u6 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="floor filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Armchair
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h5 class="d-flex">
                                                    <span class="text-muted">Is there any other things you would like to add?</span>
                                                </h5>
                                                <button id="extrab" class="btn mainb" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#Ebuttons" aria-expanded="false"
                                                        aria-controls="Extra">
                                                    Extra
                                                </button>
                                                <div class="collapse" id="Ebuttons">
                                                    <div class="card card-body mt-2">
                                                        <span class="fa fa-refresh align-self-end text-secondary reset"
                                                              aria-hidden="true"></span>
                                                        <div class="row">
                                                            <div class="col-12 col-lg-6 col-xl-12">
                                                                <div class="btn-toolbar" role="toolbar"
                                                                     aria-label="Toolbar buttons for property addons l1">
                                                                    <div class="e1 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="bedroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Double Oven
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="e2 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="bathroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Double Fridge
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="e3 btn-group btn-group-sm"
                                                                         role="group"
                                                                         aria-label="floor filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Blinds</button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-lg-6 col-xl-12">
                                                                <div class="btn-toolbar" role="toolbar"
                                                                     aria-label="Toolbar buttons for property addons l2">
                                                                    <div class="e4 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="bedroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Win. external
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="e5 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="bathroom filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Glass door
                                                                        </button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                    <div class="e6 btn-group btn-group-sm mr-2"
                                                                         role="group"
                                                                         aria-label="floor filter">
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">-
                                                                        </button>
                                                                        <button class="btn service-label"><span>0</span> Balcony</button>
                                                                        <button type="button"
                                                                                class="btn btn-secondary counter">+
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="summary" class="col-lg-4 order-md-2 mb-4">
										<?php while ( $service_query->have_posts() ) { ?>
                                            <?php $service_query->the_post(); ?>
											<?php global $post; ?>

                                            <?php
                                                $studio = get_field('p_base_price');
                                                $bedroom = get_field('p_bedroom');
                                                $bathroom = get_field('p_bathroom');
                                                $floor = get_field('p_floor');
                                                $toilet = get_field('p_toilet');
                                                $utility_room = get_field('p_utility_room');
                                                $office = get_field('p_office');

                                                $noc = 0;
                                                $hoovered = get_field("c_hoovered");
                                                $carpet = get_field("c_carpet");
                                                $rug = get_field("c_rug");

                                                $single_mattress = get_field('u_single_mattress');
                                                $double_mattress = get_field('u_double_mattress');
                                                $sofa = get_field('u_sofa');
                                                $double_l_curtain = get_field('u_double_lenght_curtain');
                                                $half_l_curtain = get_field('u_half_lenght_curtain');
                                                $armchair = get_field('u_armchair');

                                                $double_oven = get_field('e_double_oven');
                                                $double_fridge = get_field('e_double_fridge');
                                                $blinds = get_field('e_blinds');
                                                $win_external = get_field('e_external_window');
                                                $glass_door = get_field('e_glass_door');
                                                $balcony = get_field('e_balcony');

                                            ?>

                                            <div id="<?php echo $post->post_name . '-summary-content'; ?>" class="summary-content">
                                                <h5 class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="text-muted">Booking summary</span>
                                                    <span class="fa fa-arrow small pr-1" style="color: #7ecdae;"></span>
                                                </h5>
                                                <ul class="list-group myshadow mb-3">
                                                    <li class="list-group-item d-flex lh-condensed property_li">
                                                        <div class="w-100 property">
                                                            <div class="d-flex justify-content-between">
                                                                <h6 class="my-0 pt-2 sproperty">Property</h6>
                                                                <div class="mb-1 property-img" data-toggle="tooltip" data-animation="true" data-placement="left" data-html="true" title="Living room, kitchen, hallway and landing cleaning is part of the service.">
                                                                    <svg height="33" viewBox="-4 0 512 512" width="33" xmlns="http://www.w3.org/2000/svg" class=""><g><path d="m446 224.402344v287.597656h-387.542969v-236.261719l-.425781-31.285156 66.035156-70.300781 75.746094-80.636719 52.410156-55.804687 4.222656 4.0625zm0 0" fill="#e8e7e6" data-original="#E8E7E6" class=""></path><path d="m155.796875 252.171875h192.855469v192.855469h-192.855469zm0 0" fill="#ff4440" data-original="#FF4440" class="" style="fill:#665F79" data-old_color="#FF4440"></path><path d="m184.214844 280.589844h53.796875v136.015625h-53.796875zm0 0" fill="#9af4e7" data-original="#9AF4E7" class="" style="fill:#BE6C84" data-old_color="#9AF4E7"></path><path d="m266.433594 280.589844h53.796875v136.015625h-53.796875zm0 0" fill="#9af4e7" data-original="#9AF4E7" class="" style="fill:#BE6C84" data-old_color="#9AF4E7"></path><path d="m132.367188 446.570312c0 28.367188-18.0625 52.519532-43.304688 61.59375-6.914062 2.488282-14.363281 3.835938-22.128906 3.835938-36.144532 0-65.429688-29.292969-65.429688-65.429688 0-36.136718 29.285156-65.429687 65.429688-65.429687 7.765625 0 15.214844 1.347656 22.128906 3.835937 25.238281 9.070313 43.304688 33.222657 43.304688 61.59375zm0 0" fill="#99aa52" data-original="#99AA52" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m132.367188 446.570312c0 28.367188-18.0625 52.519532-43.304688 61.59375-25.246094-9.074218-43.304688-33.226562-43.304688-61.59375 0-28.371093 18.058594-52.523437 43.304688-61.59375 25.238281 9.070313 43.304688 33.222657 43.304688 61.59375zm0 0" fill="#adc165" data-original="#ADC165" class="active-path" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m449.125 31.542969v228.847656l-65.566406-87.5625-48.597656-98.566406v-42.71875zm0 0" fill="#e8e7e6" data-original="#E8E7E6" class=""></path><path d="m383.558594 31.542969v163.289062l-48.597656-48.597656v-114.691406zm0 0" fill="#d1d1d1" data-original="#D1D1D1" class="" style="fill:#HTTPS:" data-old_color="#https:"></path><path d="m446 224.402344v80.375l-185.292969-185.292969-202.25 202.25v-45.996094l-.425781-31.285156 66.035156-70.300781 132.378906-132.378906zm0 0" fill="#d1d1d1" data-original="#D1D1D1" class="" style="fill:#HTTPS:" data-old_color="#https:"></path><path d="m40.980469 301.6875-40.980469-40.984375 260.703125-260.703125 243.320313 243.320312-40.980469 40.980469-202.339844-202.335937zm0 0" fill="#ff4440" data-original="#FF4440" class="" style="fill:#665F79" data-old_color="#FF4440"></path></g> </svg>
                                                                </div>

<!--                                                                <img class="mb-1 property-img"-->
<!--                                                                     src="--><?php //echo plugin_dir_url( __FILE__ ) . 'assets/img/home.png'; ?><!--"-->
<!--                                                                     alt="property icon"-->
<!--                                                                     data-toggle="tooltip" data-animation="true" data-placement="left"-->
<!--                                                                     data-html="true" title="Living room, kitchen, hallway and landing cleaning is part of the service."-->
<!--                                                                >-->
                                                            </div>

                                                            <div class="d-100 d-none studio-property justify-content-between">
                                                                <small class="text-muted" style="padding-right: 33px;">Studio Property combines living room, bedroom, and kitchen into a single room. </small>
                                                                <small class="text-muted price" data-price="<?php echo $studio; ?>">£<?php echo $studio; ?></small>
                                                            </div>

                                                            <div class="d-100 d-flex justify-content-between p1">
                                                                <small class="text-muted"><span class="min1">1</span> Bedroom</small>
                                                                <small class="text-muted price" data-price="<?php echo $bedroom; ?>">£<?php echo $bedroom; ?></small>
                                                            </div>
                                                            <div class="d-100 d-flex justify-content-between p2">
                                                                <small class="text-muted"><span class="min1">1</span> Bathroom</small>
                                                                <small class="text-muted price" data-price="<?php echo $bathroom; ?>">£<?php echo $bathroom; ?></small>
                                                            </div>
                                                            <div class="d-100 d-flex justify-content-between p3">
                                                                <small class="text-muted"><span class="min1">1</span> Floor</small>
                                                                <small class="text-muted price" data-price="<?php echo $floor; ?>">£<?php echo $floor; ?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between p4">
                                                                <small class="text-muted"><span>0</span> Toilet</small>
                                                                <small class="text-muted price" data-price="<?php echo $toilet; ?>">£<?php echo $toilet; ?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between p5">
                                                                <small class="text-muted"><span>0</span> Utility room</small>
                                                                <small class="text-muted price" data-price="<?php echo $utility_room; ?>">£<?php echo $utility_room; ?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between p6">
                                                                <small class="text-muted"><span>0</span> Office</small>
                                                                <small class="text-muted price" data-price="<?php echo $office; ?>">£<?php echo $office; ?></small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                        <div class="w-100 Cbuttons">
                                                            <div class="d-flex justify-content-between">
                                                                <h6 class="my-0 pt-2 scarpets">Carpets</h6>
                                                                <div class="carpets-img"
                                                                     data-toggle="tooltip" data-animation="true" data-placement="left"
                                                                     data-html="true" title="Professional steam cleaning of choosen carpets and rugs."
                                                                >
                                                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="33" height="33" class=""><g><path style="fill:#A2B1B7" d="M319.578,0c-51.81,0-93.96,42.151-93.96,93.96v186.233h33.758V93.96  c0-33.196,27.007-60.202,60.202-60.202S379.78,60.765,379.78,93.96v84.958h33.758V93.96C413.538,42.151,371.388,0,319.578,0z" data-original="#A2B1B7" class=""></path><rect x="374.154" y="156.413" style="fill:#C4D3D9" width="45.011" height="288.07" data-original="#C4D3D9" class=""></rect><path style="fill:#A2B1B7" d="M135.708,0c-9.322,0-16.879,7.557-16.879,16.879V145.16h33.758V16.879  C152.587,7.557,145.03,0,135.708,0z" data-original="#A2B1B7" class=""></path><rect x="107.576" y="122.655" style="fill:#C4D3D9" width="56.264" height="59.476" data-original="#C4D3D9" class=""></rect><path style="fill:#665F79" d="M180.719,164.852h-45.011l90.022,313.39h33.758V280.193  C259.488,227.713,226.825,182.858,180.719,164.852z" data-original="#ED6350" class="" data-old_color="#ED6350"></path><path style="fill:#665F79" d="M180.721,172.976l-90.024-8.124c-46.106,18.007-78.769,62.86-78.769,115.342v198.048H225.73V280.193  C225.73,234.38,207.627,194.38,180.721,172.976z" data-original="#F37C4A" class="" data-old_color="#F37C4A"></path><g>
                                                                                <path style="fill:#A2B1B7" d="M34.433,410.725c-12.43,0-22.505,10.076-22.505,22.506v56.264c0,12.43,10.076,22.505,22.505,22.505   s22.505-10.076,22.505-22.505v-56.264C56.939,420.801,46.863,410.725,34.433,410.725z" data-original="#414D53" class="active-path" data-old_color="#414D53"></path>
                                                                                <path style="fill:#A2B1B7" d="M236.983,410.725c-12.43,0-22.505,10.076-22.505,22.506v56.264c0,12.43,10.076,22.505,22.505,22.505   s22.505-10.076,22.505-22.505v-56.264C259.488,420.801,249.413,410.725,236.983,410.725z" data-original="#414D53" class="active-path" data-old_color="#414D53"></path>
                                                                            </g><path style="fill:#BE6C84" d="M180.719,164.853v36.571c0,12.378-10.127,22.506-22.506,22.506h-45.011  c-12.378,0-22.505-10.127-22.505-22.506v-36.571c13.953-5.446,29.133-8.44,45.011-8.44S166.766,159.406,180.719,164.853z" data-original="#FBCFA3" class="" data-old_color="#FBCFA3"></path><path style="fill:#748993" d="M424.791,444.484h-33.758v56.264l67.516-22.505C458.549,459.675,443.358,444.484,424.791,444.484z" data-original="#748993" class=""></path><path style="fill:#A2B1B7" d="M424.791,478.242l-45.011,22.505l-45.011-22.505c0-18.567,15.191-33.758,33.758-33.758h22.505  C409.6,444.484,424.791,459.675,424.791,478.242z" data-original="#A2B1B7" class=""></path><polygon style="fill:#A2B1B7" points="466.314,478.242 432.556,495.121 466.314,512 500.072,512 500.072,478.242 " data-original="#414D53" class="active-path" data-old_color="#414D53"></polygon><rect x="293.247" y="478.242" style="fill:#AAAAAA" width="173.067" height="33.758" data-original="#57676E" class="" data-old_color="#aaaaaa"></rect><g>
                                                                                <path style="fill:#BE6C84" d="M180.719,419.165H90.697c-4.661,0-8.44-3.779-8.44-8.44c0-4.661,3.779-8.44,8.44-8.44h90.022   c4.661,0,8.44,3.779,8.44,8.44C189.159,415.386,185.38,419.165,180.719,419.165z" data-original="#E64A55" class="" data-old_color="#E64A55"></path>
                                                                                <path style="fill:#BE6C84" d="M180.719,385.407H90.697c-4.661,0-8.44-3.779-8.44-8.44s3.779-8.44,8.44-8.44h90.022   c4.661,0,8.44,3.779,8.44,8.44S185.38,385.407,180.719,385.407z" data-original="#E64A55" class="" data-old_color="#E64A55"></path>
                                                                                <path style="fill:#BE6C84" d="M180.719,351.648H90.697c-4.661,0-8.44-3.779-8.44-8.44s3.779-8.44,8.44-8.44h90.022   c4.661,0,8.44,3.779,8.44,8.44S185.38,351.648,180.719,351.648z" data-original="#E64A55" class="" data-old_color="#E64A55"></path>
                                                                            </g></g> </svg>
                                                                </div>
                                                            </div>
                                                            <div class="d-100 d-none hoovered-carpet justify-content-between">
                                                                <small class="text-muted" style="padding-right: 33px;">All of your carpets and rugs will be hoovered</small>
                                                                <small class="text-muted price" data-price="<?php echo $hoovered; ?>">£<?php echo $hoovered; ?></small>
                                                            </div>
                                                            <div class="d-100 d-none no-carpet justify-content-between">
                                                                <small class="text-muted" style="padding-right: 33px;">You do not need any cleaning on your carpets and rugs</small>
                                                                <small class="text-muted price" data-price="<?php echo $noc; ?>">£<?php echo $noc; ?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between c1">
                                                                <small class="text-muted"><span>1</span> Carpet</small>
                                                                <small class="text-muted price" data-price="<?php echo $carpet; ?>">£<?php echo $carpet; ?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between c2">
                                                                <small class="text-muted"><span>1</span> Rugs</small>
                                                                <small class="text-muted price" data-price="<?php echo $rug; ?>" >£<?php echo $rug; ?></small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li id="upholstery_li"
                                                        class="d-none list-group-item justify-content-between lh-condensed">
                                                        <div class="w-100 Ubuttons zero-sum-hidden">
                                                            <div class="d-flex justify-content-between">
                                                                <h6 class="my-0 pt-2 supholstery">Upholstery</h6>
                                                                <div>
                                                                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 58 58" style="enable-background:new 0 0 58 58;" xml:space="preserve" width="33" height="33" class=""><g><polygon style="fill:#AAAAAA" points="9,21.5 0,21.5 4,17.5 9,17.5 " data-original="#36363A" class="active-path" data-old_color="#aaaaaa"></polygon><polygon style="fill:#AAAAAA" points="49,21.5 58,21.5 54,17.5 49,17.5 " data-original="#36363A" class="active-path" data-old_color="#aaaaaa"></polygon><path style="fill:#665F79" d="M47.066,9.5H10.934C9.866,9.5,9,10.366,9,11.434V26.5v4.264V38.5h40v-7.736V26.5V11.434  C49,10.366,48.134,9.5,47.066,9.5z" data-original="#2F2F31" class="" data-old_color="#2F2F31"></path><polygon style="fill:#665F79" points="49,31.5 53,35.5 53,21.5 49,17.5 " data-original="#232321" class="" data-old_color="#DDDDDD"></polygon><polygon style="fill:#665F79" points="9,31.5 5,35.5 5,21.5 9,17.5 " data-original="#232321" class="" data-old_color="#DDDDDD"></polygon><polygon style="fill:#665F79" points="53,21.5 53,38.5 5,38.5 5,21.5 0,21.5 0,43.5 58,43.5 58,21.5 " data-original="#2E2D2B" class="" data-old_color="#2E2D2B"></polygon><polygon style="fill:#BFA380;" points="10,43.5 9,48.5 11,48.5 16,43.5 " data-original="#BFA380" class=""></polygon><polygon style="fill:#BFA380;" points="50,43.5 51,48.5 49,48.5 44,43.5 " data-original="#BFA380" class=""></polygon><path style="fill:#AAAAAA" d="M5,38.5h4.728H29v-4.75c0-1.795-1.455-3.25-3.25-3.25H11c-2.393,0-4.534,1.056-6,2.721V38.5z" data-original="#515151" class="" data-old_color="#aaaaaa"></path><g>
                                                                                <rect x="13" y="13.5" style="fill:#665F79" width="32" height="4" data-original="#232321" class="" data-old_color="#DDDDDD"></rect>
                                                                                <polygon style="fill:#665F79" points="13,22.5 13,26.5 25.75,26.5 32.25,26.5 45,26.5 45,22.5  " data-original="#232321" class="" data-old_color="#DDDDDD"></polygon>
                                                                            </g><path style="fill:#AAAAAA" d="M53,38.5h-4.728H29v-4.75c0-1.795,1.455-3.25,3.25-3.25H47c2.393,0,4.534,1.056,6,2.721V38.5z" data-original="#414144" class="" data-old_color="#aaaaaa"></path></g> </svg>
                                                                </div>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between u1">
                                                                <small class="text-muted"><span>0</span> Single Mattress</small>
                                                                <small class="text-muted price" data-price="<?php echo $single_mattress;?>">£<?php echo $single_mattress;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between u2">
                                                                <small class="text-muted"><span>0</span> Double Mattress</small>
                                                                <small class="text-muted price" data-price="<?php echo $double_mattress;?>">£<?php echo $double_mattress;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between u3">
                                                                <small class="text-muted"><span>0</span> Sofa</small>
                                                                <small class="text-muted price" data-price="<?php echo $sofa;?>">£<?php echo $sofa;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between u4">
                                                                <small class="text-muted"><span>0</span>  Double l.curtain</small>
                                                                <small class="text-muted price" data-price="<?php echo $double_l_curtain;?>">£<?php echo $double_l_curtain;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between u5">
                                                                <small class="text-muted"><span>0</span> 1/2 Curtain</small>
                                                                <small class="text-muted price" data-price="<?php echo $half_l_curtain;?>">£<?php echo $half_l_curtain;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between u6">
                                                                <small class="text-muted"><span>0</span> Armchair</small>
                                                                <small class="text-muted price" data-price="<?php echo $armchair;?>">£<?php echo $armchair;?></small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li id="extra_li"
                                                        class="d-none list-group-item justify-content-between lh-condensed">
                                                        <div class="w-100 Ebuttons zero-sum-hidden">
                                                            <div class="d-flex justify-content-between">
                                                                <h6 class="my-0 pt-2 sextra">Extra</h6>
                                                                <div>
                                                                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve" width="33" height="33" class=""><g><rect x="13" y="42" style="fill:#AAAAAA" width="34" height="4" data-original="#BCB7B3" class="" data-old_color="#aaaaaa"></rect><rect x="14" y="7" style="fill:#DDDDDD" width="14" height="32" data-original="#7FABDA" class="" data-old_color="#dddddd"></rect><rect x="32" y="7" style="fill:#DDDDDD" width="14" height="32" data-original="#7FABDA" class="" data-old_color="#dddddd"></rect><polygon style="fill:#AAAAAA" points="49,7 49,4 11,4 11,7 28,7 28,39 13,39 13,42 47,42 47,39 32,39 32,7 " data-original="#EDE7E9" class="" data-old_color="#aaaaaa"></polygon><rect x="1" y="6" style="fill:#665F79" width="4" height="53" data-original="#DD352ESTROKE:#B02721;STROKE-WIDTH:2;STROKE-LINECAP:ROUND;STROKE-MITERLIMIT:10;" class="active-path" data-old_color="#BE6C84"></rect><rect x="5" y="6" style="fill:#665F79" width="4" height="53" data-original="#DD352ESTROKE:#B02721;STROKE-WIDTH:2;STROKE-LINECAP:ROUND;STROKE-MITERLIMIT:10;" class="active-path" data-old_color="#BE6C84"></rect><rect x="9" y="6" style="fill:#665F79" width="4" height="53" data-original="#DD352ESTROKE:#B02721;STROKE-WIDTH:2;STROKE-LINECAP:ROUND;STROKE-MITERLIMIT:10;" class="active-path" data-old_color="#BE6C84"></rect><rect x="47" y="6" style="fill:#665F79" width="4" height="53" data-original="#DD352ESTROKE:#B02721;STROKE-WIDTH:2;STROKE-LINECAP:ROUND;STROKE-MITERLIMIT:10;" class="active-path" data-old_color="#BE6C84"></rect><rect x="51" y="6" style="fill:#665F79" width="4" height="53" data-original="#DD352ESTROKE:#B02721;STROKE-WIDTH:2;STROKE-LINECAP:ROUND;STROKE-MITERLIMIT:10;" class="active-path" data-old_color="#BE6C84"></rect><rect x="55" y="6" style="fill:#665F79" width="4" height="53" data-original="#DD352ESTROKE:#B02721;STROKE-WIDTH:2;STROKE-LINECAP:ROUND;STROKE-MITERLIMIT:10;" class="active-path" data-old_color="#BE6C84"></rect><g>
                                                                                <path style="fill:#BE6C84" d="M3,0C2.448,0,2,0.447,2,1v4c0,0.553,0.448,1,1,1s1-0.447,1-1V1C4,0.447,3.552,0,3,0z" data-original="#7383BF" class="" data-old_color="#7383BF"></path>
                                                                                <path style="fill:#BE6C84" d="M7,0C6.448,0,6,0.447,6,1v4c0,0.553,0.448,1,1,1s1-0.447,1-1V1C8,0.447,7.552,0,7,0z" data-original="#7383BF" class="" data-old_color="#7383BF"></path>
                                                                                <path style="fill:#BE6C84" d="M11,0c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1s1-0.447,1-1V1C12,0.447,11.552,0,11,0z" data-original="#7383BF" class="" data-old_color="#7383BF"></path>
                                                                                <path style="fill:#BE6C84" d="M49,0c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1s1-0.447,1-1V1C50,0.447,49.552,0,49,0z" data-original="#7383BF" class="" data-old_color="#7383BF"></path>
                                                                                <path style="fill:#BE6C84" d="M53,0c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1s1-0.447,1-1V1C54,0.447,53.552,0,53,0z" data-original="#7383BF" class="" data-old_color="#7383BF"></path>
                                                                                <path style="fill:#BE6C84" d="M57,0c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1s1-0.447,1-1V1C58,0.447,57.552,0,57,0z" data-original="#7383BF" class="" data-old_color="#7383BF"></path>
                                                                            </g><polygon style="fill:#EEEEEE" points="23,7 28,7 28,12 14,26 14,16 " data-original="#A4C9EA" class="" data-old_color="#eeeeee"></polygon><polygon style="fill:#EEEEEE" points="41,7 46,7 46,12 32,26 32,16 " data-original="#A4C9EA" class="" data-old_color="#eeeeee"></polygon></g> </svg>
                                                                </div>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between e1">
                                                                <small class="text-muted"><span>0</span>  Double Oven</small>
                                                                <small class="text-muted price" data-price="<?php echo $double_oven;?>">£<?php echo $double_oven;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between e2">
                                                                <small class="text-muted"><span>0</span>  Double Fridge</small>
                                                                <small class="text-muted price" data-price="<?php echo $double_fridge;?>">£<?php echo $double_fridge;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between e3">
                                                                <small class="text-muted"><span>0</span>  Blinds</small>
                                                                <small class="text-muted price" data-price="<?php echo $blinds;?>">£<?php echo $blinds;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between e4">
                                                                <small class="text-muted"><span>0</span>  Win. external</small>
                                                                <small class="text-muted price" data-price="<?php echo $win_external;?>">£<?php echo $win_external;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between e5">
                                                                <small class="text-muted"><span>0</span>  Glass door</small>
                                                                <small class="text-muted price" data-price="<?php echo $glass_door;?>">£<?php echo $glass_door;?></small>
                                                            </div>
                                                            <div class="d-100 d-none justify-content-between e6">
                                                                <small class="text-muted"><span>0</span>  Balcony</small>
                                                                <small class="text-muted price" data-price="<?php echo $glass_door;?>">£<?php echo $balcony;?></small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li
                                                            class="d-none list-group-item justify-content-between bg-light promo_li"
                                                            style="color:#BE6C84;">
                                                        <div>
                                                            <h6 class="my-0">Promo code</h6>
                                                            <small>FIVECLEANOFF</small>
                                                        </div>
                                                        <span class="discount">-£5</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span>Total (GBP)</span>
                                                        <strong class="total-price"></strong>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                        <div class="w-100">
                                                            <div class="d-flex justify-content-between">
                                                                <h6 id="sdate" class="my-0 pt-3">Date</h6>
                                                                <h6 id="stime" class="my-0 pt-3">Time</h6>
                                                            </div>
                                                            <div class="d-100 d-flex justify-content-between">
                                                                <span class="text-muted small sdate">08/10/2018</span>
                                                                <div>
                                                                    <svg height="55" viewBox="-4 0 512 512" width="55" xmlns="http://www.w3.org/2000/svg" class=""><g><path d="m475.117188 43.390625h-319.554688c16.53125 18.429687 26.675781 42.710937 26.675781 69.421875h321.082031v-41.21875c0-15.578125-12.625-28.203125-28.203124-28.203125zm0 0" fill="#dd352e" data-original="#DD352E" class="" style="fill:#665F79" data-old_color="#BE6C84"></path><path d="m208.269531 112.8125h-208.269531v373.039062c0 14.441407 11.707031 26.148438 26.148438 26.148438h451.027343c14.441407 0 26.144531-11.707031 26.144531-26.148438v-373.039062zm0 0" fill="#f2ecbf" data-original="#F2ECBF" class="" style="fill:#EEEEEE" data-old_color="#eeeeee"></path><g fill="#28384c" fill-rule="evenodd"><path d="m312.40625 156.203125h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m399.1875 156.203125h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m52.066406 242.984375h52.070313v52.066406h-52.070313zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m138.847656 242.984375h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m225.628906 242.984375h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m52.066406 329.761719h52.070313v52.070312h-52.070313zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m138.847656 329.761719h52.066406v52.070312h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m225.628906 329.761719h52.066406v52.070312h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m52.066406 416.542969h52.070313v52.066406h-52.070313zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m138.847656 416.542969h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m225.628906 416.542969h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m312.40625 242.984375h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m312.40625 329.761719h52.066406v52.070312h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m312.40625 416.542969h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m399.1875 242.984375h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m399.1875 329.761719h52.066406v52.070312h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path><path d="m399.1875 416.542969h52.066406v52.066406h-52.066406zm0 0" data-original="#000000" class="" style="fill:#665F79" data-old_color="#000000"></path></g><path d="m425.21875 52.066406c-4.789062 0-8.675781-3.878906-8.675781-8.675781 0-23.925781 19.464843-43.390625 43.390625-43.390625 4.789062 0 8.675781 3.878906 8.675781 8.679688 0 4.796874-3.886719 8.675781-8.675781 8.675781-14.355469 0-26.035156 11.679687-26.035156 26.035156 0 4.796875-3.886719 8.675781-8.679688 8.675781zm0 0" fill="#e7eced" data-original="#E7ECED" class="" style="fill:#DDDDDD" data-old_color="#dddddd"></path><path d="m425.21875 86.78125c-4.789062 0-8.675781-3.878906-8.675781-8.679688v-34.710937c0-4.800781 3.886719-8.679687 8.675781-8.679687 4.792969 0 8.679688 3.878906 8.679688 8.679687v34.710937c0 4.800782-3.886719 8.679688-8.679688 8.679688zm0 0" fill="#e7eced" data-original="#E7ECED" class="" style="fill:#DDDDDD" data-old_color="#dddddd"></path><path d="m208.269531 112.8125c0 57.511719-46.621093 104.136719-104.132812 104.136719s-104.136719-46.625-104.136719-104.136719 46.625-104.132812 104.136719-104.132812 104.132812 46.621093 104.132812 104.132812zm0 0" fill="#e7eced" data-original="#E7ECED" class="" style="fill:#DDDDDD" data-old_color="#dddddd"></path><path d="m130.167969 121.492188h-26.03125c-4.792969 0-8.679688-3.878907-8.679688-8.679688 0-4.796875 3.886719-8.675781 8.679688-8.675781h26.03125c4.792969 0 8.679687 3.878906 8.679687 8.675781 0 4.800781-3.886718 8.679688-8.679687 8.679688zm0 0" fill="#28384c" data-original="#28384C" class="active-path" style="fill:#665F79" data-old_color="#BE6C84"></path><path d="m104.136719 121.492188c-4.792969 0-8.679688-3.878907-8.679688-8.679688v-43.390625c0-4.796875 3.886719-8.675781 8.679688-8.675781 4.789062 0 8.675781 3.878906 8.675781 8.675781v43.390625c0 4.800781-3.886719 8.679688-8.675781 8.679688zm0 0" fill="#28384c" data-original="#28384C" class="active-path" style="fill:#665F79" data-old_color="#BE6C84"></path><path d="m451.253906 86.78125h-52.066406c-4.792969 0-8.679688-3.878906-8.679688-8.679688 0-4.796874 3.886719-8.679687 8.679688-8.679687h52.066406c4.789063 0 8.679688 3.882813 8.679688 8.679687 0 4.800782-3.890625 8.679688-8.679688 8.679688zm0 0" fill="#ebba16" data-original="#EBBA16" class="" style="fill:#BE6C84" data-old_color="#EBBA16"></path><path d="m171.347656 33.332031-12.601562 12.601563c-3.394532 3.390625-3.394532 8.875 0 12.269531 1.691406 1.691406 3.914062 2.542969 6.136718 2.542969 2.21875 0 4.441407-.851563 6.132813-2.542969l12.601563-12.601563c-3.75-4.414062-7.855469-8.519531-12.269532-12.269531zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m104.136719 8.679688c-2.933594 0-5.808594.199218-8.679688.433593v25.597657c0 4.800781 3.886719 8.679687 8.679688 8.679687 4.789062 0 8.675781-3.878906 8.675781-8.679687v-25.597657c-2.871094-.234375-5.742188-.433593-8.675781-.433593zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m24.652344 45.601562 12.601562 12.601563c1.691406 1.691406 3.914063 2.542969 6.136719 2.542969s4.441406-.851563 6.132813-2.542969c3.394531-3.394531 3.394531-8.878906 0-12.269531l-12.597657-12.601563c-4.417969 3.75-8.523437 7.855469-12.273437 12.269531zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m171.015625 167.425781c-3.382813-3.394531-8.886719-3.394531-12.269531 0-3.394532 3.390625-3.394532 8.875 0 12.269531l12.601562 12.601563c4.425782-3.742187 8.519532-7.847656 12.269532-12.273437zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m104.136719 182.238281c-4.792969 0-8.679688 3.878907-8.679688 8.675781v25.601563c2.871094.234375 5.746094.433594 8.679688.433594 2.933593 0 5.804687-.199219 8.675781-.433594v-25.601563c0-4.796874-3.886719-8.675781-8.675781-8.675781zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m173.558594 112.8125c0 4.800781 3.886718 8.679688 8.679687 8.679688h25.589844c.242187-2.875.441406-5.746094.441406-8.679688s-.199219-5.804688-.441406-8.675781h-25.589844c-4.792969 0-8.679687 3.878906-8.679687 8.675781zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m26.035156 104.136719h-25.59375c-.242187 2.871093-.441406 5.742187-.441406 8.675781s.199219 5.804688.441406 8.679688h25.59375c4.789063 0 8.675782-3.878907 8.675782-8.679688 0-4.796875-3.886719-8.675781-8.675782-8.675781zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path><path d="m37.253906 167.425781-12.601562 12.597657c3.75 4.425781 7.847656 8.53125 12.273437 12.273437l12.597657-12.601563c3.394531-3.394531 3.394531-8.878906 0-12.269531-3.382813-3.394531-8.882813-3.394531-12.269532 0zm0 0" fill="#7383bf" data-original="#7383BF" class="" style="fill:#AAAAAA" data-old_color="#aaaaaa"></path></g> </svg>
                                                                </div>
                                                                <span class="text-muted small stime"></span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <form class="card">
                                                    <div class="input-group redeem">
                                                        <input type="text" class="form-control"
                                                               placeholder="PROMO CODE" style="border-right: none">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-secondary">Redeem
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

										<?php } ?>
	                                    <?php wp_reset_query(); ?>
                                    </div>

                                </div>
                                <div class="row mr-1">
                                    <div class="col-8">

                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-outline-primary btn-next disabled btn-lg float-right"
                                                onclick="OpenService3()">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                                <div class="row mt-1 ml-1 mr-1 summContainer">
                                    <div class="col-lg-8 order-lg-0">
                                        <h4 class="mb-3">Personal details</h4>
                                        <form id="personal" class="needs-validation">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="firstName">First name</label>
                                                    <input type="text" class="form-control" id="firstName"
                                                           placeholder=""
                                                           value="" required>
                                                    <div class="invalid-feedback">
                                                        Valid first name is required.
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="lastName">Last name</label>
                                                    <input type="text" class="form-control" id="lastName" placeholder=""
                                                           value="" required>
                                                    <div class="invalid-feedback">
                                                        Valid last name is required.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="email">Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="fa fa-envelope input-group-text pt-3"></span>
                                                        </div>
                                                        <input type="text" class="form-control" id="email"
                                                               placeholder="Email" required>
                                                        <div class="invalid-feedback" style="width: 100%;">
                                                            Your email is required.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label for="phone"> Phone number</label>
                                                    <input type="tel" class="form-control" id="phone"
                                                           placeholder="+44 555 555">
                                                    <div class="invalid-feedback">
                                                        Please enter a valid phone number.
                                                    </div>
                                                </div>
                                            </div>

                                            <h4 class="mb-3">Property details</h4>
                                            <div class="mb-3">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="address"
                                                       placeholder="1234 Main St" required>
                                                <div class="invalid-feedback">
                                                    Please enter your shipping address.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="address2">Address 2 <span
                                                            class="text-muted">(Optional)</span></label>
                                                <input type="text" class="form-control" id="address2"
                                                       placeholder="Apartment or suite">
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="houseno">House/Flat no.</label>
                                                    <input type="text" class="form-control" id="houseno" placeholder=""
                                                           required>
                                                    <div class="invalid-feedback">
                                                        House/Flat Number required.
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="postcode">Postcode</label>
                                                    <input type="text" class="form-control" id="postcode" placeholder=""
                                                           required>
                                                    <div class="invalid-feedback">
                                                        Postcode required.
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <label for="city">City</label>
                                                    <input type="text" class="form-control" id="city" placeholder=""
                                                           required>
                                                    <div class="invalid-feedback">
                                                        City/Town required.
                                                    </div>
                                                </div>
                                            </div>

                                            <h4 class="mb-1"> Additional details</h4>
                                            <hr class="mb-4">
                                            <div class="additional-details custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="key-pickup" name="key">
                                                <label class="custom-control-label" for="key-pickup">Key pick up
                                                    needed</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="save-info"
                                                       data-toggle="collapse" data-target="#keypick">
                                                <label class="custom-control-label" for="save-info">Key pick up address
                                                    NOT
                                                    same as property address?</label>
                                            </div>

                                            <div id="keypick" class="collapse mt-3" aria-expanded="true">
                                                <h5 class="mb-3">Key pick up details</h5>
                                                <div class="mb-3">
                                                    <label for="address">Address</label>
                                                    <input type="text" class="form-control" id="key-address"
                                                           placeholder="1234 Main St">
                                                    <div class="invalid-feedback">
                                                        Please enter your shipping address.
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="address2">Address 2 <span
                                                                class="text-muted">(Optional)</span></label>
                                                    <input type="text" class="form-control" id="key-address2"
                                                           placeholder="Apartment or suite">
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="houseno">House/Flat no.</label>
                                                        <input type="text" class="form-control" id="key-houseno"
                                                               placeholder=""
                                                               >
                                                        <div class="invalid-feedback">
                                                            House/Flat Number required.
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="postcode">Postcode</label>
                                                        <input type="text" class="form-control" id="key-postcode"
                                                               placeholder=""
                                                               >
                                                        <div class="invalid-feedback">
                                                            Postcode .
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-4">
                                                        <label for="city">City</label>
                                                        <input type="text" class="form-control" id="key-city" placeholder=""
                                                               >
                                                        <div class="invalid-feedback">
                                                            City/Town required.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="mb-4">
                                            <label for="addcomment" class="text-muted">Is there anything else you'd like
                                                us
                                                to know?</label>
                                            <textarea id="addcomment" class="form-control mb-3">
	                                        </textarea>
                                            <input id="confirm-button" class="btn btn-primary shadow btn-lg btn-block" type="submit" value="Confirm Booking">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }
