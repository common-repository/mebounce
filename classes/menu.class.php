<?php
require_once( plugin_dir_path(__FILE__) . 'custom-table.class.php');

class Mebounce_menu_settings {
    //private $options;
    
    public function __construct() {
        //$this->options = get_option('mebounce_plugin_options');
        $this->mebounce_add_menu_page();
        //$this->register_settings_and_fields();
    }
    
    public function mebounce_add_menu_page() {
        add_action('admin_menu', array('Mebounce_menu_settings', 'create_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueque_scripts_for_admin'));
    }
    
    public function enqueque_scripts_for_admin() {
        if( $_GET['page'] == 'mebounce_subscribers_list' ) {
            wp_enqueue_script('mebounce-notify-js', plugin_dir_url(__FILE__) . '../assets/admin/notification.js', array('jquery'), '20740520', true);
            wp_enqueue_style('mebounce-custom-admin-css', plugin_dir_url(__FILE__) . '../assets/admin/custom.css');
        }
        
        if( $_GET['page'] == 'mebounce_settings' ) {
            wp_enqueue_script( 'mebounce-tabs', plugin_dir_url(__FILE__) . '../assets/admin/mebounce-tabs.js', array( 'jquery-ui-tabs' ) );
            wp_enqueue_style('mebounce-settings_style', plugin_dir_url(__FILE__) . '../assets/admin/mebounce-settings.css');
        }
    }
    
    public function create_menu_pages() {
        global $wpdb;
        $matches = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}mebounce WHERE view_status=0;" );

        $notification_count = $matches;//get_transient( 'mebounce_bubble_notification' );// count( $warnings );
        $notification_title = esc_attr( sprintf( '%d new mebounce subscriber(s)', $notification_count ) );

        $menu_label = sprintf( __( 'meBounce %s' ), "<span class='update-plugins count-$notification_count' title='$notification_title'><span class='update-count'>" . number_format_i18n($notification_count) . "</span></span>" );

        add_menu_page('meBounce', $menu_label, 'administrator', 'mebounce_subscribers_list', array('Mebounce_menu_settings', 'mebounce_subscribers_page'), plugin_dir_url(__FILE__) . '../images/box-solid.png', 29.5);
        
        $subscriber_menu_label = sprintf( __( 'Subscribers %s' ), "<span class='update-plugins count-$notification_count' title='$notification_title'><span class='update-count'>" . number_format_i18n($notification_count) . "</span></span>" );
        
        add_submenu_page('mebounce_subscribers_list', 'meBounce Subscribers List', $subscriber_menu_label, 'administrator', 'mebounce_subscribers_list', array('Mebounce_menu_settings', 'mebounce_subscribers_page'));
        
        add_submenu_page('mebounce_subscribers_list', 'meBounce Settings', 'Settings', 'administrator', 'mebounce_settings', array('Mebounce_menu_settings', 'mebounce_settings_page'));
        
