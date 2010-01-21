var FilePreviewDialogue = {
   //   win : null,
   file : null,
   path : null,
   someChanges : false,
   cmsUrl : null,
   width : 0,
   height : 0,
   init : function () {
      // Here goes your code for setting your custom things onLoad.
      //      FilePreviewDialogue.win = tinyMCEPopup.getWindowArg("window");
      FilePreviewDialogue.file = tinyMCEPopup.getWindowArg("file");
      FilePreviewDialogue.path = tinyMCEPopup.getWindowArg("path");
      FilePreviewDialogue.cmsUrl = tinyMCEPopup.getWindowArg("cmsUrl");
      FilePreviewDialogue.loadImage();
   },
   close : function () {
      if(this.someChanges == true){
         tinyMCE.activeEditor.windowManager.confirm("Obrázek nebyl uložen, chcete zahodit změny?", function(s) {
            if (s) tinyMCEPopup.close();
            else return false;
         });
      }
      tinyMCEPopup.close();

   },
   loadImage : function (fn) {
      if(FilePreviewImageFunctions.imageChanged == true){
         FilePreviewImageFunctions.imageChanged = false;
         var timestamp =  new Date().getTime();
         $('#prevImgBox').empty();
         $('<img id="prevImg" src="'+this.path+this.file+"?time="+timestamp+'"/>').load(function(){
            $('#prevImgBox').html(this);
            FilePreviewDialogue.width = $('#prevImgBox img').width();
            FilePreviewDialogue.height = $('#prevImgBox img').height();
            if(typeof(fn) != "undefined")
            fn.call();
         });
      } else {
         $('#prevImgBox img').width(FilePreviewDialogue.width);
         $('#prevImgBox img').height(FilePreviewDialogue.height);
         if(typeof(fn) != "undefined")
            fn.call();
      }
   //      tinyMCEPopup.resizeToInnerSize();
   }
}

