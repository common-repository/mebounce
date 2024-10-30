<div class="wrap">
    <div class="mebounce-edit">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                        <?php //settings_fields('sdn_order_plugin_options'); ?>
                        <?php //do_settings_sections('sdn_order_setting'); ?>
                <input type="hidden" name="date" value="<?php echo esc_attr($item['date'])?>" />
                <p>
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_attr($item['name'])?>" required />
                </p>
                <p>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="regular-text" value="<?php echo esc_attr($item['email'])?>" required />
                </p>
                <p>
                    <label for="mobile">Mobile</label>
                    <input type="text" name="mobile" id="mobile" class="regular-text" value="<?php echo esc_attr($item['mobile'])?>" />
                </p>
            
            <?php wp_nonce_field('mebounce_edit_data','mebounce_edit_data_nonce'); ?>
            <input name="action" value="mebounce_edit_data" type="hidden">

            
        </form>  
        <div class="clear"></div>
    </div>
</div>
