(function ($) {
	'use strict';
	if ($('.wFormContainer')) {// Check if this page contains a Form Assembly Form
		if ($('input[title="Association ID"]')) { // Check for our hidden field
			$('input[title="Association ID"]').val(associationId)
		}
	}
})(jQuery);
