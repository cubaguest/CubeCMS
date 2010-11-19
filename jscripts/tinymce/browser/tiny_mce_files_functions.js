var FileBrowserDialogue = {
   listType : null,
   win : null,
   category : 0,
   init : function () {
      // Here goes your code for setting your custom things onLoad.
      FileBrowserDialogue.listType = tinyMCEPopup.getWindowArg("listType");
      FileBrowserDialogue.win = tinyMCEPopup.getWindowArg("window");
      FileBrowserDialogue.category = tinyMCEPopup.getWindowArg("cat");
      FileBrowser.init();
      FileBrowserDirs.init();
      FileBrowserFiles.init();
   },
   submitFile : function () {
      // insert information now
      FileBrowserDialogue.win.document.getElementById(tinyMCEPopup.getWindowArg("input"))
      .value = FileBrowserFilesFunctions.currentFilePath;
      // are we an image browser
      if (typeof(FileBrowserDialogue.win.ImageDialog) != "undefined") {
         // we are, so update image dimensions...
         if (FileBrowserDialogue.win.ImageDialog.getImageData)
            FileBrowserDialogue.win.ImageDialog.getImageData();
         // ... and preview if necessary
         if (FileBrowserDialogue.win.ImageDialog.showPreviewImage)
            FileBrowserDialogue.win.ImageDialog.showPreviewImage(FileBrowserFilesFunctions.currentFilePath);
      }
      // close popup window
      tinyMCEPopup.close();
   }
};

var FileBrowser ={
   cmsURL : null,
   cmsPluginUrl : null,
   baseUrl : null,
   uploadLink : null,
   sessionId : null,
   typeFiles : null,
   uploadFilesPosParams : null,
   init : function(){
      this.cmsURL = window.location.toString().replace('browser.php', ''); // remove browser action of component
      this.cmsPluginUrl = this.cmsURL+"jscripts/tinymce/";
   // this.cmsURL = this.cmsURL+"jsplugin/tinymce/cat-"+FileBrowserDialogue.category+"/";
   // 
      // Upload
      this.uploadFilesPosParams = {
         sessionid: this.sessionId , 
         'upload_send' : 'send', 
         dir: FileBrowserDirs.currentDir, 
         list : this.typeFiles
      };
      
      $('.swfupload-control').swfupload({
         // Backend Settings
         upload_url: FileBrowser.uploadLink,    // Relative to the SWF file (or you can use absolute paths)
         file_post_name: "upload_file",
         post_params: FileBrowser.uploadFilesPosParams,
         file_size_limit : "10048", // 10MB
         file_types : FileBrowse.getFileTypes(),
         file_types_description : "files",
         file_upload_limit : 150,
         file_queue_limit : 0,
         button_image_url : FileBrowser.baseUrl+"/images/upload_cs.png", // Relative to the SWF file
         button_placeholder_id : "spanButtonPlaceholder",
         button_width: 61,
         button_height: 22,
         button_window_mode : SWFUpload.WINDOW_MODE.OPAQUE,
//         debug: true,
         // Flash Settings
         flash_url : FileBrowser.baseUrl+"/jscripts/swfupload/swfupload.swf",
         flash9_url : FileBrowser.baseUrl+"/jscripts/swfupload/swfupload_fp9.swf"
      });
      
      // assign our event handlers
      $('.swfupload-control')
      .bind('fileQueued', function(event, file){
         FileBrowser.uploadFilesPosParams.dir = FileBrowserDirs.currentDir;
         $(this).swfupload('setPostParams', FileBrowser.uploadFilesPosParams );
         $(this).swfupload('startUpload');
      })
      .bind('uploadComplete', function(event, file){$(this).swfupload('startUpload');})
//      .bind('fileQueueError', function(event, file, errorCode, message){alert('Velikost souboru'+file.name+'je větší než povolený limit');})
      .bind('uploadStart', function(event, file){
         $('#uploading-files').show();
      })
      .bind('uploadSuccess', function(event, file, serverData){
         FileBrowserFiles.load();
         $('#uploading-files').hide();
         $('#fileInfo');
//         FileBrowser.showResult(serverData.infomsg, serverData.errmsg);
      });
   },
   showResult : function(infomsg, errmsg){
      $('#operationResult').html('');
      if(infomsg.length > 0 ){
         $('#operationResult').append('<span class="msg">'+infomsg.split(',')+'</span>');
      } 
      if(errmsg.length > 0) {
         $('#operationResult').append('<span class="errmsg">'+infomsg.split(',')+'</span>');
      }
   },
   showData : function(item){
      var info = item.data();
      $('#fileInfo').html(info.name+"<br />"+info.path+"<br />"+info.writable+"<br />");
   },
   getFileTypes : function(){
//      return "*.jpg;*.jpeg;*.png;*.JPG;*.JPEG;*.PNG";
      return "*.*";
   },
   logMsg : function(msg){}
   
};

