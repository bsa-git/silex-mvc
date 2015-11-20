define(['jquery'], function ($) {
    /**
     * Highlight - Syntax highlighting for the Web
     *
     *
     * JavaScript
     *
     * @author     Sergei Beskorovainyi <bs261257@gmail.com>
     * @copyright  2011 Sergei Beskorovainyi
     * @license    BSD
     * @version    1.00.00
     * @link       http://my-site.com
     */
    var Highlight = Class.extend({
        init: function (params) {
            try {
                this.params = params || {};
                
                // Change class name
                // class = "language-yaml" => class = "yaml"
                $('pre code').each(function (i, block) {
                    var c = $(block).attr("class");
                    if(c && c.length > 0){
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

            // Получим параметры для создания обьекта
            var params = BSA.ScriptParams['Highlight'];
            // Ф-ия создания обьектов по их параметрам
            var createObject = function (param) {
                var highlight = BSA.ScriptInstances['Highlight'];
                if (highlight) {
                    highlight.push(new Highlight(param));
                } else {
                    BSA.ScriptInstances['Highlight'] = [new Highlight(param)];
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
    return Highlight;

});
