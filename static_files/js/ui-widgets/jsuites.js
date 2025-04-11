/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 2.2
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
$( document ).ready(function() {

    /**
     * jSuites
     * A free and comprehensive collection of lightweight JavaScript plugins.
     *
     * @link https://jsuites.net/docs/javascript-mask
     */

    $.when(
        $('head').append('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.min.css" type="text/css" />'),
        $.getScript("https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.min.js"),
        $.Deferred(function( deferred ){
            $( deferred.resolve );
        })
    ).done(function(){

        /**
         * To get your mask applied in your fields just add the "data-mask" custom attribute with the respective value.
         * E.g.
         * <input type="text" data-mask="U$ #.##0,00" />
         * <input type="text" data-mask="# ##0,00 EUR" />
         * <input type="text" data-mask="0,00%" />
         * <input type="text" data-mask="dd/mm/yyyy" />
         */

    });

});
