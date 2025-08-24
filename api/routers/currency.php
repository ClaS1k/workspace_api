<?php
function route($method, $urlData, $formData){

    include 'config.php';
    include 'workspace_lib.php';

    $headers = apache_request_headers();

    include "token_validation.php";

    $is_error=false;
    $response=array();

    if(!isset($urlData[0])){
        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "market_main") AND !get_access_flag_value($user_id, "config")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        $ch = curl_init($BIOME_API_ADDRESS . "currency");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $full_currency_list = json_decode($response);
                $full_currency_list =  $full_currency_list -> result;

                $response_currency_list = array();

                foreach($full_currency_list as $single_currency){
                    $cur_currency = array(
                        "id" => $single_currency -> id,
                        "name" => $single_currency -> name,
                        "symbol" => $single_currency -> symbol
                    );

                    array_push($response_currency_list, $cur_currency);
                }

                $response = array(
                    "result" => $response_currency_list 
                );

                header('HTTP/1.0 200 Success');
                echo json_encode($response);
            break;
            case 401:
                // не авторизован (устарели данные)
                header('HTTP/1.0 401 Unauthorized');
            break;
            default:
                // неизвестная ошибка сервера
                header('HTTP/1.0 500 Internal Server Error');
            break;
        }
    }
}
?>