var FilePreviewImageFunctions = {
   actionType : null,
   tmpObj : null,
   imageChanged : true,
   initTool : function(obj, toolName){
      FilePreviewImageFunctions.destroy();
      this.actionType = toolName;
      $(obj).addClass("toolSelected");
      $("#box_"+toolName).show();
      FilePreviewDialogue.loadImage(function(){
         switch (toolName) {
            case 'crop':
               FilePreviewImageFunctions.initCrop();
               break;
            case 'rotate':
               FilePreviewImageFunctions.initRotate();
               break;
            case 'flip':
               FilePreviewImageFunctions.initFlip();
               break;
            case 'gray':
               FilePreviewImageFunctions.initGray();
               break;
            case 'resize':
            default:
               FilePreviewImageFunctions.initResizing();
               break;
         }

      });
   },

   //   },
   initResizing : function(){
      $("#boxResize").show();
      $('input[name=resize_w]').val($('#prevImgBox img').width());
      $('input[name=resize_h]').val($('#prevImgBox img').height());
      FilePreviewImageFunctions.initResObj();
      
      $("input[name=resize_ar]").change(function(){
         FilePreviewImageFunctions.initResObj();
      });
       

   },
   initResObj : function(){
      this.tmpObj = $('#prevImgBox img').resizable('destroy');
      if($("input[name=resize_ar]").is(":checked")){
         $('#prevImgBox img').resizable({
            aspectRatio: true,
            resize: function(event, ui) {
               $('input[name=resize_w]').val(ui.size.width);
               $('input[name=resize_h]').val(ui.size.height);
            }
         });
      } else {
         $('#prevImgBox img').resizable({
            aspectRatio: false,
            resize: function(event, ui) {
               $('input[name=resize_w]').val(ui.size.width);
               $('input[name=resize_h]').val(ui.size.height);
            }
         });
      }
      return true;
   },
   destroy : function(){
      $(".boxToolEdit").hide();
      $(".tools input").removeClass("toolSelected");
      switch (this.actionType) {
         case "resize":
            $('#prevImgBox img').resizable('destroy');
            break;
         case "crop":
            this.tmpObj.destroy();
            break;
         case "flip":
            $('#prevImgBox img').removeClass("flip-horizontal");
            $('#prevImgBox img').removeClass("flip-vertical");
            break;
         case "rotate":
            break;
         case "gray":

            break;
         default:
            break;
      }
   },

   initCrop : function(){
      $("#boxCrop").show();
      function showCoords(c){
         $('input[name=crop_x1]').val(c.x);
         $('input[name=crop_y1]').val(c.y);
         $('input[name=crop_x2]').val(c.x2);
         $('input[name=crop_y2]').val(c.y2);
         $('input[name=crop_w]').val(c.w);
         $('input[name=crop_h]').val(c.h);
      }

      FilePreviewImageFunctions.tmpObj = $.Jcrop('#prevImgBox img', {
         onChange: showCoords,
         onSelect: showCoords,
         aspectRatio: 0,
         setSelect: [ 0, 0, $('#prevImgBox img').width(), $('#prevImgBox img').height() ]
      });

      $('input[name=crop_ar]').change(function(){
         this.tmpObj.setOptions(this.checked? {
            aspectRatio: 4/3
         }: {
            aspectRatio: 0
         });
         FilePreviewImageFunctions.tmpObj.focus();
      });
   },

   initFlip : function(){
      $("#boxFlip").show();
      FilePreviewImageFunctions.flipObj();
      $("input[name=flip]").change(function(){
         FilePreviewImageFunctions.flipObj();
      });
   },
   flipObj : function(){
      if($("input[name=flip]:checked").val() == "x"){
         $('#prevImgBox img').removeClass("flip-vertical").addClass("flip-horizontal");
      } else if($("input[name=flip]:checked").val() == "y"){
         $('#prevImgBox img').removeClass("flip-horizontal").addClass("flip-vertical");
      }
   },
   initRotate : function(){
      $("#boxRotate").show();
   },

   initGray : function(){
      this.imageChanged = true;
      var imgObj = document.getElementById('prevImg');

      if($.browser.msie){
         imgObj.style.filter = 'progid:DXImageTransform.Microsoft.BasicImage(grayScale=1)';
      } else {
         var canvas = document.createElement('canvas');
         var canvasContext = canvas.getContext('2d');

         var imgW = imgObj.width;
         var imgH = imgObj.height;
         canvas.width = imgW;
         canvas.height = imgH;

         canvasContext.drawImage(imgObj, 0, 0);
         var imgPixels = canvasContext.getImageData(0, 0, imgW, imgH);

         for(var y = 0; y < imgPixels.height; y++){
            for(var x = 0; x < imgPixels.width; x++){
               var i = (y * 4) * imgPixels.width + x * 4;
               var avg = (imgPixels.data[i] + imgPixels.data[i + 1] + imgPixels.data[i + 2]) / 3;
               imgPixels.data[i] = avg;
               imgPixels.data[i + 1] = avg;
               imgPixels.data[i + 2] = avg;
            }
         }

         canvasContext.putImageData(imgPixels, 0, 0, 0, 0, imgPixels.width, imgPixels.height);
         imgObj.src = canvas.toDataURL();
      }
   },


   resizeImage : function(){
      $.ajax({
         type: "POST",
         url: FilePreviewDialogue.cmsUrl+"resizeimage.html",
         data: ({
            path : FilePreviewDialogue.path,
            file : FilePreviewDialogue.file ,
            size_w : $('input[name=resize_w]').val(),
            size_h : $('input[name=resize_h]').val()
         }),
         cache: false,
//         dataType : 'json',
         success: function(dataObj){
            alert(dataObj);
            //            FileBrowserFunctions.showResult(dataObj);
//            FileBrowserFunctions.showResult(dataObj.message);
//            if(dataObj.code == true){
//               FileBrowserFilesFunctions.loadFiles();
//            }
         },
         error : function(XMLHttpRequest, textStatus, errorThrown){
            FileBrowserFunctions.showResult('Chyba při přijímání požadavku na změnu velikosti obrázku '+textStatus);
         }
      });
      this.imageChanged = true;
      this.initTool(null, 'resize');
      return true;
   },
   cropImage : function(){
      $.ajax({
         type: "POST",
         url: FilePreviewDialogue.cmsUrl+"cropimage.html",
         data: ({
            path : FilePreviewDialogue.path,
            file : FilePreviewDialogue.file ,
            x1 : $('input[name=crop_x1]').val(),
            y1 : $('input[name=crop_y1]').val(),
            x2 : $('input[name=crop_x2]').val(),
            y2 : $('input[name=crop_y2]').val()
         }),
         cache: false,
//         dataType : 'json',
         success: function(dataObj){
            alert(dataObj);
            //            FileBrowserFunctions.showResult(dataObj);
//            FileBrowserFunctions.showResult(dataObj.message);
//            if(dataObj.code == true){
//               FileBrowserFilesFunctions.loadFiles();
//            }
         },
         error : function(XMLHttpRequest, textStatus, errorThrown){
            FileBrowserFunctions.showResult('Chyba při přijímání požadavku na změnu velikosti obrázku '+textStatus);
         }
      });
      this.initTool(null, 'crop');
      this.imageChanged = true;

      return true;
   },
   rotateImage : function(fileName){
      var angle = prompt("Zadejte úhel otočení (pouze desetiná čísla). Pokud je \n\
uveden parametr bg=X, je nastavena i barva pozadí, kde za X se doplné kód barvy.");
      if(angle == ''){
         alert("Musíte zadat úhel");
         return false;
      }

      $.ajax({
         type: "POST",
         url: FileBrowserFunctions.cmsURL+"rotateimage.html",
         data: ({
            path : FileBrowserDirsFunctions.currentPath,
            file : fileName,
            angle : angle
         }),
         cache: false,
         dataType : 'json',
         success: function(dataObj){
            FileBrowserFunctions.showResult(dataObj.message);
            //            FileBrowserFunctions.showResult(dataObj);
            if(dataObj.code == true){
               FileBrowserFilesFunctions.loadFiles();
            }
         },
         error : function(){
            FileBrowserFunctions.showResult('Chyba při přijímání požadavku na změnu rotace obrázku');
         }
      });

      return true;
   },
   flipImage : function(fileName){
      var axis = prompt("Zadejte osu podle které se má obrázek otočit \"x\" nebo \"y\".", 'x');
      if(axis == false){
         return false;
      }
      if(axis == ''){
         alert("Musíte zadat osu");
         return false;
      }
      $.ajax({
         type: "POST",
         url: FileBrowserFunctions.cmsURL+"flipimage.html",
         data: ({
            path : FileBrowserDirsFunctions.currentPath,
            file : fileName,
            axis : axis
         }),
         cache: false,
         dataType : 'json',
         success: function(dataObj){
            FileBrowserFunctions.showResult(dataObj.message);
            //            FileBrowserFunctions.showResult(dataObj);
            if(dataObj.code == true){
               FileBrowserFilesFunctions.loadFiles();
            }
         },
         error : function(XMLHttpRequest, textStatus, errorThrown){
            FileBrowserFunctions.showResult('Chyba při přijímání požadavku na změnu zrcadlení obrázku '+textStatus);
         }
      });
      return true;
   }
}
