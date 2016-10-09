/*
 * 
 * Template js
 */

$(document).ready(function () {
    $("#mapModal").on("shown.bs.modal", function () {
        var map = new GMaps({
            el: '#markermap',
            lat: 10.746078,
            lng: 106.666883

        });

        map.addMarker({
            lat: 10.746078,
            lng: 106.666883,
        });

        google.maps.event.trigger(map, "resize");
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue'
    });

    //Left nav scroll
    $(".nano").nanoScroller();

    // Left menu collapse
    $('.left-nav-toggle a').on('click', function (event) {
        event.preventDefault();
        $("body").toggleClass("nav-toggle");
    });
 // Left menu collapse
    $('.right-sidebar-toggle').on('click', function (event) {
        event.preventDefault();
        $("#right-sidebar-toggle").toggleClass("right-sidebar-toggle");
    });
//metis menu
    $("#menu").metisMenu();
    //slim scroll
    $('.scrollDiv').slimScroll({
        color: '#eee',
        size: '5px',
        height: '250px',
        alwaysVisible: false
    });
//tooltip popover
 $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover();
});
