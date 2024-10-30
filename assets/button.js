(function () {
    /* Register the buttons */
    tinymce.create("tinymce.plugins.InboxifyShortcode", {
        init: function (ed, url) {
            
            /**
             * Add shortcode generator button
             */
            ed.addButton("iysc", {
                title: typeof(inboxify) != "undefined" ? inboxify.button_label : "Inboxify Shortcode Generator",
                image: url + "/av.png",
                cmd: "iysc_cmd"
            });
            
            /**
             * Shortcode generator command: display shortcode generator
             */
            ed.addCommand("iysc_cmd", function () {
                ed.windowManager.open(
                    {
                        file : ajaxurl + "?action=inboxify_tinymce",
                        width : 450,
                        height : 450,
                        inline : 1,
                        title: "Inboxify Shortcode Generator"
                    },
                    {
                        plugin_url : url
                    }
                );
            });
        },
        
        createControl: function (n, cm) {
            return null;
        },
    });
    
    /* Start the buttons */
    tinymce.PluginManager.add("iysc", tinymce.plugins.InboxifyShortcode);
})();
