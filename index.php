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
   function pow(n, p) {
      if (p > 1)
         return n * pow(n, p-1);
      else
         return n;
   }
   function fixYear(n) {
      return 1900 + n; //-2000 so prints 15 instead of 2015
   }
   function milliToHours(milli, round) {
      var numHours = milli / 60 / 60 / 60;
      var round = pow(10, round);
      var numHoursRounded = Math.round(round * numHours) / round;
      return numHoursRounded;
   }
   //converts long Date string to something like 27/04/2015
   function formatJsDate(d) {
      return d.getDate() + '/' + (d.getMonth() + 1) + '/' + (1900 + d.getYear());
   }
   function dayNumToName(n) {
      if (n == 0)
         return 'Sunday';
      else if (n == 1)
         return 'Monday';
      else if (n == 2)
         return 'Tuesday';
      else if (n == 3)
         return 'Wednesday';
      else if (n == 4)
         return 'Thursday';
      else if (n == 5)
         return 'Friday';
      else if (n == 6)
         return 'Saturday';
      else
         return 'Bad day of the week'; 
   }

   var milliInDay = 1000 * 60 * 60 * 24;
   var milliInWeek = milliInDay * 7;
   function getNumWeeksSinceEpoch(milli) {
      milli = milli - (milliInDay*2);
      var numWeeks = (milli - (milli % milliInWeek)) / milliInWeek;
      return numWeeks;
   }
   function jsDateFromEpochWeek(weekN) {
      return new Date(weekN*milliInWeek + milliInDay*2);
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

      var weeklyTable = $('#weekly-sum');
      var wbody = weeklyTable.find('tbody');

      var dateSorted = {}
      var weeklyTotal = 0;
      var currWeek = -1;

      for (var e in data) {
         var entry = data[e];
         var entryDate = new Date(e * 1000);
         var entryDateKey = formatJsDate(entryDate);
         if (dateSorted[entryDateKey] == null)
            dateSorted[entryDateKey] = []
         entry['time'] = e;
         dateSorted[entryDateKey].push(entry);
      }
      var first = true;
      for (var dateIndex in dateSorted) {
         var currDayArr = dateSorted[dateIndex];

         //document.write(currDayArr + '................' + e + '<br>');
         var totalTimeToday = 0;
         var timeStart;
         var clocking = false;

         for (var entryIndex in currDayArr) {
            var entry = currDayArr[entryIndex];
            var entryDate = new Date(entry['time'] * 1000);
            if (entry['action'] == 'clockin' && !clocking) {
               timeStart = entryDate;
               clocking = true;
            }
            else if (entry['action'] == 'clockout' && clocking) {
               clocking = false;
               totalTimeToday += getMilliDay(entryDate) - getMilliDay(timeStart);
            }
         }
         if (first) {
            currWeek = getNumWeeksSinceEpoch(currDayArr[0]['time']*1000);
            first = false;
         }

         //add stuff to daily table
         var dateTd = $('<td>').text(dayNumToName(entryDate.getDay()) + ' ' + dateIndex);
         var totalTimeTd = $('<td>').text(milliToHours(totalTimeToday, 1));
         var tr = $('<tr>').append(dateTd).append(totalTimeTd);
         dbody.append(tr); 

         //weekly table
         weeklyTotal += totalTimeToday;
         var firstEntry = currDayArr[0];
         //var firstEntryDate = new Date(firstEntry['time'] * 1000);
         var epochWeek = getNumWeeksSinceEpoch(firstEntry['time'] * 1000);
         var beginningWeek = formatJsDate(jsDateFromEpochWeek(epochWeek));

         if (currWeek != epochWeek) { //gotta get down on Friday
            var weekTd = $('<td>').text(beginningWeek);
            var weekTimeTd = $('<td>').text(milliToHours(weeklyTotal, 2));
            var tr = $('<tr>').append(weekTd).append(weekTimeTd);
            wbody.append(tr);
            weeklyTotal = 0;
            currWeek = epochWeek;
         }
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
