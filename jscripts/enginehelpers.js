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
   var parentBox = $(box).parent('div,p');
//   var overBox = $('<div id="loadingBox"><img src="images/progress.gif" alt="Loading..." /></div>')
   var overBox = $('<div id="loadingBox"><div class="loader-w"><span class="loader-text">loading...</span></div>')
   .css({
      position : 'absolute',
      top : 0,
      left : 0,
      opacity : 0,
      width : jbox.width()+parseInt(parentBox.css('padding-left'))+parseInt(parentBox.css('padding-right'))
                          +parseInt(jbox.css('margin-left'))+parseInt(jbox.css('margin-right')),
      height : jbox.height()+parseInt(parentBox.css('padding-top'))+parseInt(parentBox.css('padding-bottom'))
                          +parseInt(jbox.css('margin-top'))+parseInt(jbox.css('margin-bottom'))
   });
   parentBox.css('position', 'relative').prepend(overBox.hide());
   parentBox.children('#loadingBox').fadeTo(500, 1);
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

function initToolboxEvents(){
   /**
    * Initializa all button only once
    */
   var toolboxButtons = $('a.toolbox-button');
   // remove all previous toolboxes
   $('div.toolbox-temp').remove();
   
   $("div.toolbox").parent('div').css({position: 'relative'});
   $(".toolbox-tool").hover(function(){
      $(this).addClass('ui-state-highlight');
      $(this).find('input').addClass('ui-state-highlight');
   }, function(){
      $(this).removeClass('ui-state-highlight');
      $(this).find('input').removeClass('ui-state-highlight');
   });
   
   /* events */
   toolboxButtons.hover(function(){
         $(this).css({'z-index': 3});
         $(this).next('div.toolbox').trigger('showToolbox', [$(this)]);
      }, 
      function(){
         $(this).css({'z-index': 1});
   });
   
   $("div.toolbox").bind('showToolbox', function(event, $button){
      // podklad pro editaci
      $button.parent().addClass('toolbox-active-content');
      // kontrola jestli není už vytvořen
      var toolId = $(this).attr('id')+'-copy';
      var $toolbox;
      if($('#'+toolId).length == 0){
         $toolbox = $(this).clone(true).attr('id', toolId).addClass('toolbox-temp');
         $('body').append($toolbox);
         $toolbox.bind('mouseleave', function(){
            $('.toolbox-active-content').removeClass('toolbox-active-content');
            $(this).animate({opacity:0}, 0, function(){
               $(this).css({'z-index':-10000}).hide();
            });
         });
      } else {
         $toolbox = $('#'+toolId);
      }
      
      $toolbox.css({
         opacity     : 1, 
         top         : $button.offset().top-2,
         left        : $button.offset().left-$toolbox.width()+22,
         width       : $toolbox.width(),
         'z-index'   : 10000
      }).show();
   });
   
   // move toolbox with document when scrolling
   $(document).unbind('scroll');
   $(document).scroll(function(){
      var top = $(this).scrollTop();
      toolboxButtons.each(function(){
         var $container = $(this).parent();
         if($container.offset().top < top+30 && top < $container.offset().top+$container.height()-30){
            $(this).css({
               position : "fixed",
               top: 30,
               left: $(this).offset().left
            });
         } else {
            $(this).css({
               position : "absolute",
               top: 1,
               right: 1, left : "auto"
            });
         }
      });
   });
}

function str2url(str,encoding,ucfirst)
{
	str = str.toUpperCase();
	str = str.toLowerCase();

	str = str.replace(/[\u0105\u0104\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g,'a');
	str = str.replace(/[\u00E7\u010D\u0107\u0106]/g,'c');
	str = str.replace(/[\u010F]/g,'d');
	str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u011B\u0119\u0118]/g,'e');
	str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g,'i');
	str = str.replace(/[\u0142\u0141]/g,'l');
	str = str.replace(/[\u00F1\u0148]/g,'n');
	str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u00D3]/g,'o');
	str = str.replace(/[\u0159]/g,'r');
	str = str.replace(/[\u015B\u015A\u0161]/g,'s');
	str = str.replace(/[\u00DF]/g,'ss');
	str = str.replace(/[\u0165]/g,'t');
	str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u016F]/g,'u');
	str = str.replace(/[\u00FD\u00FF]/g,'y');
	str = str.replace(/[\u017C\u017A\u017B\u0179\u017E]/g,'z');
	str = str.replace(/[\u00E6]/g,'ae');
	str = str.replace(/[\u0153]/g,'oe');
	str = str.replace(/[\u013E\u013A]/g,'l');
	str = str.replace(/[\u0155]/g,'r');

	str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
	str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
	str = str.replace(/[ ]/g,'-');
	str = str.replace(/[\/]/g,'-');
	if (ucfirst == 1) {
		var c = str.charAt(0);
		str = c.toUpperCase()+str.slice(1);
	}
	return str;
}

function vveCheckUrlKey(checkerUrl, element, callback, params){
   var $e = $(element); var postParams = {key : $e.val(), lang : $e.attr("lang")};
   if(typeof params != "undefined"){ $.extend(postParams, params); }
   $.ajax({
      type: "POST",
      url: checkerUrl,
      data: postParams,
      cache: false,
      success: function(data){
         if( (callback || callback != '' ) && (typeof callback !== 'undefined') ) {
            if(typeof data.urlkey !== "undefined" ){
               callback(data.urlkey);   
            } else {
               callback(null);
            }
         }
      }
   });
}

$(document).ready(function(){
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
   // select first language
   $('.form-link-lang-container a:first-child').trigger('click', [false]);
   // toolbox events
   initToolboxEvents();
   // open external link in new window
   $(".link-external").live('click',function(){
      window.open(this.href);
      return false;
   });
   // načítání jazykových mutací
   $('a.toolbox-changelang-button').live('click', function(){
      showLoadBox('.main-content');
      $('.main-content').load(this.href, function(){
         hideLoadBox();
         initToolboxEvents();
         $(document).scroll();
      });
      return false;
   });   
});
