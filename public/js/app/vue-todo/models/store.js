
define([], function () {
    
    var STORAGE_KEY = 'todos-vuejs';

    // Methods
    var fetch = function () {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    };
    var save = function (todos) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(todos));
    };

    // Exposed public methods
    return {
        fetch: fetch,
        save: save
    }
});