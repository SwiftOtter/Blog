(function($){
    if (!$.SwiftOtter) {
        $.SwiftOtter = {};
    }

    $.SwiftOtter.Blog = function(el, options) {
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("SwiftOtter.Blog", base);

        base.options = options;

        base.init = function() {
            base.$form = base.$el.find('form');
            base.$list = base.$el.find('.comment-list');
            base.$addComment = base.$el.find('.add-new-comment');
            base.$success = base.$el.find('.comment-success');

            base.$form.on('submit', function(e) {
                try {
                    base.update.call(base, e);
                } finally {
                    e.preventDefault();
                }
            });

            base.$addComment.click(base.toggleCommentBox);

            return base;
        };

        base.handleSuccess = function(response) {
            base.$success.show(200);

            // Can we trust this data?
            base.$list.append(jQuery.parseHTML(response.html)).fadeIn('slow');
            base.$list.find('.no-items').remove();
            base.toggleCommentBox();
            base.$form.find('.comment-text').val('');

            if (typeof grecaptcha !== undefined) {
                grecaptcha.reset();
            }
        };

        base.toggleCommentBox = function() {
            base.$form.toggle(600);
            base.$addComment.toggle(700);
        };

        base.obtainErrorCode = function(error) {
            var errorCode;

            if (typeof error === "string" && error.split("\n")[1]) {
                errorCode = error.split("\n")[1].match("[0-9]+$");
            }

            if (!errorCode || errorCode < 0) {
                errorCode = undefined;
            }

            return errorCode;
        };

        base.removeError = function() {
            base.$form.find('.error').fadeOut('slow', function() {
                $(this).remove();
            });

        };

        base.displayError = function(errorMessage) {
            base.$success.hide();

            var $error = base.$form.find('.error');

            if ($error.length === 0) {
                $error = $('<div class="error error-msg"></div>');
            }

            $error.text(errorMessage);

            base.$form.prepend($error);

            setTimeout(function() {
                base.removeError();
            }, 10000);

            if (typeof grecaptcha !== undefined) {
                grecaptcha.reset();
            }
        };

        base.obtainErrorFromType = function(error) {
            var errorCode = parseInt(base.obtainErrorCode(error)),
                errorMessage;

            switch (true) {
                case (errorCode < 400):
                    errorMessage = base.options.error;
                    break;
                case (errorCode === 409):
                    errorMessage = base.options.errorDuplicate;
                    break;
                case (errorCode >= 400 && errorCode < 500):
                    errorMessage = base.options.error400;
                    break;
                case (errorCode >= 500 && errorCode < 600):
                    errorMessage = base.options.error500;
                    break;
            }

            return errorMessage;
        };

        base.handleError = function(error) {
            var errorMessage = error,
                transcribedMessage = base.obtainErrorFromType(error),
                $submit = base.$el.find('button[type="submit"]');

            $submit.removeClass('disabled').attr('disabled', false);

            if (transcribedMessage !== undefined) {
                errorMessage = transcribedMessage;
            }

            base.displayError(errorMessage);
        };

        base.update = function() {
            var $submit = base.$el.find('button[type="submit"]'),
                originalText = $submit.text(),
                url = base.$form.attr('action');

            $submit.text(base.options.submittingText).addClass('disabled').attr('disabled', true);

            $.ajax(url, {
                data: base.$form.serialize(),
                method: "POST",
                complete: function(response) {
                    var json = {};
                    $submit.text(originalText).removeClass('disabled').attr('disabled', false);

                    if (!response.hasOwnProperty('responseJSON')) {
                        json = JSON.parse(response.responseText);
                    } else {
                        json = response.responseJSON;
                    }

                    if (response.success() && json.html) {
                        base.handleSuccess(json);
                    } else {
                        base.handleError(json.error);
                    }
                }
            });

            return false;
        };

        // Run initializer
        base.init();
    };

    $.fn.blog = function(options){
        return this.each(function(){
            (new $.SwiftOtter.Blog(this, options));
        });
    };
})(jQuery);
