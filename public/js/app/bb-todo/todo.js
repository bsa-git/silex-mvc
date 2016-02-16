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
    var Todo = Class.extend({
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
    }, {
        // The static class method, executed when loading the browser window
        // objects are class instances of holding up in the list of instances
        // ex. {Todo: [new Todo(), ... ,new Todo()]}
        RegRunOnLoad: function () {

            // Получим параметры для создания обьекта
            var params = BSA.ScriptParams['Todo'];
            // Ф-ия создания обьектов по их параметрам
            var createObject = function (param) {
                var todo = BSA.ScriptInstances['Todo'];
                if (todo) {
                    todo.push(new Todo(param));
                } else {
                    BSA.ScriptInstances['Todo'] = [new Todo(param)];
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
    return Todo;

});
