function bmsortable(sort_id, table_id) {
    var $tbody = jQuery("#" + table_id + " tbody");

    $tbody.sortable({
        cursor: "move",
        stop: function() {
            var sortables = [];
            var $sortables = jQuery("#" + table_id + " .bmsortables");

            $sortables.each(function(i, e) {
                var id = bmfieldid( jQuery(e).attr("name") );
                sortables.push(id);
            });

            jQuery("#" + sort_id).val(sortables);
        }
    });

    //$("#" + id + " tbody *").disableSelection();
}

function bmfieldid(element_name) {
    var regex = /\[(\w+)\]$/;
    var matches = element_name.match(regex);

    return matches[1];
}

(function($) {
    var InboxifySettings = function(element) {
        this.$form = $(element);
        
        this.$fieldEndpoint = $("#api_end_point");
        this.$fieldKey = $("#api_key");
        this.$fieldList = $("#list");
        this.$fieldSecret = $("#api_secret");
        this.$fieldTtl = $("#ttl");
        this.$messages = $("#inboxify-messages");
        this.$status = $("#status");
        
        this.init();
    };
    
    InboxifySettings.TIMEOUT = 1000;
    
    InboxifySettings.prototype = {
        $form: null,
        $fieldEndpoint: null,
        $fieldKey: null,
        $fieldList: null,
        $fieldSecret: null,
        $fieldTtl: null,
        $messages: null,
        $status: null,
        locked: false,
        timer: null,
        
        ajaxDone: function(data, textStatus, jqXHR) {
            //console.log("done", data, textStatus, jqXHR);
            
            this.unlock();
            
            if ( data === "0" ) {
                alert(inboxify.error_message);
                return;
            }

            var response = JSON.parse( data );

            if ("object" == typeof(response) && "undefined" != typeof(response.success) && response.success ) {
                // console.log("OK", response);
                
                this.resetFieldList();
                
                var that = this;
                $.each(response.lists, function(i, e) {
                    var selected = response.list == i ? ' selected="selected"' : '';
                    var option = '<option value="' + i + '"' + selected + '>' + e + '</option>';
                    
                    that.$fieldList.append(option);
                });
                
                this.ajaxMessage(inboxify.ok);
            } else if ("undefined" != typeof(response.exception)) {
                //console.log("error exception");
                this.ajaxMessage(inboxify.error_message);
            } else if (!response.success) {
                //console.log("error bad credentials");
                this.ajaxMessage(inboxify.error_message);
            } else {
                //console.log("error generic");
                this.ajaxMessage(inboxify.error_message);
            }
        },
        
        ajaxFail: function(jqXHR, textStatus, errorThrown) {
            //console.log("fail", jqXHR, textStatus, errorThrown);
            this.unlock();
            this.ajaxMessage(inboxify.error_message);
        },
        
        ajaxMessage: function(message) {
            this.$status.replaceWith("<p id='status'>" + message + "</p>");
            this.$status = $("#status");
        },
        
        init: function() {
            var that = this;
            
            this.$fieldEndpoint.on("change", function() {
                that.onFieldChange();
            });
            this.$fieldKey.on("change", function() {
                that.onFieldChange();
            });
            this.$fieldSecret.on("change", function() {
                that.onFieldChange();
            });
            
            this.$fieldEndpoint.on("keyup", function() {
                that.onFieldChange();
            });
            this.$fieldKey.on("keyup", function() {
                that.onFieldChange();
            });
            this.$fieldSecret.on("keyup", function() {
                that.onFieldChange();
            });
            
            this.refresh();
        },
        
        onFieldChange: function() {
            this.scheduleRefresh();
        },
        
        refresh: function() {
            this.lock();
            
            var data = {
                action: "inboxify_api_validate",
                end_point: this.$fieldEndpoint.val(),
                key: this.$fieldKey.val(),
                secret: this.$fieldSecret.val(),
                ttl: this.$fieldTtl.val()
            };
            
            // only check credentials if we have all fields set
            if (!data.end_point || !data.key || !data.secret) {
                this.resetFieldList(true);
                this.unlock();
                return;
            }
            
            var that = this;

            $.ajax(ajaxurl, {
                data: data,
                method: "POST",
            }).done(function(data, textStatus, jqXHR) {
                that.ajaxDone(data, textStatus, jqXHR);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                that.ajaxFail(jqXHR, textStatus, errorThrown);
            });
        },
        
        resetFieldList: function(error) {
            if ("undefined" == typeof(error)) {
                error = false;
            }
            
            var label = error ? inboxify.no_credentials : inboxify.select_list;
            this.$fieldList.html('');
            this.$fieldList.append('<option value="0">' + label + '</option>');
        },
        
        scheduleRefresh: function() {
            if (this.locked) {
                return;
            }
            
            var that = this;
            
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            
            this.timer = setTimeout(function() {
                that.refresh();
            }, InboxifySettings.TIMEOUT);
        },
        
        lock: function() {
            this.locked = true;
            
            this.$fieldEndpoint.attr("disabled", "disabled");
            this.$fieldKey.attr("disabled", "disabled");
            this.$fieldList.attr("disabled", "disabled");
            this.$fieldSecret.attr("disabled", "disabled");
        },
        
        unlock: function() {
            this.$fieldEndpoint.removeAttr("disabled");
            this.$fieldKey.removeAttr("disabled");
            this.$fieldList.removeAttr("disabled");
            this.$fieldSecret.removeAttr("disabled");
            
            this.locked = false;
        }
    };
    
    var InboxifyShortcodeGenerator = function(element) {
        this.$form = $(element);
        this.$fields = $(this.$form.find("input, select, textarea"));
        
        var that = this;
        
        this.$form.on("submit", function(e) {
            that.onSubmit(e);
        });
        
        this.editorContent = tinyMCE.activeEditor.getContent();
        this.editorSelected = tinyMCEPopup.editor.selection.getRng().startContainer.data;
        this.editorShortcode = wp.shortcode.next("inboxify_subscribe", this.editorSelected);
        
        if (this.editorShortcode) {
            this.editorShortcodeAtts = this.editorShortcode.shortcode.attrs.named;
            
            $.each(this.$fields, function(i, e) {
                var $e = $(e);
                var name = $e.data("name");
                var key = iy_map[name];
                
                if (typeof(that.editorShortcodeAtts[key]) != "undefined") {
                    if ("checkbox" == $e.attr("type")) {
                        if ($e.val()) {
                            $e.attr("checked", "checked");
                        } else {
                            $e.removeAttr("checked");
                        }
                    } else {
                        $e.val(that.editorShortcodeAtts[key]);
                    }
                }
            });
        }
    };
    
    InboxifyShortcodeGenerator.prototype = {
        $fields: null,
        $form: null,
        
        editorContent: null,
        editorSelected: null,
        editorShortcode: null,
        editorShortcodeAtts: null,
        
//        ajax: function() {
//            
//        },
//        
//        ajaxDone: function(data, textStatus, jqXHR) {
//            
//        },
//        
//        ajaxFail: function(jqXHR, textStatus, errorThrown) {
//            
//        },
        
        onSubmit: function(e) {
            e.preventDefault();
            
            var shortcode = "[inboxify_subscribe";
            
            $.each(this.$fields, function(i, e) {
                //console.log(e);
                
                var $e = $(e);
                var hasValue = false;
                var type = $e.attr("type");
                
                if ("submit" == type) {
                    return;
                } else if ("checkbox" == type && $e.is(":checked") ) {
                    //console.log($e.data("name") + " CC");
                    hasValue = true;
                } else if ("checkbox" != type && $e.val()) {
                    //console.log($e.data("name") + " VAL")
                    hasValue = true;
                } else {
                    //console.log($e.data("name") + " NO VAL");
                }
                
                if (hasValue) {
                    var name = $e.data("name");
                    var key = iy_map[name];
                    
                    if ("undefined" == typeof(key)) {
                        return;
                    }

                    shortcode += " " + key + "='" + $e.val() + "'";
                }
            });
            
            shortcode += "]";
            
            if (this.editorShortcode) {
                var new_content = this.editorContent.replace(this.editorSelected, shortcode);
                tinyMCE.activeEditor.setContent(new_content);
            } else {
                tinyMCEPopup.execCommand('mceReplaceContent', false, shortcode);
            }
            
            tinyMCEPopup.close();
        },
        
        setShortcode: function() {
            
        }
    };

    $.fn.inboxifySettings = function() {
        return this.each(function() {
            new InboxifySettings(this);
            
            return this;
        });
    };
    
    $.fn.inboxifyShortcodeGenerator = function() {
        return this.each(function() {
            new InboxifyShortcodeGenerator(this);
            
            return this;
        });
    };
}(jQuery));
