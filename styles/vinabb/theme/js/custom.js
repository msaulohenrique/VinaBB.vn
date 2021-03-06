/* global GMaps */
/* global google */
/* global $t_theme_path */
/* global $t_smilies_path */
/* global $map_lat */
/* global $map_lng */
/* global $load_highlight */
/* global Clipboard */
/* global navigator */
/* global $l_copy_explain */
/* global $l_copy_success */
/* global $l_copy_error */
/* global $l_copy_error_mac */
/* global $load_sceditor */
/* global $sceditor_lang */
/* global $sceditor_smilies */
/* global $sceditor_hidden_smilies */
/* global $sceditor_smilies_desc */
/* global vex */
/* global $l_yes */
/* global $l_no */
/* global $l_confirm_logout */

$(document).ready(function()
{
	// Google Map on modal
	$('#mapModal').on('shown.bs.modal',
		function()
		{
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
		}
	);

	// Highlight.js
	if ($load_highlight)
	{
		$('pre code').each(
			function(i, block)
			{
				hljs.highlightBlock(block);
			}
		);

		// Clipboard.js
		var clipboard = new Clipboard('.btn-clipboard',
		{
			target: function(trigger)
			{
				return trigger.parentNode.nextElementSibling;
			}
		});

		clipboard.on('success', function(event)
		{
			$(event.trigger)
				.attr('data-original-title', $l_copy_success)
				.tooltip('fixTitle')
				.tooltip('show')
				.attr('data-original-title', $l_copy_explain)
				.tooltip('fixTitle');

			event.clearSelection();
		});

		clipboard.on('error', function(event)
		{
			var errorMsg = /Mac/i.test(navigator.userAgent) ? $l_copy_error_mac : $l_copy_error;

			$(event.trigger)
				.attr('data-original-title', errorMsg)
				.tooltip('fixTitle')
				.tooltip('show')
				.attr('data-original-title', $l_copy_explain)
				.tooltip('fixTitle');
		});
	}

	// SCEditor
	if ($load_sceditor)
	{
		$('textarea[data-toggle="wysiwyg-editor"]').sceditor({
			plugins: 'bbcode',
			locate: $sceditor_lang,
			enablePasteFiltering: true,
			toolbar: 'bold,italic,underline,strike,superscript,subscript|size,color,removeformat|left,center,right,justify|bulletlist,orderedlist,table|quote,code,emoticon|image,link,unlink|source,maximize',
			style: $t_theme_path + '/css/jquery.sceditor.min.css',
			emoticonsRoot: $t_smilies_path,
			emoticons: {
				dropdown: $sceditor_smilies,
				hidden: $sceditor_hidden_smilies
			},
			emoticonsCompat: true,
			colors: '#fff, #afeeee, #56afd7, #b9f9b2, #fff68f, #fad7a0, #ffb6c1, #d7bde2,' +
					'#aaa, #00ffff, #03a9f4, #48f548, #fdfd35, #ff7043, #f96ef9, #a569bd,' +
					'#555, #00cde1, #047094, #9acd32, #f9ea3e, #b38b63, #f44336, #6c3483,' +
					'#000, #088da5, #1e48e0, #4caf50, #ffd700, #ba4a00, #ff0000, #8a2be2'
		});

		// Show tooltip for buttons
		$('a[data-sceditor-command]').tooltip({
			placement: 'bottom'
		});

		// Show tooltip for emoticons
		$('a[data-sceditor-command="emoticon"]').on('click', function () {
			$('img[unselectable="on"]').each(
				function(i, img)
				{
					$(this).attr('title', $sceditor_smilies_desc[img.alt]);
				}
			);

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
					};

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
			quoteType: function (val) {
				return '"' + val.replace('"', '\\"') + '"';
			},
			breakStart: false,
			breakEnd: false
		});
	}

	// Right panel collapse
	$('.right-sidebar-toggle').on('click',
		function(event)
		{
			event.preventDefault();
			$('#right-sidebar-toggle').toggleClass('right-sidebar-toggle');
			$('.right-sidebar-toggle i').toggleClass('fa-chevron-circle-right');
		}
	);

	// Tooltip and popover
	$('[data-tooltip="true"]').tooltip({container: 'body'});
	$('[data-popover="true"]').popover({
		container: 'body',
		trigger: 'hover',
		html: true
	});

	// Close tooltip when clicking into the notification box
	$('#iconNotification').on('click',
		function()
		{
			$('#iconNotification').tooltip('hide');
		}
	);

	// Bootstrap MaxLength
	$('input[maxlength]').maxlength({
		placement: 'bottom-left'
	});

	// Vex
	vex.defaultOptions.className = 'vex-theme-top';
	vex.dialog.buttons.YES.text = $l_yes;
	vex.dialog.buttons.NO.text = $l_no;

	// Logout confirm
	$('#linkLogout').on('click',
		function()
		{
			vex.dialog.confirm({
				message: $l_confirm_logout,
				callback: function(value)
				{
					if (value)
					{
						window.location.href = $('#linkLogout').attr('data-href');
					}
				}
			})
		}
	);

	// Slim Scroll
	$('.scrollDiv').slimScroll({
		color: '#eee',
		size: '5px',
		height: '250px',
		alwaysVisible: false
	});

	$('.scrollDiv-lg').slimScroll({
		color: '#eee',
		size: '5px',
		height: '500px',
		alwaysVisible: false
	});

	// iCheck
	$('input').iCheck({
		checkboxClass: 'icheckbox_flat-blue',
		radioClass: 'iradio_flat-blue'
	});
});