var FileBrowserFiles = { 
   $box : null,
   $fileCnt : null,
   // init dir view
   init : function(){
      this.$box = $('#fileList'); 
      $('#fileList li a').live('click',function(event){
         event.preventDefault();
         if($(this).hasClass('file-selected')){//dbclick
            // add file to tinyMCE
         } else {
            FileBrowser.showData($(this));
            if(event.metaKey == false){
               $('#fileList li a').removeClass('file-selected');
               $(this).addClass('file-selected');
            } else {
               $(this).addClass('file-selected');
            }
         }
      });

   },
   // load dirs
   load : function(){
      FileBrowserFiles.$box.html('<li class="files-loading">Načítám ...</li>');
      $.ajax({
         type : 'POST',
         data : {dir : FileBrowserDirs.currentDir, type : FileBrowser.typeFiles},
         url : FileBrowser.cmsURL+"getfiles.json",
         cache : false,
         success: function(data){
            FileBrowserFiles.$box.html('');
            if(data.files.length > 0){
               $.each(data.files, function(){
                  var $item = $('<a>');
                  $item.text(this.name).attr('title', this.name).data(this);
                  $item.addClass('file');
                  if(this.writable == true) $item.addClass('file-writable');
                  FileBrowserFiles.$box.append($('<li></li>').append($item));
               });
            } else {
               FileBrowserFiles.$box.append($('<li></li>').text('Složka je prázdná'));
            }
            
         }
      })
   }
};


var FileBrowserDirs = {
   $box : null,
   $dirCnt : null,
   currentDir : 'home',
   // init dir view
   init : function(){
      this.$box = $('#directoryList'); 
      this.load();

      $('#directoryList li a').live('click',function(event){
         event.preventDefault();
         FileBrowser.showData($(this));
         if($(this).hasClass('dir-selected')){//dbclick
            FileBrowserDirs.currentDir = $(this).data('path');
            FileBrowserDirs.load();
         } else {
            $('#directoryList li a').removeClass('dir-selected');
            $(this).addClass('dir-selected');
            // disabling buttons
            if($(this).data('writable') == true){
               
            } else {
               
            }
            
         }
      });

   },
   // load dirs
   load : function(){
      $.ajax({
         type : 'POST',
         data : {
            dir : this.currentDir
            },
         url : FileBrowser.cmsURL+"getdirs.json",
         cache : false,
         success: function(data){
            FileBrowserDirs.$box.html('');
            $.each(data.dirs, function(){
               var $item = $('<a>');
               $item.text(this.name).attr('title', this.name).data(this);
               $item.addClass('dir');
               if(this.isdot == true) $item.addClass('dir-up');
               if(this.home == true) $item.addClass('dir-home');
               if(this.writable == true) $item.addClass('dir-writable');
               FileBrowserDirs.$box.append($('<li></li>').append($item));
            });
            FileBrowserFiles.load();
            FileBrowserDirs.currentDir = data.current;
         // FileBrowserDirs.box.html(data);
         }
      })
   },
   goHome : function(){
      this.currentDir = 'home';
      this.load();
   }
};