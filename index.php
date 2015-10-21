<html>
<head>
   <?php include 'include.php'; ?>
   <style>
      .today {
         background-color: red;
         border-radius:    10px;
      }
      .table {
         border: 2px solid black;
         margin: 15px;
         width: 95%;
         border-collapse: separate;
         border-radius: 4px;

      }

      .section {
         font-size: 150%;
         text-align: center;
         padding-top: 20px;
      }
   </style>

   <script>
   $(
   function () {
      //$('.table').addClass('table-curved');

      $('.tbtn').addClass('btn');
      $('.tbtn').addClass('btn-default');

      $('.tbtn').click(function () {
         var data = {
            'action'  : this.id,
            'comment' : $('#comment').val()
         }
         post('network-code.php', data)
      });
   });


   //Time milliseconds since beginning of the day
   function getMilliDay(d) {
      return d.getMilliseconds() + d.getSeconds()*60 + d.getMinutes()*60*60 + d.getHours()*60*60*60;
   }

   function addTableStuff(data) {
      var data = $.parseJSON(data);
      var t = $('#timeTable');
      var tbody = t.find('tbody');

      var today  = new Date(Date.now());
      var todayD = today.getDate(), todayM = today.getMonth();

      for (var e in data) {
         var action = data[e]['action'];
         var comment = data[e]['comment'];

         var time = new Date(e * 1000);
         var timeD = time.getDate(), timeM = time.getMonth();

         var timeTd = $('<td>').text(time);
         var actionTd = $('<td>').text(action);
         var commentTd = $('<td>').text(comment);

         var tr = $('<tr>').append(actionTd).append(timeTd).append(commentTd);
         if (timeD == todayD && timeM == todayM)
            tr.addClass('today');

         tbody.append(tr);
      }

      //////////////////////////////////////////
      var dailyTable = $('#daily-sum');
      var dbody = dailyTable.find('tbody');

      var totalTimeToday = 0, currTimeStart = 0, reportDate = -1, currDay = -1;

      for (var e in data) {
         var entryDate = new Date(e * 1000);
         var entryDay = entryDate.getDate();
         var action = data[e]['action'];

         //print summary if timeD and day (from curStart) differ
         if (entryDay != currDay && currDay != -1) {
            var d1 = reportDate.getDate();
            var m1 = reportDate.getMonth() + 1;
            var dateTd = $('<td>').text(d1 + '/' + m1);
            var dateTotalTimeTd = $('<td>').text(totalTimeToday / 60 / 60);
            var myTr = $('<tr>').append(dateTd).append(dateTotalTimeTd);
            dbody.append(myTr);
            totalTimeToday = currTimeStart = 0;
            currDay = entryDay;
            reportDate = entryDate;
            continue;
         }
         if (currDay == -1) {
            currDay = entryDay;
            reportDate = entryDate;
         }

         //if clocked-in but not today then reset and clock in
         if (action == 'clockin' && currTimeStart != 0) {
            if (entryDay != currTimeStart.getDay())
               currTimeStart = entryDate;
         }
         //if not clocked in then clock in
         else if (action == 'clockin' && currTimeStart == 0) {
            currTimeStart = entryDate;
         }
         //if clocked in then clock out
         else if (action == 'clockout' && currTimeStart != 0) {
            totalTimeToday += getMilliDay(entryDate) - getMilliDay(currTimeStart);
            currTimeStart = 0;
         }
         //////////////////////////////////////////
      }
   }

   </script>
</head>
<body>
   <center style='padding-top: 10px'>
      <input type='text' id='comment'>
      <button id='clockin' class='tbtn'>Clock in!</button>
      <button id='clockout' class='tbtn'>Clock out!</button>
   </center>
   <hr>

   <div class='section'>Daily summary</div>
   <table class='table table-hover' id='daily-sum'>
      <th>Day</th>
      <th>Total hours</th>
   </table>

   <div class='section'>Weekly summary</div>
   <table class='table table-hover' id='weekly-sum'>
      <th>Week</th>
      <th>Total hours</th>
   </table>


   <div class='section'>Log</div>
   <table class='table table-hover' id='timeTable'>
      <th>Action</th>
      <th>Time</th>
      <th>Comment</th>
      <!-- <tr> <td>1</td> <td>2</td> </tr> -->
   </table>


   <?php
      $data_raw = file_get_contents('test.json');
      
      $data = json_decode($data_raw, true);
      $data_raw = json_encode($data); //fix file if we did manual changes

      /*foreach ($data as $time => $event) {
         print strftime($time);
      }     
      //print($data_raw);
      //$start = 1444156418;
      print time(); */
      print "<script>addTableStuff('" . $data_raw . "')</script>";
   ?>

</body>
</html>
