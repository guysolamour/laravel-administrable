jQuery(function () {
    const shopcart = {
        // Define the name of the hidden input field for method submission
        methodInputName: '_method',
        // Define the name of the hidden input field for token submission
        tokenInputName: '_token',
        // Define the name of the meta tag from where we can get the csrf-token
        metaNameToken: 'csrf-token',

        initialize: function () {
            const links = jQuery('[data-cart]')

            links.on('click', this.handleMethod);
        },

        handleMethod: function (e) {
            e.preventDefault();
            var link = jQuery(this),
                httpMethod = link.data('cartmethod') || 'POST',
                form;

            // Exit out if there is no data-methods of PUT, PATCH or DELETE.
            if (!['POST', 'PUT', 'PATCH'].includes(httpMethod)) {
                return;
            }
            // Allow user to optionally provide data-confirm="Are you sure?"
            form = shopcart.createForm(link);
            form.trigger('submit');
        },

        createForm: function (link) {

            var form = jQuery('<form>',
                {
                    'method': 'POST',
                    'action': link.data('cart')
                });
            var token = jQuery('<input>',
                {
                    'type': 'hidden',
                    'name': shopcart.tokenInputName,
                    'value': jQuery('meta[name=' + shopcart.metaNameToken + ']').prop('content')
                });
            var method = jQuery('<input>',
                {
                    'type': 'hidden',
                    'name': shopcart.methodInputName, 'value': link.data('cartmethod') || 'POST'
                });
            return form.append(token, method).appendTo('body');
        }
    }

    shopcart.initialize();
}())



