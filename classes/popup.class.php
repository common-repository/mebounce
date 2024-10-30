<?php
class Mebounce_popup {
    public $front_options;
    public $options;
    
    public function __construct() {
        $this->front_options = get_option('mebounce_plugin_front_options');
        $this->options = get_option('mebounce_plugin_options');
        add_action('wp_footer', array($this, 'generate_popup'));
        add_action('wp_footer', array($this, 'popup_js'));
        add_action('wp_enqueue_scripts', array($this, 'load_mebounce_scripts_and_styles'));
    }
    
    public function load_mebounce_scripts_and_styles() {
        wp_enqueue_script('ouibounce-js', plugin_dir_url(__FILE__) . '../assets/ouibounce.min.js', array('jquery'), '32009400', true);
        //wp_enqueue_script('mebounce-custom', plugin_dir_url(__FILE__) . '../assets/custom.js', array('jquery'), '32009400', true);
        wp_enqueue_script('mebounce-submit', plugin_dir_url(__FILE__) . '../assets/mebounce-submit.js', array('jquery'), '32009400', true);
        wp_enqueue_style('mebounce-style', plugin_dir_url(__FILE__) . '../assets/mebounce-style.css');
    }
    
    public function generate_popup() {
        // Set value for head title of the popup
        if($this->front_options['mebounce_head'] != '') 
            $head_title = $this->front_options['mebounce_head'];
        // If no value set then set the default value
        else
            $head_title = 'Hello, I\'m a meBounce popup!';
        
        // Set value for content of the popup
        if($this->front_options['mebounce_box_content'] != '') 
            $content = $this->front_options['mebounce_box_content'];
        // If no value set then set the default value
        else
            $content = 'Hey, before leaving would you like to share some information about yourself with us? We will share more interesting contents in future, make sure you don\'t miss them by subscribing.';
        
        // Set value for success message of the popup
        if($this->front_options['mebounce_success_msg'] != '') 
            $success_msg = $this->front_options['mebounce_success_msg'];
        // If no value set then set the default value
        else
            $success_msg = 'Thank you for subscribing!';
        
        require_once ( plugin_dir_path( __FILE__ ) . '../includes/popup.php' );
    }
    
    public function popup_js() {
        $chain_output = '';
        
        // Set value for aggressive mode
        if($this->options['mebounce_aggressive'] != '') 
            $aggressive = $this->options['mebounce_aggressive'];
        // If no value set then set the default value
        else
            $aggressive = 'false';
        
        // Creating JS chain output for meBounce
        if($this->options['mebounce_cookie_expiration'] != '') 
            $chain_output .= "cookieExpire: " . $this->options['mebounce_cookie_expiration'] . ",";
        
        if($this->options['mebounce_cookie_domain'] != '') 
            $chain_output .= "cookieDomain: '" . $this->options['mebounce_cookie_domain'] . "',";
        
        if($this->options['mebounce_delay'] != '') 
            $chain_output .= "delay: " . $this->options['mebounce_delay'] . ",";
        
        // include JS file
        require_once ( plugin_dir_path( __FILE__ ) . '../includes/custom-js.php' );
    }
}