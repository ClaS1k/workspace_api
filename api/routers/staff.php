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

        if(!get_access_flag_value($user_id, "staff")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        $sql = "SELECT * FROM `staff`";
        $result = mysqli_query($dbc, $sql);

        $staff_list = array();

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $staffgroup_id = $row['staffgroup_id'];

            $sql = "SELECT * FROM `staffgroups` WHERE `id`='$staffgroup_id'";
            $res = mysqli_query($dbc, $sql);

            $staffgroup_data = mysqli_fetch_array($res, MYSQLI_ASSOC);

            $single_staff = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "second_name" => $row['second_name'],
                "staffgroup" => array(
                    "id" => $staffgroup_data['id'],
                    "name" => $staffgroup_data['name']
                )
            );
            
            array_push($staff_list, $single_staff);
        }

        $response = array(
            "result" => $staff_list
        );

        header('HTTP/1.1 200 Success');
        echo json_encode($response);
        exit();
    }

    if($urlData[0] == "staffgroup"){
        if($method!='POST'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "staff")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(!isset($urlData[1])){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Staff id not setted'
            ));
            exit();
        }

        if(!isset($urlData[2])){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Staffgroup id not setted'
            ));
            exit();
        }

        $staff_id = $urlData[1];
        $staffgroup_id = $urlData[2];
        
        $sql = "SELECT * FROM `staffgroups` WHERE `id`='$staffgroup_id'";
        $result = mysqli_query($dbc, $sql);

        if(mysqli_num_rows($result) == 0){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Staffgroup not found'
            ));
            exit();
        }

        $sql = "UPDATE `staff` SET `staffgroup_id`='$staffgroup_id' WHERE `id`='$staff_id'";
        mysqli_query($dbc, $sql);

        header('HTTP/1.0 200 Success');
        exit();
    }

    if($urlData[0] == "create"){
        if($method!='POST'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "staff")){
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

        if(empty($formData -> staffgroup_id)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Staffgroup id is empty'
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

        if(empty($formData -> second_name)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Second name is empty'
            ));
            exit();
        }

        $username = $formData -> username;
        $password = $formData -> password;
        $staffgroup_id = $formData -> staffgroup_id;
        $name = $formData -> name;
        $second_name = $formData -> second_name;

        $sql = "SELECT * FROM `staffgroups` WHERE `id`='$staffgroup_id'";
        $result = mysqli_query($dbc, $sql);

        if(mysqli_num_rows($result) == 0){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Staffgroup is not found'
            ));
            exit();
        }

        $data = array(
            "username" => $username,
            "password" => $password
        ); 

        $ch = curl_init($BIOME_API_ADDRESS . "admins/create");
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
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Not unique data or server error!'
            ));
            exit();
        }
        
        $biome_auth_str = $username . ":" . $password;
        // авторизационная строка для biome 

        $ch = curl_init($BIOME_API_ADDRESS . "admins/validate");
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $biome_auth_str);

        $response = curl_exec($ch);
        
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $valid_user_data = json_decode($response);

        $valid_user_id = $valid_user_data -> result -> user_id;

        $sql = "SELECT * FROM `staff` WHERE `id`='$valid_user_id'";
        $result = mysqli_query($dbc, $sql);

        if(mysqli_num_rows($result) == 0){
            // если админ ещё не входил в workspace - создаём пользователя в подсистеме
                
            while(true){
                // цикл нужен, чтоб гарантировать, что токены не совпадут
                $token = bin2hex(random_bytes(9));

                $sql = "SELECT * FROM `staff` WHERE `token`='$token'";
                $result = mysqli_query($dbc, $sql);
                    // получаем список пользователей с таким-же токеном

                if(mysqli_num_rows($result) == 0){
                    // если таких пользователей нет - токен свободен
                    break;
                }
            }
                
            $admin_auth_data = array(
                "username" => $username,
                "password" => $password
            );

            $admin_auth_data = json_encode($admin_auth_data);
            // создаём json строку с логином и паролем админа
            // с этими авторизационными данными будут идти запросы в biome

            $sql = "INSERT INTO `staff`(`id`, `token`, `auth`, `staffgroup_id`, `name`, `second_name`) VALUES ('$valid_user_id','$token','$admin_auth_data', '$staffgroup_id', '$name', '$second_name')";
            mysqli_query($dbc, $sql);
        }

        header('HTTP/1.0 201 Created');
        exit();
    }

}
?>
