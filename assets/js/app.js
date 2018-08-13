/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('bootstrap/dist/css/bootstrap.css');
require('../css/app.scss');
const $ = require('jquery');

$(function() {
  $('form[name="new-outcome"]').each(function(index, form) {
    const formFields = $('.form-fields', form);
    formFields.css('display', 'none');

    $('button[type=submit]', form).click(function() {
      if (formFields.css('display') === 'none') {
        formFields.css('display', '');

        return false;
      }
    })
  });
});
