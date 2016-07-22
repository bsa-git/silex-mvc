(function ($) {
    var lang = $('#language').html();
    requirejs.config({
        baseUrl: "/js",
        paths: {
            sugar: "lib/sugarjs/sugar-full.min", // Sugar is a library that extends native Javascript objects
            css: "lib/requirejs/css.min", //requirejs plugin for load css
            text: "lib/requirejs/text", //requirejs plugin for load text
            jquery: 'lib/jquery/jquery.min',
            underscore: 'lib/underscore/underscore',
            storage: 'lib/jstorage/jstorage',
            json: 'lib/json/json2',
            backbone: 'lib/backbone/backbone-min',
            vue: 'lib/vue/dist/vue',
            router: 'lib/director/build/director',
            ExtendClass: 'lib/Extend.class',
            Highlight: 'app/services/Highlight.class',
            FormValidation: 'app/services/FormValidation.class',
            Datepicker: 'app/services/Datepicker.class',
            MaskInput: 'app/services/MaskInput.class',
            TodoBackbone: 'app/bb-todo/todo',
            TodoVue: 'app/vue-todo/todo'
        },
        shim: {
            'jquery': {
                exports: '$',
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
                deps: ['json', 'jquery']
            },
            'router': {
                exports: 'Router'
            },
            'app/app': {
                deps: ['ExtendClass', 'underscore', 'storage']
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
            'Highlight': {
                deps: ['css!lib/highlight/css/github.css', 'lib/highlight/highlight.pack']
            },
            'TodoBackbone': {
                deps: ['css!app/bb-todo/css/style.css', 'backbone', 'lib/backbone/backbone.localStorage', 'lib/sugarjs/sugar-str.min']
            },
            'TodoVue': {
                deps: ['css!app/vue-todo/css/style.css']
            }
        }
    });
})(jQuery)

require(['app/app'], function (App) {
    window.app = new App();
});
