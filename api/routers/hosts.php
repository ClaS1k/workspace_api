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

        if(!get_access_flag_value($user_id, "hosts_controls")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        $ch = curl_init($BIOME_API_ADDRESS . "hosts");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $full_hosts_list = json_decode($response);
                $full_hosts_list =  $full_hosts_list -> result;

                $response_hosts_list = array();

                foreach($full_hosts_list as $single_host){
                    $player_id = $single_host -> player_id;
                    $player_name = "Свободен";

                    if($player_id != 0){
                        $ch = curl_init($BIOME_API_ADDRESS . "users/$player_id");
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
                        
                        $player_response = curl_exec($ch);

                        curl_close($ch);

                        $player_data = json_decode($player_response);

                        $player_name = $player_data -> result -> username;
                    }

                    $cur_host = array(
                        "id" => $single_host -> id,
                        "name" => $single_host -> name,
                        "identifier" => $single_host -> identifier,
                        "status" => $single_host -> status,
                        "player" => array(
                            "user_id" => $player_id,
                            "username" => $player_name  
                        )
                    );

                    array_push($response_hosts_list, $cur_host);
                }

                $response = array(
                    "result" => $response_hosts_list 
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

    if($urlData[0] == "create"){
        if($method!='POST'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "hosts_edit")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
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

        if(empty($formData -> identifier)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Identifier is empty'
            ));
            exit();
        }


        $name = $formData -> name;
        $identifier = $formData -> identifier;

        $data = array(
            "name" => $name,
            "identifier" => $identifier
        ); 

        $ch = curl_init($BIOME_API_ADDRESS . "hosts/create");
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($httpcode != 201){
            // not success creation in biome
            if($httpcode == 409){
                header('HTTP/1.0 409 Conflict');
                echo json_encode(array(
                    'message' => 'Not unique host name or identifier!'
                ));
                exit();
            }else{
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Biome server error!'
                ));
                exit();
            }
        }
        
        header('HTTP/1.0 201 Created');
        exit();
    }

    if(isset($urlData[0])){
        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "hosts_controls")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        $query_id = $urlData[0];

        $ch = curl_init($BIOME_API_ADDRESS . "hosts/$query_id");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $host_data = json_decode($response);
                $host_data =  $host_data -> result;

                $player_id = $host_data -> player_id;
                $player_name = "Свободен";

                if($player_id != 0){
                    $ch = curl_init($BIOME_API_ADDRESS . "users/$player_id");
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
                        
                    $player_response = curl_exec($ch);

                    curl_close($ch);

                    $player_data = json_decode($player_response);

                    $player_name = $player_data -> result -> username;
                }

                $host = array(
                    "id" => $host_data -> id,
                    "name" => $host_data -> name,
                    "identifier" => $host_data -> identifier,
                    "status" => $host_data -> status,
                    "player" => array(
                        "user_id" => $player_id,
                        "username" => $player_name  
                    )
                );

                $response = array(
                    "result" => $host 
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
