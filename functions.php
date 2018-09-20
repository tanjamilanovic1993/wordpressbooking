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


function tm_add_new_booking( $booking_title, $text_content, $date_time ) {
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
	echo wp_insert_post( $my_post );

}


function tm_add_new_booking_ajax() {
	$first_name   = trim( sanitize_text_field( $_POST['first_name'] ) );
	$last_name    = trim( sanitize_text_field( $_POST['last_name'] ) );
	$date_time    = trim( sanitize_text_field( $_POST['date'] ) ) . " " . trim( sanitize_text_field( $_POST['time'] ) ) ;
    $date_time    = date('Y-m-d H:i:s', strtotime($date_time));

	$text_content = trim( $_POST['text_content'] );

	$booking_title = $first_name . " " . $last_name;


	tm_add_new_booking( $booking_title, $text_content, $date_time );
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
                                                                <img class="mb-1"
                                                                     src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/home.png'; ?>"
                                                                     alt="property icon">
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
                                                                <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/home.png'; ?>"
                                                                     alt="carpets icon">
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
                                                                <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/home.png'; ?>"
                                                                     alt="carpets icon">
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
                                                                <img class="mb-1"
                                                                     src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/home.png'; ?>"
                                                                     alt="carpets icon">
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
                                                            style="color:#7ecdae;">
                                                        <div>
                                                            <h6 class="my-0">Promo code</h6>
                                                            <small>FIVECLEANOFF</small>
                                                        </div>
                                                        <span>-£5</span>
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
                                                                <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/time.png'; ?>"
                                                                     alt="date&time icon">
                                                                <span class="text-muted small stime"></span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <form class="card">
                                                    <div class="input-group redeem">
                                                        <input type="text" class="form-control"
                                                               placeholder="Try FIVECLEANOFF">
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
                                    <div class="col-lg-8 order-lg-1">
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
                                                <input type="checkbox" class="custom-control-input" id="key-pickup">
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
<!--                                            <input id="confirm-booking" class="btn btn-primary shadow btn-lg btn-block" type="submit" value="Confirm Booking">-->
                                            <input class="btn btn-primary shadow btn-lg btn-block" type="submit" value="Confirm Booking">

<!--                                            <div id="ovde">-->
<!--                                                tu-->
<!--                                            </div>-->
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
