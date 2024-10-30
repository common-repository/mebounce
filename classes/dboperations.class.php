<?php
// Let's start by including the MailChimp API wrapper
include( plugin_dir_path( __FILE__ ) . '../lib/MailChimp.php' );
// Then call/use the class
use \DrewM\MailChimp\MailChimp;

class Mebounce_db_operations {
    public $mailchimp_options;
    
    public function __construct() {
        $this->all_ajax_calls();
        $this->mailchimp_options = get_option('mebounce_plugin_mailchimp_options');
        $this->front_options = get_option('mebounce_plugin_front_options');
    }
    
    public function all_ajax_calls() {
        // Action for submitting request demo from front end of the site
        add_action('wp_ajax_mebounce_ajax_submit', array($this, 'add_mebounce_data'));
        add_action('wp_ajax_nopriv_mebounce_ajax_submit', array($this, 'add_mebounce_data'));
        /*add_action('admin_post_mebounce_ajax_submit', array($this, 'add_mebounce_data'));
        add_action('admin_post_nopriv_mebounce_ajax_submit', array($this, 'add_mebounce_data'));*/
        
        add_action('wp_ajax_mebounce_update_notificatio', array($this, 'view_status_update'));
        add_action('wp_ajax_nopriv_mebounce_update_notificatio', array($this, 'view_status_update'));
        
    }
    
    public function add_mebounce_data() {        
        if ( empty($_POST) || !wp_verify_nonce($_POST['new_mebounce_nonce'],'add_new_mebounce') ) {
            echo 'You targeted the right function, but sorry, your nonce did not verify.';
            die();
        } else {
            // do your function here 
            if(isset($_POST['name'])) { 
                $name = $_POST['name'];
                if(strlen($name) < 3) {
                    echo 'Name should be atleast 3 character long.';
                    die();
                }
            }
            
            if(isset($_POST['email'])) {
                $email = $_POST['email'];
                $emailval = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
                if( !preg_match($emailval, $email) ) {
                    echo 'Please enter a valid email.';
                    die();
                }
            }
            if(isset($_POST['mobile'])) {
                $mobile = $_POST['mobile'];
                $numCheck = "/^\d+$/";
                
                if(strlen($mobile) > 0 ){
                    if( !preg_match($numCheck, $mobile) ) {
                        echo 'Please enter a valid mobile number';
                        die();
                    }
                }
            }
            
            // Put your MailChimp API and List ID hehe
            $api_key = $this->mailchimp_options['mailchimp_api_key'];   // data from settings field
            $list_id = $this->mailchimp_options['mailchimp_list_id'];
            
            $MailChimp = new MailChimp($api_key);
            // Submit subscriber data to MailChimp
            // For parameters doc, refer to: http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
            // For wrapper's doc, visit: https://github.com/drewm/mailchimp-api
            $result = $MailChimp->post("lists/$list_id/members", [
                'email_address' => $email,
                'merge_fields'  => ['NAME'=>$name, 'PHONE'=>$mobile],
                'status'        => 'subscribed',
            ]);
            if ($MailChimp->success()) {
                // Success message
                //echo "<h4>Thank you, you have been added to our mailing list.</h4>";
                
                // DB operation starts
                global $wpdb;
                $table_name = $wpdb->prefix . 'mebounce';

                if($wpdb->insert( $table_name, 
                      array(
                          'name' => $name,
                          'email' => $email,
                          'mobile' => $mobile,
                          'date' => current_time('mysql'),
                          'view_status' => 0,
                      )
                     ) === FALSE) {
                    echo '<h4>Thank you, you have been added to our mailing list, But error in posting database.</h4>';
                    die();
                }
                else {
                    $success_msg = 'Thank you, you have been added to our mailing list.';
                    // Set value for success message of the popup
                    if($this->front_options['mebounce_success_msg'] != '') 
                        $success_msg = $this->front_options['mebounce_success_msg'];
                    echo $success_msg;
                    die();
                }
            } else {
                // Display error
                echo $MailChimp->getLastError();
                die();
                // Alternatively you can use a generic error message like:
                // echo "<h4>Please try again.</h4>";
            }

        }
    }
    
    public function view_status_update() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mebounce';
        
        $update = $wpdb->update(
            $table_name,
            array(
                'view_status' => 1,
            ),
            array(
                'view_status' => 0,
            ),
            array( '%d' ),
            array( '%d' )
        );
        
        if($update)
            echo 'Updated';
        else echo 'Error';
    }
    
}