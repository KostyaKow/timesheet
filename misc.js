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
function milliToHours(milli, round) {
   var numHours = milli / 60 / 60 / 60;
   var round = pow(10, round);
   var numHoursRounded = Math.round(round * numHours) / round;
   return numHoursRounded;
}
function fixYear(n) {
   return 1900 + n; //-2000 so prints 15 instead of 2015
}
function fixMonth(n) {
   return n + 1;
}
//converts long Date string to something like 27/04/2015
function formatJsDate(milli) {
   var d = new Date(milli);
   var dayName = dayNumToName(d.getDay()) + ' ';
   var year = fixYear(d.getYear());
   var month = fixMonth(d.getMonth());
   return dayName + d.getDate() + '/' + month + '/' + year;
}
function dayNumToName(day) {
   var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
   if (day < 0 || day > 6)
      return 'Bad day of the week'; 
   else return days[day];
}

milliInDay = 1000 * 60 * 60 * 24;
milliInWeek = milliInDay * 7;


