<?php
function route($method, $urlData, $formData){

    include 'config.php';
    include 'workspace_lib.php';

    $headers = apache_request_headers();

    include "token_validation.php";

    $is_error=false;
    $response=array();
    
    if($urlData[0] == "supergroups"){
        if($urlData[1] == "create"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_edit")){
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

            $supergroup_name = $formData -> name;

            $sql = "SELECT * FROM `market_supergroups` WHERE `name`='$supergroup_name'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) > 0){
                header('HTTP/1.0 409 Conflict');
                echo json_encode(array(
                    'message' => 'Supergroup name is taken'
                ));
                exit();
            }

            $sql = "INSERT INTO `market_supergroups`(`name`) VALUES ('$supergroup_name')";
            mysqli_query($dbc, $sql);

            header('HTTP/1.1 201 Created');
            exit();
        }

        if(!isset($urlData[1])){
            if($method!='GET'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            $sql = "SELECT * FROM `market_supergroups`";
            $result = mysqli_query($dbc, $sql);

            $response_supergroups = array();

            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $supergroup = array(
                    "id" => $row['id'],
                    "name" => $row['name']
                );

                array_push($response_supergroups, $supergroup);
            }

            $response = array(
                "result" => $response_supergroups
            );

            header('HTTP/1.1 200 Success');
            echo json_encode($response);
            exit();
        }
    }

    if($urlData[0] == "groups"){
        if(!isset($urlData[1])){
            if($method!='GET'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            $sql = "SELECT * FROM `market_groups`";
            $result = mysqli_query($dbc, $sql);

            $response_groups = array();

            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $cur_group = array(
                    "id" => $row['id'],
                    "supergroup_id" => $row['supergroup_id'],
                    "name" => $row['name']
                );

                array_push($response_groups, $cur_group);
            }

            $response = array(
                "result" => $response_groups
            );

            header('HTTP/1.1 200 Success');
            echo json_encode($response);
            exit();
        }

        if($urlData[1] == "create"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_edit")){
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

            if(empty($formData -> supergroup_id)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Supergroup_id is empty'
                ));
                exit();
            }

            $product_name = $formData -> name;
            $supergroup_id = $formData -> supergroup_id;

            $sql = "SELECT * FROM `market_supergroups`";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) == 0){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Supergroup id is invalid!'
                ));
                exit();
            }

            $sql = "SELECT * FROM `market_groups` WHERE `name`='$product_name'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) > 0){
                header('HTTP/1.0 409 Conflict');
                echo json_encode(array(
                    'message' => 'Group name is taken'
                ));
                exit();
            }

            $sql = "INSERT INTO `market_groups`(`name`, `supergroup_id`) VALUES ('$product_name', '$supergroup_id')";
            mysqli_query($dbc, $sql);

            header('HTTP/1.1 201 Created');
            exit();
        }
    }

    if($urlData[0] == "products"){
        if($urlData[1] == "create"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_edit")){
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

            if(empty($formData -> group_id)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Group id is empty'
                ));
                exit();
            }

            if(empty($formData -> coast_sheme)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Coast sheme is empty'
                ));
                exit();
            }

            $product_name = $formData -> name;
            $product_group_id = $formData -> group_id;

            $sql = "SELECT * FROM `market_products` WHERE `name`='$product_name'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) > 0){
                header('HTTP/1.0 409 Conflict');
                echo json_encode(array(
                    'message' => 'Product name is taken'
                ));
                exit();
            }

            $sql = "SELECT * FROM `market_groups` WHERE `id`='$product_group_id'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) == 0){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Incorrect group id!'
                ));
                exit();
            }

            $coast_sheme = $formData -> coast_sheme;
            $coast_sheme_correct = array();
            
            if(count($coast_sheme) == 0){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Coast sheme does not contain any currency'
                ));
                exit();
            }

            foreach($coast_sheme as $currency_sheme){
                if(empty($currency_sheme -> currency_id) or empty($currency_sheme -> value)){
                    header('HTTP/1.0 400 Bad request');
                    echo json_encode(array(
                        'message' => 'Coast sheme is invalid 1'
                    ));
                    exit();
                }

                $cur_currency_id = $currency_sheme -> currency_id;
                $cur_value = $currency_sheme -> value;

                if($cur_value < 0){
                    header('HTTP/1.0 400 Bad request');
                    echo json_encode(array(
                        'message' => 'Coast sheme is invalid 2'
                    ));
                    exit();
                }

                $cur_sheme = array(
                    "currency_id" => $cur_currency_id,
                    "value" => $cur_value
                );

                array_push($coast_sheme_correct, $cur_sheme);
            }

            $coast_sheme_correct = json_encode($coast_sheme_correct);

            $sql = "INSERT INTO `market_products`(`group_id`, `name`, `quantity`, `coast_sheme`) VALUES ('$product_group_id','$product_name','0','$coast_sheme_correct')";
            mysqli_query($dbc, $sql);

            header('HTTP/1.1 201 Created');
            exit();
        }

        if($urlData[1] == "remains"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_edit")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            if(empty($formData -> product_id)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Product id is empty'
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

            $product_id = $formData -> product_id;
            $product_quantity = $formData -> value;

            if($product_quantity < 0){
                $product_quantity = 0;
            }

            $sql = "SELECT * FROM `market_products` WHERE `id`='$product_id'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) == 0){
                header('HTTP/1.0 404 Not found');
                echo json_encode(array(
                    'message' => 'Product not found'
                ));
                exit();
            }

            $product_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

            $product_old_quantity = $product_data['quantity'];

            $log_data = json_encode(array(
                "operation_type" => "correct",
                "product_id" => $product_id,
                "old_quantity" => $product_old_quantity,
                "new_quantity" => $product_quantity
            ));

            $sql = "UPDATE `market_products` SET `quantity`='$product_quantity' WHERE `id`='$product_id'";
            mysqli_query($dbc, $sql);

            $sql = "INSERT INTO `market_log`(`staff_id`, `type`, `data`, `date`) VALUES ('$user_id','correct','$log_data',NOW())";
            mysqli_query($dbc, $sql);

            header('HTTP/1.1 200 Success');
            exit();
        }

        if($urlData[1] == "sell"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
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

            if(empty($formData -> product_id)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Product id is empty'
                ));
                exit();
            }

            if(empty($formData -> currency_id)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Currency id is empty'
                ));
                exit();
            }

            $client_id = $formData -> user_id;
            $product_id = $formData -> product_id;
            $currency_id = $formData -> currency_id;

            // check is user exists

            $ch = curl_init($BIOME_API_ADDRESS . "users/$client_id");
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
            
            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if($status_code != 200){
                if($status_code == 404){
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'User not found'
                    ));
                    exit();
                }else{
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'Biome server error'
                    ));
                    exit();
                }
            }

            // end check is user exists

            // get product data

            $sql = "SELECT * FROM `market_products` WHERE `id`='$product_id'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) == 0){
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Product not found'
                ));
                exit();
            }

            $product_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

            // check product quantity and another...

            if($product_data['quantity'] < 1){
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(array(
                    'message' => 'The product is over'
                ));
                exit();
            }

            $product_coast_sheme = json_decode($product_data['coast_sheme']);
            $product_coast = 0;
            // this variable will contain product coast in selected currency
            $is_currency_available = false;
            // this variable contains currency available param
            
            foreach($product_coast_sheme as $currency_sheme){
                // searching selected currency in product coast sheme
                if($currency_sheme -> currency_id == $currency_id){
                    // currency founded
                    $product_coast = $currency_sheme -> value;
                    $is_currency_available = true;
                    break;
                }
            }

            if(!$is_currency_available){
                // selected currency is unavailable for product
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You cannot pay for this product in this currency'
                ));
                exit();
            }

            // product succesful checked

            // create transaction for user

            $data = array(
                "user_id" => $client_id,
                "summ" => $product_coast,
                "currency_id" => $currency_id,
                "type" => "payment"
            ); 

            $ch = curl_init($BIOME_API_ADDRESS . "transactions/create");
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);

            $response = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if($httpcode != 201){
                // Transaction error handler

                if($httpcode == 403){
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'Not enough money'
                    ));
                    exit();
                }else{
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'Biome server error'
                    ));
                    exit();
                }
            }

            // Transaction is success, finish sale and log

            $sql = "UPDATE `market_products` SET `quantity`=`quantity`-1 WHERE `id`='$product_id'";
            mysqli_query($dbc, $sql);

            $log_data = json_encode(array(
                "operation_type" => "sale",
                "user_id" => $product_id,
                "product_id" => $product_id,
                "currency_id" => $currency_id,
                "summ" => $product_coast
            ));

            $sql = "UPDATE `market_products` SET `quantity`='$product_quantity' WHERE `id`='$product_id'";
            mysqli_query($dbc, $sql);

            $sql = "INSERT INTO `market_log`(`staff_id`, `type`, `data`, `date`) VALUES ('$user_id','sale','$log_data',NOW())";
            mysqli_query($dbc, $sql);            
            
            header('HTTP/1.1 200 Success');
            exit();
        }

        if(isset($urlData[1])){
            if($method!='GET'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            $product_id = $urlData[1];

            $sql = "SELECT * FROM `market_products` WHERE `id`='$product_id'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) == 0){
                header('HTTP/1.0 404 Not found');
                echo json_encode(array(
                    'message' => 'Product not found'
                ));
                exit();
            }

            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

            $group_id = $row['group_id'];
            $sheme = json_decode($row['coast_sheme']);

            $sql = "SELECT * FROM `market_groups` WHERE `id`='$group_id'";
            $res = mysqli_query($dbc, $sql);

            $group_data = mysqli_fetch_array($res, MYSQLI_ASSOC);

            $product = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "quantity" => $row['quantity'],
                "group" => array(
                    "id" => $group_data['id'],
                    "supergroup_id" => $group_data['supergroup_id'],
                    "name" => $group_data['name']
                ),
                "sheme" => $sheme
            );

            $response = array(
                "result" => $product
            );

            header('HTTP/1.1 200 Success');
            echo json_encode($response);
            exit();
        }

        if(!isset($urlData[1])){
            if($method!='GET'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            $sql = "SELECT * FROM `market_products`";
            $result = mysqli_query($dbc, $sql);

            $products_list = array();

            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $group_id = $row['group_id'];
                $sheme = json_decode($row['coast_sheme']);

                $sql = "SELECT * FROM `market_groups` WHERE `id`='$group_id'";
                $res = mysqli_query($dbc, $sql);

                $group_data = mysqli_fetch_array($res, MYSQLI_ASSOC);

                $cur_product = array(
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "quantity" => $row['quantity'],
                    "group" => array(
                        "id" => $group_data['id'],
                        "supergroup_id" => $group_data['supergroup_id'],
                        "name" => $group_data['name']
                    ),
                    "sheme" => $sheme
                );

                array_push($products_list, $cur_product);
            }

            $response = array(
                "result" => $products_list
            );

            header('HTTP/1.1 200 Success');
            echo json_encode($response);
            exit();
        }
    }

    if($urlData[0] == "invoice"){
        if($urlData[1] == "create"){ 
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_edit")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            if(empty($formData -> type)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Type is empty'
                ));
                exit();
            }

            if(empty($formData -> price)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Price id is empty'
                ));
                exit();
            }

            if(empty($formData -> products_list)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Products list sheme is empty'
                ));
                exit();
            }

            $type = $formData -> type;
            $price = $formData -> price;
            $products_list = $formData -> products_list;

            if($type != "entrance" AND $type != "cancellation"){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Invalid invoice type!'
                ));
                exit();
            }

            if($price < 0){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Invalid invoice price!'
                ));
                exit();
            }

            if(count($products_list) < 1){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Invalid invoice products_list!'
                ));
                exit();
            }

            $products_list_correct = array();

            foreach ($products_list as $product) {
                if(empty($product -> product_id) OR empty($product -> value)){
                    header('HTTP/1.0 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'Invalid product in list!'
                    ));
                    exit();
                }

                $cur_prod_id = $product -> product_id;
                $cur_prod_value = $product -> value;

                if($cur_prod_value < 1){
                    header('HTTP/1.0 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'Invalid product in list!'
                    ));
                    exit();
                }

                $sql = "SELECT * FROM `market_products` WHERE `id`='$cur_prod_id'";
                $result = mysqli_query($dbc, $sql);

                if(mysqli_num_rows($result) == 0){
                    header('HTTP/1.0 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'Invalid product in list!'
                    ));
                    exit();
                }

                $products_list_correct_item = array(
                    "product_id" => $cur_prod_id,
                    "value" => $cur_prod_value
                );

                array_push($products_list_correct, $products_list_correct_item);
            }

            $products_list_correct = json_encode($products_list_correct);

            $sql = "INSERT INTO `market_invoices`(`created_by`, `type`, `price`, `products_list`, `creation_date`, `status`) VALUES ('$user_id','$type','$price','$products_list_correct',NOW(),'created')";
            mysqli_query($dbc, $sql);

            header('HTTP/1.1 201 Created');
            exit();
        }

        if($urlData[1] == "list"){ 
            if($method!='GET'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            if(!isset($urlData[2])){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Incorrect type!'
                ));
                exit();
            }

            if(isset($formData['status'])){
                $status = $formData['status'];
            }else{
                $status = false;
            }

            $type = $urlData[2];

            $status = $formData['status'];

            $sql = "SELECT * FROM `market_invoices`";
            $is_params_added = false;

            if($type != "all"){
                $is_params_added = true;
                $sql = $sql . " WHERE `type`='$type'";
            }

            if($status != false){
                if($is_params_added){
                    $is_params_added = true;
                    $sql = $sql . " AND `status`='$status'";
                }else{
                    $sql = $sql . " WHERE `status`='$status'";
                }
            }

            $result = mysqli_query($dbc, $sql);

            $invoices_list = array();

            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $invoice_products_list = json_decode($row['products_list']);
                $response_products_list = array();

                foreach($invoice_products_list as $single_position){
                    $single_product_id = $single_position -> product_id;
                    $value = $single_position -> value;

                    $sql = "SELECT * FROM `market_products` WHERE `id`='$single_product_id'";
                    $res = mysqli_query($dbc, $sql);

                    $product_data = mysqli_fetch_array($res, MYSQLI_ASSOC);

                    $single_response_prod = array(
                        "id" => $single_product_id,
                        "name" => $product_data['name'],
                        "value" => $value
                    );

                    array_push($response_products_list, $single_response_prod);
                }

                $invoice_doc = array(
                    "id" => $row['id'],
                    "created_by" => $row['created_by'],
                    "type" => $row['type'],
                    "price" => $row['price'],
                    "creation_date" => $row['creation_date'],
                    "status" => $row['status'],
                    "products_list" => $response_products_list
                );

                array_push($invoices_list, $invoice_doc);
            }

            $response = array(
                "result" => $invoices_list
            );

            header('HTTP/1.1 200 Success');
            echo json_encode($response);
            exit();
        }

        if($urlData[1] == "accept"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            if(!isset($urlData[2])){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Incorrect invoice id!'
                ));
                exit();
            }

            $invoice_id = $urlData[2];

            $sql = "SELECT * FROM `market_invoices` WHERE `id`='$invoice_id'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) == 0){
                header('HTTP/1.0 404 Not found');
                echo json_encode(array(
                    'message' => 'Invoice not found!'
                ));
                exit();
            }

            $invoice_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

            if($invoice_data['status'] != 'created'){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Invoice already accepted or deleted!'
                ));
                exit();
            }

            $invoice_type = $invoice_data['type'];
            $symbol = ($invoice_type == "entrance") ? "+" : "-";

            $products_list = json_decode($invoice_data['products_list']);

            foreach($products_list as $position){
                $product_id = $position -> product_id;
                $value = $position -> value;

                $sql = "UPDATE `market_products` SET `quantity`=`quantity` $symbol $value WHERE `id`='$product_id'";
                mysqli_query($dbc, $sql);
            }

            $sql = "UPDATE `market_invoices` SET `status`='accepted' WHERE `id`='$invoice_id'";
            mysqli_query($dbc, $sql);

            $log_data = json_encode(array(
                "invoice_id" => $invoice_id
            ));

            $sql = "INSERT INTO `market_log`(`staff_id`, `type`, `data`, `date`) VALUES ('$user_id','invoice_acceptance','$log_data',NOW())";
            mysqli_query($dbc, $sql);

            header('HTTP/1.0 200 Success');
            exit();
        }

        if($urlData[1] == "delete"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            if(!isset($urlData[2])){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Incorrect invoice id!'
                ));
                exit();
            }

            $invoice_id = $urlData[2];

            $sql = "SELECT * FROM `market_invoices` WHERE `id`='$invoice_id'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) == 0){
                header('HTTP/1.0 404 Not found');
                echo json_encode(array(
                    'message' => 'Invoice not found!'
                ));
                exit();
            }

            $invoice_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

            if($invoice_data['status'] != 'created'){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Invoice already accepted or deleted!'
                ));
                exit();
            }

            $sql = "UPDATE `market_invoices` SET `status`='deleted' WHERE `id`='$invoice_id'";
            mysqli_query($dbc, $sql);

            $log_data = json_encode(array(
                "invoice_id" => $invoice_id
            ));

            $sql = "INSERT INTO `market_log`(`staff_id`, `type`, `data`, `date`) VALUES ('$user_id','invoice_delete','$log_data',NOW())";
            mysqli_query($dbc, $sql);

            header('HTTP/1.0 200 Success');
            exit();
        }
    }

    if($urlData[0] == "combo"){
        if($urlData[1] == "create"){ 
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_edit")){
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

            if(empty($formData -> coast_sheme)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Coast sheme is empty'
                ));
                exit();
            }

            if(empty($formData -> positions)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Products list sheme is empty'
                ));
                exit();
            }

            $name = $formData -> name;
            $coast_sheme = $formData -> coast_sheme;
            $positions = $formData -> positions;
            // содержит n массивов, каждый из которых содержит варианты
            // при покупке комбо из каждого такого массива выбирается один товар
            
            $coast_sheme_correct = array();
            
            if(count($coast_sheme) == 0){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Coast sheme does not contain any currency'
                ));
                exit();
            }

            if(count($positions) == 0){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Positions does not contain any position'
                ));
                exit();
            }

            $sql = "SELECT * FROM `market_combo` WHERE `name`='$name'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) > 0){
                header('HTTP/1.0 409 Conflict');
                echo json_encode(array(
                    'message' => 'Combo name is not unique!'
                ));
                exit();
            }

            foreach($coast_sheme as $currency_sheme){
                if(empty($currency_sheme -> currency_id) or empty($currency_sheme -> value)){
                    header('HTTP/1.0 400 Bad request');
                    echo json_encode(array(
                        'message' => 'Coast sheme is invalid!'
                    ));
                    exit();
                }

                $cur_currency_id = $currency_sheme -> currency_id;
                $cur_value = $currency_sheme -> value;

                if($cur_value < 0){
                    header('HTTP/1.0 400 Bad request');
                    echo json_encode(array(
                        'message' => 'Coast sheme is invalid!'
                    ));
                    exit();
                }

                $cur_sheme = array(
                    "currency_id" => $cur_currency_id,
                    "value" => $cur_value
                );

                array_push($coast_sheme_correct, $cur_sheme);
            }

            $coast_sheme_correct = json_encode($coast_sheme_correct);

            $positions_correct = array();

            foreach($positions as $position){
                // position содержит список вариантов для выбора позиции в дальнейшем
                // каждый элемент массива position доложен содержать...
                // ..product_id и product_count

                if(count($position) == 0){
                    header('HTTP/1.0 400 Bad request');
                    echo json_encode(array(
                        'message' => 'One of positions is empty!'
                    ));
                    exit();
                }

                $correct_position = array();

                foreach($position as $option_position){
                    if(empty($option_position -> product_id) or empty($option_position -> product_count)){
                        header('HTTP/1.0 400 Bad request');
                        echo json_encode(array(
                            'message' => 'One of positions is invalid!'
                        ));
                        exit();
                    }

                    $cur_product_id = $option_position -> product_id;
                    $cur_product_count = $option_position -> product_count;

                    if($cur_product_count < 1){
                        header('HTTP/1.0 400 Bad request');
                        echo json_encode(array(
                            'message' => 'One of positions is invalid!'
                        ));
                        exit();
                    }

                    $sql = "SELECT * FROM `market_products` WHERE `id`='$cur_product_id'";
                    $result = mysqli_query($dbc, $sql);

                    if(mysqli_num_rows($result) < 1){
                        header('HTTP/1.0 400 Bad request');
                        echo json_encode(array(
                            'message' => 'One of positions is invalid!'
                        ));
                        exit();
                    }

                    $correct_option = array(
                        "product_id" => $cur_product_id,
                        "product_count" => $cur_product_count
                    );

                    array_push($correct_position, $correct_option);
                }
                
                array_push($positions_correct, $correct_position);
            }

            $positions_correct = json_encode($positions_correct);

            $sql = "INSERT INTO `market_combo`(`name`, `coast_sheme`, `products`) VALUES ('$name','$coast_sheme_correct','$positions_correct')";
            mysqli_query($dbc, $sql);

            header('HTTP/1.1 201 Created');
            exit();
        }

        if($urlData[1] == "sell"){
            if($method!='POST'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
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

            if(empty($formData -> currency_id)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Currency id is empty'
                ));
                exit();
            }

            if(empty($formData -> combo_id)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Combo id is empty'
                ));
                exit();
            }

            if(empty($formData -> positions)){
                header('HTTP/1.0 400 Bad request');
                echo json_encode(array(
                    'message' => 'Positions is empty'
                ));
                exit();
            }

            $client_id = $formData -> user_id;
            $currency_id = $formData -> currency_id;
            $combo_id = $formData -> combo_id;
            $positions = $formData -> positions;

            // check is user exists

            $ch = curl_init($BIOME_API_ADDRESS . "users/$client_id");
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_USERPWD, $BIOME_AUTH_STR);
            
            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if($status_code != 200){
                if($status_code == 404){
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'User not found'
                    ));
                    exit();
                }else{
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(array(
                        'message' => 'Biome server error'
                    ));
                    exit();
                }
            }

            // end check is user exists

            // check is combo exist and get data

            $sql = "SELECT * FROM `market_combo` WHERE `id`='$combo_id'";
            $result = mysqli_query($dbc, $sql);

            if(mysqli_num_rows($result) < 1){
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(array(
                    'message' => 'Incorrect combo id!'
                ));
                exit();
            }

            $combo_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

            $combo_coast_sheme = json_decode($combo_data['coast_sheme']);
            $combo_positions = json_decode($combo_data['products']);

            // end check is combo exist and get data

            // check is selected currency_id available

            $combo_coast = 0;
            // this variable will contain product coast in selected currency
            $is_currency_available = false;
            // this variable contains currency available param
            
            foreach($combo_coast_sheme as $currency_sheme){
                // searching selected currency in product coast sheme
                if($currency_sheme -> currency_id == $currency_id){
                    // currency founded
                    $combo_coast = $currency_sheme -> value;
                    $is_currency_available = true;
                    break;
                }
            }

            if(!$is_currency_available){
                // selected currency is unavailable for product
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You cannot pay for this combo in this currency'
                ));
                exit();
            }

            // end check is selected currency_id available


            // check selected positions

            // end check selected positions


            // check is positions quantity=0

            // end check is positions quantity=0


            // create transaction for user

            // end create transaction for user


            // change products quantity and add log data

            // end change products quantity and add log data


            header('HTTP/1.1 200 Success');
            exit();
        }

        if(!isset($urlData[1])){
            if($method!='GET'){
                header('HTTP/1.0 405 Method Not Allowed');
                exit();
            }

            if(!get_access_flag_value($user_id, "market_main")){
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'message' => 'You have no access'
                ));
                exit();
            }

            $sql = "SELECT * FROM `market_combo`";
            $result = mysqli_query($dbc, $sql);

            $combo_list = array();

            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $current_combo_id = $row['id'];
                $current_combo_name = $row['name'];
                $current_combo_coast_sheme = json_decode($row['coast_sheme']);
                $current_combo_products = json_decode($row['products']);

                $cur_combo = array(
                    "id" => $current_combo_id,
                    "name" => $current_combo_name,
                    "coast_sheme" => $current_combo_coast_sheme,
                    "products" => $current_combo_products
                );

                array_push($combo_list, $cur_combo);
            }

            $response = array(
                "result" => $combo_list
            );

            header('HTTP/1.1 200 Success');
            echo json_encode($response);
            exit();
        }
    }

    if($urlData[0] == "log"){
        if($method!='GET'){
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if(!get_access_flag_value($user_id, "market_main")){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'You have no access'
            ));
            exit();
        }

        if(!isset($urlData[1])){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Log type is empty'
            ));
            exit();
        }

        $type = $urlData[1];
        $date_from = false;
        $date_to = false;

        if($type != "all" AND $type != "sale" AND $type != "correct"){
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'message' => 'Invalid log type'
            ));
            exit();
        }

        if(isset($formData['date_from'])){
            $date_from = $formData['date_from'];
        }

        if(isset($formData['date_to'])){
            $date_to = $formData['date_to'];
        }

        $is_filter_added = false;

        if($type == "all"){
            $sql = "SELECT * FROM `market_log`";
        }else{
            $sql = "SELECT * FROM `market_log` WHERE `type`='$type'";
            $is_filter_added = true;
        }

        if($date_from != false){
            if(!$is_filter_added){
                $sql = $sql . " WHERE ";
                $is_filter_added = true;
            }else{
                $sql = $sql . " AND ";
            }

            $sql = $sql . "`date` > '$date_from'";
        }

        if($date_to != false){
            if(!$is_filter_added){
                $sql = $sql . " WHERE ";
                $is_filter_added = true;
            }else{
                $sql = $sql . " AND ";
            }

            $sql = $sql . "`date` < '$date_to'";
        }

        $sql = $sql . " ORDER BY `date` DESC";

        $result = mysqli_query($dbc, $sql);
        $history = array();

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $log_id = $row['id'];
            $staff_id = $row['staff_id'];
            $log_type = $row['type'];
            $data = json_decode($row['data']);
            $date = $row['date'];

            if($log_type == "correct"){
                $product_id = $data -> product_id;
                $sql = "SELECT * FROM `market_products` WHERE `id`='$product_id'";

                $res = mysqli_query($dbc, $sql);

                $product_data = mysqli_fetch_array($res, MYSQLI_ASSOC);

                $cur_log = array(
                    "id" => $log_id,
                    "staff_id" => $staff_id,
                    "type" => $log_type,
                    "date" => $date,
                    "product" => array(
                        "id" => $product_data['id'],
                        "name" => $product_data['name'],
                        "old_quantity" => $data -> old_quantity,
                        "new_quantity" => $data -> new_quantity
                    )
                );
            }

            if($log_type == "sale"){
                $product_id = $data -> product_id;
                $sql = "SELECT * FROM `market_products` WHERE `id`='$product_id'";

                $res = mysqli_query($dbc, $sql);

                $product_data = mysqli_fetch_array($res, MYSQLI_ASSOC);

                $cur_log = array(
                    "id" => $log_id,
                    "staff_id" => $staff_id,
                    "type" => $log_type,
                    "date" => $date,
                    "sale" => array(
                        "currency_id" => $data -> currency_id,
                        "summ" => $data -> summ,
                        "user_id" => $data -> user_id,
                        "product" => array(
                            "id" => $product_data['id'],
                            "name" => $product_data['name']
                        )
                    )
                );
            }

            array_push($history, $cur_log);
        }

        $response = array(
            "result" => $history
        );

        header('HTTP/1.1 200 Success');
        echo json_encode($response);
        exit();
    }
}
?>
