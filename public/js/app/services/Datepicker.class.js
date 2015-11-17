define(['jquery'], function ($) {
    /**
     * Datepicker - component jQuery-UI(datetime) 
     *
     *
     * JavaScript
     *
     * @author     Sergei Beskorovainyi <bs261257@gmail.com>
     * @copyright  2011 Sergei Beskorovainyi
     * @license    BSD
     * @version    1.00.00
     * @link       http://my-site.com
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

            // Получим параметры для создания обьекта
            var params = BSA.ScriptParams['Datepicker'];
            // Ф-ия создания обьектов по их параметрам
            var createObject = function (param) {
                var datepicker = BSA.ScriptInstances['Datepicker'];
                if (datepicker) {
                    datepicker.push(new Datepicker(param));
                } else {
                    BSA.ScriptInstances['Datepicker'] = [new Datepicker(param)];
                }
            };
            // Создание обьектов
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