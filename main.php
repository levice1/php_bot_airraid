<?php
include "texts.php";
include "functions.php";
//ОТРИМАННЯ ДАННИХ ВІД ТЕЛЕГРАМУ-----
$input_data = json_decode(file_get_contents('php://input'), true);
file_put_contents('file.txt', '$input_data: ' . print_r($input_data, 1) . "\n", FILE_APPEND);
//-----------------------------------
// ОБРОБКА ВІДПОВІДЕЙ ВІД КОРИСТУВАЧА--------
// старт роботи бота, перше повідомлення
if ($input_data['message']['text'] == '/start') {
  SendMsgTG($first_message_after_start, $input_data['message']['chat']['id'], $btn_select_obl);
  exit();
}
// реєстрація та вибір регіону
 if ($input_data['callback_query']['data'] != false) {
  $user = [
    "name" => "{$input_data['callback_query']['message']['chat']['first_name']}",
    "username" => "{$input_data['callback_query']['message']['chat']['username']}",
    "user_id" => "{$input_data['callback_query']['message']['chat']['id']}",
    "region" => $input_data['callback_query']['data']
  ];
  $region = $user['region'];
  include "texts.php";
  WriteToBD($user);
  $state_status = json_decode(file_get_contents("state_status.json"), true);
  SendMsgTG($done_msg, $input_data['callback_query']['message']['chat']['id'], $btn_regular);
  if($state_status[$region] == true){
  SendMsgTG($alert_now_msg, $input_data['callback_query']['message']['chat']['id']);
  };
  exit();
}
// зміна регіону
if ($input_data['message']['text'] == 'Змінити регіон для сповіщення') {
  $user_id = $input_data['message']['chat']['id'];
  UnsetFromBD($user_id);
  SendMsgTG($change_region_msg, $input_data['message']['chat']['id'], $btn_select_obl);
  exit();
}
// відписка від сповіщення
if ($input_data['message']['text'] == 'Відписатися від сповіщення') {
  $user_id = $input_data['message']['chat']['id'];
  UnsetFromBD($user_id);
  SendMsgTG($unsubscribe, $input_data['message']['chat']['id']);
  exit();
}
//-------------------------------------------