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
    });
    return Highlight;

});
