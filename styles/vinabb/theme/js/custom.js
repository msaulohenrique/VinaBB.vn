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

	// Highlight.js
	if ($load_highlight)
	{
		$('pre code').each(
			function(i, block)
			{
				hljs.highlightBlock(block);
			}
		);
	}

	// SCEditor
	if ($load_sceditor)
	{
		$('textarea').sceditor({
			plugins: 'bbcode',
			enablePasteFiltering: true,
			toolbar: 'bold,italic,underline,strike,superscript,subscript|size,color,removeformat|left,center,right,justify|bulletlist,orderedlist,table|quote,code,emoticon|image,link,unlink|source,maximize',
			style: './styles/vinabb/theme/css/jquery.sceditor.min.css',
			emoticonsRoot: "",
			emoticons: {
				dropdown: {
					":)": './images/smilies/1.png',
					":(": './images/smilies/1.png',
					":))": './images/smilies/1.png',
					":((": './images/smilies/1.png',
					":p": './images/smilies/1.png',
					":d": './images/smilies/1.png',
					":f": './images/smilies/1.png',
					":g": './images/smilies/1.png',
					":d": './images/smilies/1.png',
					":r": './images/smilies/1.png',
					":e": './images/smilies/1.png',
				}
			},
			emoticonsEnabled: true,
			colors: '#fff, #aaa, #555, #000,#16a085,#27ae60,#2980b9,#8e44ad,#2c3e50,#c0392b,#d35400,#f39c12'
		});

		$('a[data-sceditor-command]').attr('data-toggle', 'tooltip').attr('data-placement', 'bottom');
	}

	// iCheck
	$('input').iCheck({
		checkboxClass: 'icheckbox_flat-blue',
		radioClass: 'iradio_flat-blue'
	});

	// Left nav scroll
	$('.nano').nanoScroller();

	// Left menu collapse
	$('.left-nav-toggle a').on('click', function (event) {
		event.preventDefault();
		$('body').toggleClass('nav-toggle');
	});

	// Right panel collapse
	$('.right-sidebar-toggle').on('click', function (event) {
		event.preventDefault();
		$('#right-sidebar-toggle').toggleClass('right-sidebar-toggle');
		$('.right-sidebar-toggle i').toggleClass('fa-chevron-circle-right');
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
