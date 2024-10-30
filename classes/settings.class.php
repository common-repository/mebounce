<?php 
class Mebounce_register_settings {
    public $options;
    public $front_options;
    public $mailchimp_options;
    
    public function __construct() {
        $this->options = get_option('mebounce_plugin_options');
        $this->front_options = get_option('mebounce_plugin_front_options');
        $this->mailchimp_options = get_option('mebounce_plugin_mailchimp_options');
        $this->register_settings_and_fields();
        $this->mebounce_notices_function();
    }
    
    public function register_settings_and_fields() {
        register_setting('mebounce_plugin_options', 'mebounce_plugin_options');   // 3rd param = optional cb
        
        // Settings Section
        add_settings_section('mebounce_main_settings_section', 'Settings', array($this, 'mebounce_main_section_cb'), 'mebounce_settings');   // id, title, cb, which page?
        
        add_settings_field('mebounce_aggressive', 'Aggressive Mode', array($this, 'mebounce_aggressive_setting'), 'mebounce_settings', 'mebounce_main_settings_section');
                
        add_settings_field('mebounce_cookie_expiration', 'Cookie Expiration', array($this, 'mebounce_cookie_expire_setting'), 'mebounce_settings', 'mebounce_main_settings_section');
        
        add_settings_field('mebounce_cookie_domain', 'Cookie Domain', array($this, 'mebounce_cookie_domain_setting'), 'mebounce_settings', 'mebounce_main_settings_section');
        
        add_settings_field('mebounce_delay', 'Delay', array($this, 'mebounce_delay_setting'), 'mebounce_settings', 'mebounce_main_settings_section');
        
        // Front-End Section
        register_setting('mebounce_plugin_front_options', 'mebounce_plugin_front_options');   // 3rd param = optional cb
        
        add_settings_section('mebounce_front_settings_section', 'Front-end Content Settings', array($this, 'mebounce_front_section_cb'), 'mebounce_front_settings');   // id, title, cb, which page?
        
        add_settings_field('mebounce_head', 'Box Head', array($this, 'mebounce_head_setting'), 'mebounce_front_settings', 'mebounce_front_settings_section');
        
        add_settings_field('mebounce_box_content', 'Box Content', array($this, 'mebounce_box_content_setting'), 'mebounce_front_settings', 'mebounce_front_settings_section');
        
        add_settings_field('mebounce_success_msg', 'Success Message', array($this, 'mebounce_success_msg_setting'), 'mebounce_front_settings', 'mebounce_front_settings_section');
        
        // MailChimp Section
        register_setting('mebounce_plugin_mailchimp_options', 'mebounce_plugin_mailchimp_options');   // 3rd param = optional cb
        
        add_settings_section('mebounce_mailchimp_settings_section', 'MailChimp Settings', array($this, 'mebounce_mailchimp_section_cb'), 'mebounce_mailchimp_settings');   // id, title, cb, which page?
        
        add_settings_field('mailchimp_api_key', 'API Key', array($this, 'mailchimp_api_key_setting'), 'mebounce_mailchimp_settings', 'mebounce_mailchimp_settings_section');
        
        add_settings_field('mailchimp_list_id', 'List ID', array($this, 'mailchimp_list_id_setting'), 'mebounce_mailchimp_settings', 'mebounce_mailchimp_settings_section');
        
    } 
    
    public function mebounce_main_section_cb() {
        
    }
    
    public function mebounce_aggressive_setting() {
        $aggressive = '';
        if( !isset($this->options['mebounce_aggressive'])) 
            $aggressive = 'false';
        else
            $aggressive = $this->options['mebounce_aggressive'];
        
        if(isset($this->options['mebounce_aggressive']))
            $aggressive = $this->options['mebounce_aggressive'];
        
        $yesChecked = ($aggressive=='true') ? 'checked' : '';
        $noChecked = ($aggressive=='false') ? 'checked' : '';
        
        $fields = "<label><input name='mebounce_plugin_options[mebounce_aggressive]' type='radio' value='true' ".$yesChecked ." /> Enable </label> <label><input name='mebounce_plugin_options[mebounce_aggressive]' type='radio' value='false' ".$noChecked ." /> Disable</label>";
        
        $info = '<p>By default, meBounce will only fire once for each visitor. When meBounce fires, a cookie is created to ensure a non obtrusive experience. If you enable aggressive, the modal will fire any time the page is reloaded, for the same user. We suggest you to test this plugin enable this mode, but on your live site <strong>make it as default to ensure a non obtrusive experience</strong>.</p>';
        
        echo $fields . $info;
    }
    
