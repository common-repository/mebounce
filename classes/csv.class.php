<?php
class Mebounce_csv {
    
    public function __construct() {
        $this->mebounce_csv_download();
    }
    
    function mebounce_csv_download() {
        add_action( 'admin_post_csv_download_submit', array( $this, 'csv_downloader_func' ) );
    }
    
    function csv_downloader_func() {
        if ( empty($_POST) || !wp_verify_nonce($_POST['csv_download_nonce'],'new_csv_download') ) {
            echo 'You targeted the right function, but sorry, your nonce did not verify.';
            die();
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'mebounce';

            $results = $wpdb->get_results( "SELECT email FROM $table_name", ARRAY_A );

            //var_dump( $results );

            // output headers so that the file is downloaded rather than displayed
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="mebounce-subscriber.csv"');

            // do not cache the file
            header('Pragma: no-cache');
            header('Expires: 0');

            // create a file pointer connected to the output stream
            $file = fopen('php://output', 'w');

            foreach( $results as $row ){
                fputcsv($file, $row);
            }
        }
    }
}