define(['jquery'], function ($) {
    /**
     * FormValidation - validation form input values
     *
     *
     * JavaScript
     *
     * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
     * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
     * @link     https://github.com/bsa-git/silex-mvc/
     */
    var FormValidation = Class.extend({
        init: function (params) {
            try {

                if (!params) {
                    return;
                }

                this.$params = params;
                this.$options = {};
                this.$validator = null;
                this.$form = null;

                // get form params
                this.$frmParams = app.sys.getMessages('#frm-params p');

                this.iniFormValidation();

            } catch (ex) {
                if (ex instanceof Error) {
                    app.sys.onFailure(ex.name + ": " + ex.message);
                }

            }
        },
        iniFormValidation: function () {

            //------------------
            var form = $(this.$params.form);
            if (form.size()) {

                this.$form = form;

                //---- Validation events -----//
                if (this.$params.onsubmit === false) {
                    this.$options['onsubmit'] = this.$params.onsubmit;
                }
                if (this.$params.onfocusout === false) {
                    this.$options['onfocusout'] = this.$params.onfocusout;
                }
                if (this.$params.onkeyup === false) {
                    this.$options['onkeyup'] = this.$params.onkeyup;
                }
                if (this.$params.onclick === false) {
                    this.$options['onclick'] = this.$params.onclick;
                }

                //---- Rules, messages, class -----//
                if (this.$params.messages) {
                    this.$options['messages'] = this.iniMessages(this.$params.messages);
                }

                if (this.$params.id_messages) {
                    this.$options['messages'] = this.iniIdMessages(this.$params.id_messages);
                }

                if (this.$params.rules) {
                    this.$options['rules'] = this.iniRules(this.$params.rules);
                }

                if (this.$params.id_rules) {
                    this.$options['rules'] = this.iniIdRules(this.$params.id_rules);
                }

                if (this.$params.class_rules) {
                    var class_rules = this.iniRules(this.$params.class_rules);
                    $.validator.addClassRules(class_rules);
                }

                if (this.$params.is_validate === false) {
                    return;
                } else {
                    this.$validator = form.validate(this.$options);
                }

            }
        },
        Validate: function () {

            if (this.$form.size()) {
                this.$validator = this.$form.validate(this.$options);
            }
        },
        iniRules: function (aRules) {
            var self = this;
            var index, rules = {};
            var arrRules = ['minlength', 'maxlength', 'rangelength', 'min', 'max', 'range', 'require_from_select'];
            //------------------
            $.each(aRules, function (name, rule) {
                rules[name] = {};
                $.each(rule, function (rule_name, rule_value) {

                    if ($.inArray(rule_name, arrRules) !== -1) {
                        if (_.isArray(rule_value)) {
                            if (_.isString(rule_value[0])) {
                                index = rule_value[0];

                                if (self.$frmParams[index]) {
                                    rule_value[0] = self.$frmParams[index];
                                }

                                if (app.lb.trans(index)) {
                                    rule_value[0] = app.lb.trans(index);
                                }

                                if (!rule_value[0]) {
                                    rule_value[0] = index;
                                }

                            }
                            if (_.isString(rule_value[1])) {
                                index = rule_value[1];

                                if (self.$frmParams[index]) {
                                    rule_value[1] = self.$frmParams[index];
                                }

                                if (app.lb.trans(index)) {
                                    rule_value[1] = app.lb.trans(index);
                                }

                                if (!rule_value[1]) {
                                    rule_value[1] = index;
                                }

                            }
                            rules[name][rule_name] = rule_value;
                        }
                        if (_.isString(rule_value)) {

                            if (self.$frmParams[rule_value]) {
                                rules[name][rule_name] = self.$frmParams[rule_value];
                            }

                            if (app.lb.trans(rule_value)) {
                                rules[name][rule_name] = app.lb.trans(rule_value);
                            }

                            if (!rules[name][rule_name]) {
                                rules[name][rule_name] = rule_value;
                            }

                        }
                        if (_.isNumber(rule_value)) {
                            rules[name][rule_name] = rule_value;
                        }
                    } else {
                        if (rule_name === 'remote') {
                            rule_value['url'] = app.lb.urlBase + rule_value['url'];
                            if (rule_value['data']) {
                                var data = rule_value['data'];
                                $.each(data, function (key, value) {
                                    if ($(value).size()) {
                                        rule_value['data'][key] = function () {
                                            return $(value).val();
                                        }
                                    } else {
                                        rule_value['data'][key] = value;
                                    }
                                });
                            }
                            rules[name][rule_name] = rule_value;
                        } else {
                            rules[name][rule_name] = rule_value;
                        }
                    }
                });
            });
            return rules;
        },
        iniIdRules: function (aRules) {
            var input, name, rules = {};
            //------------------
            $.each(aRules, function (name, rule) {
                input = $('#' + name);
                if (input.size()) {
                    name = input.attr('name');
                    rules[name] = rule;
                }
            });
            return this.iniRules(rules);
        },
        iniMessages: function (aMessages) {
            var self = this;
            var messages = {};
            //------------------
            $.each(aMessages, function (name, message) {
                if (_.isObject(message)) {
                    messages[name] = {};
                    $.each(message, function (rule, msg_rule) {

                        if (self.$frmParams[msg_rule]) {
                            messages[name][rule] = self.$frmParams[msg_rule];
                        }

                        if (app.lb.trans(msg_rule)) {
                            messages[name][rule] = app.lb.trans(msg_rule);
                        }

                        if (!messages[name][rule]) {
                            messages[name][rule] = msg_rule;
                        }

                    });
                } else {

                    if (self.$frmParams[message]) {
                        messages[name] = self.$frmParams[message];
                    }

                    if (app.lb.trans(message)) {
                        messages[name] = app.lb.trans(message);
                    }

                    if (!messages[name]) {
                        messages[name] = message;
                    }
                }
            });
            return messages;
        },
        iniIdMessages: function (aMessages) {
            var input, name, messages = {};
            //------------------
            $.each(aMessages, function (name, message) {
                input = $('#' + name);
                if (input.size()) {
                    name = input.attr('name');
                    messages[name] = message;
                }
            });
            return this.iniMessages(messages);
        }
    });
    return FormValidation;
});
