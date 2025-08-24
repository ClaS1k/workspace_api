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

        $ch = curl_init($BIOME_API_ADDRESS . "reservations");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $full_reservations_list = json_decode($response);
                $full_reservations_list =  $full_reservations_list -> result;

                $response_reservations_list = array();

                foreach($full_reservations_list as $single_reservation){
                    $player_id = $single_reservation -> user_id;
                    $host_id = $single_reservation -> host_id;
                    $player_name = "";

                    $ch = curl_init($BIOME_API_ADDRESS . "users/$player_id");
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
                        
                    $player_response = curl_exec($ch);

                    curl_close($ch);

                    $player_data = json_decode($player_response);

                    $player_name = $player_data -> result -> username;

                    $ch = curl_init($BIOME_API_ADDRESS . "hosts/$host_id");
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

                    $host_response = curl_exec($ch);

                    curl_close($ch);

                    $host_data = json_decode($host_response);

                    $cur_reservation = array(
                        "id" => $single_reservation -> id,
                        "date_from" => $single_reservation -> date_from,
                        "date_to" => $single_reservation -> date_to,
                        "host" => array(
                            "id" => $host_data -> result -> id,
                            "name" => $host_data -> result -> name
                        ),
                        "player" => array(
                            "user_id" => $player_id,
                            "username" => $player_name 
                        )
                    );

                    array_push($response_reservations_list, $cur_reservation);
                }

                $response = array(
                    "result" => $response_reservations_list 
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

    if($urlData[0] == "nearest"){
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

        $ch = curl_init($BIOME_API_ADDRESS . "reservations/nearest");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $full_reservations_list = json_decode($response);
                $full_reservations_list =  $full_reservations_list -> result;

                $response_reservations_list = array();

                foreach($full_reservations_list as $single_reservation){
                    $player_id = $single_reservation -> user_id;
                    $host_id = $single_reservation -> host_id;
                    $player_name = "";

                    $ch = curl_init($BIOME_API_ADDRESS . "users/$player_id");
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
                        
                    $player_response = curl_exec($ch);

                    curl_close($ch);

                    $player_data = json_decode($player_response);

                    $player_name = $player_data -> result -> username;

                    $ch = curl_init($BIOME_API_ADDRESS . "hosts/$host_id");
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

                    $host_response = curl_exec($ch);

                    curl_close($ch);

                    $host_data = json_decode($host_response);

                    $cur_reservation = array(
                        "id" => $single_reservation -> id,
                        "date_from" => $single_reservation -> date_from,
                        "date_to" => $single_reservation -> date_to,
                        "host" => array(
                            "id" => $host_data -> result -> id,
                            "name" => $host_data -> result -> name
                        ),
                        "player" => array(
                            "user_id" => $player_id,
                            "username" => $player_name 
                        )
                    );

                    array_push($response_reservations_list, $cur_reservation);
                }

                $response = array(
                    "result" => $response_reservations_list 
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

        if(empty($formData -> user_id)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'User id is empty'
            ));
            exit();
        }

        if(empty($formData -> host_id)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Host id is empty'
            ));
            exit();
        }

        if(empty($formData -> date_from)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Date from is empty'
            ));
            exit();
        }

        if(empty($formData -> date_to)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Date to is empty'
            ));
            exit();
        }

        $reservation_user_id = $formData -> user_id;
        $reservation_host_id = $formData -> host_id;
        $reservation_date_from = $formData -> date_from;
        $reservation_date_to = $formData -> date_to;

        $data = array(
            "user_id" => $reservation_user_id,
            "host_id" => $reservation_host_id,
            "date_from" => $reservation_date_from,
            "date_to" => $reservation_date_to
        ); 

        $ch = curl_init($BIOME_API_ADDRESS . "reservations/create");
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
            if($httpcode == 403){
                header('HTTP/1.0 409 Conflict');
                echo json_encode(array(
                    'message' => 'Host is unavailable!'
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
}
?>
