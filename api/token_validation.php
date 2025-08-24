<?php
  //тут проверяем токен, если она задан
  $token=$headers['auth'];

  $sql = "SELECT * FROM `staff` WHERE `token`='$token'";
  $result = mysqli_query($dbc, $sql);

  if(mysqli_num_rows($result)==0){
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(array(
        'message' => 'You need authorization'
    ));
    exit();
  }

  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $user_id = $row['id'];
  $user_auth = json_decode($row['auth']);

  $BIOME_USERNAME = $user_auth -> username;
  $BIOME_PASSWORD = $user_auth -> password;

  $BIOME_AUTH_STR = "$BIOME_USERNAME:$BIOME_PASSWORD";
?>
