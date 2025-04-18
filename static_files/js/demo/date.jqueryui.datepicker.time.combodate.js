/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.3.9
 * @author Baluart E.I.R.L.
 * * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
$( document ).ready(function() {

    /**
     * jQuery UI Datepicker & Combodate for Time Field
     *
     * @link https://jqueryui.com/datepicker/
     * @link http://vitalets.github.io/combodate/
     */

    $.when(
        $('head').append('<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" type="text/css" />'),
        $.getScript( "//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" ),
        $.getScript( "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.js" ),
        $.getScript( "//vitalets.github.io/combodate/combodate.js" ),
        $.Deferred(function( deferred ){
            $( deferred.resolve );
        })
    ).done(function(){
        $('form').attr('novalidate', true);
        $('body').css('padding-bottom', '200px'); // Set up min bottom padding for show the datepicker
        $('input[type=date]').each(function () {
            $(this).attr('type', 'text')
                .after($(this).clone().attr('id', this.id + '_alt').attr('name', this.id + '_alt')
                    .datepicker({
                        // Consistent format with the HTML5 picker
                        dateFormat: 'mm/dd/yy',
                        changeMonth: true,
                        changeYear: true,
                        // yearRange: "1970:2060",
                        // isRTL: true,
                        altField: this,
                        altFormat: "yy-mm-dd"
                    }))
                .hide();
        });
        var style = $('<style>.combodate { display: block } .form-control-date { display: inline-block; }</style>');
        $('input[type="time"]')
            .attr('type','text')
            .append(style)
            .combodate({
                customClass: 'form-control form-control-date',
                firstItem: 'name', //show 'hour' and 'minute' string at first item of dropdown
                format: 'HH:mm',
                template: 'hh : mm a',
                minuteStep: 1
            });
    });

});
