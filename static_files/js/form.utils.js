/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.0
 * @author Baluart E.I.R.L.
 * * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
if (typeof Utils !== 'object') {

    var Utils = (function () {

        /**
         * Parse the URL
         * and return his protocol and domain as URL (http://example.com)
         *
         * URI Parsing with Javascript
         * @returns {string}
         */
        function getDomainFromUrl(url) {
            // See: https://gist.github.com/jlong/2428561
            var parser = document.createElement('a');
            parser.href = url;
            // return parser.protocol + "//" + parser.host;
            return parser.protocol + "//" + parser.hostname + ":" + parser.port;
        }

        /**
         * Read a page's GET URL variables and return them as an associative array.
         *
         * @param url
         * @returns {Array}
         */
        function getUrlVars(url)
        {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            if (url && url.length > 0) {
                hashes = url.slice(url.indexOf('?') + 1).split('&');
            }
            for(var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

        /**
         * Detect if page was loaded in an iframe
         * @returns {boolean}
         */
        function inIframe () {
            try {
                return window.self !== window.top;
            } catch (e) {
                return true;
            }
        }

        /**
         * Send a message to parent window or same window
         */
        function postMessage(message)
        {
            var referrer = getDomainFromUrl(document.referrer);
            var url = getDomainFromUrl(window.location.href);
            if (referrer === url) {
                var urlVars = getUrlVars();
                referrer = decodeURIComponent(urlVars['url']);
            }
            message.formID = options.id;
            message.hashId = options.hashId;
            try {
                if (window.location !== window.parent.location) {
                    parent.postMessage(JSON.stringify(message), getDomainFromUrl(referrer));
                }
                // Same window
                if (!inIframe()) {
                    if (typeof message.scrollToTop !== "undefined") {
                        var formContainer = document.getElementsByClassName('form-container');
                        if (formContainer.length === 1) {
                            window.scrollTo(0, formContainer[0].offsetTop);
                        } else {
                            window.scrollTo(0, 1);
                        }
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }

        /**
         * Show alert message
         * @param container
         * @param txt
         * @param type
         * @param custom
         */
        function showMessage(container, txt, type, custom = false)
        {
            var message = $("<div>", {"class": "d-flex", "html": txt});
            if (!custom) {
                var icon =
                    '<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">\n' +
                    '  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>\n' +
                    '  <path d="M5 12l5 5l10 -10"></path>\n' +
                    '</svg>';
                if (type === 'danger') {
                    icon =
                        '<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">\n' +
                        '    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>\n' +
                        '    <circle cx="12" cy="12" r="9"></circle>\n' +
                        '    <line x1="12" y1="8" x2="12" y2="12"></line>\n' +
                        '    <line x1="12" y1="16" x2="12.01" y2="16"></line>\n' +
                        '</svg>';
                }
                var alertBody = $("<div>", {"class": "d-flex", "html": '<div>' + txt + '</div>'});
                message = $("<div>", {"class": "alert alert-important alert-"+type, "html": alertBody});
            }
            $(container).append(message);
        }

        /**
         * Hide alert message
         * @param container
         */
        function hideMessage(container)
        {
            $(container).empty();
        }

        /**
         * Convert string to 32bit integer
         *
         * @param str
         * @returns {number}
         */
        function hashCode(str)
        {
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                var charCodeAt = str.charCodeAt(i);
                hash = ((hash<<5)-hash)+charCodeAt;
                hash = hash & hash; // Convert to 32bit integer
            }
            return hash;
        }

        /**
         * Utils
         *
         * @type {{getDoaminFromUrl: getDomainFromUrl, getUrlVars: getUrlVars, postMessage: postMessage}}
         */
        return {
            getDomainFromUrl: getDomainFromUrl,
            getUrlVars: getUrlVars,
            inIframe: inIframe,
            postMessage: postMessage,
            showMessage: showMessage,
            hideMessage: hideMessage,
            hashCode: hashCode
        };

    }());
}
