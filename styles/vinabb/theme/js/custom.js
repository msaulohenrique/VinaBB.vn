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

	// Summernote
	if ($load_summernote)
	{
		// Button: Align Left
		var alignLeft = function (context) {
			var ui = $.summernote.ui;
			var lang = $.summernote.lang[$summernote_lang].paragraph.left;

			var button = ui.button({
				contents: '<i class="fa fa-align-left"></i>',
				tooltip: lang,
				click: function () {
					context.invoke('editor.justifyLeft');
				}
			});

			return button.render();
		};

		// Button: Align Center
		var alignCenter = function (context) {
			var ui = $.summernote.ui;
			var lang = $.summernote.lang[$summernote_lang].paragraph.center;

			var button = ui.button({
				contents: '<i class="fa fa-align-center"></i>',
				tooltip: lang,
				click: function () {
					context.invoke('editor.justifyCenter');
				}
			});

			return button.render();
		};

		// Button: Align Right
		var alignRight = function (context) {
			var ui = $.summernote.ui;
			var lang = $.summernote.lang[$summernote_lang].paragraph.right;

			var button = ui.button({
				contents: '<i class="fa fa-align-right"></i>',
				tooltip: lang,
				click: function () {
					context.invoke('editor.justifyRight');
				}
			});

			return button.render();
		};

		// Button: Align Justify
		var alignJustify = function (context) {
			var ui = $.summernote.ui;
			var lang = $.summernote.lang[$summernote_lang].paragraph.justify;

			var button = ui.button({
				contents: '<i class="fa fa-align-justify"></i>',
				tooltip: lang,
				click: function () {
					context.invoke('editor.justifyFull');
				}
			});

			return button.render();
		};

		// Button: Quote
		var BlockQuote = function (context) {
			var ui = $.summernote.ui;
			var lang = $.summernote.lang[$summernote_lang].style.blockquote;

			var button = ui.button({
				contents: '<i class="fa fa-quote-left"></i>',
				tooltip: lang,
				click: function () {
					context.invoke('editor.formatBlock', 'Blockquote');
				}
			});

			return button.render();
		};

		// Button: Code
		var BlockCode = function (context) {
			var ui = $.summernote.ui;
			var lang = $.summernote.lang[$summernote_lang].style.pre;

			var button = ui.button({
				contents: '<i class="fa fa-terminal"></i>',
				tooltip: lang,
				click: function () {
					context.invoke('editor.formatBlock', 'Pre');
				}
			});

			return button.render();
		};

		// Let's run!
		$('#summernote').summernote({
			height: 300,
			lang: $summernote_lang,
			toolbar: [
				['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript']],
				['style', ['fontsize', 'color', 'clear']],
				['align', ['left', 'center', 'right', 'justify']],
				['list', ['quote', 'code', 'ul', 'ol']],
				['insert', ['link', 'picture', 'table']],
				['misc', ['fullscreen', 'codeview']]
			],
			buttons: {
				left: alignLeft,
				center: alignCenter,
				right: alignRight,
				justify: alignJustify,
				quote: BlockQuote,
				code: BlockCode
			}
		});
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
