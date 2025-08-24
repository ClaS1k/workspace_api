<?php
function route($method, $urlData, $formData){

  include 'config.php';

  $headers = apache_request_headers();

  $is_error=false;
  $response=array();

  if (!isset($urlData[0])){
    // [POST] api/auth

    if($method!='POST'){
       header('HTTP/1.0 405 Method Not Allowed');
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

    $username = $formData -> username;
    $password = $formData -> password;
    
    //валидация логина и пароля админа для workspace
    
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

    switch($status_code){
        case 200:
            // успешная валидация
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

                $sql = "INSERT INTO `staff`(`id`, `token`, `auth`, `staffgroup_id`, `name`, `second_name`) VALUES ('$valid_user_id','$token','$admin_auth_data', '1', '-', '-')";
                mysqli_query($dbc, $sql);

                $response = array(
                    "result" => array(
                        "user_id" => $valid_user_id,
                        "token" => $token
                    )
                );

                header('HTTP/1.1 202 Accepted');
                echo json_encode($response);
            }else{
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $token = $row['token'];
                $staffgroup_id = $row['staffgroup_id'];

                $sql = "SELECT * FROM `staffgroups` WHERE `id`='$staffgroup_id'";
                $res = mysqli_query($dbc, $sql);
                $staffgroup_data = mysqli_fetch_array($res, MYSQLI_ASSOC);
                
                $staffgroup_flags = json_decode($staffgroup_data['rights']);

                $response = array(
                    "result" => array(
                        "web_access" => $staffgroup_flags -> web -> access,
                        "mobile_access" => $staffgroup_flags -> mobile -> access,
                        "user_id" => $valid_user_id,
                        "token" => $token
                    )
                );

                header('HTTP/1.0 202 Accepted');
                echo json_encode($response);
            }
        break;
        case 401:
            // не авторизован
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(array(
                'message' => 'Invalid username or password'
            ));
            exit();
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
