(function($) {

    $.belowthefold = function(element, settings) {
        var fold = $(window).height() + $(window).scrollTop();
        return fold <= $(element).offset().top - settings.threshold;
    };

    $.abovethetop = function(element, settings) {
        var top = $(window).scrollTop();
        return top >= $(element).offset().top + $(element).height() - settings.threshold;
    };

    $.rightofscreen = function(element, settings) {
        var fold = $(window).width() + $(window).scrollLeft();
        return fold <= $(element).offset().left - settings.threshold;
    };

    $.leftofscreen = function(element, settings) {
        var left = $(window).scrollLeft();
        return left >= $(element).offset().left + $(element).width() - settings.threshold;
    };

    $.inviewport = function(element, settings) {
        return !$.rightofscreen(element, settings) && !$.leftofscreen(element, settings) && !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
    };

    $.extend($.expr[':'], {
        "below-the-fold": function(a, i, m) {
            return $.belowthefold(a, {threshold : 0});
        },
        "above-the-top": function(a, i, m) {
            return $.abovethetop(a, {threshold : 0});
        },
        "left-of-screen": function(a, i, m) {
            return $.leftofscreen(a, {threshold : 0});
        },
        "right-of-screen": function(a, i, m) {
            return $.rightofscreen(a, {threshold : 0});
        },
        "in-viewport": function(a, i, m) {
            return $.inviewport(a, {threshold : 0});
        }
    });


})(jQuery);
var noChanges = true;
function resizeSlideElements() {
    $ = jQuery.noConflict();
    $('.slide, #active-slide').css('height',$(window).height());

    $('.main .weather-extended:visible, .main .clock, .side .clock, .side .text, .main .text', '#active-slide').each(function() {
        if ($(this).parent().parent().parent().find('.bottom').length == 0) { height = 100.0; } else { height = 70.0; }
        $(this).css('margin-top', ($(window).height()/100.0*height-$(this).height())/2);
    });
    $('.bottom', '#active-slide').each(function() {
        $(this).css('margin-top', ($(window).height()/100.0*30.0-$(this).height())/2);
    });
}

//resize elements when loaded and do so on every window resize
jQuery(document).ready(function($) {
    //resizeSlideElements();
    //$( window ).resize(function() { resizeSlideElements(); });
});

//setup clock and data
jQuery(document).ready(function($) {
    // Create two variable with the names of the months and days in an array
    var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
    var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]
    var newDate = new Date();


    setInterval( function() {
        // Create a newDate() object and extract the seconds of the current time on the visitor's
        var seconds = new Date().getSeconds();
        // Add a leading zero to seconds value
        $(".sec").html(( seconds < 10 ? "0" : "" ) + seconds);
        newDate = new Date();
        newDate.setDate(newDate.getDate());
        $('.Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()].substring(0, 3) + ' ' + newDate.getFullYear());

    },1000);

    setInterval( function() {
        // Create a newDate() object and extract the minutes of the current time on the visitor's
        var minutes = new Date().getMinutes();
        // Add a leading zero to the minutes value
        $(".min").html(( minutes < 10 ? "0" : "" ) + minutes);
    },1000);

    setInterval( function() {
        // Create a newDate() object and extract the hours of the current time on the visitor's
        var hours = new Date().getHours();
        // Add a leading zero to the hours value
        $(".hours").html(( hours < 10 ? "0" : "" ) + hours);

    }, 1000);
});

//fetch the display if modified and load it to a #loader div for later use
function refreshSlide() {

    if (!noChanges) {
        jQuery('#display .slide').addClass('old-slide');
        jQuery('#display').append(jQuery("#loader").find('.slide'));
        jQuery('#display .slide.old-slide').remove();
        noChanges = true;
    }


    jQuery.ajax({
        type: "POST",
        url: jQuery('#display').data('permalink'),
        data: { name: $("#display").data('display-started') }
    })
        .done(function( msg ) {
            try {
                msg = jQuery.parseJSON(msg);
            }

            catch (err)
            {;}
            if (msg.status == 'not_modified') {

                return;
            }
            noChanges = false;

            jQuery('#loader').html('');
            jQuery('#loader').append(jQuery(msg).find('.slide'));

        });
}

