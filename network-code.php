<?php

   $data_raw = file_get_contents('test.json');
   $data = json_decode($data_raw, true);
   $data[time()] = array(
      'action'  => $_POST['action'],
      'comment' => $_POST['comment']
   );

   $f = fopen('test.json', 'w');
   fwrite($f, json_encode($data, true));
?>
