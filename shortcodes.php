<?php function tm_booking_content($atts) {
	$a = shortcode_atts( array(
		'foo' => 'Book Now'
	), $atts );
    ?>


    <button id="bookb" class="btn booking-button" type="button" data-toggle="modal" data-target="#bookingModal"><?php echo $a['foo'];?></button>


    <div class="modal fade" id="success" tabindex="-1" role="dialog" style="overflow-y: auto;"  aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="success-content" class="modal-content">

                    <div id ="success-msg" class="alert alert-success" role="alert">
                       <p>Thank you for booking with us. </p>
                       <p>You will soon recive an email confirmation to this address: <span id="success-email"></span> </p>
                    </div>

            </div>
        </div>
    </div>

<?php }