var feedRefresh = 0;

function changeSlides($slide) {
    feedRefresh++;
    $ = jQuery.noConflict();
    $active = $('#active-slide .slide');

    if ($active.data('layout-id') == $slide.data('layout-id') &&
        $active.data('slide-id') == $slide.data('slide-id') &&
        feedRefresh > 20 && false
    )
    {
        feedRefresh = 0;
        $('.main.block, .bottom.block', $active).contents().addClass('old-blocks');
        $('.main.block', $active).prepend($('.main.block', $slide).contents());
        $('.bottom.block', $active).prepend($('.bottom.block', $slide).contents());
        $('.old-blocks', $active).remove();
    } else {
        $active = $('#active-slide .slide');
        $('#active-slide .slide').addClass('old-active-slide');
        $slide.clone(true).appendTo($('#active-slide'));
        $('#active-slide .slide.old-active-slide').remove();
    }

}

jQuery(document).ready(function($) {
    var running = -1;
    var nextCallAmount = 0;
    var updateRunning = false;
    var k = 0;


    setTimeout(function() {
        window.setInterval(
            function() {
                k++;
                if (k % 10 == 0) { refreshSlide(); }
                $('.main #wpc-weather:visible, .main .clock, .side .clock').each(function() {
                    //$(this).css('margin-top', ($(window).height()/100*70-$(this).height())/2);
                });

                var timeTmp = new Date();
                var now = 1293840000000 +
                    timeTmp.getHours() * 1000 * 60 * 60 +
                    timeTmp.getMinutes() * 60 * 1000;

                if (false) {
                    return;
                } else {
                    $slides = $('#display .slide');
                    for (var i = 0; i < $slides.length; i++) {
                        $slide = $($slides[i]);
                        if ('exact' == $slide.data('duration-type')) {
                            if ($slide.data('duration-from') * 1000 - 3600000  <= Date.now() &&
                                $slide.data('duration-from') * 1000  + $slide.data('duration-amount')*1000 - 3600000 > Date.now() ) {

                                if (running == $slide.data('position') && noChanges) { return; }

                                $active = $('#active-slide .slide');
                                $active.addClass('old-active-slide');
                                $slide.clone(true).appendTo($('#active-slide'));

                                $('#active-slide .slide.old-active-slide').remove();

                                resizeSlideElements();

                                running = $slide.data('position');
                                return;
                            }
                        }
                    }
                    for (i = 0; i < $slides.length; i++) {
                        $slide = $($slides[i]);
                        if ('everyday' == $slide.data('duration-type')) {
                            var time = new Date();
                            if ($slide.data('duration-from') * 1000  <= 1293840000000 + time.getHours()*1000*60*60 + time.getMinutes()*60*1000  &&
                                $slide.data('duration-from') * 1000  +
                                $slide.data('duration-amount')*1000 > 1293840000000 +
                                time.getHours()*1000*60*60 +
                                time.getMinutes()*60*1000 && running != $slide.data('position') )
                            {

                                if (running == $slide.data('position') && noChanges) { return; }

                                $active = $('#active-slide .slide');
                                $active.addClass('old-active-slide');
                                $slide.clone(true).appendTo($('#active-slide'));

                                $('#active-slide .slide.old-active-slide').remove();

                                resizeSlideElements();

                                running = $slide.data('position');
                                return;
                            }
                        }
                    }
                    for (i = 0; i < $slides.length; i++) {
                        $slide = $($slides[i]);
                        if ('amount' == $slide.data('duration-type')) {
                            if (Date.now() < nextCallAmount) { return; }

                            if (running >= $slide.data('position')
                                && $slides.length > 1) { continue; }

                            $active = $('#active-slide .slide');
                            $active.addClass('old-active-slide');
                            $slide.clone(true).appendTo($('#active-slide'));

                            $('#active-slide .slide.old-active-slide').remove();

                            resizeSlideElements();

                            nextCallAmount = Date.now() + $slide.data('duration-amount') * 1000;
                            runningType = 'amount';
                            running = $slide.data('position');
                            return;

                        }

                    }
                    running = -1;
                }

            }, 500);

    }, 2000);
});
