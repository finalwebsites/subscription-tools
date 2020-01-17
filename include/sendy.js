function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
	return pattern.test(emailAddress);
}


jQuery(document).ready(function($) {

	$('.sendy-subscr-fws').click(function() {
		var subform = $(this).closest('form');
		var formID = subform.attr('id');
		var msgID = $('#' + formID + ' + .error-message').attr('id');

		$('#' + msgID).html('<img src="' + msp_ajax_object.plugin_base_path + 'include/loading.gif" alt="' + msp_ajax_object.js_alt_loading + '">');
		$.ajax({
			type: 'POST',
			url: msp_ajax_object.ajax_url,
			data: subform.serialize(),
			dataType: 'json',
			beforeSend: function() {
				var name = $('#' + formID + ' input[name="name"]').val();
				var email = $('#' + formID + ' input[name="email"]').val();
				if (!name || !email) {
					$('#' + msgID).html(msp_ajax_object.js_msg_enter_email_name);
					return false;
				}
				if (!isValidEmailAddress(email)) {
					$('#' + msgID).html(msp_ajax_object.js_msg_invalid_email);
					return false;
				}
			},
			success: function(response) {
				//alert(response);
				if (response.status == 'success') {
					subform[0].reset();
					if (msp_ajax_object.googleanalytics) {
						ga('send', 'pageview', msp_ajax_object.googleanalytics);
					}
					if (msp_ajax_object.clickyanalytics) {
						clicky.goal( msp_ajax_object.clickyanalytics );
						clicky.pause( 500 );
					}
				}
				$('#' + msgID).html(response.errmessage);
			}
		});
	});

	$('.sendy-unsub-fws').click(function() {
		var subform = $(this).closest('form');
		var formID = subform.attr('id');
		var msgID = $('#' + formID + ' + .error-message').attr('id');

		$('#' + msgID).html('<img src="' + msp_ajax_object.plugin_base_path + 'include/loading.gif" alt="' + msp_ajax_object.js_alt_loading + '">');
		$.ajax({
			type: 'POST',
			url: msp_ajax_object.ajax_url,
			data: subform.serialize(),
			dataType: 'json',
			beforeSend: function() {
				var email = $('#' + formID + ' input[name="email"]').val();
				if (!email) {
					$('#' + msgID).html(msp_ajax_object.js_msg_enter_email);
					return false;
				}
				if (!isValidEmailAddress(email)) {
					$('#' + msgID).html(msp_ajax_object.js_msg_invalid_email);
					return false;
				}
			},
			success: function(response) {
				//alert(response);
				if (response.status == 'success') {
					subform[0].reset();
				}
				$('#' + msgID).html(response.errmessage);
			}
		});
	});

	$('.sendy-resub-fws').click(function() {
		var subform = $(this).closest('form');
		var formID = subform.attr('id');
		var msgID = $('#' + formID + ' + .error-message').attr('id');
		$.ajax({
			type: 'POST',
			url: msp_ajax_object.ajax_url,
			data: subform.serialize(),
			dataType: 'json',
			success: function(response) {
				//alert(response);
				if (response.status == 'success') {
					if (msp_ajax_object.googleanalytics) {
						ga('send', 'pageview', msp_ajax_object.googleanalytics);
					}
					if (msp_ajax_object.clickyanalytics) {
						clicky.goal( msp_ajax_object.clickyanalytics );
						clicky.pause( 500 );
					}
					formID.html('');
					$('#' + msgID).html(response.errmessage);
				}
			}
		});
	});
});
