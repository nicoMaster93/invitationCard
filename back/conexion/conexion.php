<?php

if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false){
    require_once('dev.ini');
}else{
    require_once('prod.ini');
}

class Database {
    public function encrypt_decrypt($action, $string){
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = KEY_ENCRYPT;
        $secret_iv = IV_SECRET;
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
    public function is_valid_email($email){
        return (false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}(new Database);

?>
