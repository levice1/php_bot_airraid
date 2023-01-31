<?php

function SendMsgTG($txtMsg, $chat_id, $keyboard = [])
{ // отправка ответа боту Телеграм
  
  $params = array(
    'chat_id' => $chat_id, // id получателя сообщения
    'text' => $txtMsg, // текст сообщения
    'reply_markup' => $keyboard );// кнопки которые передаёт бот 
        if(!$keyboard) unset($params['reply_markup']);// удаление елемента клавиатуры из массива, если его не передали


  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot:VOZA/sendMessage'); // адрес api телеграмм
  curl_setopt($curl, CURLOPT_POST, true); // отправка данных методом POST
  curl_setopt($curl, CURLOPT_TIMEOUT, 10); // максимальное время выполнения запроса
  curl_setopt($curl, CURLOPT_POSTFIELDS, $params); // параметры запроса
  $result = curl_exec($curl); // запрос к api
  curl_close($curl);
}


function WriteToBD($user)
{ // запис підписників в базу данных
      
      $name = htmlspecialchars($user['name']);
      $username = htmlspecialchars($user['username']);
      $user_id = htmlspecialchars($user['user_id']);
      $region = htmlspecialchars($user['region']);
      $mysql = new mysqli();
      $mysql -> query("SET NAMES 'UTF-8'");
      $mysql -> query("INSERT INTO `air_raid_alert_bot` (`name`,`username`,`user_id`,`region`) VALUES ('$name','$username','$user_id','$region')");
      if(!$mysql){file_put_contents('err.txt', 'errors: ' . mysqli_errno($mysql), 1 . "\n", FILE_APPEND);}
      $mysql -> close();
}


function UnsetFromBD($user_id)
{ //видалення підписника з бази данних
  $user_id = htmlspecialchars($user_id);
  $mysql = new mysqli();
  $mysql -> query("SET NAMES 'UTF-8'");
  $mysql -> query("DELETE FROM air_raid_alert_bot WHERE user_id = '$user_id'");
  $mysql ->close();
}


function SendMsgToRegUsers($state, $alert=true)
{ //відправка повідомлення зареєстрованим користувачам
  $user_state = [
    '22' => 'черкаській',
    '24' => 'чернігівській',
    '23' => 'чернівецькій',
    '3' => 'дніпропетровській',
    '4' => 'донецькій',
    '8' => 'івано-франківській',
    '19' => 'харківській',
    '20' => 'херсонській',
    '21' => 'хмельницькій',
    '9' => 'київській',
    '10' => 'кіровоградській',
    '11' => 'луганській',
    '12' => 'львівській',
    '13' => 'миколаївській',
    '14' => 'одесьській',
    '15' => 'полтавській',
    '16' => 'рівненській',
    '17' => 'сумській',
    '17' => 'тернопільській',
    '1' => 'вінницькій',
    '2' => 'волинській',
    '6' => 'закарпатській',
    '7' => 'запорізькій',
    '5' => 'житомирській'
];

    $name_user_state = $user_state[$state];// регіон користувача Українською
    $mysql = new mysqli("a043um.forpsi.com","f146078","7btGnhE8","f146078");
    $mysql -> query("SET NAMES 'UTF-8'");
    $usrs = $mysql -> query("SELECT * FROM `air_raid_alert_bot` WHERE `region` LIKE '$state' ");

    $alert_start = "У $name_user_state області об'явлена повітряна тривога! Негайно в укриття!";
    $alert_end = "Повітряна тривога у $name_user_state області закінчилась. Якщо не чуєте вибухів - можете вийти з укриття!";

    foreach(mysqli_fetch_all($usrs) as $usr){
    if( $alert) {SendMsgTG($alert_start, $usr[3]);} 
    if(!$alert) {SendMsgTG($alert_end, $usr[3]);}
      
  }
}