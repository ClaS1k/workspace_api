<?php
function route($method, $urlData, $formData){

  include 'config.php';
  include 'workspace_lib.php';

  $headers = apache_request_headers();

  include "token_validation.php";

  $is_error=false;
  $response=array();

    if (!isset($urlData[0])){
        // [GET] api/usergroups

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

        $ch = curl_init($BIOME_API_ADDRESS . "usergroups");
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch($status_code){
            case 200:
                $full_groups_list = json_decode($response);
                $full_groups_list =  $full_groups_list -> result;

                $response_groups_list = array();

                foreach($full_groups_list as $single_group){
                    $cur_group = array(
                        "id" => $single_group -> id,
                        "name" => $single_group -> name,
                        "billing_profile_id" => $single_group -> billing_profile_id
                    );

                    array_push($response_groups_list, $cur_group);
                }

                $response = array(
                    "result" => $response_groups_list 
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
