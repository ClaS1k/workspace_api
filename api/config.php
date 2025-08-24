<?php

$dbc=mysqli_connect('localhost', 'root', '', 'workspace');
// база данных mysql

$WORKSPACE_API_ADRESS = "http://workspace/api/";
//адрес api приложения

$WORKSPACE_MESSAGE_PREFIX = "[Workspace]";

$TELEGRAMM_BOT_API_ADRESS = "http://example.com/";
//адрес api телеграм бота
$TELEGRAMM_BOT_TOKEN = "";

$SMS_SERVICE_USERNAME = "";
 //логин и пароль для сервиса SMS
$SMS_SERVICE_PASSWORD = "";

//строка для отправки СМС
$SMS_SERVICE_ADRESS = "https://smsc.ru/sys/send.php?login=$SMS_SERVICE_USERNAME&psw=$SMS_SERVICE_PASSWORD&";

$SERVICE_TOKEN = "";
//токен для сервисных методов(не используются обыными пользователями)

$KKM_SERVICE_ADRESS = "http://expample.com/Execute";
//адрес API ККМ webserver

$KKM_AUTH_STRING = "User:Password";
//строка авторизации ККМ в формате ЛОГИН|ПАРОЛЬ


 ?>
