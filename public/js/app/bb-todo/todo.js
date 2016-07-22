define(['app/bb-todo/models/tasklist', 'app/bb-todo/views/appview'], function (TaskList, AppView) {
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
    var TodoBackbone = Class.extend({
        init: function (params) {
            try {
                
                // Set global link to object for Todo class
                app.todo = this;
                
                this.params = params || {};

                // Create our global collection of **TaskList**.
                this.taskList = new TaskList;

                // Finally, we kick things off by creating the **AppView**.
                this.appView = new AppView;

            } catch (ex) {
                if (ex instanceof Error) {
                    app.sys.onFailure(ex.name + ": " + ex.message);
                }

            }
        }
    });
    return TodoBackbone;

});
