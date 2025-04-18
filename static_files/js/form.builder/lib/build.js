({
    name: "../main",
    out: "../main-built.js"
    , shim: {
        'underscore': {
            exports: '_'
        },
        'backbone': {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },
        'bootstrap': {
            deps: ['jquery']
        },
        'jquery.fontselect': {
            deps: ['jquery']
        },
        'jquery.flexdatalist': {
            deps: ['jquery']
        },
        'jquery.cookie': {
            deps: ['jquery']
        },
        'polyglot': {
            exports: 'Polyglot'
        },
        'prism': {
            exports: 'Prism'
        },
        'simplebar': {
            exports: 'SimpleBar'
        },
        'cssjson': {
            exports: 'CSSJSON'
        },
        'gradpick': {
            exports: 'Gradpick'
        },
        'clipboard': {
            exports: 'ClipboardJS'
        }
    }
    , paths: {
        app         : ".."
        , collections : "../collections"
        , data        : "../data"
        , models      : "../models"
        , helper      : "../helper"
        , templates   : "../templates"
        , views       : "../views"
    }
})
