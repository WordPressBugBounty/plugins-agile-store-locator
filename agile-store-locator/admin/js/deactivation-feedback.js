(function ($) {
	'use strict';

	$(function () {
		var config = window.ASL_DEACTIVATION_FEEDBACK;
		var $modal = $('#asl-deactivate-modal');
		var $form = $('#asl-deactivate-feedback-form');
		var deactivateUrl = '';

		if (!config || !$modal.length) {
			return;
		}

		var pluginSelector = 'tr[data-plugin="' + config.pluginFile.replace(/"/g, '\\"') + '"] .deactivate a';

		$(document).on('click', pluginSelector, function (event) {
			event.preventDefault();
			deactivateUrl = this.href;
			$modal.attr('aria-hidden', 'false').addClass('is-open');
			$('body').addClass('asl-deactivate-modal-open');
			window.setTimeout(function () {
				$modal.find('input[name="reason"]').first().trigger('focus');
			}, 50);
		});

		function closeModal() {
			$modal.attr('aria-hidden', 'true').removeClass('is-open');
			$('body').removeClass('asl-deactivate-modal-open');
		}

		function deactivate() {
			if (deactivateUrl) {
				window.location.href = deactivateUrl;
			}
		}

		$modal.on('click', '.asl-deactivate-modal__close, .asl-deactivate-modal__backdrop', closeModal);
		$modal.on('click', '.asl-deactivate-skip', deactivate);

		$(document).on('keydown', function (event) {
			if (event.key === 'Escape' && $modal.hasClass('is-open')) {
				closeModal();
			}
		});

		$form.on('change', 'input[name="reason"]', function () {
			var needsDetails = this.value !== 'temporary';
			$form.find('.asl-deactivate-details').prop('hidden', !needsDetails);
			$form.find('.asl-deactivate-submit').prop('disabled', false);

			if (needsDetails) {
				$('#asl-deactivate-details').trigger('focus');
			}
		});

		$form.on('submit', function (event) {
			event.preventDefault();

			var $submit = $form.find('.asl-deactivate-submit');
			var originalLabel = $submit.text();
			var requestFinished = false;
			$submit.prop('disabled', true).text(config.sending);
			$form.find('input, textarea').prop('disabled', true);

			var request = $.post(config.ajaxUrl, {
				action: 'asl_submit_deactivation_feedback',
				nonce: config.nonce,
				reason: $form.find('input[name="reason"]:checked').val(),
				details: $('#asl-deactivate-details').val()
			});

			request.always(function () {
				if (!requestFinished) {
					requestFinished = true;
					deactivate();
				}
			});

			window.setTimeout(function () {
				if (!requestFinished) {
					requestFinished = true;
					$submit.text(originalLabel);
					deactivate();
				}
			}, 3000);
		});
	});
})(jQuery);
