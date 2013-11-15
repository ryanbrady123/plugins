/* dCodes Framework: (c) TemplateAccess */

var Website = {
	run: function () {
		oScroll1 = $('#dc_vcontent_scroll');
		if (oScroll1.length > 0) {
			oScroll1.tinyscrollbar();
		}
		var oScroll2 = $('#dc_hcontent_scroll');
		if (oScroll2.length > 0) {
			oScroll2.tinyscrollbar({
				axis: 'x'
			});
		}
		var oScroll2 = $('#scrollbar3');
		if (oScroll2.length > 0) {
			oScroll2.tinyscrollbar({
				size: 100
			});
		}
	}
};
//Initialize
$(document).ready(function () {
	Website.run();
});