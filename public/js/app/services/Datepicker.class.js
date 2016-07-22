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
    });
    return Datepicker;
});