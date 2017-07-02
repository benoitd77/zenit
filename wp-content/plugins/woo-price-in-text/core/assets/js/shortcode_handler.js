(function() {
    var construct_shortcode = function(data) { 
        return '[woo_wpintxt text="' + data.text + '" sku="' + data.sku + '"]';
    }
    tinymce.create('tinymce.plugins.WRAP', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished its initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init: function(ed, url) {
            ed.addButton('wpintxt', {
                type: 'button',
                //cmd: 'button_green_cmd',
                text: 'Price in Text', // dashes to alleviate some padding issues
                fixedWidth: false,
                icon: false,
                onclick: function(e) {
                        ed.windowManager.open({
                            title: this.text(),
                            body: [{
                                type: 'textbox',
                                name: 'text',
                                label: 'Text',
                                value: 'The price of the product is %s',
                                minWidth: 400
                            },
                            {
                                type: 'textbox',
                                name: 'sku',
                                label: 'SKU',
                                value: '',
                                description: 'leave empty for current SKU',
                                help: 'leave empty for current SKU',
                                minWidth: 400
                            }
                            ],
                            classes: "mbsocials-popup",
                            onsubmit: function(e) {
                                ed.insertContent(construct_shortcode(e.data));
                            }
                        });
                }
            });
        }

    });
    // Register plugin
    tinymce.PluginManager.add('wpintxt', tinymce.plugins.WRAP);
})();
