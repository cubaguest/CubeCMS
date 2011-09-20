var FileBrowserDialogue = {
   listType : null,
   win : null,
   category : 0,
   init : function () {
      // Here goes your code for setting your custom things onLoad.
      FileBrowser.listType = tinyMCEPopup.getWindowArg("listType");
      FileBrowserDialogue.win = tinyMCEPopup.getWindowArg("window");
      FileBrowserDialogue.category = tinyMCEPopup.getWindowArg("cat");
      var path = tinyMCEPopup.getWindowArg("url");
      if(path != ""){
         var regex = /data(\/.*\/)[a-z0-9._-]+/i;
         var matches = path.match(regex);
         if(matches){
            FileBrowser.currentDir = matches[1];
         }
      }
      FileBrowser.init();
   },
   submitFile : function () {
      // insert information now
      FileBrowserDialogue.win.document.getElementById(tinyMCEPopup.getWindowArg("input"))
      .value = FileBrowser.getSelected().data('realpath');
      // are we an image browser
      if (typeof(FileBrowserDialogue.win.ImageDialog) != "undefined") {
         // we are, so update image dimensions...
         if (FileBrowserDialogue.win.ImageDialog.getImageData)
            FileBrowserDialogue.win.ImageDialog.getImageData();
         // ... and preview if necessary
         if (FileBrowserDialogue.win.ImageDialog.showPreviewImage)
            FileBrowserDialogue.win.ImageDialog.showPreviewImage(FileBrowser.getSelected().data('realpath'));
      }
      // close popup window
      tinyMCEPopup.close();
   }
};

