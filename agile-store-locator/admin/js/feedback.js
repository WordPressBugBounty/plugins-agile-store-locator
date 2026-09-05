/* Voluntary feedback: no dialog or request until the user clicks the link. */
(function($) {
  'use strict';

  // Links styled like the existing header actions retain button keyboard behavior.
  $(document).on('keydown', '.asl-feedback-link', function(event) {
    if (event.key === ' ' || event.keyCode === 32) {
      event.preventDefault();
      $(this).trigger('click');
    }
  });

  $(document).on('click', '.asl-feedback-link', function(event) {
    event.preventDefault();
    var text = ASL_FEEDBACK;
    var sending = false;
    function blockEscape(event) {
      if (sending && (event.key === 'Escape' || event.keyCode === 27)) {
        event.preventDefault();
        event.stopImmediatePropagation();
      }
    }
    function escape(value) { return $('<div>').text(value).html(); }
    aswal({
      title: text.title,
      customClass: 'asl-feedback-modal',
      html: '<p>' + escape(text.intro) + '</p><div class="asl-feedback-fields">' +
        '<label for="asl-feedback-type">' + escape(text.type) + '</label>' +
        '<select id="asl-feedback-type"><option value="suggestion">' + escape(text.suggestion) + '</option><option value="issue">' + escape(text.issue) + '</option><option value="general">' + escape(text.general) + '</option></select>' +
        '<label for="asl-feedback-message">' + escape(text.message) + '</label>' +
        '<textarea id="asl-feedback-message" rows="5" maxlength="5000" required></textarea>' +
        '<label for="asl-feedback-email">' + escape(text.email) + '</label>' +
        '<input id="asl-feedback-email" type="email" maxlength="254" aria-describedby="asl-feedback-email-help">' +
        '<small id="asl-feedback-email-help">' + escape(text.emailHelp) + '</small>' +
        '<label class="asl-feedback-site"><input id="asl-feedback-site" type="checkbox" checked> <span>' + escape(text.includeSite) + '<small>' + escape(text.siteUrl) + '</small></span></label>' +
        '</div>',
      showCancelButton: true,
      confirmButtonText: text.title,
      cancelButtonText: text.cancel,
      showLoaderOnConfirm: true,
      allowOutsideClick: false,
      allowEscapeKey: true,
      onOpen: function() {
        document.addEventListener('keydown', blockEscape, true);
        $('#asl-feedback-message').trigger('focus');
      },
      onClose: function() { document.removeEventListener('keydown', blockEscape, true); },
      preConfirm: function() {
        return new Promise(function(resolve, reject) {
          var message = $.trim($('#asl-feedback-message').val());
          var emailField = document.getElementById('asl-feedback-email');
          var email = $.trim(emailField.value);
          function fail(message) {
            sending = false;
            $('.asl-feedback-fields :input').prop('disabled', false);
            $('.asl-feedback-modal').removeClass('asl-feedback-sending');
            aswal.showValidationError(escape(message));
            reject();
          }
          if (!message || message.length > 5000) { fail(text.required); return; }
          emailField.value = email;
          if (!emailField.checkValidity()) { fail(text.invalidEmail); return; }
          var payload = {
            action: 'asl_ajax_handler',
            'sl-action': 'send_feedback',
            'asl-nounce': text.nonce,
            feedback_type: $('#asl-feedback-type').val(),
            message: message,
            email: email,
            include_site: $('#asl-feedback-site').prop('checked') ? '1' : '0'
          };
          sending = true;
          $('.asl-feedback-fields :input').prop('disabled', true);
          $('.asl-feedback-modal').addClass('asl-feedback-sending');
          $.ajax({url: text.url, type: 'POST', dataType: 'json', data: payload, timeout: 30000})
            .done(function(response) {
              if (!response || !response.success) {
                fail(response && (response.message || response.error) || text.failed);
                return;
              }
              sending = false;
              resolve(response);
            })
            .fail(function() { fail(text.failed); });
        });
      }
    }).then(function() {
      aswal({type: 'success', title: text.thanks, confirmButtonText: text.close}).catch(aswal.noop);
    }, aswal.noop);
  });
})(jQuery);
