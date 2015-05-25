CubeImagesEditor = {
   options : {
      uploadImageUrl : null,
      imageDeleteUrl : null,
      imageChangeStateUrl : null,
      imageMoveUrl : null,
      imageRotateUrl : null,
      editLabelsUrl : null,
      
      getImagesUrl : null,
      editImageUrl : null
   },
   uploadItemTpl : null,
   $queueList : null,
   $imagesList : null,
   $uplaodForm : null,
   
   init : function(params)
   {
      var that = this;
      this.options = $.extend(this.options, params);
      this.uploadItemTpl = $('#upload-queue-tpl li').first().clone();
      this.$queueList = $('#upload-queue');
      this.$imagesList = $('#images-list');
      this.$uplaodForm = $('#images-upload-form');
      this.$imagesActions = $('#images-actions');
      this.$dialogLabels = $('#dialog-image-labels');
      
      // reset selected
      this.$imagesList.find('.image-checkbox').prop('checked', false);
      this.$imagesActions.find('#images-action-select,#buttons-process-images').prop('disabled', 'disabled');
      that.$imagesActions.removeClass('fixed-box');
      // eventy
      $.fn.prettyPhoto();
      // náhled
      this.$imagesList.on("click", '.toolbox-button-preview', function(){
         $.prettyPhoto.open( $(this).closest('li').data('src'), $(this).closest('li').data('name')[CubeCMS.lang], $(this).closest('li').data('desc').cs );
      });
      // označení
      this.$imagesList.on("change", '.image-checkbox', function(){
         if($(this).is(":checked")){
            $(this).closest('li').addClass('selected');
         } else {
            $(this).closest('li').removeClass('selected');
         }
         if(that.$imagesList.find('.image-checkbox:checked').length > 0 ){
            that.$imagesActions.find('#images-action-select,#buttons-process-images').prop('disabled', false);
            that.$imagesActions.addClass('fixed-box');
         } else {
            that.$imagesActions.find('#images-action-select,#buttons-process-images').prop('disabled', true);
            that.$imagesActions.removeClass('fixed-box');
         }
      });
      this.$imagesActions.on('click', '#button-select-all-images', function(){
         if(that.$imagesList.find('.image-checkbox:checked').length > 0 ){
            that.$imagesList.find('.image-checkbox').prop('checked', false).change();
         } else {
            that.$imagesList.find('.image-checkbox').prop('checked', true).change();
         }
      });
      this.$imagesActions.on('click', '#buttons-process-images', function(){
         var val = that.$imagesActions.find('#images-action-select').val();
         console.log(val);
         if(val === "rotate-left"){
            $('li', that.$imagesList).each(function(){
               if($(this).has('.image-checkbox:checked').length){
                  $(this).find('.toolbox-button-rotate_left').click();
               }
            });
         } else if(val === "rotate-right"){
            $('li', that.$imagesList).each(function(){
               if($(this).has('.image-checkbox:checked').length){
                  $(this).find('.toolbox-button-rotate_right').click();
               }
            });
         } else if(val === "remove"){
            if(confirm('Opravdu smazat?')){
               $('li', that.$imagesList).each(function(){
                  var $li = $(this);
                  if($li.has('.image-checkbox:checked').length){
                     $.ajax({
                        url: that.options.imageDeleteUrl,
                        type: "POST",
                        cache: false,
                        data: {id : $li.data('id')},
                        success : function(data){
                           $li.fadeOut(100, function(){
                              $li.remove();
                           });
                        }
                     });   
                  }
               });
            }
         } else if(val === "changeState"){
            $('li', that.$imagesList).each(function(){
               var $li = $(this);
               if($li.has('.image-checkbox:checked').length){
                  $.ajax({
                     url: that.options.imageChangeStateUrl,
                     type: "POST",
                     cache: false,
                     data: {id : $li.data('id')},
                     success : function(data){
                       data.newState == true ? $li.removeClass('inactive') : $li.addClass('inactive');
                     }
                  });   
               }
            });
         }
         that.$imagesActions.find('#images-action-select').val(that.$imagesActions.find('#images-action-select option:first').val());
      });
      // mazani
      this.$imagesList.on('click', '.toolbox-button-delete', function(){
         if(confirm('Opravdu smazat?')){
            var $li = $(this).closest('li');
            $.ajax({
               url: that.options.imageDeleteUrl,
               type: "POST",
               cache: false,
               data: {id : $li.data('id')},
               success : function(data){
                  $li.fadeOut(100, function(){
                     $li.remove();
                  });
               }
            });
         }
      });
      // změna stavu
      this.$imagesList.on('click', '.toolbox-button-changeState', function(){
         var $li = $(this).closest('li');
         $.ajax({
            url: that.options.imageChangeStateUrl,
            type: "POST",
            cache: false,
            data: {id : $li.data('id')},
            success : function(data){
               data.newState == true ? $li.removeClass('inactive') : $li.addClass('inactive');
            }
         });
      });
      // řazení
      this.$imagesList.sortable({
         placeholder: "ui-state-highlight",
         handle : '.image-thumb > img',
         update : function( event, ui){
            var $li = $(this);
            $.ajax({
               url: that.options.imageMoveUrl,
               type: "POST",
               cache: false,
               data: {id : ui.item.data('id'), pos : ui.item.index()+1},
               success : function(data){
                  if(data.errmsg.length !== 0){
                     that.$imagesList.sortable( "cancel" );
                     alert('Chyba při přesunu: '+data.errmsg.join(";"));
                  }
               }
            });
         }
      });
      // editace
      this.$dialogLabels.dialog({
         autoOpen : false,
         minWidth : 650,
         title : 'Úprava popisků obrázku',
         resizable : false,
         close : function(){
            that.$imagesList.find('li').removeClass('active-edit');
         },
         open : function(){
            $(this).find('input.imglabels_name_class').first().select();
         }
      });
      this.$imagesList.on('click', '.toolbox-button-edit_texts', function(){
         var $li = $(this).closest('li').addClass('active-edit');
         $.each($li.data('name'), function(index, value){
            that.$dialogLabels.find('input.imglabels_name_class[lang="'+index+'"]').val(value);
         });
         $.each($li.data('desc'), function(index, value){
            that.$dialogLabels.find('textarea.imglabels_text_class[lang="'+index+'"]').val(value);
         });
         that.$dialogLabels.find('input.imglabels_id_class').val($li.data('id'));
         that.$dialogLabels.dialog( "open" );
      });
      this.$dialogLabels.on('submit', 'form', function(e){
         e.preventDefault();
         // ajax to save
         $.ajax({
            url: that.options.editLabelsUrl,
            type: "POST",
            cache: false,
            data: $(this).serialize(),
            success : function(data){
               // update current data
               $('#image-'+data.image.id, that.$imagesList)
                  .data('name', data.image.name)
                  .data('desc', data.image.desc)
                  .find('.image-title').text(data.image.name[CubeCMS.lang]);
            }
         });
         return false;
      });
      this.$dialogLabels.on('click', 'input[name="saveandclose"]', function(e){
         that.$dialogLabels.dialog( "close" );
      });
      this.$dialogLabels.on('click', 'input[name="saveandnext"]', function(e){
         e.preventDefault();
         var $next = that.$imagesList.find('.active-edit').next();
         $(this).closest('form').submit();
         that.$dialogLabels.dialog( "close" );
         if($next.length > 0){
            $next.find('.toolbox-button-edit_texts').click();
         }
         return false;
      });
      this.$dialogLabels.on('click', 'input[name="close"]', function(e){
         that.$dialogLabels.dialog( "close" );
      });
      // enter to next image
      this.$dialogLabels.on('keypress', 'input.imglabels_name_class', function(e){
         if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
            $('input[name="saveandnext"]').click();
            return false;
         } else {
            return true;
         }
      });
      this.$imagesList.on('click', '.toolbox-button-rotate_left', function(){
         var $li = $(this).closest('li');
         $.ajax({
            url: that.options.imageRotateUrl,
            type: "POST",
            cache: false,
            data: {id : $li.data('id'), to : 'left'},
            success : function(data){
               // update image
               that._refreshImage($li, data.url, data.urlThumb);
            }
         });
      });
      this.$imagesList.on('click', '.toolbox-button-rotate_right', function(){
         var $li = $(this).closest('li');
         $.ajax({
            url: that.options.imageRotateUrl,
            type: "POST",
            cache: false,
            data: {id : $li.data('id'), to : 'right'},
            success : function(data){
               // update image
               that._refreshImage($li, data.url, data.urlThumb);
            }
         });
      });
      
      $('.drop-area')
      .on('dragenter', function(e) {
         e.stopPropagation();
         e.preventDefault();
      })
      .on('dragover',function(e) {
         e.stopPropagation();
         e.preventDefault();
         $(this).addClass('draging');
      })
      .on('dragleave',function(e) {
         e.stopPropagation();
         $(this).removeClass('draging');
      })
      .on('drop', function(e){
         e.stopPropagation();
         e.preventDefault();
         $(this).removeClass('draging');
         that.handleFileUpload(e.originalEvent.dataTransfer.files);
      });
      
      if(window.FileReader) {
         // html5 upload
         $('#images-upload-form').on('submit', function(e){
            e.preventDefault();
            that.handleFileUpload($(this).find('#addimage_image_1')[0].files);
            return false;
         });
      } else {
         $('.drop-area-label').hide();
         $('#images-upload-form').prop('target', 'upload-target');
         // standart upload
//         $('#upload_file_1').on('change', function(){
//            $(this).closest('form').submit();
//         });
         // upload kompletní
         $('#upload_target').load(function(){
            if(that.iframeinicialized){
               var error = $('#upload_target').get(0).contentWindow.error;
               var info = $('#upload_target').get(0).contentWindow.info;
//               $.each(error, function(index, cnt){
//               });
//               $.each(info, function(index, cnt){
//               });
               that.updateImages();
            } else {
               that.iframeinicialized = true;
            }
         });
      }
   },
   
   _refreshImage : function($item, url, urlThumb)
   {
      $item.find('.image-thumb img').prop('src', urlThumb);
      $item.data('src', url);
   },
   
   _createImage : function(imageData)
   {
      var $newItem = $('#image-tpl > li').clone();
      $newItem.prop('id', 'image-'+imageData.id);
      $newItem.data('id', imageData.id);
      $newItem.data('name', imageData.name);
      $newItem.data('desc', imageData.desc);
      $newItem.data('src', imageData.url);
      $newItem.find('.image-thumb > img').prop('src', imageData.urlSmall);
      $newItem.find('.image-title').text(imageData.name[CubeCMS.lang]).prop('title', imageData.name[CubeCMS.lang]);
      $newItem.hide();
      this.$imagesList.append($newItem);
      $newItem.fadeIn('300');
   },
   
   _createUploadItem : function(file)
   {
      var $item = this.uploadItemTpl.clone();
      $item.find('.name').text(file.name+ ' ('+CubeCMS.Tools.formatFileSize(file.size)+')');
      
      if(window.FileReader) {
         var reader = new FileReader();
         reader.readAsDataURL(file);
         reader.onload = function(e) {
            $('.thumbnail img', $item).attr('src',e.target.result);
         };
      } else {
         $item.remove('.col-left');
      }
      return $item;
   },
   
   handleFileUpload: function(files) 
   {
      // create form
      var that = this;
      $.each(files, function(index, file){
         var fd = new FormData();
         fd.append('upload_iframe', 0);
         fd.append('_addimage__check', 1);
         fd.append('addimage_idItem', that.$uplaodForm.find('input[name="addimage_idItem"]').val());
         fd.append('addimage_image[]', file);
         fd.append('addimage_send', 'send');
         that._sendFileToServer(fd, file);
      });
      
   },
   
   _sendFileToServer: function(formData, file)
   {
      var that = this;
      
      // create row
      var $item = this._createUploadItem(file);
      this.$queueList.append($item);
      var uploadXHR = $.ajax({
         xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
               xhrobj.upload.addEventListener('progress', function(event) {
                  var percent = 0;
                  if (event.lengthComputable) {
                     percent = Math.ceil((event.loaded || event.position) / event.total * 100);
                  }
                  //Set progress
                  that._updateItemProgress($item, percent);
               }, false);
            }
            return xhrobj;
         },
         url: that.options.uploadImageUrl,
         type: "POST",
         contentType: false,
         processData: false,
         cache: false,
         data: formData,
         success: function(data) {
            // remove item
            $.each(data.errmsg, function(index, cnt){
            });
            $.each(data.infomsg, function(index, cnt){
            });
            // je hláška ok
            if(data.allOk){
               setTimeout(function(){
                  $item.remove();
               }, 500);
            }
            
            if(data.images){
               $.each(data.images, function(index, image){
                  that._createImage(image);
               });
            }
         }
      });
      $item.on('click', '.button-cancel-upload',function(e){
         e.preventDefault();
         uploadXHR.abort();
         $item.fadeOut(300, function(){
            $(this).remove();
         });
         return false;
      });
   },
   
   _updateItemProgress : function($li, progress)
   {
      $li.find('.progress-bar').text(progress+"%").css('width', progress+'%');
   }
};