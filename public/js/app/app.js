define(['app/system', 'app/lang'], function (System, Lang) {
    /**
     * App - application
     *
     *
     * JavaScript
     *
     * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
     * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
     * @link     https://github.com/bsa-git/silex-mvc/
     */
    var App = Class.extend({
        init: function () {
            try {
                // Create System object
                this.sys = new System();
                // Create Lang(language) object
                this.lb = new Lang(this.sys);

                // Create resources
                this.createResources();
            }
            catch (ex) {
                if (ex instanceof Error) {
                    var message = ex.stack;
                    if (this.sys && this.sys.messagebox_write) {
                        this.sys.messagebox_write('warning', [message]);
                    } else {
                        alert(message);
                    }
                }
            }
        },
        createResources: function () {
            // Add script resources 
            if (undefined !== window.BSA) {
                $.each(BSA.ScriptResources, function (i, resName) {    
                    require([resName], function (resClass) {

                        // Receive settings to create the object
                        var params = BSA.ScriptParams[resName];
                        // The function to create objects of their parameters
                        var createObject = function (param) {
                            var resObjects = BSA.ScriptInstances[resName];
                            if (resObjects) {
                                resObjects.push(new resClass(param));
                            } else {
                                BSA.ScriptInstances[resName] = [new resClass(param)];
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


                    });
                });
            }
        }
    });
    return App;
});
