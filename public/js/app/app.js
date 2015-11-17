define(['app/system', 'app/lang'], function (System, Lang) {
    var App = Class.extend({
        init: function () {
            // Create System object
            this.sys = new System();
            // Create Lang(language) object
            this.lb = new Lang(this.sys);

            // Add script resources 
            if (undefined !== window.BSA) {
                _.each(BSA.ScriptResources, function (resource) {
                    require([resource], function (res) {
                        // Create resource object 
                        if(res && res.RegRunOnLoad){
                            res.RegRunOnLoad();
                        }
                    });
                });
            }
        }
    });
    return App;
});
