function vveShowMessages(dataObj){
   clearInfoMsg();
   if(typeof(dataObj.infomsg) == 'object'){
      for(var i=0; i < dataObj.infomsg.length; i++){
         infoMsg(dataObj.infomsg[i], false);
      }
   } else if(typeof(dataObj.infomsg) == 'string'){
      infoMsg(dataObj.infomsg);
   }
   clearErrMsg();
   if(typeof(dataObj.errmsg) == 'object'){
      for(var y=0; y < dataObj.errmsg.length; y++){
         errMsg(dataObj.errmsg[y], false);
      }
   } else if(typeof(dataObj.infomsg) == 'string'){
      errMsg(dataObj.errmsg);
   }
}

function infoMsg(msg, clear){
   if(msg == null) return;
   if(typeof (clear) == 'undefined') clear = true;
   if(clear == true){
      clearInfoMsg();
   }
   $('#infoMessages').prepend("<p>"+msg+"</p>").show();
}

function clearInfoMsg(){
   $('#infoMessages').html(null).hide();
}

function clearErrMsg(){
   $('#errMessages').html(null).hide();
}

function errMsg(msg, clear){
   if(msg == null) return;
   if(typeof (clear) == 'undefined') clear = true;
   if(clear == true){
      $('#errMessages').html(null);
   }
   $('#errMessages').prepend("<p>"+msg+"</p>").show();
}

function showLoadBox(box, timeout){
   if(typeof(timeout) == "undefined"){
      timeout = 5000;
   }
   var jbox = $(box);
   var overBox = $('<div id="loadingBox" style="background-color: white; \n\
text-align:center;">\n\
<img src="images/progress.gif" alt="Loading..." /></div>').css({
      position : 'absolute',
      top : 0,
      left : 0,
      opacity : 0,
      width : jbox.width(),
      height : jbox.height()
   });
   var parentBox = $(box).parent('div,p');
   parentBox.css('position', 'relative').prepend(overBox.hide());
   parentBox.children('#loadingBox').fadeTo(500, 0.5);
   setTimeout("hideLoadBox()", timeout); // zrušení
}

function hideLoadBox(){
   $('#loadingBox').fadeOut(500,function(){
      $('#loadingBox').remove();
   })
}

function vveLoadImage(src, callback){
   var cacheImage = document.createElement('img');
   //set the onload method before the src is called otherwise will fail to be called in IE
   cacheImage.onload = function(){
      if ($.isFunction(callback)) {
         callback.call(cacheImage);
      }
   }
   cacheImage.src = src;
}