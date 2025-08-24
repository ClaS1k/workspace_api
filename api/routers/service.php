<?php
function route($method, $urlData, $formData){

  include 'config.php';
  include 'workspace_lib.php';

  $headers = apache_request_headers();

  include "token_validation.php";

  $is_error=false;
  $response=array();

    if($urlData[0] == "access"){
        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        $sql = "SELECT * FROM `staff` WHERE `id`='$user_id'";
        $result = mysqli_query($dbc, $sql);

        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $staffgroup_id = $row['staffgroup_id'];

        $sql = "SELECT * FROM `staffgroups` WHERE `id`='$staffgroup_id'";
        $result = mysqli_query($dbc, $sql);

        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $sheme = json_decode($row['rights']);

        $response = array(
            "result" => $sheme
        );

        header('HTTP/1.1 200 Success');
        echo json_encode($response);
        exit();
    }

}
?>
