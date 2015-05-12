

jQuery(document).ready(function($) {
    Globalize.culture( 'de-DE');
    jQuery.fn.extend({
        setupDurationControl: function() {
            setupTarget = $(this);
            $( ".from", setupTarget ).datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'y-mm-dd',
                onClose: function( selectedDate ) {
                    $( ".to", setupTarget ).datepicker( "option", "minDate", selectedDate );
                }
            });
            $( ".to", setupTarget ).datepicker({
                defaultDate: "+1w",
                dateFormat: 'y-mm-dd',
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $( ".from", setupTarget ).datepicker( "option", "maxDate", selectedDate );
                }
            });

            $('.seconds', setupTarget).spinner({
                spin: function (event, ui) {
                    if (ui.value >= 60) {
                        $(this).spinner('value', ui.value - 60);
                        $('.minutes', setupTarget).spinner('stepUp');
                        return false;
                    } else if (ui.value < 0) {
                        $(this).spinner('value', ui.value + 60);
                        $('.minutes', setupTarget).spinner('stepDown');
                        return false;
                    }
                }
            });
            $('.minutes', $(this)).spinner({
                spin: function (event, ui) {
                    if (ui.value >= 60) {
                        $(this).spinner('value', ui.value - 60);
                        $('.hours', setupTarget).spinner('stepUp');
                        return false;
                    } else if (ui.value < 0) {
                        $(this).spinner('value', ui.value + 60);
                        $('.hours', setupTarget).spinner('stepDown');
                        return false;
                    }
                }
            });
            $('.hours', setupTarget).spinner({
                min: 0
            });
            $(".spinner-from", setupTarget).timespinner();
            $(".spinner-to", setupTarget).timespinner();

            $('.duration input', setupTarget).on('click', function (e) {
                $('.duration-picker', $(this).parent().parent()).hide('fast').removeClass('active');
                $(this).parent().parent().find('#' + $(this).data('target-id')).show('fast').addClass('active');
                $(this).parent().parent().find('#duration-type').val($(this).val());
            });
        }
    });
    $.widget( "ui.timespinner", jQuery.ui.spinner, {
        options: {
            // seconds
            step: 60 * 1000,
            // hours
            page: 60
        },

        _parse: function( value ) {
            if ( typeof value === "string" ) {
                // already a timestamp
                if ( Number( value ) == value ) {
                    return Number( value );
                }
                return +Globalize.parseDate( value );
            }
            return value;
        },

        _format: function( value ) {
            return Globalize.format( new Date(value), "t" );
        }
    });

    $('.slides-available .slide:not(.add)').draggable({
        revert:true,
        revertDuration: 0,
        helper: 'clone'
    });

    $( ".slides-display" ).droppable({
        activeClass: "active",
        drop: function( event, ui ) {
            var dropped = $(ui.draggable).clone(false);
            var droppedOn = $(this);

            dropped.appendTo(droppedOn.find("ul")).find('input.active').val("1");
            dropped.setupDurationControl();
            dropped.find('.slide-control-b').click();

        },
        accept: '.slides-available .slide'
    });

    $('.slides-display ul').sortable({
        revert: true,
        revertDuration: 0
    });

    $( "ul, li" ).disableSelection();

    $( '.slides-display' ).on( "click", ".remove-slide", function() {
        $(this).parent().parent().remove();
    });

    $( '.slides-display' ).on( "click", "button.slide-control-b", function() {
        $(this).parent().find('.slide-control').slideToggle();
        if(!$(this).hasClass('open')) {
            $(this).find('span').removeClass('glyphicon-time').addClass('glyphicon-minus');
            $(this).addClass('open');
        } else {
            $(this).removeClass("open").find('span').removeClass('glyphicon-minus').addClass('glyphicon-time');
        }
    });

    $("#search-slides").on('input paste', function() {
        $(".slides-available li").show();
        $(".slides-available li").each(function() {
            if($(this).find("p.name").html().toLowerCase().indexOf($('#search-slides').val().toLowerCase()) == -1 &&
                $(this).find("input.id").val().toLowerCase().indexOf($('#search-slides').val().toLowerCase()) == -1
            ) {
                $(this).hide();
            }
        });
    });

    $('.slides-display').setupDurationControl();

});


