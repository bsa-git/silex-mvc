define(['jquery'], function ($) {
    /**
     * Highlight - Syntax highlighting for the Web
     *
     *
     * JavaScript
     *
     * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
     * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
     * @link     https://github.com/bsa-git/silex-mvc/
     */
    var Highlight = Class.extend({
        init: function (params) {
            try {
                this.params = params || {};
                
                // Change class name
                // class = "language-yaml" => class = "yaml"
                $('pre code').each(function (i, block) {
                    var c = $(block).attr("class");
                    if(c){
                        c = c.replace("language-","")
                        $(block).attr("class", c);
                    }
                });

                // Set new configure
                hljs.configure(this.params);
                // Applies highlighting to all <pre><code>..</code></pre> blocks on a page.
                hljs.initHighlighting();

            } catch (ex) {
                if (ex instanceof Error) {
                    app.sys.onFailure(ex.name + ": " + ex.message);
                }

            }
        }
    }, {
        // The static class method, executed when loading the browser window
        // objects are class instances of holding up in the list of instances
        // ex. {Highlight: [new Highlight(), ... ,new Highlight()]}
        RegRunOnLoad: function () {

            // Receive settings to create the object
            var params = BSA.ScriptParams['Highlight'];
            // The function to create objects of their parameters
            var createObject = function (param) {
                var highlight = BSA.ScriptInstances['Highlight'];
                if (highlight) {
                    highlight.push(new Highlight(param));
                } else {
                    BSA.ScriptInstances['Highlight'] = [new Highlight(param)];
                }
            };
            // Creating objects
            if (params) {
                $.each(params, function (i, param) {
                    createObject(param);
                });
            } else {
                createObject();
            }
        }
    });
    return Highlight;

});