var FileBrowser = {
   cmsPluginUrl : null,
   currentDir : null,
   baseUrl : null,
   iconsDir : 'images/files/',
   uploadLink : null,
   sessionId : null,
   listType : null,
   uploadFilesPosParams : null,
   $itemsBox : null,
   $dirCnt : null,
   writable : false,
   clipboard : {cut : false, items : new Array},
   extendJQ : function(){
      $.fn.selectItem = function() {
         $(this).addClass('item-selected');
      };
      $.fn.markItem = function() {
         $(this).addClass('item-marked');
      };
      $.fn.unselectItem = function() {
         $(this).removeClass('item-selected');
      };
      $.fn.unmarkItem = function() {
         $(this).removeClass('item-marked');
      };
      $.fn.clipCutItem = function() {
         $(this).addClass('item-cut');
         // logika pro vyjmutí

      };
      $.fn.clipUncutItem = function() {
         $(this).removeClass('item-cut');
      };
      $.extend( {
         isSelectedItem: function(item) {
            return $(item).hasClass('item-selected');
         },
         isMarkedItem: function(item) {
            return $(item).hasClass('item-marked');
         },
         getItemSize: function(size){
            var size = parseFloat(size);
            if(size > 1048576){ // MB
               return Number(size/1048576).toFixed(2).toString()+' MB';
            } else if(size > 1024) { // KB
               return Number(size/1024).toFixed(2).toString()+' KB';
            } else { // B
               return size.toString()+' B';
            }
         }
      });
   },
   init : function(){
      this.extendJQ();
      this.cmsPluginUrl = window.location.toString().replace('browser.php', 'jscripts/tinymce/');
      if(this.currentDir == null){
         // load previous path
         this.currentDir = this.loadPath();
      }
      // UPLOAD POST PARAMS
      this.uploadFilesPosParams = {
         sessionid: FileBrowser.sessionId,
         'upload_send' : 'send',
         path: FileBrowser.currentDir,
         type : FileBrowser.listType
      };

      // SWF UPLOAD
      $('.swfupload-control').swfupload({
         // Backend Settings
         upload_url: FileBrowser.uploadLink,    // Relative to the SWF file (or you can use absolute paths)
         file_post_name: "upload_file",
         post_params: FileBrowser.uploadFilesPosParams,
         file_size_limit : (maxUploadFileSize/1024), // global upload size - 1KB
         file_types : FileBrowser.getFileTypes(),
         file_types_description : "files",
         file_upload_limit : 150,
         file_queue_limit : 0,
         button_image_url : FileBrowser.baseUrl+"/images/upload_cs.png", // Relative to the SWF file
         button_placeholder_id : "spanButtonPlaceholder",
         button_width: 61,
         button_height: 22,
         button_window_mode : SWFUpload.WINDOW_MODE.OPAQUE,
         debug: true,
         // Flash Settings
         flash_url : FileBrowser.baseUrl+"/jscripts/swfupload/swfupload.swf",
         flash9_url : FileBrowser.baseUrl+"/jscripts/swfupload/swfupload_fp9.swf"
      });

      // assign our event handlers
      $('.swfupload-control')
      .bind('fileQueued', function(event, file){
         FileBrowser.uploadFilesPosParams.path = FileBrowser.currentDir;
         $(this).swfupload('setPostParams', FileBrowser.uploadFilesPosParams );
         $(this).swfupload('startUpload');
      })
      .bind('uploadComplete', function(event, file){$(this).swfupload('startUpload');})
      .bind('uploadStart', function(event, file){$('#uploading-files').show();})
      .bind('fileQueueError', function(event, file, errorCode, message){FileBrowser.showResult([], ['Velikost souboru '+file.name+' je větší než povolený limit']);})
      .bind('uploadSuccess', function(event, file, serverData){
         FileBrowser.load();
         $('#uploading-files').hide();
         var data = $.parseJSON(serverData);
         FileBrowser.showResult(data.infomsg, data.errmsg);
      });

      // init items box
      this.$itemsBox = $('#itemsList');

      // EVENTS
      // keys
      $('#itemsList li a').live('keypress',function(event){
         if(event.keyCode == 38 || event.keyCode == 40 // cursor
            || event.keyCode == 13 // enter
            || event.keyCode == 46 //delete
            || event.keyCode == 45 // insert
         ){
            event.preventDefault();

            switch (event.keyCode) {
               case 38:// up
                  $(this).parent('li').prev().children('a').trigger('click', event.shiftKey);
                  break;
               case 40:// down
                  $(this).parent('li').next().children('a').trigger('click', event.shiftKey);
                  break;
               case 13:// enter
                  $(this).trigger('dblclick', false);
                  break;
               case 45:// insert (move down and mark item)
                  $(this).parent('li').next().children('a').trigger('click', true);
                  break;
               case 46:// delete
                  $('#buttonDelete').click();
                  break;
               default:
                  alert(event.keyCode);
                  break;
            }
         }
      });
      // click
      $('#itemsList li a').live('click',function(event, ctrlKey){
         event.preventDefault();
         $(this).focus();
         if(typeof(ctrlKey) != 'undefined') event.ctrlKey = ctrlKey;
         if(typeof(event.shiftKey) == 'undefined') event.shiftKey = false;

            if(event.ctrlKey){ // ctrl
               if($.isMarkedItem(this)){
                  $(this).unmarkItem();
               } else {
                  $(this).markItem();
               }
            } else if(event.shiftKey){ // shift
               var selected = FileBrowser.getSelected();
               var fIndex = $('li').index(selected.parent('li'));
               var tIndex = $('li').index($(this).parent('li'));
               var items = FileBrowser.getAllItems();
               // move down
               if(fIndex <= tIndex){
                  for (var i = fIndex; i <= tIndex; i++) {
                     $(items[i]).markItem();
                  }
               } else {
                  for (var y = fIndex; y >= tIndex; y--) {
                     $(items[y]).markItem();
                  }
               }
            } else {
               FileBrowser.getMarked().unmarkItem();
               $(this).markItem();
            }
            FileBrowser.getSelected().unselectItem();
            $(this).selectItem(); // vybereme označený
            FileBrowser.showData($(this)); // show info
         return;
      });
      // dbclick
      $('#itemsList li a').live('dblclick',function(event, ctrlKey){
         if($(this).data('type') == 'dir' || $(this).data('type') == 'dot'){ // is dir or dot
            FileBrowser.load($(this).data('path')+$(this).data('name')+'/');
         } else { // is file
            //alert('selected: '+$(this).data('realpath'));
            FileBrowserDialogue.submitFile();
         }
         return;
      });
      // load all items
      this.load();
      // show tip
      FileBrowser.showTip();
   },
   showResult : function(infomsg, errmsg){
      if(errmsg.length > 0){
         $('#log').prepend('<br />').prepend($('<span></span>').text(errmsg.join(', ')).addClass("errmsg"));
      }
      if(infomsg.length > 0){
         $('#log').prepend('<br />').prepend($('<span></span>').text(infomsg.join(', ')).addClass("msg"));
      }
   },
   logMsg : function(msg){},
   clearLog : function(){$('#log').html('');},
   showData : function(item){
      var imgSize = 150;
      var info = item.data();
      var $infoBox = $('#infoBox');
      var $actionsBox = $('#fileActions').html('<p>Žádné nástroje nejsou k dispozici.</p>');
      // clear boxs
      $infoBox.html('');

      $('#currentFile').text(info.name);
      if(info.type == 'dir') {
            // není zapisovatelný
         if(info.access.write == true){
            var $abox = $('<p></p>');
            var $select = $('<select></select>').attr({name : 'users', size : 6, multiple : true});
            var $selI = FileBrowser.getSelected();

            FileBrowser.request('getUsers', {path : $selI.data('path'), name : $selI.data('name')}, function(data){
               var $opt = $('<option></option>');
               $.each(data.users, function(index){
                  var $o = $opt.clone();
                  $o.attr({value : this.id, selected : this.selected}).text(this.name);
                  $select.append($o);
               });
            });
            $abox.append('<strong>Povolení zápisu pro:</strong><br />');
            $abox.append($select);
            $abox.append('<br />');
            $abox.append('<input name="chenge_perms" value="Přiřadit" type="button" onClick="FileBrowser.setDirPerms()" />');
            $actionsBox.html($abox);
         }
      } else if(info.type == 'file'){
         if(info.info.type == 'image'){ // IMAGE
            var $img = $('<img />');
            var w = info.info.dimension.w;
            var h = info.info.dimension.h;
            if(info.info.dimension.w > imgSize || info.info.dimension.h > imgSize){ // must be resized
               if(info.info.dimension.w > info.info.dimension.h){ // width
                  h = info.info.dimension.h/info.info.dimension.w*imgSize;
                  w = imgSize;
               } else { //height
                  w = info.info.dimension.w/info.info.dimension.h*imgSize;
                  h = imgSize;
               }
            }
            var time = new Date;
            $img.attr('src', FileBrowser.baseUrl+info.realpath+'?t='+(time.getTime()/1000).toString())
            .css({
               width: w, height : h-5//, 'padding-top' : ((imgSize-h)/2)-2 // centering
            });
            $infoBox.append($('<p></p>').addClass('imgprev-box').html($img));
            // file info
            var $sizes = $('<p></p>').addClass('file-info')
               .html('Rozměry:<br/>'+info.info.dimension.w+'x'+info.info.dimension.h+' px<br />'+'Velikost:<br/>'+$.getItemSize(info.info.size)+'<br />');

            if(info.info.dimension.w > imageBigSizeW){
               $sizes.append('<br /><strong>Obázek je příliš velký pro vložení do stránky!!!. Doporučujeme zmenšit alespoň na '+imageBigSizeW+'x'+imageBigSizeH+' px.</strong><br />');
            }
            $infoBox.append($sizes);

            // action box
            var $abox = $('<p></p>');

            $abox.append('<strong>Vytvořit zmenšeninu:</strong><br />');
            $abox.append(
               '<input type="text" size="5" maxlength="5" name="img_w" value="0" />&times;'
               +'<input type="text" size="5" maxlength="5" name="img_h" value="0" /> px '
               +'<input type="button" name="img_resize" value="Vytvořit" onClick="FileBrowser.imageResize()" /><br />'
               +'<input type="checkbox" name="img_ratio" checked="checked" /> Zachovat poměr '
               +'<input type="checkbox" name="img_crop" /> Ořezat'
               +'<hr />'
            );

            var $iW = $('input[name="img_w"]', $abox).val(info.info.dimension.w).data('origsize', info.info.dimension.w);
            var $iH = $('input[name="img_h"]', $abox).val(info.info.dimension.h).data('origsize', info.info.dimension.h);

            $iW.change(function(){
               if($('input[name="img_ratio"]',$abox).is(':checked')){
                  $('input[name="img_h"]', $abox).val(
                     Math.round(parseInt($(this).val())/parseInt($('input[name="img_w"]', $abox).data('origsize'))*parseInt($('input[name="img_h"]', $abox).data('origsize')))
                  );
               }
            });
            $iH.change(function(){
               if($('input[name="img_ratio"]',$abox).is(':checked')){
                  $('input[name="img_w"]', $abox).val(
                     Math.round(parseInt($(this).val())/parseInt($('input[name="img_h"]', $abox).data('origsize'))*$('input[name="img_w"]', $abox).data('origsize'))
                  );
               }
            });

            $abox.append('<strong>Otočit doprava:</strong><br />');
            $abox.append(
                '<select name="img_degree">'
               +'<option value="90">90°</option>'
               +'<option value="180">180°</option>'
               +'<option value="270">270°</option>'
               +'</select>&nbsp;'
               +'<input type="button" name="img_rotate" value="Otočit" onClick="FileBrowser.imageRotate()" />'
               +'<hr />'
            );

            $abox.append('<strong>Vytvořit obrázky v systémové velikosti:</strong><br />');
            $abox.append(
               'Velký:'+imageBigSizeW+'x'+imageBigSizeH+' Malý:'+imageSmallSizeW+'x'+imageSmallSizeH
               +'&nbsp;<input type="button" name="img_system_sizes" value="Vytvořit" onClick="FileBrowser.imageSystemResize()" /><br />'
               +'<input type="checkbox" name="img_createdirs" checked="checked" /> Zařadit do adresářů (small, medium) '
               +'<hr />'
            );
            $actionsBox.html($abox);
         } else if(info.info.type == 'flash'){// flash
            $infoBox.html(''); // clear box
            var w = info.info.dimension.w;
            var h = info.info.dimension.h;
            if(info.info.dimension.w > imgSize || info.info.dimension.h > imgSize){ // must be resized
               if(info.info.dimension.w > info.info.dimension.h){ // width
                  h = info.info.dimension.h/info.info.dimension.w*imgSize;
                  w = imgSize;
               } else { //height
                  w = info.info.dimension.w/info.info.dimension.h*imgSize;
                  h = imgSize;
               }
            }
            var $flash = $('<p></p>').addClass('imgprev-box');
            $flash.html('<object width="'+w+'" height="'+h+'">'
               +'<param name="movie" value="'+FileBrowser.baseUrl+info.realpath+'">'
               +'<embed src="'+FileBrowser.baseUrl+info.realpath+'" width="'+w+'" height="'+h+'">'
               +'</embed></object>');
            $infoBox.append($flash);

            $infoBox.append($('<p></p>').addClass('file-info')
            .html('Rozměry:<br/>'+info.info.dimension.w+'x'+info.info.dimension.h+' px<br />'+'Velikost:<br/>'+$.getItemSize(info.info.size)+'<br />'));
         } else { // OTHER FILES
            $infoBox.html($('<p></p>').addClass('file-info').html('Velikost:<br/>'+$.getItemSize(info.info.size)+'<br />'));
         }
      }
   },
   timer : null,
   showWorking : function(){
      FileBrowser.timer = setTimeout(function(){
         $("#working").show();
      }, 500);
//      $("#working").show();
   },
   hideWorking : function(){
      clearTimeout(FileBrowser.timer);
      $("#working").hide()
   },
   getFileTypes : function(){
      var extensions = Array();
      if(this.listType == 'image'){
         extensions = Array('jpg','jpeg','png');
      } else if(this.listType == 'media'){
         extensions = Array('swf', // falsh
            'mp4','m4v', 'ogv', 'mov' , 'flv', 'rm', 'qt', 'avi', // video
            'mp3', 'ogg', 'wma', 'wav' // audio
         );
      }
      
      if(extensions.length != 0){
         var len = extensions.length;
         for ( var i = 0; i < len ; i++ ) {
            extensions[i] = '*.'+extensions[i]; 
            extensions[i+len] = extensions[i].toUpperCase(); 
         }
         return extensions.join(';');
      }
      return "*.*";
   },
   request : function(action, postValues, sucessfunc, errfunc){
      $.ajax({
         type : 'POST', data : postValues, url : window.location.toString().replace('browser.php', action+'.php'),
         cache : false,
//         async : false,
         success: function(data){
            if(typeof (sucessfunc)== 'function') {
               sucessfunc.call(this, data);
            }
            if(data.infomsg.length > 0 || data.errmsg.length > 0){
               FileBrowser.showResult(data.infomsg, data.errmsg)
            }
         },
         error: function(){
            FileBrowser.showResult([], ['Chyba při komunikaci se serverem. Zkuste znovu.']);
         },
         complete : function(){}
      })
   },
   getSelected : function(){
      return this.$itemsBox.find('li a.item-selected');
   },
   getMarked : function(){
      return this.$itemsBox.find('li a.item-marked');
   },
   getAllItems : function(){
      return this.$itemsBox.find('li a');
   },
   // load dirs
   load : function(path){
      if(typeof(path) == 'undefined') path = this.currentDir;
      FileBrowser.showWorking();
      this.request('getitems', {path:path, type: FileBrowser.listType}, function(data){
         FileBrowser.hideWorking();
         var selectedItemPath = FileBrowser.getSelected().data('realpath');
         // clear box
         FileBrowser.$itemsBox.html('');
         if(typeof(data.items) != "undefined"){
            $.each(data.items, function(){
               var $item = $('<a>');
               $item
//            .append($('<input name="selected" value="'+this.name+'" type="checkbox" />'))
               .append(this.name)
               .attr({
                  title : this.name,
                  href : this.realpath
               }).data(this);

               if(this.type == 'dir') {
                  $item.addClass('dir');
                  if(this.info.type == 'home'){
                     $item.addClass('dir-home');
                  } else if(this.info.type == 'public'){
                     $item.addClass('dir-pub');
                  }
                  if(this.access.write == true) $item.addClass('dir-writable');
               } else if(this.type == 'dot'){
                  $item.addClass('dir');
                  $item.addClass('dir-up');
               } else {
            // file
                  $item.addClass('file')
                     .css('background-image', 'url('+FileBrowser.baseUrl+FileBrowser.iconsDir+this.info.type+'_icon.png)');
                  if(this.access.write == true) $item.addClass('file-writable');
               }
               FileBrowser.$itemsBox.append($('<li></li>').append($item));
            });

            FileBrowser.writable = data.writable;
            if(FileBrowser.currentDir == data.current && typeof(selectedItemPath) != 'undefined'){ // same dir
               var elem = FileBrowser.$itemsBox.find('a[href="'+selectedItemPath+'"]');
               if(elem.length > 0) {elem.click();}
               else {
                  FileBrowser.$itemsBox.children('li:first a').click();// select first item
               }
            } else { // jiný adresář
               FileBrowser.$itemsBox.children('li:first a').click();// select first item
               FileBrowser.storePath(data.current);
            }
            FileBrowser.currentDir = data.current;
            $('#currentPath').text(data.current);
         } else {
            FileBrowser.goPublic();
         }
      });
   },
   goHome : function(){
      this.currentDir = 'home';
      this.load();
   },
   goPublic : function(){
      this.currentDir = '/public/';
      this.load();
   },
   storePath : function(path){
      var value = cookieName+'='+escape(path)+';';
      // expirace
      var date = new Date();
      date.setTime(date.getTime() + 30 * 60 * 1000); // 30 minut
      value+='expires=' + date.toGMTString()+';';
      value+='path=/;';
      document.cookie = value;
   },
   loadPath : function(){
      if (document.cookie.length>0 && FileBrowser.currentDir == null){
         var c_start = document.cookie.indexOf(cookieName+"=");
         if (c_start!=-1){
            c_start=c_start + cookieName.length+1;
            var c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
         }
      }
      return '/public/';
   },
   // funkce pro obsluhu vytváření, mazání
   createDir : function(){
      if(this.writable == false){
         alert('Nemáte dostatečná práva pro vytvoření adresáře');
         return false;
      }
      var newDir = prompt('Zadejte název nového adresáře');
      if(newDir == null || newDir == '') return false;
      FileBrowser.request('createdir', {path : this.currentDir, newname : newDir}, function(){
         FileBrowser.load();
      });
      return true;
   },
   deleteItems : function(){
      if(confirm('Opravdu smazat označené položky?') == false) return;
      this.showWorking();
      var items = new Array;
      this.getMarked().each(function(){
         items.push(new Array($(this).data('path'),$(this).data('name')));
      });
      if(items.length > 0){
         this.request('delete', {items : items}, function(){
            FileBrowser.hideWorking();
            FileBrowser.load();
         });
      }
      return;
   },
   renameItems : function(){
      var items = new Array;
      var newName = "";
      this.getMarked().each(function(){
         var data = $(this).data();
         if(data.type != 'dot' && data.access.write == true) {
            // zeptáme se na každý nový název
            newName = window.prompt("Přejmenovat na", data.name);
            if(newName != null && newName != "" && newName != data.name){
               items.push(new Array(data.path,data.name, newName));
            }
         }
      });
      if(items.length > 0){
         FileBrowser.showWorking();
         this.request('rename', {items : items}, function(){
            FileBrowser.hideWorking();
            FileBrowser.load();
         });
      }
   },
   // Schránka
   clipboardCut : function(){
      this.clipboardClear();
      FileBrowser.clipboard.cut = true;
      this.getMarked().each(function(){
         if($(this).data('type') != 'dot'){
            $(this).addClass('item-cut');
            FileBrowser.clipboard.items.push($(this).data());
         }
      });
      this.clipboardShow();
      return;
   },
   clipboardCopy : function(){
      this.clipboardClear();
      FileBrowser.clipboard.cut = false;
      this.getMarked().each(function(){
         if($(this).data('type') != 'dot'){
            $(this).addClass('item-copy');
            FileBrowser.clipboard.items.push($(this).data());
         }
      });
      this.clipboardShow();
      return;
   },
   clipboardPaste : function(){
      this.showWorking();
      var items = new Array;
      $.each(this.clipboard.items , function(){
         var info = this;
         // kopie
         items.push(new Array(info.path, info.name));
      });
      this.request('copy', {target: this.currentDir, items:items, move : this.clipboard.cut}, function(){
         FileBrowser.clipboardClear();
         FileBrowser.hideWorking();
         FileBrowser.load();
      })
   },
   clipboardClear : function(){
      this.clipboard.cut = false;
      this.clipboard.items = new Array;
      FileBrowser.getMarked().removeClass('item-cut').removeClass('item-copy');
      $('#clipboard').html('');
   },
   clipboardShow : function(){
      var $box = $('#clipboard');
      $.each(FileBrowser.clipboard.items , function(){
         $box.append('<li><span>'+this.realpath+'</span></li>');
      });
   },
   imageResize : function(){
      FileBrowser.showWorking();
      var $items = Array;
      if($('input[name="apply_all"]').is(':checked')){
         $items = FileBrowser.getMarked();
      } else {
         $items = FileBrowser.getSelected();
      }
      var files = new Array;
      $items.each(function(){
            files.push($(this).data('name'));
      });
      FileBrowser.request('imageResized', {
         path : FileBrowser.currentDir,file : files,
         ratio : $('input[name="img_ratio"]').is(':checked'),
         newW : $('input[name="img_w"]').val(), newH : $('input[name="img_h"]').val(),
         crop : $('input[name="img_crop"]').is(':checked')
         }, function(){
            FileBrowser.hideWorking();
            FileBrowser.load();
      });
   },
   imageSystemResize : function(){
      FileBrowser.showWorking();
      var $items = Array;
      if($('input[name="apply_all"]').is(':checked')){
         $items = FileBrowser.getMarked();
      } else {
         $items = FileBrowser.getSelected();
      }
      var files = new Array;
      $items.each(function(){
            files.push($(this).data('name'));
      });
      FileBrowser.request('createSystemImages', {
         path : FileBrowser.currentDir, file : files,
         createdirs : $('input[name="img_createdirs"]').is(':checked')
      }, function(){FileBrowser.hideWorking();FileBrowser.load();});
   },
   imageRotate : function(){
      FileBrowser.showWorking();
      var $items = Array;
      if($('input[name="apply_all"]').is(':checked')){
         $items = FileBrowser.getMarked();
      } else {
         $items = FileBrowser.getSelected();
      }
      var files = new Array;
      $items.each(function(){
            files.push($(this).data('name'));
      });
      FileBrowser.request('imageRotate', {
         path : FileBrowser.currentDir,
         file : files,
         degree : $('select[name="img_degree"]').val()
      }, function(){
         FileBrowser.hideWorking();
         FileBrowser.load();
      });
   },
   setDirPerms : function(){
      var users = $('select[name="users"]').val();
      FileBrowser.getMarked().each(function(){
         if($(this).data('type') == 'dir'){ // only dirs
            FileBrowser.request('storeDirPerms', {path:$(this).data('path'), name : $(this).data('name'), users : users});
         }
      });
   },
   showTip : function(){
      $('#log').html($('<span></span>').addClass('tip').text('TIP: '+tips[Math.floor(Math.random()*tips.length)]));
   }
};
