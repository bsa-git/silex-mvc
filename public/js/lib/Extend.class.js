/**
 * Syntax:
 * Class.extend(props)
 * Class.extend(props, staticProps)
 * Class.extend([mixins], props)
 * Class.extend([mixins], props, staticProps)
 */
(function () {

    window.Class = function () { /* all magic - in Class.extend */
    };


    Class.extend = function (props, staticProps) {

        var mixins = [];

        // if the first argument - an array, then reassign arguments    
        if ({}.toString.apply(arguments[0]) == "[object Array]") {
            mixins = arguments[0];
            props = arguments[1];
            staticProps = arguments[2];
        }

        // This function will be returned as a result of the extend
        function Constructor() {
            this.init && this.init.apply(this, arguments);
        }

        // this - is a class "to the point", which called for extend (Animal.extend)
        // inherit from him:
        Constructor.prototype = Class.inherit(this.prototype);

        // constructor was substituted by calling inherit
        Constructor.prototype.constructor = Constructor;

        // add to inherit more
        Constructor.extend = Class.extend;

        // copied to the static properties Constructor
        copyWrappedProps(staticProps, Constructor, this);

        // copy in Constructor.prototype properties of mixins and props
        for (var i = 0; i < mixins.length; i++) {
            copyWrappedProps(mixins[i], Constructor.prototype, this.prototype);
        }
        copyWrappedProps(props, Constructor.prototype, this.prototype);

        return Constructor;
    };


    //---------- helper methods ----------

    // fnTest -- regular expression that checks the function 
    // of the fact whether there is a code in its call _super
    // 
    // for his announcement, we check whether the function supports the conversion 
    // of the code calling the toString: /xyz/.test(function() {xyz})
    // in rare mobile browsers - are not supported, so the result will be /./
    var fnTest = /xyz/.test(function () {
        xyz
    }) ? /\b_super\b/ : /./;


    // copies the properties of the props in targetPropsObj 
    // third argument - a parent properties
    // 
    // copying, if it is found out that the property exists in the parent too,
    // and is a function - call it wrapped in a wrapper,
    // which puts on the parent method this._super, 
    // then calls it, then returns this._super
    function copyWrappedProps(props, targetPropsObj, parentPropsObj) {
        if (!props)
            return;

        for (var name in props) {
            if (typeof props[name] == "function"
                    && typeof parentPropsObj[name] == "function"
                    && fnTest.test(props[name])) {
                // скопировать, завернув в обёртку
                targetPropsObj[name] = wrap(props[name], parentPropsObj[name]);
            } else {
                targetPropsObj[name] = props[name];
            }
        }

    }

    // returns a wrapper around method, which puts this._super to the parent 
    // and then returns it
    function wrap(method, parentMethod) {
        return function () {
            var backup = this._super;

            this._super = parentMethod;

            try {
                return method.apply(this, arguments);
            } finally {
                this._super = backup;
            }
        }
    }

    // Object.create emulation for old IE
    Class.inherit = Object.create || function (proto) {
        function F() {
        }
        F.prototype = proto;
        return new F;
    };
})();
