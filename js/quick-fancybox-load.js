var quick_fancybox=jQuery.extend(quick_fancybox||{},{script_suffix:1==quick_fancybox.debug_mode?".dev":"",get_script:function(b,c,a){a=jQuery.extend(a||{},{crossDomain:1==quick_fancybox.debug_mode?!0:!1,dataType:"script",cache:!0,success:c,url:b});return jQuery.ajax(a)},load:function(){0<jQuery(".post, .page, .attachment").find("a:has(img)").length&&quick_fancybox.get_script(quick_fancybox.url+"js/jquery.fancybox"+quick_fancybox.script_suffix+".js?"+quick_fancybox.fancybox_version,function(){quick_fancybox.get_script(quick_fancybox.url+
"js/quick-fancybox"+quick_fancybox.script_suffix+".js?"+quick_fancybox.version)})}});jQuery(document).ready(quick_fancybox.load());