(function ( $ ) {
    // decouple to settings: messages, ire_options (was constructor params)
    
    var InboxifySubscribe = function(form, messages, ire_options) {
        this.$form = $(form);
        this.messages = messages;
        this.ire_options = ire_options;
        
        var that = this;
        this.$form.on("submit", function(e) {
            that.onSubmit(e);
        });
    };
    
    InboxifySubscribe.CLS_BUTTON = "inboxify-button";
    InboxifySubscribe.CLS_ERROR = "inboxify-invalid";
    InboxifySubscribe.CLS_INPUT = "inboxify-input";
    InboxifySubscribe.CLS_PROGRESS = "inboxify-progress";
    InboxifySubscribe.CLS_REQUIRED = "inboxify-required";
    InboxifySubscribe.CLS_SUCCESS = "inboxify-success";
    InboxifySubscribe.CLS_VAL_EMAIL = "validate-email";

    InboxifySubscribe.prototype = {
        $form: null,
        messages: null,
        ire_options: null,
        
        addFormMessage: function(message, type) {
            if ("undefined" == typeof(type)) {
                type = InboxifySubscribe.CLS_ERROR;
            }

            if (!this.$form.prev().hasClass(InboxifySubscribe.CLS_ERROR)) {
                this.$form.before('<span class="' + type + '">' + message + '</span>');
            }
        },

        ajax: function() {
            var data = {
                action: "inboxify_subscribe"
            };
            var inputs = this.$form.find("." + InboxifySubscribe.CLS_INPUT);
            var ire = this.$form.find("#iy-ire-wrapper");
            var re = this.$form.find('.g-recaptcha-response');
            var re2 = this.$form.find("#recaptcha_challenge_field");
            var re2_response = this.$form.find("#recaptcha_response_field");
            var si = this.$form.find('input[name*="si_code"]');
            var that = this;

            $.each(inputs, function(i, e) {
                var $e = $(e);

                if (!$e.attr('data-name')) {
                    return;
                }

                if ( ( "checkbox" == $e.attr("type") || "radio" == $e.attr("type") )
                        && !$e.is(":checked")
                ) {
                    return;
                }

                // array values
                if ($e.attr('data-name') in data) {
                    if ("object" == typeof(data[$e.attr('data-name')])) {
                        // add to array
                        data[$e.attr('data-name')].push($e.val());
                    } else {
                        // convert to array
                        data[$e.attr('data-name')] = [
                            data[$e.attr('data-name')],
                            $e.val()
                        ];
                    }
                // string values
                } else {
                    data[$e.attr('data-name')] = $e.val();
                }
            });
            
            //console.log("ire, re, re2, si", ire, re, re2, si);
            
            // add captcha plug-ins data if any
            if (ire.length) { 
                // process invisible recaptcha
                var holderId = grecaptcha.render(
                    ire[0],
                    {
                        'sitekey': this.ire_options.siteKey,
                        'size': 'invisible',
                        'badge' : this.ire_options.badgePosition,
                        'callback' : function (recaptchaToken) {
                            //console.log("gre callback", recaptchaToken);
                            data['g-recaptcha-response'] = recaptchaToken;

                            that.$form.addClass('inboxify-progress');
                            that.$form.find('.inboxify-button').attr('disabled', 'disabled');

                            $.ajax(ajaxurl, {
                                data: data,
                                method: "POST",
                            })
                            .done(function(data, textStatus, jqXHR) {
                                that.onAjaxDone(data, textStatus, jqXHR);
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                that.onAjaxFail(jqXHR, textStatus, errorThrown);
                            });
                        },
                        'expired-callback' : function() {
                            grecaptcha.reset(holderId);
                        }
                    }
                );

                grecaptcha.execute(holderId);
            } else {
                if (re.length) { data['captcha'] = re[0].value; }
                if (re2.length) { data['captcha'] = re2[0].value; data['captcha_response'] = re2_response[0].value; }
                if (si.length) { data[si[0].name] = si[0].value; }

                this.$form.addClass('inboxify-progress');
                this.$form.find('.inboxify-button').attr('disabled', 'disabled');

                $.ajax(ajaxurl, {
                    data: data,
                    method: "POST",
                })
                .done(function(data, textStatus, jqXHR) {
                    that.onAjaxDone(data, textStatus, jqXHR);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    that.onAjaxFail(jqXHR, textStatus, errorThrown);
                });
            }
        },

        removeFormMessage: function() {
            if (this.$form.prev().hasClass(InboxifySubscribe.CLS_ERROR)) {
                this.$form.prev().remove();
            }
        },
        
        onAjaxDone: function(data, textStatus, jqXHR) {
            this.$form.removeClass(InboxifySubscribe.CLS_PROGRESS);
            this.$form.find('.' + InboxifySubscribe.CLS_BUTTON).removeAttr('disabled');

            if ( data === "0" ) {
                this.addFormMessage(this.messages.message_error);
                return;
            }

            var response = JSON.parse( data );

            if ("object" == typeof(response) && "undefined" != typeof(response.success) && response.success ) {
                this.addFormMessage(this.messages.message_success, InboxifySubscribe.CLS_SUCCESS);
                this.$form.slideUp();
            } else if ("undefined" != typeof(response.exception)) {
                this.addFormMessage(this.messages.message_error);
            } else if ( "undefined" != typeof(response.invalid)) {
                //console.log('err2');
                var that = this;

                $.each(response.invalid, function(i, e) {
                    if ("captcha" == e) {
                        var $e = $( that.$form.find('div.inboxify-captcha')[0] );
                    } else {
                        var $e = $( that.$form.find('[data-name=' + e + ']')[0] );
                    }

                    that.addElementMessage($e, that.messages.message_invalid);
                });

                this.addFormMessage(this.messages.message_invalid_form);
            } else {
                this.addFormMessage(this.messages.message_error);
            }
        },
    
        onAjaxFail: function(jqXHR, textStatus, errorThrown) {
            this.$form.removeClass(InboxifySubscribe.CLS_PROGRESS);
            this.$form.find('.' + InboxifySubscribe.CLS_BUTTON).removeAttr('disabled');

            this.addFormMessage(this.messages.message_error);
        },
        
        onSubmit: function(e) {
            //console.log('sub');
            e.preventDefault();

            if (this.validate()) {
                this.removeFormMessage();
                this.ajax();
            } else {
                e.stopPropagation();
                this.addFormMessage(this.messages.message_invalid_form);
            }
        },
        
        addElementMessage: function($element, message) {
            $element.addClass(InboxifySubscribe.CLS_REQUIRED);

            if (!$element.next('span').length) {
                $element.after('<span class="' + InboxifySubscribe.CLS_ERROR + '">' + message + '</span>');
            }
        },
        
        removeElementMessage: function($element) {
            $element.removeClass(InboxifySubscribe.CLS_REQUIRED);
            $element.next('span').remove();
        },
        
        validate: function() {
            var inputs = this.$form.find('.' + InboxifySubscribe.CLS_INPUT);
            var that = this;
            var valid = true;
            var tags = [];

            $.each(inputs, function(i, e) {
                var $e = $(e);

                if ( ( "checkbox" == $e.attr("type") || "radio" == $e.attr("type") ) ) {
                    tags.push($e);
                    return;
                }

                if ($e.attr('required') && ( !$e.val() || !that.validateEmailElement($e) ) ) {
                    valid = false;
                    //console.log($e, messages);

                    that.addElementMessage($e, that.messages.message_invalid);
                } else {
                    that.removeElementMessage($e);
                }
            });
            
            if (tags.length && tags[0].attr("required")) {
                var hasTag = false;
                
                $.each(tags, function(i, $e) {
                    if ($e.is(":checked")) {
                        hasTag = true;
                    }
                });
                
                var lastTag = tags[tags.length - 1].parent();
                
                if (!hasTag) {
                    valid = false;
                    that.addElementMessage(lastTag, that.messages.message_invalid);
                } else {
                    that.removeElementMessage(lastTag);
                }
            }
            
            if (valid) {
                that.removeFormMessage();
            }

            return valid;
        },
        
        validateEmailElement($element) {
            if (!$element.hasClass(InboxifySubscribe.CLS_VAL_EMAIL)) {
                return true;
            }

            var email = $element.val();

            return email.indexOf('@') > 0 && email.indexOf('.') > 0;
        }
    };

    $.fn.inboxifySubscribe = function(messages, ire_options) {
        return this.each(function() {
            new InboxifySubscribe(this, messages, ire_options);
        });
    };

}( jQuery ));
