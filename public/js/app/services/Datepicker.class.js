define(['jquery'], function ($) {
    /**
     * Datepicker - component jQuery-UI(datetime) 
     *
     *
     * JavaScript
     *
     * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
     * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
     * @link     https://github.com/bsa-git/silex-mvc/
     */
    var Datepicker = Class.extend({
        init: function (params) {
            try {

                if (!params) {
                    return;
                }

                this.$params = params;

                this.iniDatepicker();

            } catch (ex) {
                if (ex instanceof Error) {
                    app.sys.onFailure(ex.name + ": " + ex.message);
                }

            }
        },
        iniDatepicker: function () {
            var ids;
            var definitions = this.$params.definitions;
            //---------------------------

            $.each(definitions, function (i, definition) {
                var opt = {};
                $.each(definition, function (key, value) {
                    if (key === "ids") {
                        ids = value;
                    } else {
                        opt[key] = value;
                    }
                });
                if ($(ids).size()) {
                    $(ids).datepicker(opt);
                    // get dateFormat value
                    var dateFormat = $(ids).datepicker("option", "dateFormat");
                    // set dateFormat value
                    $(ids).datepicker("option", "dateFormat", dateFormat);
                }

            });
        }
    }, {
        // The static class method, executed when loading the browser window
        // objects are class instances of holding up in the list of instances
        // ex. {Datepicker: [new Datepicker(), ... ,new Datepicker()]}
        RegRunOnLoad: function () {

            // Receive settings to create the object
            var params = BSA.ScriptParams['Datepicker'];
            // The function to create objects of their parameters
            var createObject = function (param) {
                var datepicker = BSA.ScriptInstances['Datepicker'];
                if (datepicker) {
                    datepicker.push(new Datepicker(param));
                } else {
                    BSA.ScriptInstances['Datepicker'] = [new Datepicker(param)];
                }
            };
            // Creating objects
            if (params) {
                $.each(params, function (i, param) {
                    createObject(param);
                });
            } else {
                createObject();
            }
        }
    });
    return Datepicker;
});