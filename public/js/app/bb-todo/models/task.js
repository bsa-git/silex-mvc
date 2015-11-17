define([], function () {

    // Task Model
    // ----------
    var Task = Backbone.Model.extend({
        // Reference to this collection's model.
        urlRoot: function () {
            if(app.todo.params.storage === 'local'){
                return null;
            }
            if(app.todo.params.storage === 'server'){
                return app.todo.params.serverStorage.urlRoot;
            }
        },
        // Default attributes for the todo item.
        defaults: function () {
            return {
                title: "Empty task...",
                task_order: app.todo.taskList.nextOrder(),
                done: false
            };
        },
        // Ensure that each todo created has `title`.
        initialize: function () {
            if (!this.get("title")) {
                this.set({"title": this.defaults().title});
            }
        },
        // Toggle the `done` state of this todo item.
        toggle: function () {
            this.update({done: !this.get("done")});
        },
        // Destroy the model.
        delete: function (options) {
            var opts = options || {};
            var defaults = {
                wait: true,
                emulateHTTP: _.isUndefined(app.todo.params.serverStorage.emulateHTTP) ? false : app.todo.params.serverStorage.emulateHTTP,
                success: function (collection, response, options) {
                },
                error: function (collection, xhr, options) {
                    app.sys.onFailure(xhr);
                }
            };
            this.destroy(_.extend(defaults, opts));
        },
        // Save the model.
        update: function (attributes, options) {
            var opts = options || {};
            var defaults = {
                wait: true,
                emulateHTTP: _.isUndefined(app.todo.params.serverStorage.emulateHTTP) ? false : app.todo.params.serverStorage.emulateHTTP,
                success: function (collection, response, options) {
                },
                error: function (collection, xhr, options) {
                    app.sys.onFailure(xhr);
                }
            };
            this.save(attributes, _.extend(defaults, opts));
        }

    });

    return  Task;
});
