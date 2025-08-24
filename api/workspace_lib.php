<?php
function get_access_flag_value($staff_id, $flag){
    include "config.php";
    // get flag value and return

    $sql = "SELECT * FROM `staff` WHERE `id`='$staff_id'";
    $result = mysqli_query($dbc, $sql);

    if(mysqli_num_rows($result) == 0){
        return false;
    }

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $staffgroup_id = $row['staffgroup_id'];

    $sql = "SELECT * FROM `staffgroups` WHERE `id`='$staffgroup_id'";
    $result = mysqli_query($dbc, $sql);

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $flags_data = json_decode($row['rights']);

    switch($flag){
        case "users_get":
            return $flags_data -> access_flags -> users_get;
        break;
        case "users_edit":
            return $flags_data -> access_flags -> users_edit;
        break;
        case "payments":
            return $flags_data -> access_flags -> payments;
        break;
        case "config":
            return $flags_data -> access_flags -> config;
        break;
        case "staff":
            return $flags_data -> access_flags -> staff;
        break;
        case "staffgroups":
            return $flags_data -> access_flags -> staffgroups;
        break;
        case "hosts_controls":
            return $flags_data -> access_flags -> hosts_controls;
        break;
        case "hosts_edit":
            return $flags_data -> access_flags -> hosts_edit;
        break;
        case "market_main":
            return $flags_data -> access_flags -> market_main;
        break;
        case "market_edit":
            return $flags_data -> access_flags -> market_edit;
        break;
        case "apps":
            return $flags_data -> access_flags -> apps;
        break;
        default:
            return false;
        break;
    }
}

function get_config_param($param){
     include "config.php";
    // get param value and return

    $sql = "SELECT * FROM `config` WHERE `param`='$param'";
    $result = mysqli_query($dbc, $sql);

    if(mysqli_num_rows($result) == 0){
        return false;
    }

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    return $row['value'];
}
?>