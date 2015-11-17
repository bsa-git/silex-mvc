(function (factory) {
    if (typeof define === "function" && define.amd) {
        define(["jquery", "../jquery.validate"], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {

    /*
     * Translated default messages for the jQuery validation plugin.
     * Locale: RU (Russian; русский язык)
     */
    $.extend($.validator.messages, {
        required: "This field is required.",
        remote: "Please fix this field.",
        email: "Please enter a valid email address.",
        url: "Please enter a valid URL.",
        date: "Please enter a valid date.",
        dateISO: "Please enter a valid date ( ISO ).",
        number: "Please enter a valid number.",
        digits: "Please enter only digits.",
        creditcard: "Please enter a valid credit card number.",
        equalTo: "Please enter the same value again.",
        maxlength: $.validator.format("Please enter no more than {0} characters."),
        minlength: $.validator.format("Please enter at least {0} characters."),
        rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
        range: $.validator.format("Please enter a value between {0} and {1}."),
        max: $.validator.format("Please enter a value less than or equal to {0}."),
        min: $.validator.format("Please enter a value greater than or equal to {0}."),
        integer: "Please enter a positive or negative integer.",
        alphanumeric: "Please enter the letters, numbers or underscores.",
        lettersonly: "Please enter only alphabetic characters.",
        letterswithbasicpunc: "Please enter the letters or punctuation.",
        dateRU: "Please type the date in the format 01.12.2014",
        phoneRU: "Please enter a phone number in a format +38XXXXXXXXXX",
        require_from_select: "Please select from the list value",
        equal: "The value entered not match the required",
        require_if_select: $.validator.format('This field must be filled, if selected "{0}".'),
        edrpou: "Please enter the correct code EDRPOU",
        cents_for_dollar: "Please enter the correct number of cents",
        minlength_with_cleaning: $.validator.format("Please enter at least {0} characters."),
        required_with_cleaning: "This field is required."
    });

}));