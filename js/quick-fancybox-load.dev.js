var quick_fancybox = jQuery.extend(quick_fancybox || {}, {
    script_suffix: (quick_fancybox.debug_mode == 1) ? '.dev' : '',
    get_script: function(url, callback, options) {
        options = jQuery.extend(options || {}, {
            crossDomain: (quick_fancybox.debug_mode == 1) ? true : false,
            dataType: "script",
            cache: true,
            success: callback,
            url: url
        });

        return jQuery.ajax(options);
    },
    load: function(){
        /* If there is .post or .attachment element that contains a
         * with img element inside */
        if(jQuery('.post, .page, .attachment').find('a:has(img)').length > 0) {
            quick_fancybox.get_script(quick_fancybox.url+'js/jquery.fancybox'+quick_fancybox.script_suffix+'.js?'+quick_fancybox.fancybox_version, function(){
                quick_fancybox.get_script(quick_fancybox.url+'js/quick-fancybox'+quick_fancybox.script_suffix+'.js?'+quick_fancybox.version);
            });
        }
    }
});

jQuery(document).ready(quick_fancybox.load());