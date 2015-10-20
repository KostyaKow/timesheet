<html>
<head>
   <?php
      include 'include.php';      
      
      $data_raw = file_get_contents('test.json1');
      //print($data_raw);
      $data = json_decode($data_raw, true);
      print $data['unixtime']['action'];

      //$start = 1444156418;
      //print time();
   ?>

   <script>
   $(function () {
      $('.tbtn').addClass('btn');
      $('.tbtn').addClass('btn-default');

      $('.tbtn').click(function () {
         var data = {
            'action'  : this.id,
            'comment' : 'hello'
         }
         post('network-code.php', data)
      });
   });
   </script>
</head>
<body>
   <center style='padding-top: 10px'>
      <button id='clockin' class='tbtn'>Clock in!</button>
      <button id='clockout' class='tbtn'>Clock out!</button>
   </center>

   <table class='table table-hover' id=''>
      
   </table>

</body>
</html>
