<?php

   $data_raw = file_get_contents('test.json');
   $data = json_decode($data_raw, true);
   /*array_push($data, array(
      'time'    => date(),
      'action'  => $_POST['action'],
      'comment' => $_POST['comment']
   ));*/
   $data[time()] = array(
      'action'  => $_POST['action'],
      'comment' => $_POST['comment']
   );
   /*$data_raw = file_get_contents('test.json');
   print($data_raw);*/

   $f = fopen('test.json', 'w');
   fwrite($f, json_encode($data, true));
?>
