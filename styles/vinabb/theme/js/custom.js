$(document).ready(function () {
	// Google Map on modal
	$('#mapModal').on('shown.bs.modal', function () {
		var map = new GMaps({
			el: '#markermap',
			lat: $map_lat,
			lng: $map_lng
		});

		map.addMarker({
			lat: $map_lat,
			lng: $map_lng
		});

		google.maps.event.trigger(map, 'resize');
	});

	// iCheck
	$('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue'
	});

	// Left nav scroll
	$('.nano').nanoScroller();

	// Left menu collapse
	$('.left-nav-toggle a').on('click', function (event) {
		event.preventDefault();
		$('body').toggleClass('nav-toggle');
	});

	// Left menu collapse
	$('.right-sidebar-toggle').on('click', function (event) {
		event.preventDefault();
		$('#right-sidebar-toggle').toggleClass('right-sidebar-toggle');
	});

	// Menu
	$('#menu').metisMenu();

	// Slim scroll
	$('.scrollDiv').slimScroll({
		color: '#eee',
		size: '5px',
		height: '250px',
		alwaysVisible: false
	});

	// Tooltip and popover
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
});
