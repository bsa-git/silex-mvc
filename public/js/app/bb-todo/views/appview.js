define(['jquery', 'app/bb-todo/views/taskview', 'text!app/bb-todo/templates/stats-template.html'], function ($, TaskView, statsTemplate) {

    // The Application
    // ---------------

    // Our overall **AppView** is the top-level piece of UI.
    var AppView = Backbone.View.extend({
        // Instead of generating a new element, bind to the existing skeleton of
        // the App already present in the HTML.
        el: $("#todoapp"),
        // Our template for the line of statistics at the bottom of the app.
        statsTemplate: _.template(statsTemplate),
        // Delegated events for creating new items, and clearing completed ones.
        events: {
            "keypress #new-todo": "createOnEnter",
            "click #clear-completed": "clearCompleted",
            "click #toggle-all": "toggleAllComplete"
        },
        // At initialization we bind to the relevant events on the `TaskList`
        // collection, when items are added or changed. Kick things off by
        // loading any preexisting TaskList that might be saved in *localStorage*.
        initialize: function () {
            var self = this;
            //--------------------
            this.input = this.$("#new-todo");
            this.allCheckbox = this.$("#toggle-all")[0];

            this.listenTo(app.todo.taskList, 'add', this.addOne);
            this.listenTo(app.todo.taskList, 'reset', this.addAll);
            this.listenTo(app.todo.taskList, 'all', this.render);

            this.footer = this.$('footer');
            this.main = this.$('#main-todo');
            // Fetch models
            app.todo.taskList.fetchModels({
                success: function (collection, response, options) {
                    self.$el.show();
                }
            });
        },
        // Re-rendering the App just means refreshing the statistics -- the rest
        // of the app doesn't change.
        render: function () {
            var done = app.todo.taskList.done().length;
            var remaining = app.todo.taskList.remaining().length;

            if (app.todo.taskList.length) {
                this.main.show();
                this.footer.show();
                this.footer.html(this.statsTemplate({done: done, remaining: remaining}));
            } else {
                this.main.hide();
                this.footer.hide();
            }

            this.allCheckbox.checked = !remaining;
        },
        // Add a single todo item to the list by creating a view for it, and
        // appending its element to the `<ul>`.
        addOne: function (task) {
            var view = new TaskView({model: task});
            this.$("#todo-list").append(view.render().el);
        },
        // Add all items in the **TaskList** collection at once.
        addAll: function () {
            app.todo.taskList.each(this.addOne, this);
        },
        // If you hit return in the main input field, create new **Task** model
        createOnEnter: function (e) {
            if (e.keyCode != 13){
                return;
            }
            if (!this.input.val()){
                return;
            }
            app.todo.taskList.createModel({title: this.input.val()});
            this.input.val('');
        },
        // Clear all done todo items, destroying their models.
        clearCompleted: function () {
            _.invoke(app.todo.taskList.done(), 'delete');
            return false;
        },
        toggleAllComplete: function () {
            var done = this.allCheckbox.checked;
            app.todo.taskList.each(function (task) {
                task.update({'done': done});
            });
        }

    });

    return AppView;
});
