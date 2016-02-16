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
                
                // Add script resources 
                if (undefined !== window.BSA) {
                    _.each(BSA.ScriptResources, function (resource) {
                        require([resource], function (res) {
                            // Create resource object 
                            if (res && res.RegRunOnLoad) {
                                res.RegRunOnLoad();
                            }
                        });
                    });
                }
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
        }
    });
    return App;
});
