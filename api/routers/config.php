<?php
function route($method, $urlData, $formData){

  include 'config.php';
  include 'workspace_lib.php';

  $headers = apache_request_headers();

  include "token_validation.php";

  $is_error=false;
  $response=array();

    if(!isset($urlData[0])){
        // [GET] api/config

        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "config")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }
        
        $sql = "SELECT * FROM `config`";
        $result = mysqli_query($dbc, $sql);

        $params_list = array();

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $params_list[$row['param']] = $row['value'];
        }

        $response = array(
            "result" => $params_list
        );

        header('HTTP/1.1 200 Success');
        echo json_encode($response);
        exit();
    }

    if($urlData[0] == "update"){
        // [POST] api/config/update

        if($method!='POST'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "config")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(empty($formData -> key)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Key is empty'
            ));
            exit();
        }

        if(empty($formData -> value)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Value is empty'
            ));
            exit();
        }

        $param_key = $formData -> key;
        $param_value = $formData -> value;

        $sql = "UPDATE `config` SET `value`='$param_value' WHERE `param`='$param_key'";
        mysqli_query($dbc, $sql);

        header('HTTP/1.1 200 Success');
        exit();
    }
}
?>
