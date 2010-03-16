function infoMsg(msg, clear){
   if(typeof (clear) == 'undefined') clear = true;
   if(clear == true){
      clearInfoMsg();
   }
   $('#infoMessages').prepend("<p>"+msg+"</p>").show();
}

function clearInfoMsg(){
   $('#infoMessages').html(null).hide();
}

function errMsg(msg, clear){
   if(typeof (clear) == 'undefined') clear = true;
   if(clear == true){
      $('#errMessages').html(null);
   }
   $('#errMessages').prepend("<p>"+msg+"</p>").show();
}

function showLoadBox(box){
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
}

function hideLoadBox(){
   $('#loadingBox').fadeOut(500,function(){
      $('#loadingBox').remove();
   })
}
