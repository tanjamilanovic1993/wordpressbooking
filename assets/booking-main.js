var $ = jQuery.noConflict();

$( document ).ready(function() {

    $(".Cbuttons .carpets-img").tooltip('disable');

    $("#nav-contact-tab").on("click", function () {
        $("#nav-service .summContainer #summary").prependTo("#nav-contact .summContainer");
        $("#nav-contact #summary").removeClass('order-md-2');
        $("#nav-contact #summary").addClass('order-lg-1');
    });

    $("#nav-service-tab").on("click", function () {
        $("#nav-contact .summContainer #summary").appendTo("#nav-service .summContainer");
    });

    $("#nav-home-tab").addClass("fejl-active");

    $('[data-toggle="tooltip"]').tooltip();

    $("#studio-summary-content .property-img").tooltip('disable');

    clickCalendar();

    changeCalendar();

    timeslotSelection();

    checkIfChoosen();
});

//Ispisivanje kalendara na klik next, prev
function changeCalendar(){
$(".calendar-container li.change").on("click", function () {
    var monthNum = parseInt($(".calendar").attr('data-month'));
    var yearNum = parseInt($(".calendar").attr('data-year'));

    if($(this).hasClass('next')){
        monthNum++;
        if(monthNum >= 13){
            monthNum = 1;
            yearNum++;
        }
    }
    else if($(this).hasClass('prev')){
        monthNum--;
        if(monthNum <= 0){
            monthNum = 12;
            yearNum--;
        }
    }
    $.ajax({
        type: "POST",
        url: bookingObj.ajaxUrl,
        data: {action: 'tm_the_calendar_ajax', month: monthNum, year: yearNum},
        success: function (data) {
            $('.calendar-container').html(data);
            changeCalendar();
            clickCalendar();
            $(".calendar li.selected span").trigger("click");
        },
        error: function (data) {
            //alert('Error');
        },
    });
});
}

// Provera da li je izabra datum i vreme kako bi nastavili na T2
function checkIfChoosen() {
    var t = $(".timeslots-container li.selected").length;

    if(!t){
        $("#nav-service-tab").addClass('disabled');
        $("#next1").addClass('disabled');
    }
    else{
        $("#nav-service-tab").removeClass('disabled');
        $("#next1").removeClass('disabled');
    }
}


//Klik na kalendar ucitavanje timeslotsa - AJAX
function clickCalendar(){
    $(".calendar li.active span").on("click", function () {
        $(".calendar li").removeClass('selected');
        $(this).parent().addClass('selected');
        var dayNum = parseInt($(this).html());
        var monthNum = parseInt($(".calendar").attr('data-month'));
        var yearNum = parseInt($(".calendar").attr('data-year'));

        $.ajax({
            type: "POST",
            url: bookingObj.ajaxUrl,
            data: {action: 'tm_the_timeslots_ajax', day: dayNum, month: monthNum, year: yearNum},
            success: function (data) {
                $('.timeslots-container').html(data);
                timeslotSelection();
                checkIfChoosen();
            },
            error: function (data) {
                //alert('Error');
            },
        });

    });
}

//Klik na timeslots
function timeslotSelection(){
    var dayNum = parseInt($(".timeslot-header").attr('data-day'));
    var monthNum = parseInt($(".calendar").attr('data-month'));
    var yearNum = parseInt($(".calendar").attr('data-year'));

    $(".timeslots-container li").on("click", function () {
        $(".timeslots-container li").removeClass('selected');
        $(this).addClass('selected');

        checkIfChoosen();

        var date = dayNum + "/" + monthNum + "/" + yearNum;
        var time = $(this).html();
        $(".summary-content").each(function () {
            $(".stime", this).html(time);
            $(".sdate", this).html(date);
        });
    });
}




//Provera da li su na T2 zadovoljeni uslovi za sl. korak
function checkIfDisabled(){
  if($(".summary-active").length){
      var text = $(".summary-active .scarpets").text();

      if((text == "Steam Cleaned")){
        if( $(".c1").hasClass('d-flex') || $(".c2").hasClass('d-flex')){
            $("#nav-contact-tab").removeClass('disabled');
            $("#nav-service .btn-next").removeClass('disabled');
        }
        else{
            $("#nav-contact-tab").addClass('disabled');
            $("#nav-service .btn-next").addClass('disabled');
        }
      }

      if (text == "No Carpets" || text == "Hoovered"){
          $("#nav-contact-tab").removeClass('disabled');
          $("#nav-service .btn-next").removeClass('disabled');
      }
  }
};


