<?php
function route($method, $urlData, $formData){

  include 'config.php';
  include 'workspace_lib.php';

  $headers = apache_request_headers();

  include "token_validation.php";

  $is_error=false;
  $response=array();

    if($urlData[0] == "find"){
        // [GET] api/users/find/<query>

        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "users_get")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(!isset($urlData[1])){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Invalid query!'
            ));
            exit();
        }

        if(strlen($urlData[1]) < 2){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Query must be longer 2 symbols!'
            ));
            exit();
        }

        $query = $urlData[1];

        $ch = curl_init($BIOME_API_ADDRESS . "users/find/$query");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $full_users_list = json_decode($response);
                $full_users_list =  $full_users_list -> result;

                $response_users_list = array();

                foreach($full_users_list as $single_user){
                    $cur_user = array(
                        "user_id" => $single_user -> id,
                        "username" => $single_user -> username,
                        "phone" => $single_user -> phone
                    );

                    array_push($response_users_list, $cur_user);
                }

                $response = array(
                    "result" => $response_users_list 
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

        exit();
    }

    if($urlData[0] == "balance"){
        // [GET] api/users/balance/<user_id>

        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "users_get")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(!isset($urlData[1])){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Invalid user id!'
            ));
            exit();
        }

        $query_id = $urlData[1];

        $ch = curl_init($BIOME_API_ADDRESS . "users/$query_id/balance");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
        
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        switch($status_code){
            case 200:
                $data = json_decode($response);
                $balance_data = $data -> result;

                $response = array(
                    "result" => $balance_data
                );

                header('HTTP/1.0 200 Success');
                echo json_encode($response);
            break;
            case 401:
                // не авторизован (устарели данные)
                header('HTTP/1.0 401 Unauthorized');
            break;
            case 404:
                // пользователь не найден
                header('HTTP/1.0 404 Not found');
            break;
            default:
                // неизвестная ошибка сервера
                header('HTTP/1.0 500 Internal Server Error');
            break;
        }

        exit();
    }

    if($urlData[0] == "time"){
        // [GET] api/users/time/<user_id>

        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "users_get")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(!isset($urlData[1])){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Invalid user id!'
            ));
            exit();
        }

        $query_id = $urlData[1];

        $ch = curl_init($BIOME_API_ADDRESS . "users/$query_id/time");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
        
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        switch($status_code){
            case 200:
                $data = json_decode($response);
                $time_data = $data -> result;

                $response = array(
                    "result" => $time_data
                );

                header('HTTP/1.0 200 Success');
                echo json_encode($response);
            break;
            case 401:
                // не авторизован (устарели данные)
                header('HTTP/1.0 401 Unauthorized');
            break;
            case 404:
                // пользователь не найден
                header('HTTP/1.0 404 Not found');
            break;
            default:
                // неизвестная ошибка сервера
                header('HTTP/1.0 500 Internal Server Error');
            break;
        }

        exit();
    }

    if($urlData[0] == "create"){
        // [POST] api/users/create
        if($method!='POST'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "users_edit")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(empty($formData -> username)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Username is empty'
            ));
            exit();
        }

        if(empty($formData -> password)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Password is empty'
            ));
            exit();
        }

        if(empty($formData -> name)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Name is empty'
            ));
            exit();
        }

        if(empty($formData -> surname)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Surname is empty'
            ));
            exit();
        }

        if(empty($formData -> email)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Email is empty'
            ));
            exit();
        }

        if(empty($formData -> phone)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Phone is empty'
            ));
            exit();
        }

        if(empty($formData -> city)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'City is empty'
            ));
            exit();
        }

        if(empty($formData -> country)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Country is empty'
            ));
            exit();
        }

        if(empty($formData -> adress)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Adress is empty'
            ));
            exit();
        }

        $data = array(
            "username" => $formData -> username,
            "password" => $formData -> password,
            "name" => $formData -> name,
            "surname" => $formData -> surname,
            "email" => $formData -> email,
            "phone" => $formData -> phone,
            "city" => $formData -> city,
            "country" => $formData -> country,
            "adress" => $formData -> adress
        ); 

        $ch = curl_init($BIOME_API_ADDRESS . "users/create");
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        switch($httpcode){
            case 201:
                header('HTTP/1.0 201 Created');
                exit();
            break;
            case 400:
                $data = json_decode($response);

                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => $data -> result -> message
                ));
                exit();
            break;
            case 409:
                $data = json_decode($response);

                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => $data -> result -> message
                ));
                exit();
            break;
            default:
                // not success creation in biome
                echo $httpcode;
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Undefinded server error!'
                ));
                exit();
            break;
        }
    }

    if (isset($urlData[0])){
        // [GET] api/users/<user_id>

        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "users_get")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }
        
        $query_id = $urlData[0];

        $ch = curl_init($BIOME_API_ADDRESS . "users/$query_id");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
        
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        switch($status_code){
            case 200:
                $data = json_decode($response);
                $user_data = $data -> result;

                $response = array(
                    "result" => $user_data
                );

                header('HTTP/1.0 200 Success');
                echo json_encode($response);
            break;
            case 401:
                // не авторизован (устарели данные)
                header('HTTP/1.0 401 Unauthorized');
            break;
            case 404:
                // пользователь не найден
                header('HTTP/1.0 404 Not found');
            break;
            default:
                // неизвестная ошибка сервера
                header('HTTP/1.0 500 Internal Server Error');
            break;
        }

        exit();
    }

    if (!isset($urlData[0])){
        // [GET] api/users

        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "users_get")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        $ch = curl_init($BIOME_API_ADDRESS . "users");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $full_users_list = json_decode($response);
                $full_users_list =  $full_users_list -> result;

                $response_users_list = array();

                foreach($full_users_list as $single_user){
                    $cur_user = array(
                        "user_id" => $single_user -> id,
                        "username" => $single_user -> username,
                        "phone" => $single_user -> phone
                    );

                    array_push($response_users_list, $cur_user);
                }

                $response = array(
                    "result" => $response_users_list 
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

        exit();
    }
  
}
?>
