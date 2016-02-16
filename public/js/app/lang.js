define(['jquery'], function ($) {
    /**
     * Lang - language functions
     *
     *
     * JavaScript
     *
     * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
     * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
     * @link     https://github.com/bsa-git/silex-mvc/
     */
    var Lang = Class.extend({
        init: function (sys) {
            this.sys = sys;
            //------------

            // Msgs
            var msgs = this.sys.getMessages('div.msg-box p');
            _.extend(this, msgs);

            // Removed from the base URL addresses last slash
            this['urlBase'] = _.initial(this['urlBase']).join('');

            // Get translation data
            this.getTransData();
        },
        /**
         * Get translation data from server
         * and save to jStorage database
         * 
         */
        getTransData: function ()
        {
            var url, onAjaxSuccess;
            var translate, hash = "";
            var ttl_jstorage = this.sys.settings['ttl_jstorage'];
            //--------------------
            try {
                // Get translation data
                translate = $.jStorage.get("trans");
                if (translate) {
                    hash = translate.hash;
                    if (this.lang_hash === hash) {
                        return;
                    }
                }

                onAjaxSuccess = function (jsonData) {
                    // Get language data
                    if (jsonData.hash) {
                        // Set data for TTL
                        $.jStorage.set("trans", jsonData);
                        $.jStorage.setTTL("trans", ttl_jstorage);
                    }


                };
                // Send ajax request to the server
                url = this['urlBase'] + '/lang';
                this.sys.post(url, {hash: hash}, onAjaxSuccess, false);

            } catch (ex) {
                if (ex instanceof Error) {
                    this.sys.onFailure(ex.name + ": " + ex.message);
                }

            }
        },
        /**
         * Get translation value for id
         * and set value options 
         * 
         * @param String messageId
         * @param Object options {title: 'My Title' }
         * @returns String
         */
        trans: function (messageId, options)
        {
            var ttl_jstorage = this.sys.settings['ttl_jstorage'];
            var translate, tmpl, url, msg;
            var result = "";
            options = options || {};
            //-----------------------

            // Find value in this
            if (this[messageId]) {
                msg = this[messageId];
                tmpl = _.template(msg);
                result = tmpl(options);
                return result;
            }

            // Find value in jStorage
            translate = $.jStorage.get("trans");
            if (translate) {
                if (translate.values[messageId]) {
                    msg = translate.values[messageId];
                    tmpl = _.template(msg);
                    result = tmpl(options);
                }
            } else {
                onAjaxSuccess = function (jsonData) {
                    // Get language data
                    if (jsonData.hash) {
                        // Set data for TTL
                        $.jStorage.set("trans", jsonData);
                        $.jStorage.setTTL("trans", ttl_jstorage);

                        // Get value
                        translate = $.jStorage.get("trans");
                        if (translate.values[messageId]) {
                            msg = translate.values[messageId];
                            tmpl = _.template(msg);
                            result = tmpl(options);
                        }
                    }
                };
                // Send synchronous ajax request to the server
                url = this['urlBase'] + '/lang';
                this.sys.post(url, {hash: ''}, onAjaxSuccess, false);
            }
            return result;
        }
    });

    return Lang;
});