$(".propertyb").on("click", function () {
    $(".property-buttons").slideUp();
    $(".summary-content").hide().removeClass('summary-active');

    var btnID = $(this).attr("id");
    btnID = btnID.substr(0, btnID.length - 1);
    if ($("#" + btnID + "-buttons").length) {
        if($("#" + btnID + "-buttons").is(':visible')){
            $("#" + btnID + "-buttons").slideUp();
        }
        else {
            $("#" + btnID + "-buttons").slideDown();
        }
    }
    else {
       var list = $("#" + btnID + '-summary-content .property .d-100');
       for(var i=0; i<list.length; i++){
           if(list.eq(i).hasClass('d-flex')){
               console.log("Jeste ima neki flex");
               list.eq(i).removeClass('d-flex').addClass('d-none');
           }
       }
        $("#" + btnID + '-summary-content .property .studio-property').removeClass('d-none').addClass('d-flex');
    }

    $("#" + btnID + '-summary-content').show().addClass('summary-active');

    checkIfDisabled();

    totalSum();
});


//Reedem kod check
$(".redeem button").on('click', function () {
    var val = $(this).parent().parent().find('.form-control').val();
    var sum = 0;
    $(".summary-content").each(function () {
        if (val === "FIVECLEANOFF") {
            $(".promo_li", this).removeClass("d-none").addClass("d-flex");
        }
        else {
            $(".promo_li", this).removeClass("d-flex").addClass("d-none");
        }
        totalSum();
    });
});


// Active mainb
$("#studiob, #flatb, #houseb, #noc, #hoov, #steamc").on("click", function () {
    var btnklik = $(this);
    btnklik.addClass('bactive');
    btnklik.siblings().removeClass('bactive');
    var t = btnklik.text().trim();

    if (t === "Studio" || t === "Flat" || t === "House") {
        if (t === "Flat" || t === "House") {
            $(".sproperty").parent().siblings().eq(0).removeClass("d-none").addClass("d-flex");
            $(".sproperty").parent().siblings().eq(1).removeClass("d-none").addClass("d-flex");
            $(".sproperty").parent().siblings().eq(2).removeClass("d-none").addClass("d-flex");

        }
        t = t + " Property";
        $(".sproperty").text(t);
    }

    else if (t === "No Carpets" || t === "Hoovered" || t === "Steam Cleaned") {

        if(t==="Hoovered"){
            $(".summary-content").each(function () {
                $(".hoovered-carpet", this).removeClass('d-none').addClass('d-flex');
                $(".no-carpet", this).removeClass('d-flex').addClass('d-none');
                $(".Cbuttons .carpets-img").tooltip('disable');
            });
        }
        else if(t==="No Carpets"){
            $(".summary-content").each(function () {
                $(".no-carpet", this).removeClass('d-none').addClass('d-flex');
                $(".hoovered-carpet", this).removeClass('d-flex').addClass('d-none');
                $(".Cbuttons .carpets-img").tooltip('disable');
            });
        }
        else{

            $("#nav-contact-tab").addClass('disabled');
            $("#nav-service .btn-next").addClass('disabled');

            $(".summary-content").each(function () {
                $(".hoovered-carpet", this).removeClass('d-flex').addClass('d-none');
                $(".no-carpet", this).removeClass('d-flex').addClass('d-none');
                $(".Cbuttons .carpets-img").tooltip('enable');
            });
        }
        console.log("Usao je u carpets");
        $(".scarpets").text(t);
        $("#Cbuttons .reset").trigger("click");

    }
    checkIfDisabled();
});


// Counter class
$(".counter").on("click", function () {
    var el = $(this).parent().find($('span'));
    var t = el.html();
    var sign = $(this).html().trim();
    var minVal = 0;
    var buttonsID = $(this).parent().parent().parent().parent().parent().parent().attr('id');
    var list = $("#" + buttonsID + ' .btn-group span');

    if (el.hasClass('min1')) {
        minVal = 1;
    }

    if (sign == "+") {
        t++;
    }
    else if (sign == "-") {
        if (t > minVal) {
            t--;
        }
        else {
            t = minVal;
        }

    }

    $(this).parent().find($('span')).html(t);                                        // Button updated after counter

    console.log(list.length);
    if(btngroupVisible(list) == list.length && $("." + buttonsID).hasClass('zero-sum-hidden')){
        console.log("Prosao je karjni if ovde se sklanja naslov");
        $("." + buttonsID).parent().removeClass('d-flex').addClass('d-none');

    }

    summaryCalc($(this), t, minVal);                                                 // Summary filled
    checkIfDisabled();
});


