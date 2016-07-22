define(['jquery'], function ($) {
    /**
     * MaskInput - setting mask input form 
     * 
     * $.mask.definitions['~'] = "[+-]";
     * $("#date").mask("99/99/9999",{completed:function(){alert("completed!");}});
     * $("#phone").mask("(999) 999-9999");
     * $("#phoneExt").mask("(999) 999-9999? x99999");
     * $("#iphone").mask("+33 999 999 999");
     * $("#tin").mask("99-9999999");
     * $("#ssn").mask("999-99-9999");
     * $("#product").mask("a*-999-a999", { placeholder: " " });
     * $("#eyescript").mask("~9.99 ~9.99 999");
     * $("#po").mask("PO: aaa-999-***");
     * $("#pct").mask("99%");
     *
     * JavaScript
     *
     * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
     * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
     * @link     https://github.com/bsa-git/silex-mvc/
     */
    var MaskInput = Class.extend({
        init: function (params) {
            try {

                if (!params) {
                    return;
                }

                this.$params = params;

                this.iniMaskInput();

            } catch (ex) {
                if (ex instanceof Error) {
                    app.sys.onFailure(ex.name + ": " + ex.message);
                }

            }
        },
        iniMaskInput: function () {
            
            var definitions = this.$params.definitions;
            if (definitions) {
                $.each(definitions, function () {
                    var arrDef = this.split('=');
                    var key = arrDef[0];
                    var val = arrDef[1];
                    $.mask.definitions[key] = val;
                });
            }

            var masks = this.$params.masks;
            if (masks) {
                $.each(masks, function (id, mask) {
                    var input = $("input#" + id);
                    if (!input.size()) {
                        var sel = _.template("[name='{{id}}']")({id: id});
                        input = $(sel);
                    }
                    if (mask.length > 1) {
                        input.mask(mask[0], mask[1]);
                    } else {
                        input.mask(mask[0]);
                    }
                });
            }


            var mask_list = this.$params.mask_list;
            if (mask_list) {
                $.each(mask_list, function () {
                    var items = this;
                    var ids = items['ids'];
                    var mask_ = items['mask'];
                    $.each(ids, function (i, id) {
                        var input = $(id);
                        if (input.size()) {
                            if (mask_.length > 1) {
                                input.mask(mask_[0], mask_[1]);
                            } else {
                                input.mask(mask_[0]);
                            }
                        }
                    });
                });
            }
        }
    });
    return MaskInput;
});