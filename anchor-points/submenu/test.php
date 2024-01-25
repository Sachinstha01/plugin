<?php

add_action('wp_ajax_handle_recovery_codes', 'handle_recovery_codes');
add_action('wp_ajax_nopriv_handle_recovery_codes', 'handle_recovery_codes');

function insertRecoveryCodes()
    {

        // echo "This function hits";
        // die;
        // var_dump($_POST['recoveryCodes']);

        if (isset($_POST['hello'])) {
            $hello = isset($_POST['hello']);
            
            echo json_encode(['msg'=> 'Post is set','value'=>$hello]);    
            
            // die();
            //create recovery_codes column
         
        }else{
            echo json_encode(['msg'=> 'POST is not set']);
         }
    }

