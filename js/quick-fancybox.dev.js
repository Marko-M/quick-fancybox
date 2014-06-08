var quick_fancybox = jQuery.extend(quick_fancybox || {}, {
    // a with href ending with ...
    a: 'a[href$=".png"], a[href$=".jpg"], a[href$=".jpeg"], a[href$=".gif"], a[href$=".bmp"], a[href$=".tiff"]',
    before_load: function() {
        // Use alt as caption
        this.title = jQuery(this.element).children('img').attr('alt');
    }
});

/* For galleries: select a with href ending with ... that has img element
 * inside and is inside .gallery element placed inside .post or .attachment */
jQuery('.post, .page, .attachment')
    .find('.gallery')
    .find(quick_fancybox.a)
    .has('img')
    .each(function() {
        var el = jQuery(this);
        el.attr('rel', el.parents('.gallery').attr('id'));
}).fancybox({ beforeLoad: quick_fancybox.before_load });

/* For non galleries: select a with href ending with ... that has img element
 * inside */
jQuery('.post, .page, .attachment')
    .find(quick_fancybox.a)
    .has('img:not(.gallery img)')
    .fancybox({ beforeLoad: quick_fancybox.before_load });