<?php
/*
Plugin Name: Quick Fancybox
Plugin URI: http://www.techytalk.info/wordpress-plugins/quick-fancybox/
Description: Fancybox for WordPress
Author: Marko Martinović
Version: 1.00
Author URI: http://www.techytalk.info
License: GPL2

Copyright 2012.  Marko Martinović  (email : marko AT techytalk.info)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Quick_Fancybox{
    const version = '1.00';
    const fancybox_version = '2.1.5';
    const name = 'Quick Fancybox';
    const link = 'http://www.techytalk.info/wordpress-plugins/quick-fancybox/';
    const donate_link = '';
    const support_link = 'http://www.techytalk.info/wordpress-plugins/quick-fancybox/';
    const faq_link = 'http://wordpress.org/extend/plugins/quick-fancybox/faq/';
    const changelog_link = 'http://wordpress.org/extend/plugins/quick-fancybox/changelog/';
    const log_filename = 'quick-fancybox.log';
    const default_db_version = '1';

    protected $url;
    protected $path;
    protected $basename;
    protected $db_version;
    protected $options;
    protected $log_file;

    public function __construct() {
        $this->url = plugin_dir_url(__FILE__);
        $this->path =  plugin_dir_path(__FILE__);
        $this->basename = plugin_basename(__FILE__);
        $this->log_file = $this->path . self::log_filename;

        $this->db_version = get_option('quick_fancybox_db_version');
        $this->options = get_option('quick_fancybox_options');

        add_action('plugins_loaded', array($this, 'update_db_check'));
        add_filter('plugin_row_meta', array($this, 'plugin_meta'), 10, 2);
        add_action('init', array($this, 'text_domain'));

        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_menu', array($this, 'add_options_page'));
        add_action('wp_enqueue_scripts', array($this, 'script'));
        add_action('wp_print_styles', array($this, 'style'));
    }

    public function style() {
        $fancybox_style_url = $this->url . 'css/jquery.fancybox.css';
        $fancybox_style_file = $this->path . 'css/jquery.fancybox.css';

        if (file_exists($fancybox_style_file)) {
            wp_enqueue_style('quick_fancybox_style_sheet', $fancybox_style_url);
        }
    }

    public function script() {
        wp_enqueue_script('jquery');

        if(isset($this->options['debug_mode']) || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)) {
            $script_suffix = '.dev';
            $debug_mode = 1;
        }else{
            $script_suffix = '';
            $debug_mode = 0;
        }

        wp_enqueue_script('quick-fancybox-load', ($this->url.'js/quick-fancybox-load'.$script_suffix.'.js'), array('jquery'), self::version, true);
        wp_localize_script('quick-fancybox-load', 'quick_fancybox',
            array(
                'url' => $this->url,
                'ajaxurl' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
                'version' => self::version,
                'fancybox_version' => self::fancybox_version,
                'debug_mode' => $debug_mode,
                'i18n' =>
                array()
            )
        );
    }

    public function options_validate($input) {
        return $input;
    }

    public function install() {
        update_option('quick_fancybox_options', $this->options);
        update_option('quick_fancybox_db_version', self::default_db_version);
    }

    public function update_db_check() {
        if ($this->db_version != self::default_db_version) {
            $this->install();
        }
    }

    public function text_domain() {
        load_plugin_textdomain('quick-fancybox', false, dirname($this->basename) . '/languages/');
    }

    public function plugin_meta($links, $file) {
        if ($file == $this->basename) {
            return array_merge(
                $links,
                array( '<a href="'.self::donate_link.'">'.__('Donate', 'quick-fancybox').'</a>' )
            );
        }
        return $links;
    }

    public function add_options_page() {
        add_options_page(self::name, self::name, 'manage_options', __FILE__, array($this, 'options_page'));
        add_filter('plugin_action_links', array($this, 'action_links'), 10, 2);
    }

    public function action_links($links, $file) {
        if ($file == $this->basename) {
            $settings_link = '<a href="' . get_admin_url(null, 'admin.php?page='.$this->basename) . '">'.__('Settings', 'quick-fancybox').'</a>';
            $links[] = $settings_link;
        }

        return $links;
    }

    public function options_page() {
    ?>
        <div class="wrap">
            <div class="icon32" id="icon-options-general"><br></div>
            <h2><?php echo self::name ?></h2>
            <form action="options.php" method="post">
            <?php settings_fields('quick_fancybox_options'); ?>
            <?php do_settings_sections(__FILE__); ?>
            <p class="submit">
                <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
            </p>
            </form>
        </div>
    <?php
    }

    public function settings_init() {
        register_setting('quick_fancybox_options', 'quick_fancybox_options', array($this, 'options_validate'));

        add_settings_section('donate_section', __('Donating or getting help', 'quick-fancybox'), array($this, 'settings_section_donate'), __FILE__);
        add_settings_section('general_section', __('General options', 'quick-fancybox'), array($this, 'settings_section_general'), __FILE__);

        add_settings_field('quick_fancybox_debug_mode', __('Debug mode (enable only when debugging):', 'quick-fancybox'), array($this, 'settings_field_debug_mode'), __FILE__, 'general_section');

        add_settings_field('quick_fancybox_paypal', __('Donate using PayPal (sincere thank you for your help):', 'quick-fancybox'), array($this, 'settings_field_paypal'), __FILE__, 'donate_section');
        add_settings_field('quick_fancybox_version', sprintf(__('%s version:', 'quick-fancybox'), self::name), array($this, 'settings_field_version'), __FILE__, 'donate_section');
        add_settings_field('quick_fancybox_faq', sprintf(__('%s FAQ:', 'quick-fancybox'), self::name), array($this, 'settings_field_faq'), __FILE__, 'donate_section');
        add_settings_field('quick_fancybox_changelog', sprintf(__('%s changelog:', 'quick-fancybox'), self::name), array($this, 'settings_field_changelog'), __FILE__, 'donate_section');
        add_settings_field('quick_fancybox_support_page', sprintf(__('%s support page:', 'quick-fancybox'), self::name), array($this, 'settings_field_support_page'), __FILE__, 'donate_section');
    }

    public function settings_section_donate() {
        echo '<p>';
        echo sprintf(__('If you find %s useful you can donate to help it\'s development. Also you can get help with %s:', 'quick-fancybox'), self::name, self::name);
        echo '</p>';
    }

    public function settings_section_general() {
        echo '<p>';
        echo __('Here you can control all general options:', 'quick-fancybox');
        echo '</p>';
    }

    public function settings_field_faq() {
        echo '<a href="'.self::faq_link.'" target="_blank">'.__('FAQ', 'quick-fancybox').'</a>';
    }

    public function settings_field_version() {
        echo self::version;
    }

    public function settings_field_changelog() {
        echo '<a href="'.self::changelog_link.'" target="_blank">'.__('Changelog', 'quick-fancybox').'</a>';
    }

    public function settings_field_support_page() {
        echo '<a href="'.self::support_link.'" target="_blank">'.sprintf(__('%s at TechyTalk.info', 'quick-fancybox'), self::name).'</a>';
    }

    public function settings_field_paypal() {
        echo '<a href="'.self::donate_link.'" target="_blank"><img src="'.$this->url.'img/paypal.gif" /></a>';
    }

    public function settings_field_debug_mode() {
        echo '<input id="quick_fancybox_debug_mode" name="quick_fancybox_options[debug_mode]" type="checkbox" value="1" ';
        if(isset($this->options['debug_mode'])) echo 'checked="checked"';
        echo '/>';
    }

    protected function log($title, $code = null, $message = null) {
        if(isset($this->options['debug_mode']) || (defined('WP_DEBUG') && WP_DEBUG)) {
            $log_file_append = '['.gmdate('D, d M Y H:i:s \G\M\T').'] ' . $title;

            if($code !== null) {
               $log_file_append .= ', code: ' . $code;
            }

            if($message !== null) {
               $log_file_append .= ', message: ' . $message;
            }
            file_put_contents($this->log_file, $log_file_append . "\n", FILE_APPEND);
        }
    }
}
global $quick_fancybox;
$quick_fancybox = new Quick_Fancybox();
?>