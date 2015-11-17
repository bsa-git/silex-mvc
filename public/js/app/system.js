define(['jquery'], function ($) {
    /**
     * System - system functions
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
    var System = Class.extend({
        init: function () {
            this.settings = {
                message_box: 'msg-box', // 'alert-box', 'alert-block-box'
                ttl_jstorage: 3600000   // 1h=3600000
            };
            //---------------
            try {
                // Init template
                this.initTemplate();
                // Init VarDumper
                this.initVarDumper();
            } catch (ex) {
                if (ex instanceof Error) {
                    this.onFailure(ex.name + ": " + ex.message);
                }
            } finally {
            }
        },
        /** Function template settings
         *
         *   var tmpl = _.template("Hello {{ name }}!");
         *   return tmpl({name : "Mustache"});
         *   // returns "Hello Mustache!"
         *   OR
         *   return _.template("Hello {{ name }}!")({name : "Mustache"});
         *   // returns "Hello Mustache!"
         *   
         **/
        initTemplate: function ()
        {
            _.templateSettings = {
                interpolate: /\{\{([\s\S]+?)\}\}/g, ///<%=([\s\S]+?)%>/g
                evaluate: /<%([\s\S]+?)%>/g,
                escape: /<%-([\s\S]+?)%>/g
            };
        },
        /** Function ini VarDumper
         *  set css.z-index = 0
         *   
         **/
        initVarDumper: function ()
        {
            $("pre.sf-dump").css("z-index", 0);
        },
        //====== Message functions ====//

        /**
         * Show message
         * 
         * @param string class_message (alert_warning|alert-warning, alert_danger|alert-danger, alert_success|alert-success, alert_info|alert-info)
         * @param array messages
         * @param int delay_clear Delay in milliseconds, that is 1000 is a 1 second
         * @param string type_box ('alert_box', 'alert_block_box')
         */
        messagebox_write: function (class_message, messages, delay_clear, type_box) {
            var htmlBox, htmlMessages = "", titleMessages = "", strMessage = "";
            var params = {};
            var self = this;
            var idBox = this.settings.message_box;
            //---------------------

            // Container type: 'msg_box', 'alert_box', 'alert_block_box'
            if (type_box) {
                idBox = type_box.replace(/_/g, "-");
                class_message = class_message.replace(/_/g, "-");
            } else {
                class_message = class_message.replace(/-/g, "_");
            }

            // Exit, else not message box
            var message_box = $('#' + idBox);
            if (!message_box.size()) {
                return;
            }

            // Clear message
            message_box.empty();

            // Set message
            $.each(messages, function (i, message) {
                if (message) {
                    strMessage = message.replace(/&lt;/g, "<");
                    strMessage = strMessage.replace(/&gt;/g, ">");
                    htmlMessages = htmlMessages + strMessage + '<br />';
                }

            });

            // Get message title
            if (app.lb) {
                titleMessages = app.lb.trans(class_message);
            } else {
                var msgs = this.getMessages('div.msg-box p');
                titleMessages = msgs[class_message];
            }

            // Get html message
            params.class_message = class_message;
            params.title = titleMessages;
            params.message = htmlMessages;

            htmlBox = this.messagebox_html(params, type_box);

            // Add message to DOM
            message_box.html(htmlBox);

            // Set event for close message
            var elClose = message_box.find("button").eq(0);
            if (elClose) {
                elClose.one('click', function () {
                    elClose.unbind('click');
                    setTimeout($.proxy(self.messagebox_delay_clear, self), 0);
                });
            }

            // Show message
            message_box.show();

            // Message Box clear with delay
            if (delay_clear) {
                message_box.hide(delay_clear, function () {
                    message_box.empty();
                });
            }
        },
        // Clear message with delay
        messagebox_delay_clear: function (delay, idBox) {
            var msgBox;
            //----------------
            if (idBox) {
                msgBox = $('#' + idBox);
            } else {
                msgBox = $('#' + this.settings.message_box);
            }

            if (!delay) {
                delay = 1000;
            }
            if (msgBox.size()) {
                msgBox.hide(delay, function () {
                    msgBox.empty();
                });
            }
        },
        /**
         * HTML message
         * 
         * @param object params {class_message: 'alert_warning', title: 'My title', message: 'My message'}
         * @param string type_box ('msg_box', 'alert_box', 'alert_block_box')
         * @returns string
         */
        messagebox_html: function (params, type_box) {
            var tmpl = "";
            //--------------------
            // Type box: 'msg_box', 'alert_box', 'alert_block_box'
            switch (type_box) {
                case 'alert_box':
                    tmpl = ''
                            + '<div class="alert {{ class_message }} alert-dismissable">'
                            + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                            + '<strong>{{ title }}</strong> {{ message }}'
                            + '</div>';
                    break
                case 'alert_block_box':
                    tmpl = ''
                            + '<div class="alert {{ class_message }} alert-dismissable">'
                            + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                            + '<h4>{{ title }}</h4>'
                            + '{{ message }}'
                            + '</div>';
                    break
                default:
                    tmpl = ''
                            + '<div class="alert {{ class_message }} alert-dismissable">'
                            + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                            + '<h5 class="{{ class_message }}">{{ title }}</h5>'
                            + '{{ message }}'
                            + '</div>';
            }

            //var html = $.tmpl(tmpl, params);
            var tmpl = _.template(tmpl);
            var html = tmpl(params);

            return html;
        },
        /**
         * Get messages from containers
         * 
         * @param string containers ('div.msg-box p')
         * @returns object
         */
        getMessages: function (containers) {
            var oMessages = {};
            var msgs = $(containers);
            if (msgs.size()) {
                msgs.each(function () {
                    var id, msg;
                    var p = $(this);
                    id = p.attr('id');
                    msg = p.html();
                    oMessages[id] = msg;
                });
            }
            return oMessages;
        },
        //====== Ajax functions ====//

        /**
         * Check Ajax Data
         * 
         * @param string|object data
         * @returns Boolean
         */
        checkAjaxData: function (data) {

            if (_.isString(data)) {
                this.onFailure(data);
                return false;
            }

            if ( _.isObject(data) && data.class_message){
                this.onFailure(data);
                return false;
            }
            
            return true;

        },
        /**
         * Show Ajax Error
         * 
         * @param {type} XMLHttpRequest
         * @param {type} textStatus
         * @param {type} errorThrown
         * @returns {undefined}
         */
        showAjaxError: function (XMLHttpRequest, textStatus, errorThrown) {
            var messages = [
                '<strong><em>' + errorThrown + '</em></strong>',
                'Error status: ' + textStatus,
                XMLHttpRequest.responseText];
            this.messagebox_write('alert_danger', messages);
        },
        /**
         * Ajax send get
         * 
         * @param string url
         * @param object data
         * @param function callback
         * @param bool isAsync
         * @returns {undefined}
         */
        get: function (url, data, callback, isAsync) {
            try {

                // Set defaults args
                data = data || {};
                if (_.isUndefined(isAsync)) {
                    isAsync = true;
                }

                // Make ajax request to the server
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    async: isAsync,
                    context: this,
                    success: function (jsonData) {
                        if (this.checkAjaxData(jsonData)) {
                            if (callback) {
                                callback(jsonData);
                            }
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        this.showAjaxError(XMLHttpRequest, textStatus, errorThrown);
                    },
                    cache: false
                });
            } catch (ex) {
                if (ex instanceof Error) { // Это экземпляр Error или подкласса?
                    this.onFailure(ex.name + ": " + ex.message);
                }
            }
        },
        /**
         * Ajax send post
         * 
         * @param string url
         * @param object data
         * @param function callback
         * @param bool isAsync
         * @returns {undefined}
         */
        post: function (url, data, callback, isAsync) {
            try {

                // Set defaults args
                data = data || {};
                if (_.isUndefined(isAsync)) {
                    isAsync = true;
                }

                // Make ajax request to the server
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    async: isAsync,
                    context: this,
                    success: function (jsonData) {
                        if (this.checkAjaxData(jsonData)) {
                            if (callback) {
                                callback(jsonData);
                            }
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        this.showAjaxError(XMLHttpRequest, textStatus, errorThrown);
                    },
                    cache: false
                });
            } catch (ex) {
                if (ex instanceof Error) { // Это экземпляр Error или подкласса?
                    this.onFailure(ex.name + ": " + ex.message);
                }
            }
        },
        //====== Additional functions ====//

        /**
         * Generate four random hex digits.
         * 
         * @returns string
         */
        S4: function () {
            return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
        },
        /**
         * Generate a pseudo-GUID by concatenating random hexadecimal.
         * guid = {b1eaa119-3fb0-a9f2-4381-762fb4c464bb}
         * 
         * @returns string
         */
        getGUID: function () {
            return ("{" + this.S4() + this.S4() + "-" + this.S4() + "-" + this.S4() + "-" + this.S4() + "-" + this.S4() + this.S4() + this.S4() + "}");
        },
        
        //====== Error functions ====//
        /**
         * Error event
         * 
         * @param object message
         * @param int delay_clear
         * @returns void
         */
        onFailure: function (message, delay_clear) {
            var msgs;
            //-------------
            
            if(_.isString(message)){
                this.messagebox_write('alert_danger', [message], delay_clear);
                return;
            }
            
            if (_.isObject(message) && message.class_message){
                msgs = message.messages;
                this.messagebox_write(message.class_message, msgs, delay_clear);
                return;
            }
            
            if (_.isObject(message) &&  message.responseJSON ) {
                this.onFailure(message.responseJSON, delay_clear);
                return;
            }

            if (_.isObject(message) &&  message.responseText ) {
                this.onFailure(message.responseText, delay_clear);
                return;
            }
        }
    });

    return System;
});
