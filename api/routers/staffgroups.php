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

        if(!get_access_flag_value($user_id, "staffgroups")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        $sql = "SELECT * FROM `staffgroups`";
        $result = mysqli_query($dbc, $sql);

        $staffgroups_list = array();

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $staffgroup = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "sheme" => json_decode($row['rights'])
            );

            array_push($staffgroups_list, $staffgroup);
        }

        $response = array(
            "result" => $staffgroups_list
        );

        header('HTTP/1.1 200 Success');
        echo json_encode($response);
        exit();
    }

    if($urlData[0] == "update"){
        if($method!='POST'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "staffgroups")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(empty($formData -> rights)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Rights is empty'
            ));
            exit();
        }

        if(!isset($urlData[1])){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Staffgroup id is empty'
            ));
            exit();
        }

        $staffgroup_id = $urlData[1];

        $sql = "SELECT * FROM `staffgroups` WHERE `id`='$staffgroup_id'";
        $result = mysqli_query($dbc, $sql);

        if(mysqli_num_rows($result) == 0){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Staffgroup not found'
            ));
            exit();
        }

        $rights_sheme = array(
            "access_flags" => array(
                "users_get" => isset($formData -> rights -> access_flags -> users_get) ? ($formData -> rights -> access_flags -> users_get) : false,
                "users_edit" => isset($formData -> rights -> access_flags -> users_edit) ? ($formData -> rights -> access_flags -> users_edit) : false,
                "payments" => isset($formData -> rights -> access_flags -> payments) ? ($formData -> rights -> access_flags -> payments) : false,
                "config" => isset($formData -> rights -> access_flags -> config) ? ($formData -> rights -> access_flags -> config) : false,
                "staff" => isset($formData -> rights -> access_flags -> staff) ? ($formData -> rights -> access_flags -> staff) : false,
                "staffgroups" => isset($formData -> rights -> access_flags -> staffgroups) ? ($formData -> rights -> access_flags -> staffgroups) : false,
                "hosts_controls" => isset($formData -> rights -> access_flags -> hosts_controls) ? ($formData -> rights -> access_flags -> hosts_controls) : false,
                "hosts_edit" => isset($formData -> rights -> access_flags -> hosts_edit) ? ($formData -> rights -> access_flags -> hosts_edit) : false,
                "market_main" => isset($formData -> rights -> access_flags -> market_main) ? ($formData -> rights -> access_flags -> market_main) : false,
                "market_edit" => isset($formData -> rights -> access_flags -> market_edit) ? ($formData -> rights -> access_flags -> market_edit) : false,
                "apps" => isset($formData -> rights -> access_flags -> apps) ? ($formData -> rights -> access_flags -> apps) : false
            ),
            "web" => array(
                "access" => isset($formData -> rights -> web -> access) ? ($formData -> rights -> web -> access) : false,
                "tabs_flags" => array(
                    "hosts" => isset($formData -> rights -> web -> tabs_flags -> hosts) ? ($formData -> rights -> web -> tabs_flags -> hosts) : false,
                    "sales" => isset($formData -> rights -> web -> tabs_flags -> sales) ? ($formData -> rights -> web -> tabs_flags -> sales) : false,
                    "market" => isset($formData -> rights -> web -> tabs_flags -> market) ? ($formData -> rights -> web -> tabs_flags -> market) : false,
                    "staff" => isset($formData -> rights -> web -> tabs_flags -> staff) ? ($formData -> rights -> web -> tabs_flags -> staff) : false,
                    "apps" => isset($formData -> rights -> web -> tabs_flags -> apps) ? ($formData -> rights -> web -> tabs_flags -> apps) : false,
                    "config" => isset($formData -> rights -> web -> tabs_flags -> config) ? ($formData -> rights -> web -> tabs_flags -> config) : false,
                    "users" => isset($formData -> rights -> web -> tabs_flags -> users) ? ($formData -> rights -> web -> tabs_flags -> users) : false
                )
            ),
            "mobile" => array(
                "access" => isset($formData -> rights -> mobile -> access) ? ($formData -> rights -> mobile -> access) : false
            )
        );

        $rights_json = json_encode($rights_sheme);

        $sql = "UPDATE `staffgroups` SET `rights`='$rights_json' WHERE `id`='$staffgroup_id'";
        mysqli_query($dbc, $sql);

        header('HTTP/1.0 200 Success');
        exit();
    }

    if($urlData[0] == "create"){
        if($method!='POST'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "staffgroups")){
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

        if(empty($formData -> rights)){
            header('HTTP/1.0 400 Bad request');
            echo json_encode(array(
                'message' => 'Rights is empty'
            ));
            exit();
        }

        $rights_sheme = array(
            "access_flags" => array(
                "users_get" => isset($formData -> rights -> access_flags -> users_get) ? ($formData -> rights -> access_flags -> users_get) : false,
                "users_edit" => isset($formData -> rights -> access_flags -> users_edit) ? ($formData -> rights -> access_flags -> users_edit) : false,
                "payments" => isset($formData -> rights -> access_flags -> payments) ? ($formData -> rights -> access_flags -> payments) : false,
                "config" => isset($formData -> rights -> access_flags -> config) ? ($formData -> rights -> access_flags -> config) : false,
                "staff" => isset($formData -> rights -> access_flags -> staff) ? ($formData -> rights -> access_flags -> staff) : false,
                "staffgroups" => isset($formData -> rights -> access_flags -> staffgroups) ? ($formData -> rights -> access_flags -> staffgroups) : false,
                "hosts_controls" => isset($formData -> rights -> access_flags -> hosts_controls) ? ($formData -> rights -> access_flags -> hosts_controls) : false,
                "hosts_edit" => isset($formData -> rights -> access_flags -> hosts_edit) ? ($formData -> rights -> access_flags -> hosts_edit) : false,
                "market_main" => isset($formData -> rights -> access_flags -> market_main) ? ($formData -> rights -> access_flags -> market_main) : false,
                "market_edit" => isset($formData -> rights -> access_flags -> market_edit) ? ($formData -> rights -> access_flags -> market_edit) : false,
                "apps" => isset($formData -> rights -> access_flags -> apps) ? ($formData -> rights -> access_flags -> apps) : false
            ),
            "web" => array(
                "access" => isset($formData -> rights -> web -> access) ? ($formData -> rights -> web -> access) : false,
                "tabs_flags" => array(
                    "hosts" => isset($formData -> rights -> web -> tabs_flags -> hosts) ? ($formData -> rights -> web -> tabs_flags -> hosts) : false,
                    "sales" => isset($formData -> rights -> web -> tabs_flags -> sales) ? ($formData -> rights -> web -> tabs_flags -> sales) : false,
                    "market" => isset($formData -> rights -> web -> tabs_flags -> market) ? ($formData -> rights -> web -> tabs_flags -> market) : false,
                    "staff" => isset($formData -> rights -> web -> tabs_flags -> staff) ? ($formData -> rights -> web -> tabs_flags -> staff) : false,
                    "apps" => isset($formData -> rights -> web -> tabs_flags -> apps) ? ($formData -> rights -> web -> tabs_flags -> apps) : false,
                    "config" => isset($formData -> rights -> web -> tabs_flags -> config) ? ($formData -> rights -> web -> tabs_flags -> config) : false,
                    "users" => isset($formData -> rights -> web -> tabs_flags -> users) ? ($formData -> rights -> web -> tabs_flags -> users) : false
                )
            ),
            "mobile" => array(
                "access" => isset($formData -> rights -> mobile -> access) ? ($formData -> rights -> mobile -> access) : false
            )
        );

        $staffgroup_name = $formData -> name;
        $rights_json = json_encode($rights_sheme);

        $sql = "SELECT * FROM `staffgroups` WHERE `name`='$staffgroup_name'";
        $result = mysqli_query($dbc, $sql);

        if(mysqli_num_rows($result) > 0){
            header('HTTP/1.0 409 Conflict');
            echo json_encode(array(
                'message' => 'Group name is taken'
            ));
            exit();
        }

        $sql = "INSERT INTO `staffgroups`(`name`, `rights`) VALUES ('$staffgroup_name','$rights_json')";
        mysqli_query($dbc, $sql);

        header('HTTP/1.0 201 Created');
        exit();
    }

}
?>