        add_submenu_page( '', 'Edit meBounce Item', 'Edit', 'administrator', 'mebounce_edit_item', array('Mebounce_menu_settings', 'mebounce_edit_page'));
    }
    
    public function mebounce_subscribers_page() {
        global $wpdb;

        $table = new Mebounce_Custom_Table_Example_List_Table();
        $table->prepare_items();

        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'mebounce'), count($_REQUEST['id'])) . '</p></div>';
        }
        ?>
    <div class="wrap">

        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('meBounce', 'mebounce')?></h2>
        <div class="clear"></div>
        <?php echo $message; ?>

        <form id="orders-table" method="GET">
            
              <?php $table->search_box('Search meBounce Data', 'mebounce'); ?>
            
            
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php //$table->display();
            //Fetch, prepare, sort, and filter our data...
            if( isset($_POST['s']) ){
                    $table->display($_POST['s']);
            } else {
                    $table->display();
            }
            ?>
        </form>

    </div>
    <?php
    }
    
    public function mebounce_edit_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mebounce'; // do not forget about tables prefix

        $message = '';
        $notice = '';
        
        // this is default $item which will be used for new records
        $default = array(
            'id' => 0,
            'name' => '',
            'email' => '',
            'mobile' => null,
            'date' => '',
        );

        // here we are verifying does this request is post back and have correct nonce
        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = Mebounce_menu_settings::mebounce_validate_data($item);
            if ($item_valid === true) {
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'mebounce');
                    } else {
                        $notice = __('There was an error while saving item', 'mebounce');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    if ($result) {
                        $message = __('Item was successfully updated', 'mebounce');
                    } else {
                        $notice = __('There was an error while updating item', 'mebounce');
                    }
                }
            } else {
                // if $item_valid not true it contains error message(s)
                $notice = $item_valid;
            }
        }
        else {
            // if this is not post back we load item to edit or give new one to create
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'mebounce');
                }
            }
        }

        // here we adding our custom meta box
        add_meta_box('mebounce_meta_box', 'Edit Details', array('Mebounce_menu_settings', 'mebounce_meta_box_handler'), 'mebounce', 'normal', 'default');

        ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Edit Data', 'mebounce')?> 
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=mebounce_subscribers_list');?>"><?php _e('Back to meBounce', 'mebounce')?></a>
        </h2>
        <div class="clear"></div>

        <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
            <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php /* And here we call our custom meta box */ ?>
                        <?php do_meta_boxes('mebounce', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Save', 'mebounce')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
            
    }
    
    function mebounce_meta_box_handler($item) {
        require_once( plugin_dir_path(__FILE__) . '../includes/edit-data.php');
    }
    
    /**
     * Simple function that validates data and retrieve bool on success
     * and error message(s) on error
     *
     * @param $item
     * @return bool|string
     */
    function mebounce_validate_data($item) {
        $messages = array();

        if (empty($item['name'])) $messages[] = __('Name is required', 'custom_table_example');
        if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
        //if (!ctype_digit($item['amount'])) $messages[] = __('Amount in wrong format', 'custom_table_example');
        //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
        if(!empty($item['mobile']) && !preg_match('/[0-9]+/', $item['mobile'])) $messages[] = __('Mobile must be number');
        //...

        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }
    
    public function mebounce_settings_page() {
        
        ?>
<div class="wrap">
    <h2>Settings</h2>
    <div class="mebounce" id="mebounce-tabs">
        <ul class="tabs">
            <li><a href="#tab1">Settings</a></li>
            <li><a href="#tab2">Front-end</a></li>
            <li><a href="#tab3">CSV Download</a></li>
            <li><a href="#tab4">MailChimp</a></li>
        </ul>
        <div class="clear"></div>
                
        <!--Settings Section-->
        <div id="tab1" class="mebounce-tab-cont">
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields('mebounce_plugin_options');
                do_settings_sections('mebounce_settings');
                ?>
                <p class="submit">
                    <input name="submit" type="submit" class="button-primary" value="Save Changes">
                </p>
            </form>
        </div>
        <!--Settings Section-->
        
        <!--Front End Setting Section-->
        <div id="tab2" class="mebounce-tab-cont">
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields('mebounce_plugin_front_options');
                do_settings_sections('mebounce_front_settings');
                ?>
                <p class="submit">
                    <input name="submit" type="submit" class="button-primary" value="Save Changes">
                </p>
            </form>
        </div>
        <!--Front End Setting Section-->
        
        <!--CSV Download Section Start-->
        <div id="tab3" class="mebounce-tab-cont">
            <?php 
            global $wpdb;
            $table_name = $wpdb->prefix . 'mebounce';

            $results = $wpdb->get_results( "SELECT email FROM $table_name", ARRAY_A );

            if ( count( $results ) == 0 ) {
                echo '<p style="color:red;">You don\'t have any subscriber yet.</p>';
            } else {
                echo '<p style="color:green;">Well done! you have '.count($results).' subscribers. Download subscribers list in CSV and import them to email client, like mailchimp.</p>';
            ?>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                <input type="hidden" name="action" value="csv_download_submit">
                <?php wp_nonce_field('new_csv_download','csv_download_nonce'); ?>
                <input type="submit" name="submit" value="Download CSV">
            </form>
            <?php
            }
            ?>
        </div>
        <!--CSV Download section end-->
        
        <!--Front End Setting Section-->
        <div id="tab4" class="mebounce-tab-cont">
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields('mebounce_plugin_mailchimp_options');
                do_settings_sections('mebounce_mailchimp_settings');
                ?>
                <p class="submit">
                    <input name="submit" type="submit" class="button-primary" value="Save Changes">
                </p>
            </form>
        </div>
        <!--Front End Setting Section-->
        
    </div>
</div>
        <?php
    }
    
    
}