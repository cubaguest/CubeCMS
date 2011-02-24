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
   var overBox = $('<div id="loadingBox" style="background-color: white; text-align:center;"><img src="images/progress.gif" alt="Loading..." /></div>')
   .css({
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

$(document).ready(function(){
   // select first language
   $('.form-link-lang-container a:first-child').trigger('click', [false]);
   // when language is changed
   $('.form-link-lang-container a.form-link-lang').live('click',function(event, focus){
      if(typeof focus == 'undefined') focus = true;
      var lang = this.lang;
      $(this).parent('p').find('a').removeClass('form-link-lang-sel');
      $(this).addClass('form-link-lang-sel');
      // vybereme prvek, který obsahuje inputy
      var $container = $(this).closest('*:has(p[lang])');
      $('p[lang]', $container).hide();
      var p = $('p[lang="'+lang+'"]', $container).show();
      if(focus) {
         p.find('input,textarea,select').focus();
      }
      // zobrazení popisku k elementu
      $container = $(this).closest('*:has(label[lang])')[0];
      $('label[lang]', $container).hide();
      $('label[lang="'+lang+'"]', $container).show();
      return false;
   });


   $("div.toolbox").parent('div').css({position: 'relative'});
   $(".toolbox-tool").hover(function(){
      $(this).addClass('ui-state-highlight');
      $(this).find('input').addClass('ui-state-highlight');
   }, function(){
      $(this).removeClass('ui-state-highlight');
      $(this).find('input').removeClass('ui-state-highlight');
   });
   $("a.toolbox-button").mousemove(function(){
      var $toolbox = $(this).next('div.toolbox').clone(true);
      $('body').append($toolbox);
      $toolbox.css({opacity: 1, top: $(this).offset().top-2,
         left: $(this).offset().left-$toolbox.width()+22,
         width : $toolbox.width()
      }).show().mousemove();
      return false;
   });
   $("a.toolbox-button").hover(function(){
      $(this).css({'z-index': 3}).parent().addClass('toolbox-active-content');
   }, function(){
      if($('body>div.toolbox').length == 0){
         $(this).css({'z-index': 1}).parent().removeClass('toolbox-active-content');
      }
   });
   $("div.toolbox").hover(
      function(){$(this).css({'z-index':10000, opacity:1}).show();},
      function(){
         $('.toolbox-active-content').removeClass('toolbox-active-content');
         $(this).animate({opacity:0}, 500, function(){$(this).remove();});
      });
   // open external link in new window
   $("a.link-external").click(function(){
      window.open(this.href);
      return false;
   });   
      
});
// move toolbox with document when scrolling
$(document).scroll(function(){
   var top = $(this).scrollTop()+30;
   $('a.toolbox-button').each(function(){
      var $container = $(this).parent();
      if($container.offset().top < top && top < $container.offset().top+$container.height()-10){
         $(this).css({top: top-$container.offset().top});
      } else if($container.offset().top > top){
         $(this).css({top: 0});
      }
   });
});