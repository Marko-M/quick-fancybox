<?php
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit;

if(get_option('quick_fancybox_options'))
    delete_option('quick_fancybox_options');

if(get_option('quick_fancybox_db_version'))
    delete_option('quick_fancybox_db_version');
?>
