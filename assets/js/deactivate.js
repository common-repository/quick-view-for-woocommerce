// Deactivation Form
jQuery(document).ready(function () {
	jQuery(document).on('click', function (e) {
		var popup = document.getElementById('acoqvw-aco-survey-form');
		var overlay = document.getElementById('acoqvw-aco-survey-form-wrap');
		var openButton = document.getElementById(
			'deactivate-quick-view-for-woocommerce'
		);
		if (e.target.id == 'acoqvw-aco-survey-form-wrap') {
			closePopup();
		}
		if (e.target === openButton) {
			e.preventDefault();
			popup.style.display = 'block';
			overlay.style.display = 'block';
		}
		if (e.target.id == 'acoqvw-aco_skip') {
			e.preventDefault();
			var urlRedirect = document
				.querySelector('a#deactivate-quick-view-for-woocommerce')
				.getAttribute('href');
			window.location = urlRedirect;
		}
		if (e.target.id == 'acoqvw-aco_cancel') {
			e.preventDefault();
			closePopup();
		}
	});

	function closePopup() {
		var popup = document.getElementById('acoqvw-aco-survey-form');
		var overlay = document.getElementById('acoqvw-aco-survey-form-wrap');
		popup.style.display = 'none';
		overlay.style.display = 'none';
		jQuery('#acoqvw-aco-survey-form form')[0].reset();
		jQuery('#acoqvw-aco-survey-form form .acoqvw-aco-comments').hide();
		jQuery('#acoqvw-aco-error').html('');
	}

	jQuery('#acoqvw-aco-survey-form form').on('submit', function (e) {
		e.preventDefault();
		var valid = validate();
		if (valid) {
			var urlRedirect = document
				.querySelector('a#deactivate-quick-view-for-woocommerce')
				.getAttribute('href');
			var form = jQuery(this);
			var serializeArray = form.serializeArray();
			var actionUrl = 'https://feedback.acowebs.com/plugin.php';
			jQuery.ajax({
				type: 'post',
				url: actionUrl,
				data: serializeArray,
				contentType: 'application/javascript',
				dataType: 'jsonp',
				beforeSend: function () {
					jQuery('#acoqvw-aco_deactivate').prop(
						'disabled',
						'disabled'
					);
				},
				success: function (data) {
					window.location = urlRedirect;
				},
				error: function (jqXHR, textStatus, errorThrown) {
					window.location = urlRedirect;
				},
			});
		}
	});
	jQuery('#acoqvw-aco-survey-form .acoqvw-aco-comments textarea').on(
		'keyup',
		function () {
			validate();
		}
	);
	jQuery("#acoqvw-aco-survey-form form input[type='radio']").on(
		'change',
		function () {
			validate();
			let val = jQuery(this).val();
			if (
				val == 'I found a bug' ||
				val == 'Plugin suddenly stopped working' ||
				val == 'Plugin broke my site' ||
				val == 'Other' ||
				val == "Plugin doesn't meets my requirement"
			) {
				jQuery(
					'#acoqvw-aco-survey-form form .acoqvw-aco-comments'
				).show();
			} else {
				jQuery(
					'#acoqvw-aco-survey-form form .acoqvw-aco-comments'
				).hide();
			}
		}
	);
	function validate() {
		var error = '';
		var reason = jQuery(
			"#acoqvw-aco-survey-form form input[name='Reason']:checked"
		).val();
		if (!reason) {
			error += 'Please select your reason for deactivation';
		}
		if (
			error === '' &&
			(reason == 'I found a bug' ||
				reason == 'Plugin suddenly stopped working' ||
				reason == 'Plugin broke my site' ||
				reason == 'Other' ||
				reason == "Plugin doesn't meets my requirement")
		) {
			var comments = jQuery(
				'#acoqvw-aco-survey-form .acoqvw-aco-comments textarea'
			).val();
			if (comments.length <= 0) {
				error += 'Please specify';
			}
		}
		if (error !== '') {
			jQuery('#acoqvw-aco-error').html(error);
			return false;
		}
		jQuery('#acoqvw-aco-error').html('');
		return true;
	}
});
