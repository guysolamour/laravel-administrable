jQuery(function () {
  var larails = {
    // Define the name of the hidden input field for method submission
    methodInputName: '_method',
    // Define the name of the hidden input field for token submission
    tokenInputName: '_token',
    // Define the name of the meta tag from where we can get the csrf-token
    metaNameToken: 'csrf-token',
    initialize: function () {
      const link = $('a[data-method]')

      // if the button is inside a table
      if (link.parents('table').length != 0) {
        $('table').on('click', 'a[data-method]', this.handleMethod)
      } else {
        link.on('click', this.handleMethod);
      }


    },
    handleMethod: function (e) {
      e.preventDefault();
      var link = $(this),
        httpMethod = link.data('method').toUpperCase(),
        confirmMessage = link.data('confirm'),
        type = link.data('type') || 'warning',
        title = link.data('title') || 'Suppression',
        form;
      // Exit out if there is no data-methods of PUT, PATCH or DELETE.
      if ($.inArray(httpMethod, ['POST', 'PUT', 'PATCH', 'DELETE']) === -1) {
        return;
      }
      // Allow user to optionally provide data-confirm="Are you sure?"
      if (confirmMessage) {
        swal({
          title: title,
          text: confirmMessage,
          icon: type,
          dangerMode: true,
          buttons: {
            cancel: 'Annulez',
            confirm: 'Confirmez!'
          }
        })
          .then((isConfirm) => {
            if (isConfirm) {
              form = larails.createForm(link);
              form.submit();
            }
          })

      } else {
        form = larails.createForm(link);
        form.submit();
      }
    },
    createForm: function (link) {
      var form = $('<form>',
        {
          'method': 'POST',
          'action': link.prop('href')
        });
      var token = $('<input>',
        {
          'type': 'hidden',
          'name': larails.tokenInputName,
          'value': $('meta[name=' + larails.metaNameToken + ']').prop('content')
        });
      var method = $('<input>',
        {
          'type': 'hidden',
          'name': larails.methodInputName, 'value': link.data('method')
        });
      return form.append(token, method).appendTo('body');
    }
  };
  larails.initialize();
});

(function () {

  const links = Array.from(document.querySelectorAll('a[data-alert]'));

  links.forEach((link) => {
    link.addEventListener('click', function (event) {
      event.preventDefault()

      const message = this.dataset.alert
      if (message) {
        swal({
          title: this.dataset.title || 'Suppression',
          text: message,
          icon: this.dataset.type || 'warning',
          dangerMode: true,
          buttons: {
            cancel: 'Annulez',
            confirm: 'Confirmez!'
          }
        })
          .then((isConfirm) => {
            if (isConfirm) {
              if (this.dataset.form) {
                document.querySelector(this.dataset.form).submit()
              }
            }
          })
      }
    })
  })
}())
