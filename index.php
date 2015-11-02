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

   function addTableStuff(data) {
      var data = $.parseJSON(data);
      var t = $('#timeTable');
      var tbody = t.find('tbody');

      var today  = new Date(Date.now());
      var todayD = today.getDate(), todayM = today.getMonth();

      //display every entry from logging
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
      var weeklyTotal = {};
      var currWeek = -1;
      var prevDay = 0;

      for (var e in data) {
         var entryDate = formatJsDate(new Date(e * 1000));
         if (dateSorted[entryDate] == null)
            dateSorted[entryDate] = []
         data[e]['time'] = e;
         dateSorted[entryDate].push(data[e]);
      }
      for (var entryDate in dateSorted) {
         var currDayArr = dateSorted[entryDate];

         var totalTimeToday = 0;
         var timeStart;
         var clocking = false;

         for (var entryIndex in currDayArr) {
            var entry = currDayArr[entryIndex];
            var time = entry['time'] * 1000;
            var entryDate = new Date(time);
            if (entry['action'] == 'clockin' && !clocking) {
               timeStart = entryDate;
               clocking = true;
            }
            else if (entry['action'] == 'clockout' && clocking) {
               clocking = false;
               totalTimeToday += getMilliDay(entryDate) - getMilliDay(timeStart);
            }
         }

         //kkkkk
         function newWeek(newWeek, lastWeek, b) {
            document.write('</br>' + newWeek + '...' + lastWeek + '...' + b);
         }
         if (prevDay != 0) {
            if (entryDate.getDay() < prevDay.getDay())
               newWeek(entryDate, prevDay, 0);

            var timeDiff = entryDate.getTime() - prevDay.getTime();
            if (timeDiff > milliInWeek)
               newWeek(entryDate, prevDay, 1);
         }
         //end kkkkkkkk
         /*var weekN = getNumWeeksSinceEpoch(time); //FIXME
         if (weeklyTotal[weekN] == null)
            weeklyTotal[weekN] = { 'time' : time, 'total' : 0 };
         weeklyTotal[weekN]['total'] += totalTimeToday;*/

         //add stuff to daily table
         var dateTd = $('<td>').text(dayNumToName(entryDate.getDay()) + ' ' + entryDate);
         var totalTimeTd = $('<td>').text(milliToHours(totalTimeToday, 1));
         var tr = $('<tr>').append(dateTd).append(totalTimeTd);
         dbody.append(tr);
         prevDay = entryDate;
      }

      for (var week in weeklyTotal) {
         var entry = weeklyTotal[week];
         millis = week*milliInWeek + milliInWeek /* - 2*milliInDay*/; //FIXME kk
         console.log(millis);
         var weekDate = new Date(millis);

         var total = entry['total'];
         var displayWeek = formatJsDate(weekDate);

         var weekTd = $('<td>').text(dayNumToName(weekDate.getDay()) + ' ' + displayWeek);
         var weekTimeTd = $('<td>').text(milliToHours(total, 2));
         var tr = $('<tr>').append(weekTd).append(weekTimeTd);
         wbody.append(tr);

      }

      /*for (var entryDate in dateSorted) {
         if (first) {
            currWeek = getNumWeeksSinceEpoch(entry['time']*1000);
            first = false;
         }

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
      }*/
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
