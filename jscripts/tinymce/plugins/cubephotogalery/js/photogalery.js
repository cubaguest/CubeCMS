String.prototype.format = function() {
    var formatted = this;
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{'+i+'\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};


var CubePhotogaleryDialog = {
   swfu : null,
   componentUrl : null,
   images : new Array,
   errUpload : false,
	preInit : function() {
		var url;
//      tinyMCEPopup.requireLangPack('cubephotogalery');
		tinyMCEPopup.requireLangPack();
      
//		if (url = tinyMCEPopup.getParam("external_image_list_url"))
//			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var f = document.forms[0], nl = f.elements, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();

		tinyMCEPopup.resizeToInnerSize();
      // init swf upload
      this.initSwfUpload();
      
      // if is insert new images
      if(ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('photogalery') != -1){
         f.wrapperList.value = "";
      }
      
      addClassesToList('imageClassList', 'advlink_styles');
      addClassesToList('linkClassList', 'advlink_styles');
	},
   
   initSwfUpload : function(){
      var settings = {
				flash_url : "/jscripts/swfupload/swfupload.swf",
//				flash9_url : "/jscripts/swfupload/swfupload_fp9.swf",
				upload_url: "/component/tinymce_photogalery/0/upload.php",
				post_params: {"upload_send" : "send", 'sessionid' : tinyMCEPopup.editor.settings.editorid},
				file_size_limit : "5 MB",
            file_types : "*.jpg;*.jpeg;*.png",
            file_types_description : tinyMCEPopup.getLang('cubephotogalery_dlg.images'),
				file_upload_limit : 100,
				file_queue_limit : 0,
            file_post_name : 'upload_file',
//				debug: true,
//				// The event handler functions are defined in handlers.js
				file_queued_handler : CubePhotogaleryDialog.fileQueued,
				file_queue_error_handler : CubePhotogaleryDialog.fileQueueError,
				upload_start_handler : CubePhotogaleryDialog.uploadStart,
				upload_progress_handler : CubePhotogaleryDialog.uploadProgress,
				upload_error_handler : CubePhotogaleryDialog.uploadError,
				upload_success_handler : CubePhotogaleryDialog.uploadSuccess,
//				
				// Button settings
				button_width: "120",
				button_height: "22",
				button_placeholder_id: 'swfUButtonPlaceholder',
//            button_image_url : "/images/upload_cs.png",
            button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND
			};

		CubePhotogaleryDialog.swfu = new SWFUpload(settings);
   },
   
   showSWFUButton : function(){
      CubePhotogaleryDialog.swfu.setButtonDimensions(120, 22);
   },
   hideSWFUButton : function(){
      CubePhotogaleryDialog.swfu.setButtonDimensions(0, 0);
   },
   
   fileQueued : function(file){
     document.getElementById('filesSelected').textContent = this.getStats().files_queued;
     CubePhotogaleryDialog.updateProgressBarQueue(0);
     CubePhotogaleryDialog.updateProgressBarFile(0);
     document.getElementById('uploadingImageStatus').textContent = tinyMCEPopup.getLang('cubephotogalery_dlg.statusready');
   },
   fileQueueError : function(file, errorcode, message){
      var msg = null;
      switch (errorcode) {
         case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
            msg = tinyMCEPopup.getLang('cubephotogalery_dlg.error_file_queue_size_limit').format(file.name);
            break;
         case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
            msg = tinyMCEPopup.getLang('cubephotogalery_dlg.error_file_queue_indorrect_file').format(file.name);
            break;
         case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
            msg = tinyMCEPopup.getLang('cubephotogalery_dlg.error_file_queue_zero_file').format(file.name);
            break;
         default:
            msg = tinyMCEPopup.getLang('cubephotogalery_dlg.error_unknown').format(file.name);
            break;
      }
      tinyMCEPopup.alert(msg);
   },
   uploadStart : function(file){
      // set upload info
      document.getElementById('uploadingImageInfo').textContent = file.name+' ('+file.size+'B)';
      CubePhotogaleryDialog.updateProgressBarFile(0);
//      CubePhotogaleryDialog.updateProgressBarQueue();
   },
   uploadProgress : function(file, bytescomplete, totalbytes){
      CubePhotogaleryDialog.updateProgressBarFile(totalbytes/bytescomplete*100);
   },
   uploadError : function(file, errorcode, message){
      
   },
   uploadSuccess : function(file, serverdata, receivedresponse){
      CubePhotogaleryDialog.updateProgressBarQueue();
      var data = eval("(" + serverdata+")");
      
      // add row in list
      var table = document.getElementById('imagesUploaded');
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);
      var cellN = row.insertCell(0);
      cellN.innerHTML = data.file;
      
      if(data.created){
         tinyMCE.DOM.addClass(row, 'uploaded-ok');
         // add to global array
         CubePhotogaleryDialog.images.push({
            file : data.file,
            dir : data.dir,
            dirsmall : data.dirsmall
         });
      } else {
         CubePhotogaleryDialog.errUpload = true;
         tinyMCE.DOM.addClass(row, 'uploaded-error');
      }
      if(this.getStats().files_queued == 0){
         CubePhotogaleryDialog.close();
      }
      // start new file
      this.startUpload();
   },

	insert : function(file, title) {
		if (this.swfu.getStats().files_queued == 0) {
				tinyMCEPopup.confirm(tinyMCEPopup.getLang('cubephotogalery_dlg.error_no_galery'), function(s) {
      			if (s){
						tinyMCEPopup.editor.focus();
                  tinyMCEPopup.close();
               }
				});
				return;
		}
      
      var ed = tinyMCEPopup.editor, f = document.forms[0], nl = f.elements, v, args = {}, el;
      // načtení dat o galerii z formu
      var gal_name = document.getElementById('galery_name').value;
      var dir_name = document.getElementById('dir_name').value;
      
      if(!dir_name && gal_name){
         dir_name = gal_name
      }

      // set post params for upload proces
      this.swfu.addPostParam('dir', dir_name);
      
      this.swfu.startUpload();
	},

	close : function() {
      if(CubePhotogaleryDialog.errUpload){ // potvrzení ukončení
         tinyMCEPopup.confirm(tinyMCEPopup.getLang('cubephotogalery_dlg.insert_with_error_upload'), function(s) {if (!s){return;}});
      }
      var formObj = document.forms[0];
      var cnt = "";
      var elemLink = document.createElement('a');
      var elemImage = document.createElement('img');
      var elemLabel = document.createElement('span');

      // link element
      tinyMCE.DOM.addClass(elemLink, formObj.prewievIn.value);
      tinyMCE.DOM.addClass(elemLink, formObj.linkClassList.value);
      tinyMCE.DOM.addClass(elemLink, 'image');
      if(formObj.addLabel.checked){
         tinyMCE.DOM.addClass(elemLink, 'image-with-label');
      }
      elemLink.style.cssText = formObj.linkStyle.value;
      
      // image element
      tinyMCE.DOM.addClass(elemImage, formObj.imageClassList.value);
      elemImage.style.cssText = formObj.imageStyle.value;
      elemLink.appendChild(elemImage);

      // create title
      if(formObj.addLabel.checked){
         elemLabel.innerHTML = formObj.imageLabel.value;
         elemLink.appendChild(elemLabel);
      }
      
      var arLen=CubePhotogaleryDialog.images.length;
      var image;
      for ( var i=0, len=arLen; image = CubePhotogaleryDialog.images[i], i<len; ++i ){
         elemImage.src = image.dirsmall+image.file;
         elemImage.alt = image.file;
         elemLink.href = image.dir+image.file;
         elemLink.title = image.file;
         cnt += tinymce.DOM.getOuterHTML(elemLink.cloneNode(true));
      }
      
      // nadpis
      var headline = "";
      if(formObj.galery_name.value != ""){
         var h = document.createElement(formObj.galery_name_type.value);
         h.innerHTML = formObj.galery_name.value;
         headline = tinymce.DOM.getOuterHTML(h);
      }
      
      if(formObj.wrapperList.value != ""){
         var wr = document.createElement(formObj.wrapperList.value);
         wr.setAttribute('class', 'photogalery');
//         cnt += "<span class=\"reseter\">&nbsp;</span>";
         cnt += "<hr class=\"reseter\" /><p>&nbsp;</p>";
         wr.innerHTML = cnt;
         tinyMCEPopup.execCommand('mceInsertContent', false, headline+tinymce.DOM.getOuterHTML(wr));
      } else {
         tinyMCEPopup.execCommand('mceInsertContent', false, headline+cnt);
      }

		tinyMCEPopup.restoreSelection();
		// Fixes crash in Safari
		if (tinymce.isWebKit){
			ed.getWin().focus();
      }
      
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.editor.focus();
		tinyMCEPopup.close();
      /* END */
   },

   updateProgressBarQueue : function(value){
      if(typeof(value) == 'undefined'){
         var all = this.swfu.getStats().successful_uploads + this.swfu.getStats().upload_errors  + this.swfu.getStats().files_queued;
         var uploaded = this.swfu.getStats().successful_uploads + this.swfu.getStats().upload_errors;
         value = Math.round((uploaded / all) * 100 );
      }
      document.getElementById('progressQueueText').textContent = value+' %';
      document.getElementById('progressQueueMeter').style.width = value+'%';
   },
   updateProgressBarFile : function(value){
      value = Math.round(value);
      document.getElementById('progressFileText').textContent = value+' %';
      document.getElementById('progressFileMeter').style.width = value+'%';
   }
};

CubePhotogaleryDialog.preInit();
tinyMCEPopup.onInit.add(CubePhotogaleryDialog.init, CubePhotogaleryDialog);