//Summary filled and calculation of items
function summaryCalc(counterEl, t, minVal) {
    var buttonsID = counterEl.parent().parent().parent().parent().parent().parent();
    var btnID = counterEl.parent().attr("class");
    btnID = btnID.substr(0, btnID.indexOf(" "));


    if (buttonsID.hasClass("property-buttons")) {
        var pname = counterEl.parent().parent().parent().parent().parent().parent().attr("id");
        pname = pname.substr(0, pname.indexOf('-'));
        pname = pname + "-summary-content";


        var itemPrice = $('#' + pname + ' .' + btnID + ' .price').attr("data-price") * t;
        $('#' + pname + ' .' + btnID + ' .price').html("£" + itemPrice);

        if (t == 0) {
            $('#' + pname + ' .' + btnID).removeClass("d-flex").addClass('d-none');             // If 0 not visible in summary
        }
        else {
            $('#' + pname + ' .' + btnID).removeClass("d-none").addClass('d-flex');
        }
        $('#' + pname + ' .' + btnID + ' span').html(t);
    }
    else {
        // Booking summary filled for all n summaries
        $(".summary-content").each(function () {
            itemPrice = $(' .' + btnID + ' .price', this).attr("data-price") * t;
            $(' .' + btnID + ' .price', this).html("£" + itemPrice);

            if (t == minVal) {
                $('.' + btnID, this).removeClass("d-flex").addClass('d-none');             // If 0 not visible in summary
            }
            else {
                $('.' + btnID, this).removeClass("d-none").addClass('d-flex');            // If >0 visible in summary

                if ($('.' + btnID, this).parent().hasClass('zero-sum-hidden')) {          // Checking for filters with default closed lists
                    $('.' + btnID, this).parent().parent().removeClass('d-none').addClass('d-flex'); //showing etc extra_li
                }
            }

            $('.' + btnID + ' span', this).html(t);
        });
    }

    totalSum();
}


//Total price in summary
function totalSum() {

    $(".summary-content").each(function () {
        var prices = $(".price", this);
        var price, sum = 0;

        for (var i = 0; i < prices.length; i++) {
            if (prices.eq(i).parent().hasClass('d-flex')) {
                price = prices.eq(i).html();
                price = price.slice(1);
                sum = sum + parseInt(price);
            }
        }
        if($(".promo_li", this).hasClass('d-flex')){
            sum = sum - 5;
        }
        $(".total-price", this).html("£" + sum);
    });
}


// Reset class
$(".reset").on("click", function () {
    var divID = $(this).parent().parent().attr("id");
    var btns = $("#" + divID + " .btn-group span");
    var minVal = 0;

    if($(this).parent().parent().hasClass('property-buttons')){
        console.log('Reset IZ propertija');

    }
    else{
        console.log('Reset izvan propertija');
    }
    console.log(divID);

    var pname = divID.substr(0, divID.indexOf('-'));
    pname = pname + "-summary-content";

    console.log(pname);

    var btnsSum = $('#' + pname + ' .property small span');
    var btnsSumPrice = $('#' + pname + ' .property .price');
    // var itemPrice;

    console.log(btnsSum.length + "Joj");
    console.log(btnsSumPrice.length + "Joj");

    for (var i = 0; i <= btns.length; i++) {
        if (btns.eq(i).hasClass('min1')) {
            minVal = 1;
        }
        else {
            minVal = 0;
            btnsSum.eq(i).parent().parent().removeClass('d-flex').addClass('d-none');
        }
        btns.eq(i).html(minVal);                                                        //Filter buttons value reset
        btnsSum.eq(i).html(minVal);                                                     //Summary filters counter reset
        itemPrice = btnsSumPrice.eq(i).attr("data-price");                              //Summary filter price reset
        btnsSumPrice.eq(i).html("£" + itemPrice);
    }


    $(".summary-content").each(function () {
        $('.' + divID + ' span', this).html(minVal);
        if ($("." + divID).hasClass('zero-sum-hidden')) {                                 // In case i need whole <li> to be hidden if all zero
            $('.' + divID).parent().removeClass('d-flex').addClass('d-none');
            $('.' + divID + ' span', this).parent().parent().removeClass('d-flex').addClass('d-none');
        }
        else {
            $('.' + divID + ' span', this).parent().parent().removeClass('d-flex').addClass('d-none');  // Just bedroom, bath etc hidden after reset
        }
    });

    totalSum();
    checkIfDisabled();
});

