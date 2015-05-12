jQuery(document).ready(function($) {
    //setup active elements for each layout in order to activate (show) them on click
    $("#full").data('active_elements', ['.main']);
    $("#right").data('active_elements', ['.main', '.side']);
    $("#left").data('active_elements', ['.main', '.side']);
    $("#bottom_right").data('active_elements', ['.main', '.side', '.bottom']);
    $("#bottom_left").data('active_elements', ['.main', '.side', '.bottom']);

    $( document ).tooltip({
        track: true
    });


    //show initial elements of a layout selected
    $(".block-container").hide("fast");
    if ($('.layout-box.active').length > 0)
    {
        $.each($('.layout-box.active').data('active_elements'), function (index, value) {
            $(value).show("fast");
        });
    }


    //clear block ( remove widget from it )
    $(".clear-block").click(function() {
        $(this).parent().find('.widget').remove();
        $(this).parent().find('input:not(input[name="position[]"])').val('0');
    });


    //change layout UI logic
    $('.layout-box').click(function() {
        $('.layout-box').removeClass('active');
        $(this).addClass('active');
        $('.block-container').hide("fast");
        $.each($(this).data('active_elements'), function(index, value) {
            $(value).show("fast");
        });
        $("input#dd_layout").val($(this).data('id'));
    });

    $( ".widget" ).draggable({
        revert:true,
        revertDuration: 0,
        helper: 'clone'
    });

    $( ".block" ).droppable({
        activeClass: "active",
        drop: function( event, ui ) {
            var dropped = ui.draggable;
            var droppedOn = $(this);
            if (droppedOn.find('.widget').length == 0) {
                droppedOn.find('input[name="type[]"]').val($(dropped).data('type'));
                droppedOn.find('input[name="name[]"]').val($(dropped).data('name'));
                droppedOn.find('input[name="id[]"]').val($(dropped).data('id'));
                //$(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);

                $droppedClone = $(dropped).clone();
                $droppedClone.appendTo(droppedOn);
                $name = $droppedClone.find('.block-name').html();
                $droppedClone.parent().parent().attr('title', $name);

                if($name.length > 7) {
                    $droppedClone.find('.block-name').html($name.substring(0,4)+'...');
                }
            }


        }});
/*
    $( ".social-widgets" ).droppable({
        activeClass: "active",
        accept: ".social",
        drop: function( event, ui ) {
            var dropped = ui.draggable;
            var droppedOn = $(this);
            $(dropped).parent().find('input:not(input[name="position[]"])').val('0');
            $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);
        }});
    $( ".weather-widgets" ).droppable({
        activeClass: "active",
        accept: ".weather",
        drop: function( event, ui ) {
            var dropped = ui.draggable;
            var droppedOn = $(this);
            $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);
        }});
    $( ".clock-widgets" ).droppable({
        activeClass: "active",
        accept: ".clock",
        drop: function( event, ui ) {
            var dropped = ui.draggable;
            var droppedOn = $(this);
            $(dropped).detach().css({top: 0,left: 0}).appendTo(droppedOn);
        }});*/
});