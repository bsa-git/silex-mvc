
define(['app/vue-todo/views/appview', 'router'], function (appView, Router) {
    
    var router = new Router();

    ['all', 'active', 'completed'].forEach(function (visibility) {
        router.on(visibility, function () {
            appView.visibility = visibility;
        });
    });

    router.configure({
        notfound: function () {
            window.location.hash = '';
            appView.visibility = 'all';
        }
    });

    router.init();
});