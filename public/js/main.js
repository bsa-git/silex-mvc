var lang = $('#language').html();
requirejs.config({
    baseUrl: "/js",
    paths: {
        sugar: "lib/sugarjs/sugar-full.min",// Sugar is a library that extends native Javascript objects
        css: "lib/requirejs/css.min", //requirejs plugin for load css
        text: "lib/requirejs/text", //requirejs plugin for load text
        bootstrap: 'lib/bootstrap/bootstrap.min',
        respond: 'lib/bootstrap/respond.min', // bootstrap for old vers
        html5shiv: 'lib/bootstrap/html5shiv.min',// bootstrap for old vers
        jquery: 'lib/jquery/jquery.min',
        underscore: 'lib/underscore/underscore-min',
        storage: 'lib/jstorage/jstorage',
        json: 'lib/json/json2',
        //backbone: 'lib/backbone/backbone-min',
        backbone: 'lib/backbone/backbone',
        ExtendClass: 'lib/Extend.class',
        FormValidation: 'app/services/FormValidation.class',
        Datepicker: 'app/services/Datepicker.class',
        MaskInput: 'app/services/MaskInput.class',
        Todo: 'app/bb-todo/todo'
    },
    shim: {
        'jquery': {
            exports: '$'
        },
        'bootstrap': {
            deps: ['jquery', 'respond', 'html5shiv'],
        },
        'underscore': {
            exports: '_'
        },
        'backbone': {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },
        'json': {
            exports: 'JSON'
        },
        'storage': {
            deps: ['json', 'jquery'],
        },
        'app/app': {
            deps: ['ExtendClass', 'bootstrap', 'underscore', 'storage']
        },
        'FormValidation': {
            deps: ['lib/jquery-validation/jquery.validate', 'lib/jquery-validation/additional-methods', 'lib/jquery-validation/localization/messages_' + lang]
        },
        'Datepicker': {
            deps: ['css!lib/jquery-ui/jquery-ui.min.css', 'lib/jquery-ui/jquery-ui.min', 'lib/jquery-ui/i18n/datepicker-' + lang]
        },
        'MaskInput': {
            deps: ['lib/jquery-maskedinput/jquery.maskedinput.min']
        },
        'Todo': {
            deps: ['css!app/bb-todo/css/style.css', 'backbone', 'lib/backbone/backbone.localStorage', 'lib/sugarjs/sugar-str.min']
        },
    }
});

require(['app/app'], function (App) {
    window.app = new App();
});
