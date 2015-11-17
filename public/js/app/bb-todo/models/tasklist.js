define(['app/bb-todo/models/task'], function (Task) {

    // Task Collection
    // ---------------

    var TaskList = Backbone.Collection.extend({
        // Reference to this collection's model.
        model: Task,
        initialize: function() { 
            if(app.todo.params.storage === 'local'){
                // Save all of the task items under the `"todos-backbone"` namespace.
                this.localStorage = new Backbone.LocalStorage("todos-backbone");
            }
            if(app.todo.params.storage === 'server'){
                // Collection's url
                this.url = app.todo.params.serverStorage.urlRoot;
                this.localStorage = null;
            }
        },
        
        // Filter down the list of all task items that are finished.
        done: function () {
            return this.filter(function (task) {
                return task.get('done');
            });
        },
        // Filter down the list to only todo items that are still not finished.
        remaining: function () {
            return this.without.apply(this, this.done());
        },
        // We keep the TaskList in sequential order, despite being saved by unordered
        // GUID in the database. This generates the next order number for new items.
        nextOrder: function () {
            if (!this.length) {
                return 1;
            }
            return this.last().get('task_order') + 1;
        },
        // TaskList are sorted by their original insertion order.
        comparator: function (task) {
            return task.get('task_order');
        },
        // Create model
        createModel: function (attributes, options) {
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
            this.create(attributes, _.extend(defaults, opts));
        },
        
        // Fetch models
        fetchModels: function (options) {
            var opts = options || {};
            var defaults = {
                success: function (collection, response, options) {
                },
                error: function (collection, xhr, options) {
                    app.sys.onFailure(xhr);
                }
            };
            this.fetch(_.extend(defaults, opts));
        }

    });

    return TaskList;
});
