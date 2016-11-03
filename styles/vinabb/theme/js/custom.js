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
		$('textarea[data-toggle="sceditor"]').sceditor({
			plugins: 'bbcode',
			locate: $sceditor_lang,
			enablePasteFiltering: true,
			toolbar: 'bold,italic,underline,strike,superscript,subscript|size,color,removeformat|left,center,right,justify|bulletlist,orderedlist,table|quote,code,emoticon|image,link,unlink|source,maximize',
			style: $t_theme_path + '/css/jquery.sceditor.min.css',
			emoticonsRoot: "./images/smilies/",
			emoticons: {
				dropdown: {
					":a": '1.png',
					":b": '2.png',
					":c": '3.png',
					":d": '4.png'
				}
			},
			emoticonsEnabled: true,
			colors: '#fff, #aaa, #555, #000,#16a085,#27ae60,#2980b9,#8e44ad,#2c3e50,#c0392b,#d35400,#f39c12'
		});

		// Show tooltip for buttons
		$('a[data-sceditor-command]').tooltip({
			placement: 'bottom'
		});

		// Show tooltip for emoticons
		$('a[data-sceditor-command="emoticon"]').on('click', function () {
			$('img[unselectable="on"]').tooltip({
				placement: 'bottom'
			});
		});

		$.sceditor.plugins.bbcode.bbcode
			.set("list", {
				html: function(element, attrs, content) {
					var type = (attrs.defaultattr === '1' ? 'ol' : 'ul');

					return '<' + type + '>' + content + '</' + type + '>';
				},
				breakAfter: false
			})

			.set("ul", { format: function($elm, content) { return '[list]' + content +'[/list]'; }})
			.set("ol", { format: function($elm, content) { return '[list=1]' + content +'[/list]'; }})
			.set("li", { format: function($elm, content) { return '[*]' + content; }})
			.set("li", { format: function($elm, content) { return '[*]' + content; }})
			.set("*", { excludeClosing: true, isInline: false });

		$.sceditor.command
			.set("bulletlist", { txtExec: ["[list]\n[*]", "\n[/list]"] })
			.set("orderedlist", { txtExec: ["[list=1]\n[*]", "\n[/list]"] });


		var sizes = ['65', '85', '100', '120', '150', '175', '200'];

		$.sceditor.plugins.bbcode.bbcode.set('size', {
			format: function ($elem, content) {
				var fontSize,
					sizesIdx = 0,
					size = $elem.data('scefontsize');

				if (!size) {
					fontSize = $elem.css('fontSize');

					// Most browsers return px value but IE returns 1-7
					if (fontSize.indexOf('px') > -1) {
						// convert size to an int
						fontSize = ~~(fontSize.replace('px', ''));

						if (fontSize > 31) {
							sizesIdx = 6;
						}
						else if (fontSize > 23) {
							sizesIdx = 5;
						}
						else if (fontSize > 17) {
							sizesIdx = 4;
						}
						else if (fontSize > 15) {
							sizesIdx = 3;
						}
						else if (fontSize > 12) {
							sizesIdx = 2;
						}
						else if (fontSize > 9) {
							sizesIdx = 1;
						}
					}
					else {
						sizesIdx = ~~fontSize;
					}

					if (sizesIdx > 6) {
						sizesIdx = 6;
					}
					else if (sizesIdx < 0) {
						sizesIdx = 0;
					}

					size = sizes[sizesIdx];
				}

				return '[size=' + size + ']' + content + '[/size]';
			},
			html: function (token, attrs, content) {
				return '<span data-scefontsize="' + attrs.defaultattr + '" style="font-size:' + attrs.defaultattr + '%">' + content + '</span>';
			}
		});

		$.sceditor.command.set('size', {
			_dropDown: function (editor, caller, callback) {
				var content = $('<div />'),
					clickFunc = function (e) {
						callback($(this).data('size'));
						editor.closeDropDown(true);
						e.preventDefault();
					},
					size;

				for (var i = 1; i < 7; i++) {
					// Only consider maxsize when set greater 0
					content.append($('<a class="sceditor-fontsize-option" data-size="' + i + '" href="#"><font size="' + i + '">' + i + '</font></a>').click(clickFunc));
				}

				editor.createDropDown(caller, 'fontsize-picker', content);
			},
			txtExec: function (caller) {
				var editor = this;

				$.sceditor.command.get('size')._dropDown(
					editor,
					caller,
					function (sizesIdx) {
						sizesIdx = ~~sizesIdx;
						if (sizesIdx > 6) {
							sizesIdx = 6;
						}
						else if (sizesIdx < 0) {
							sizesIdx = 0;
						}

						editor.insertText('[size=' + sizes[sizesIdx] + ']', '[/size]');
					}
				);
			}
		});

		$.sceditor.plugins.bbcode.bbcode.set('quote', {
			format: function (element, content) {
				var author = '',
					$element = $(element),
					$cite = $element.children('cite').first();

				if (1 === $cite.length || $element.data('author')) {
					author = $element.data('author') || $cite.text().replace(/(^\s+|\s+$)/g, '').replace(/:$/, '');

					$element.data('author', author);
					$cite.remove();

					content = this.elementToBbcode($element);
					author = '=' + author;

					$element.prepend($cite);
				}

				return '[quote' + author + ']' + content + '[/quote]';
			},
			html: function (token, attrs, content) {
				var addition = '';

				if ("undefined" !== typeof attrs.defaultattr) {
					content = '<cite>' + attrs.defaultattr + ':</cite>' + content;
					addition = ' data-author="' + attrs.defaultattr + '"';
				}
				else {
					addition = ' class="uncited"'
				}

				return '<blockquote' + addition + '>' + content + '</blockquote>';
			},
			quoteType: function (val, name) {
				return '"' + val.replace('"', '\\"') + '"';
			},
			breakStart: false,
			breakEnd: false
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
	$('[data-tooltip="true"]').tooltip();
	$('[data-popover="true"]').popover();
});