//Prikazuje ili sklanja deo summary liste
function btngroupVisible(list) {
    var df = 0;
    for (var i = 0; i < list.length; i++) {
        if (list.eq(i).html() == 0) {
            df++;
            console.log("Jeste jednako 0");
        }
    }
    return df;
}



//F-je onclick iz html-a
function CarpetsHide() {
    $("#Cbuttons").collapse('hide');
}

function StageCrumbs1() {
    $("#nav-service-tab").removeClass("fejl-active");
}

function StageCrumbs2() {
    $("#nav-home-tab").addClass("fejl-active");
}

function StageCrumbs3() {
    $("#nav-service-tab").addClass("fejl-active");
}

function OpenService2() {
    $("#nav-service-tab").trigger("click");
    $("#next1").tooltip('dispose');
    $("#next1").tooltip('enable');

}

function OpenService3() {
    $("#nav-contact-tab").trigger("click");
}

//F-je onclick se ovde zavrsavaju



//Submit forme i data za new booking - AJAX
$("#personal").submit(function(e) {
    e.preventDefault();

    var bName = $("#firstName").val();
    var bLastname = $("#lastName").val();
    var bemail = $("#email").val();
    var bphone = $("#phone").val();

    var propertyDetails = {
        address: $("#address").val(),
        houseno: $("#houseno").val(),
        postcode: $("#postcode").val(),
        city: $("#city").val()
    };
    var key;
    if($('#key-pickup').is(':checked')){
        key = 1;
        console.log('Usao checked')
    }
    else {
        key = 0;
        console.log('Nije chekirano')
    }

    var additionalDetails = {
        keyAddress: $("#key-address").val(),
        keyHouseno: $("#key-houseno").val(),
        keyPostcode: $("#key-postcode").val(),
        keyCity: $("#key-city").val(),
        instructions: $("#addcomment").val(),
    };


    var txt = "";
    var total = $(".summary-content.summary-active .total-price").text().trim();
    var property = $(".summary-content.summary-active .sproperty").text().trim();
    var date = $(".summary-content.summary-active .sdate").text().trim();
    var timeSlot = $(".summary-content.summary-active .stime").text().trim();
    var time = timeSlot.split(' - ')[0].trim();

    var bookingSummary = $(".summary-content.summary-active .d-100");

    txt += "<h3>" + property + "</h3>";
    txt += "<ul style=\"padding-left: 20px;\">";
    for (var i = 0; i < bookingSummary.length - 1; i++) {
        if (bookingSummary.eq(i).hasClass('d-flex')) {
            txt += "<li>";
            txt += bookingSummary.eq(i).text().replace(/\n/g, "");
            txt += "</li>";
        }
    }
    txt += "</ul>";
    txt += "<div style=\"display: flex;\">";
    txt += "<strong style=\"padding: 20px;\">Date: " + date + "</strong>";
    txt += "<strong style=\"padding: 20px;\">Time: " + timeSlot + "</strong>";
    txt += "<strong style=\"padding: 20px;\">Total sum: " + total + "</strong>";
    txt += "</div>";

    $.ajax({
        type: "POST",
        url: bookingObj.ajaxUrl,
        data: {action: 'tm_add_new_booking_ajax',
            first_name: bName, last_name: bLastname, email: bemail,
            text_content: txt, date: date.replace(/\//g, '.'), time: time, phone: bphone,
            property_details: propertyDetails, key: key, additional_details: additionalDetails},
        success: function (data) {
            $("#closeb").trigger("click");
            $("#success-email").html(bemail);
            $("#nav-contact .summContainer #summary").appendTo("#success-content");
            $("#success-content #summary").removeClass().addClass("col-lg-12");
            $("#success-email").html(bemail);
            $(".redeem").addClass('d-none');

            // $("#success-modal").trigger("click");

            $("#success").modal('show');
            $('#success').on('hidden.bs.modal', function (e) {
                location.reload();
            });
        },
        error: function (data) {
            alert('Something went wrong in the booking proccess, please try again or call +555 555.');
        },
    });
});
