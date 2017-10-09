var AuthorizationCode ='ylhvaviE9K52b64swaHcq1DsvkwAk3CbaCaCw39+Q1vqhCiItcTDxCyXXOrZbvrnMN5OXCE3LwmuQIO4VzEmwO2tl/hGsi87WHoeMKHqWzE2kb2edrapd/qrVaHwbrRcBOrROprccKhTUiW4egd0IQdB04t89/1O/w1cDnyilFU=';
var strBody = '明日の予定をお知らせします。';

// こいつを実行します
function main() {
  var msg = getCalendarEvent();
  Logger.log(msg);
  if(msg !== null) {
    sendHttpPost(msg);
  }
}

// 翌日の日付をYYYY/MM/ddの形式で返す
function nextDate() {
  var MILLIS_PER_DAY = 1000 * 60 * 60 * 24;
  var now = new Date();
  // 今日の日付に24時間足す
  var tomorrow = new Date(now.getTime() + MILLIS_PER_DAY);
  
  return Utilities.formatDate(tomorrow, 'JST', 'YYYY/MM/dd');
}

// Calendarから予定翌日の予定のTitleと時間を取得し、メッセージとして返却する
function getCalendarEvent() {
  var message;
  var targetDate = nextDate();

  // googleCalendarからeventを取得
  var myCals = CalendarApp.getCalendarById('nisidagod@gmail.com');
  var myEvents = myCals.getEventsForDay(new Date(targetDate));
  
  // 特定の文字列で始まるeventのみに絞り込む
  var groupEvents = myEvents.filter(function(e) {
    return e.getTitle().indexOf('[グループA]') == 0;
  } );

  // eventがあればメッセージに格納
  if(groupEvents.length > 0) {
      for(var i = 0; i < groupEvents.length; i++) {
        var strTitle = groupEvents[i].getTitle();
        var dispTime = getDispTime(groupEvents[i]);
        
        strBody = strBody + '\n' + strTitle + '\n' + dispTime + '\n'; 
      }
    message =　'\n' + strBody;
  } else {
    message = null;
  }
  return message;
}

// 表示時間取得
function getDispTime(event) {
  var start = Utilities.formatDate(event.getStartTime(), 'JST', 'HH:mm');
  var end = Utilities.formatDate(event.getEndTime(), 'JST', 'HH:mm');
  if(start == end) {
    return '終日';
  }
  return start + '-' + end;
}

// LINE NotifyにHTTP POSTでメッセージを送信する
function sendHttpPost(postMassage) {
   var payload = {
     "message": postMassage
   };

   var options = {
     "method" : "post",
     "headers": {
       Authorization: AuthorizationCode,
     },
     "payload" : payload
   };
   UrlFetchApp.fetch("https://notify-api.line.me/api/notify", options);
 }