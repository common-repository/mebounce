<!-- meBounce Modal -->
    <div id="mebounce-modal">
      <div class="underlay"></div>
      <div class="modal">
      <header>
        <div class="modal-title">
          <h3><?php echo $head_title; ?></h3>
        </div>
      </header>

        <div class="modal-body">
          <p><?php echo $content; ?></p>


          <form id="mebounce_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
              <input type="text" name="name" id="mebounce_name" placeholder="Your Full Name">
              <p class="mb-hide red" id="mebounce_name_error">Name is required.</p>
              
              <input type="email" name="email" id="mebounce_email" placeholder="Your Email ID">
              <p class="mb-hide red" id="mebounce_email_error">Email is required.</p>
              
              <input type="text" name="mobile" id="mebounce_mobile" placeholder="Mobile No. (Optional)">
              <p class="mb-hide red" id="mebounce_mobile_error">Enter a proper mobile no.</p>
              
              <p class="mb-hide center" id="mebounce_loader"><img src="<?php echo plugin_dir_url(__FILE__) . '../images/ripple.gif'; ?>" /></p>
              <p class="mb-hide center success" id="mebounce_success"><?php echo $success_msg; ?></p>

              <input type="submit" class="float-button-light" id="mebounce_submit" value="Submit">
              <?php wp_nonce_field('add_new_mebounce','new_mebounce_nonce'); ?>
              <input name="action" value="mebounce_ajax_submit" type="hidden">
          </form>
        </div>

        <footer>
        <div class="modal-footer">
          <p>no thanks</p>
        </div>
        </footer>
      </div>
    </div>
<!-- meBounce Modal Ends -->
