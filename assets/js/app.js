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
const Highcharts = require('highcharts');

window.$ = $;
window.Highcharts = Highcharts;

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

  $('.outcome-chart').each(function(index, chart) {
    const { name, points } = $(chart).data('chart');
    console.log(name);
    Highcharts.chart(chart, {
        title: '',
        chart: {
            type: 'line',
        },
        credits: {
            enabled: false
        },
        xAxis: {
            type: 'datetime',
            labels: {
                format: '{value:%m-%d}'
            }
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        series: [{
            name: name,
            showInLegend: false,
            data: points.map((point) => ([
                Date.parse(point[0]),
                point[1]
            ])),
            zoneAxis: 'x',
            zones: [
                {
                    value: Date.parse(points[points.length - 2][0]),
                },
                {
                    dashStyle: 'dot'
                }
            ]
        }],
    });
  });
});
