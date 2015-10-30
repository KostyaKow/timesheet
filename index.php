<html>
<head>
   <?php include 'include.php'; ?>
   <script src='misc.js'></script>
   <link rel='stylesheet' type='text/css' href='style.css'>

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

   var milliInDay = 1000 * 60 * 60 * 24;
   var milliInWeek = milliInDay * 7;
   function getNumWeeksSinceEpoch1(milli) {
      var milliSinceWeekStart = milli - (milli % milliInWeek);

      milliSinceWeekStart = milliSinceWeekStart - (milliInDay*2);
      var numWeeks = milliSinceWeekStart / milliInWeek;
      return numWeeks;
   }

   function getNumWeeksSinceEpoch(milli) {
      return Math.floor(milli / milliInWeek);
   }
   function jsDateFromEpochWeek(weekN) {
      return new Date(weekN*milliInWeek);
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

         //add stuff to daily table
         var dateTd = $('<td>').text(dayNumToName(entryDate.getDay()) + ' ' + dateIndex);
         var totalTimeTd = $('<td>').text(milliToHours(totalTimeToday, 1));
         var tr = $('<tr>').append(dateTd).append(totalTimeTd);
         dbody.append(tr); 
      }

      for (var dateIndex in dateSorted) {
         /*if (first) {
            currWeek = getNumWeeksSinceEpoch(entry['time']*1000);
            first = false;
         }*/

         //weekly table
         weeklyTotal += totalTimeToday;
         var firstEntry = currDayArr[0];
         //var firstEntryDate = new Date(firstEntry['time'] * 1000);
         var epochWeek = getNumWeeksSinceEpoch(firstEntry['time'] * 1000);
         var beginningWeek = formatJsDate(jsDateFromEpochWeek(epochWeek));
         document.write(beginningWeek + ' ');
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
