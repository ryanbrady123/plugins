/* dCodes Framework: (c) TemplateAccess */

// <![CDATA[
$(function () {
	$(".chzn-select").chosen();
	var systemFonts = ['Arial', 'Garamond', 'Georgia', 'Helvetica', 'Palatino', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana', 'Courier New', 'Comic Sans MS']

		function isnotSystemFont(font) {
			var res = true;
			for (var i = 0; i < systemFonts.length; i++) {
				if (font == systemFonts[i]) {
					res = false;
				}
			}
			return res;
		}
		// set active control when page loaded
		function setActiveControls() {
			$('.dc_control').attr('rel', $('#select_element option:selected').val());
		}
	setActiveControls();
	// select element
	$('#select_element').change(function () {
		setActiveControls();
		var thisEl = $('#select_element option:selected').val();
		// all controls to actual
		$('#select_ff option:selected').each(function () {
			this.selected = false;
		});
		$("#select_ff [value='" + $(thisEl).css('font-family') + "']").attr("selected", "selected");
		$('#select_fs option:selected').each(function () {
			this.selected = false;
		});
		if ($(thisEl).css('font-weight') == 'normal' && $(thisEl).css('font-style') == 'normal') {
			$("#select_fs [value='fs0']").attr("selected", "selected");
		}
		if ($(thisEl).css('font-weight') == 'bold' && $(thisEl).css('font-style') == 'normal') {
			$("#select_fs [value='fs1']").attr("selected", "selected");
		}
		if ($(thisEl).css('font-weight') == 'normal' && $(thisEl).css('font-style') == 'italic') {
			$("#select_fs [value='fs2']").attr("selected", "selected");
		}
		if ($(thisEl).css('font-weight') == 'bold' && $(thisEl).css('font-style') == 'italic') {
			$("#select_fs [value='fs3']").attr("selected", "selected");
		}
		$('#select_fsize').val($(thisEl + '_fsz').text());
		$('#fsize_indi').text($('#select_fsize').val());
		$('#select_fhgh').val($(thisEl + '_flh').text());
		$('#flh_indi').text($('#select_fhgh').val());
		$('#select_color').val($(thisEl + '_color').text());
	});
	// find all non-system fonts
	function find_ns_fonts() {
		$('#dc_nsf').text('');
		var notSysFonts = [];
		if (isnotSystemFont($('.dc_cs_h1_ff').text())) {
			notSysFonts.push($('.dc_cs_h1_ff').text());
		}
		if (isnotSystemFont($('.dc_cs_h2_ff').text())) {
			notSysFonts.push($('.dc_cs_h2_ff').text());
		}
		if (isnotSystemFont($('.dc_cs_h3_ff').text())) {
			notSysFonts.push($('.dc_cs_h3_ff').text());
		}
		if (isnotSystemFont($('.dc_cs_p_ff').text())) {
			notSysFonts.push($('.dc_cs_p_ff').text());
		}
		for (var i = 0; i < notSysFonts.length; i++) {
			for (var j = 0; j < i; j++) {
				if (notSysFonts[i] == notSysFonts[j]) {
					notSysFonts[i] = '';
				}
			}
		}
		var s = '';
		for (var i = 0; i < notSysFonts.length; i++) {
			if (notSysFonts[i] != '') {
				s = s + '&lt;link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' + notSysFonts[i].replace(' ', '%20') + '"&gt;<br />';
			}
		}
		$('#dc_nsf').html(s);
	}
	// change font-family
	function ffChange() {
		thisEl = $(this).attr('rel');
		//check if this system font don't load the google font
		if (!$(this).hasClass('system_font')) {
			fontLink = "http://fonts.googleapis.com/css?family=" + $('#select_ff option:selected').val().replace(' ', '+');
			//check if the font already loaded in head
			if ($("link[href*='" + fontLink + "']").length <= 0) $('head').append('<link rel="stylesheet" href="' + fontLink + '" media="all" />');
		}
		$(thisEl).css('font-family', $('#select_ff option:selected').val());
		$(thisEl + '_ff').text($('#select_ff option:selected').val());
		find_ns_fonts();
	}
	$('#select_ff').change(ffChange);
	// change t_access font-style
	function fsChange() {
		thisEl = $(this).attr('rel');
		if ($('#select_fs option:selected').val() == 'fs0') {
			$(thisEl).css({
				'font-weight': 'normal',
				'font-style': 'normal'
			});
			$(thisEl + '_fs').text('normal');
			$(thisEl + '_fw').text('normal');
		}
		if ($('#select_fs option:selected').val() == 'fs1') {
			$(thisEl).css({
				'font-weight': 'bold',
				'font-style': 'normal'
			});
			$(thisEl + '_fs').text('normal');
			$(thisEl + '_fw').text('bold');
		}
		if ($('#select_fs option:selected').val() == 'fs2') {
			$(thisEl).css({
				'font-weight': 'normal',
				'font-style': 'italic'
			});
			$(thisEl + '_fs').text('italic');
			$(thisEl + '_fw').text('normal');
		}
		if ($('#select_fs option:selected').val() == 'fs3') {
			$(thisEl).css({
				'font-weight': 'bold',
				'font-style': 'italic'
			});
			$(thisEl + '_fs').text('italic');
			$(thisEl + '_fw').text('bold');
		}
	}
	$('#select_fs').change(fsChange);
	// change font-size
	function fszChange() {
		thisEl = $(this).attr('rel');
		$(thisEl).css('font-size', $(this).val() + 'px');
		$('#fsize_indi, ' + thisEl + '_fsz').text($(this).val());
	}
	$('#select_fsize').change(fszChange);
	// change line-height
	function flhChange() {
		thisEl = $(this).attr('rel');
		v = parseFloat($(this).val()).toFixed(2);
		$(thisEl).css('line-height', v + 'em');
		$('#flh_indi, ' + thisEl + '_flh').text(v);
	}
	$('#select_fhgh').change(flhChange);
	// change color
	function colorChange() {
		$('#select_color').ColorPicker({
			onShow: function () {
				thisEl = $(this).attr('rel');
			},
			onChange: function (hsb, hex, rgb, el) {
				$('#select_color').val(hex);
				$(el).ColorPickerHide();
				$(thisEl).css('color', '#' + hex);
				$(thisEl + '_color').text(hex);
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		}).bind('keyup', function () {
			$(this).ColorPickerSetColor(this.value);
		});
	}
	colorChange();
	// change t-access background of box
	function bgChange() {
		$('#select_bg').ColorPicker({
			onShow: function () {
				thisEl = $(this).attr('rel');
			},
			onChange: function (hsb, hex, rgb, el) {
				$('#select_bg').val(hex);
				$(el).ColorPickerHide();
				$(thisEl).css('background', '#' + hex);
				$(thisEl + '_bg').text(hex);
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		}).bind('keyup', function () {
			$(this).ColorPickerSetColor(this.value);
		});
	}
	bgChange();
});
// ]]>