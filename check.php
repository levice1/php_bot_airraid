<?php
include "functions.php";
ignore_user_abort(true);
for($i=1;$i<=6;$i++){ // автоматичний повтор скрипту для перевірки кожні 10 секунд


$state_status_old = json_decode(file_get_contents("state_status.json"), true);// отримання минулих статусів в областях
$state_status = $state_status_old;
$url = 'https://alerts.com.ua/api/states';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-Key:VOZA'));// API-KEY

$html = curl_exec($ch);
$states = explode("{", $html);
    unset($states[0]);// видалення зайвих символів з відповіді по АПІ
    unset($states[1]);

foreach($states as $state){
    $id = strstr($state , ",\"name", true);
    $id = substr($id , 5);//знаходить ІД області
      if(strpos($state, "true")){
        if($state_status[$id] != true){
            $state_status[$id] = true;
            /* функція відправки повідомлення про тривогу */
            SendMsgToRegUsers($id);
        }
    } elseif ($state_status[$id] == true){
           $state_status[$id] = false;
           /* функція відправки повідомлення про відміну тривоги */
           SendMsgToRegUsers($id, false);
    }

    
}
if(array_intersect($state_status, $state_status_old)){   //якщо були зміни в статусі якоїсь області, то перезаписує файл 
  file_put_contents("state_status.json" ,json_encode($state_status));
}
sleep(9);

}



/* для обнулення json файлу
$state_status = [
"1"=>false,
"2"=>false,
"3"=>false,
"4"=>false,
"5"=>false,
"6"=>false,
"7"=>false,
"8"=>false,
"9"=>false,
"10"=>false,
"11"=>false,
"12"=>false,
"13"=>false,
"14"=>false,
"15"=>false,
"16"=>false,
"17"=>false,
"18"=>false,
"19"=>false,
"20"=>false,
"21"=>false,
"22"=>false,
"23"=>false,
"24"=>false,
"25"=>false,];
file_put_contents("state_status.json" ,json_encode($state_status)); */