    public function mebounce_cookie_expire_setting() {
        $expire = '';
        
        if(isset($this->options['mebounce_cookie_expiration']))
                $expire = $this->options['mebounce_cookie_expiration'];
        
        $field = "<input name='mebounce_plugin_options[mebounce_cookie_expiration]' type='text' class='regular-text' value='".$expire."'> <span>days</span>";
        
        $info = '<p>meBounce sets a cookie by default to prevent the modal from appearing more than once per user. You can add a <strong>cookie expiration (in days)</strong> to adjust the time period before the modal will appear again for a user.</p>';
        
        echo $field . $info;
    }
    
    public function mebounce_cookie_domain_setting() {
        $domain = '';
        
        if(isset($this->options['mebounce_cookie_domain']))
                $domain = $this->options['mebounce_cookie_domain'];
        
        $field = "<input name='mebounce_plugin_options[mebounce_cookie_domain]' type='text' class='regular-text' value='".$domain."'>";
        
        $info = '<p>By default, <strong>no extra domain information need to be added</strong>. If you need a cookie to work also in your subdomain (like blog.example.com and example.com), then set a cookieDomain such as <strong>.example.com (notice the dot in front)</strong>.</p>';
        
        echo $field . $info;
    }
    
    public function mebounce_delay_setting() {
        $delay = '';
        
        if(isset($this->options['mebounce_delay']))
                $delay = $this->options['mebounce_delay'];
        
        $field = "<input name='mebounce_plugin_options[mebounce_delay]' type='text' class='regular-text' value='".$delay."'> <span>milliseconds</span>";
        
        $info = '<p>By default, meBounce will show the modal immediately. You could instead configure it to <strong>wait x milliseconds before showing the modal</strong>. If the user\'s mouse re-enters the body before delay ms have passed, the modal will not appear.</p>';
        
        echo $field . $info;
    }
    
    
    // Front-End Section
    public function mebounce_front_section_cb() {
        
    }
    
    public function mebounce_head_setting() {
        $head = '';
        
        if(isset($this->front_options['mebounce_head']))
                $head = $this->front_options['mebounce_head'];
        
        echo "<input name='mebounce_plugin_front_options[mebounce_head]' type='text' class='widefat' value='".$head."' placeholder='Popup title here'>";
    }
    
    public function mebounce_box_content_setting() {
        $box_content = '';
        
        if(isset($this->front_options['mebounce_box_content']))
                $box_content = $this->front_options['mebounce_box_content'];
        
        echo "<textarea name='mebounce_plugin_front_options[mebounce_box_content]' rows=5 cols=45 class='widefat' placeholder='You can use HTML tags like <strong>, <em> to make your text bold, italic, underline. Except these tags no other tags are supported.'>".$box_content."</textarea>";
    }
    
    public function mebounce_success_msg_setting() {
        $success_msg = '';
        
        if(isset($this->front_options['mebounce_success_msg']))
                $success_msg = $this->front_options['mebounce_success_msg'];
        
        echo "<input name='mebounce_plugin_front_options[mebounce_success_msg]' type='text' class='widefat' value='".$success_msg."' placeholder='This message will apear to the user after submission.'>";
    }
    
    // MailChimp Section
    public function mebounce_mailchimp_section_cb() {
        
    }
    
    public function mailchimp_api_key_setting() {
        $api_key = '';
        
        if(isset($this->mailchimp_options['mailchimp_api_key']))
            $api_key = $this->mailchimp_options['mailchimp_api_key'];
        
        echo "<input name='mebounce_plugin_mailchimp_options[mailchimp_api_key]' type='text' class='widefat' value='".$api_key."' placeholder='Your MailChimp API Key.'>";
    }
    
    public function mailchimp_list_id_setting() {
        $list_id = '';
        
        if(isset($this->mailchimp_options['mailchimp_list_id']))
            $list_id = $this->mailchimp_options['mailchimp_list_id'];
        
        echo "<input name='mebounce_plugin_mailchimp_options[mailchimp_list_id]' type='text' class='widefat' value='".$list_id."' placeholder='List ID of Your MailChimp Account.'>";
    }
    
    public function mebounce_notices_function() {
        if( $this->mailchimp_options['mailchimp_api_key'] == '' ) {
            add_action( 'admin_notices', array( $this, 'mebounce_mailchimp_notice' ) );
        }
    }
    
    public function mebounce_mailchimp_notice() {
        ?>
        <div class="notice update-nag">
            <p><?php _e( 'To start getting subscribers directly to your MailChimp account with meBounce, add MailChimp API key from <a href="'.get_admin_url('/').'admin.php?page=mebounce_settings">meBounce settings</a>!', 'mebounce' ); ?></p>
        </div>
        <?php
    }
}