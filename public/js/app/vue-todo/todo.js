define(['app/vue-todo/views/appview'], function (appView) {
    /**
     * Todo - user todo list application
     *
     *
     * JavaScript
     *
     * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
     * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
     * @link     https://github.com/bsa-git/silex-mvc/
     */
    var TodoVue = Class.extend({
        init: function (params) {
            try {

                // Set global link to object for Todo class
                app.todo = this;

                this.params = params || {};

                // Get AppView
                this.appView = appView;
                // Router init
                require(['app/vue-todo/views/routes']);
                
            } catch (ex) {
                if (ex instanceof Error) {
                    app.sys.onFailure(ex.name + ": " + ex.message);
                }

            }
        }
    });
    return TodoVue;

});
