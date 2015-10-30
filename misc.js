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
function dayNumToName(day) {
   var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
   if (day < 0 || day > 6)
      return 'Bad day of the week'; 
   else return days[day];
}

milliInDay = 1000 * 60 * 60 * 24;
milliInWeek = milliInDay * 7;
function getNumWeeksSinceEpoch(milli) {
   return Math.floor(milli / milliInWeek);
}
function jsDateFromEpochWeek(weekN) {
   return new Date(weekN*milliInWeek);
}
