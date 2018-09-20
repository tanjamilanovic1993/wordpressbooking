var $ = jQuery.noConflict();
$( document ).ready(function() {
    $("#nav-contact-tab").on("click", function () {
        $("#nav-service .summContainer #summary").appendTo("#nav-contact .summContainer");
    });

    $("#nav-service-tab").on("click", function () {
        $("#nav-contact .summContainer #summary").appendTo("#nav-service .summContainer");
    });

    $("#nav-home-tab").addClass("fejl-active");

    $('[data-toggle="tooltip"]').tooltip();


    clickCalendar();

    changeCalendar();

    timeslotSelection();

    checkIfChoosen();
});


$("#personal").submit(function(e) {
    e.preventDefault();

    var bName = $("#firstName").val();
    var bLastname = $("#lastName").val();

    var txt = "";
    var total = $(".summary-content.summary-active .total-price").text().trim();
    var property = $(".summary-content.summary-active .sproperty").text().trim();
    var date = $(".summary-content.summary-active .sdate").text().trim();
    var timeSlot = $(".summary-content.summary-active .stime").text().trim();
    var time = timeSlot.split(' - ')[0].trim();

    var bookingSummary = $(".summary-content.summary-active .d-100");

    txt += "<h3>" + property + "</h3>";
    txt += "<ul>";
    for (var i = 0; i < bookingSummary.length - 1; i++) {
        if (bookingSummary.eq(i).hasClass('d-flex')) {
            txt += "<li>";
            txt += bookingSummary.eq(i).text().replace(/\n/g, "");
            txt += "</li>";
        }
    }
    txt += "</ul>";
    txt += "<p style=\"text-align: left; padding-left: 23px;\"><strong>Date: " + date + "</strong></p>\n";
    txt += "<p style=\"text-align: left; padding-left: 23px;\"><strong>Time: " + timeSlot + "</strong></p>\n";
    txt += "<p style=\"text-align: left; padding-left: 23px;\"><strong>Total sum: " + total + "</strong></p>";

    $.ajax({
        type: "POST",
        url: bookingObj.ajaxUrl,
        data: {action: 'tm_add_new_booking_ajax', first_name: bName, last_name: bLastname, text_content: txt, date: date.replace(/\//g, '.'), time: time},
        success: function (data) {
            alert('Success!');
            //$('#ovde').html(data);
        },
        error: function (data) {
            //alert('Error');
        },
    });
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

function checkIfChoosen() {
    var t = $(".timeslots-container li.selected").length;

    console.log(t);

    if(!t){
        $("#nav-service-tab").addClass('disabled');
        $("#next1").addClass('disabled');
    }
    else{
        $("#nav-service-tab").removeClass('disabled');
        $("#next1").removeClass('disabled');
    }
}

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


function timeslotSelection(){
    var dayNum = parseInt($(".timeslot-header").attr('data-day'));
    var monthNum = parseInt($(".calendar").attr('data-month'));
    var yearNum = parseInt($(".calendar").attr('data-year'));

    $(".timeslots-container li").on("click", function () {
        $(".timeslots-container li").removeClass('selected');
        $(this).addClass('selected');

        checkIfChoosen();

        //if (dayNum == $(".calendar .current span").text()) {
        //    $("li.current").addClass('selected');
        //}

        var date = dayNum + "/" + monthNum + "/" + yearNum;
        var time = $(this).html();
        $(".summary-content").each(function () {
            $(".stime", this).html(time);
            $(".sdate", this).html(date);
        });
    });
}




$(".propertyb").on("click", function () {
    // $(".reset").trigger( "click" );
    $(".property-buttons").slideUp();
    $(".summary-content").hide().removeClass('summary-active');

    var btnID = $(this).attr("id");
    btnID = btnID.substr(0, btnID.length - 1);
    if ($("#" + btnID + "-buttons").length) {
        $("#" + btnID + "-buttons").slideDown();
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

    $("#nav-contact-tab").removeClass('disabled');
    $("#nav-service .btn-next").removeClass('disabled');
    totalSum();
});


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


$(".redeem button").on('click', function () {
    var val = $(this).parent().parent().find('.form-control').val();
    $(".summary-content").each(function () {
        if (val === "FIVECLEANOFF") {
            $(".promo_li", this).removeClass("d-none").addClass("d-flex");
        }
        else {
            $(".promo_li", this).removeClass("d-flex").addClass("d-none");
        }
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
            });
        }
        else if(t==="No Carpets"){
            $(".summary-content").each(function () {
                $(".no-carpet", this).removeClass('d-none').addClass('d-flex');
                $(".hoovered-carpet", this).removeClass('d-flex').addClass('d-none');
            });
        }
        else{

            $(".summary-content").each(function () {
                $(".hoovered-carpet", this).removeClass('d-flex').addClass('d-none');
                $(".no-carpet", this).removeClass('d-flex').addClass('d-none');
            });
        }
        console.log("Usao je u carpets");
        $(".scarpets").text(t);
        $("#Cbuttons .reset").trigger("click");

    }
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
        $(".total-price", this).html("£" + sum);
    });
}


// New Reset function
$(".reset").on("click", function () {
    var divID = $(this).parent().parent().attr("id");
    var btns = $("#" + divID + " .btn-group span");
    var minVal = 0;

    console.log(divID);

    var pname = divID.substr(0, divID.indexOf('-'));
    pname = pname + "-summary-content";

    console.log(pname);

    var btnsSum = $('#' + pname + ' small span');
    var btnsSumPrice = $('#' + pname + ' .property .price');
    // var itemPrice;

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
});


//Reset subButtons to default values
// $(".reset").on("click", function () {
//     var btngroup = $(this).parent().parent();
//     var id = btngroup.attr("id");
//     var x = $("#" + id + " .btn-group");
//     var i;
//
//     for (i = 0; i <= x.length; i++) {
//
//         var text = x.eq(i).children().eq(1).text();
//         var prop = text.substring(text.indexOf(' ') + 1);
//         if (prop === "Bedroom" || prop === "Bathroom" || prop === "Floor") {
//             prop = "1 " + prop;
//         }
//         else {
//             prop = "0 " + prop;
//         }
//         x.eq(i).children().eq(1).text(prop);        // reseting the value of the button to defaults
//         x.eq(i).children().eq(0).trigger("click");// trigering click so booking summary is updated indirectly(trig. on sub(-)button)
//     }
//
//     console.log("U resetu smo a id je " + id);
// });


// $("#Ubuttons .btn-group").on("click", function () {
//     showBtngroup($(this));
// });
//
// $("#Ebuttons .btn-group").on("click", function () {
//     showBtngroup($(this));
// });


function showBtngroup(obj) {
    btnID = obj.attr("id");
    if (btnID[0] === "u") {
        $(".summary-content").each(function () {
            $("#upholstery_li").removeClass('d-none').addClass('d-flex');
            console.log("Usao u each petlju za uphol.LI");
        });
    }
    else if (btnID[0] === "e") {
        $(".summary-content").each(function () {
            $("#extra_li").removeClass('d-none').addClass('d-flex');
            console.log("Usao u each petlju za extra.LI");
        });
    }

    var btn;
    var sumlist;
    var df;
    var list;
    if (btnID[0] === "u") {
        list = $(".supholstery").parent().siblings();
        sumlist = $("#upholstery_li");
        df = btngroupVisible(list);
        btn = $("#upholsteryb");
    }
    else if (btnID[0] === "e") {
        list = $(".sextra").parent().siblings();
        sumlist = $("#extra_li");
        df = btngroupVisible(list);
        btn = $("#extrab");
    }

    if (df != 0) {
        sumlist.removeClass("d-none").addClass("d-flex");
        btn.addClass("bactive");
    }
    else if (df == 0) {
        sumlist.removeClass("d-flex").addClass("d-none");
        btn.removeClass("bactive");
    }
    console.log("Uradjena f-ja df = " + df);
}


$("#closeSum").on("click", function () {
    $("#summary").hide();
});

$("#openSum").on("click", function () {
    $("#summary").show();
});


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
