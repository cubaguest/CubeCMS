// init celého modulu
function initDataStore(){
   // označení položek po reloadu je potřeba???
   $('input.item-select').each(function(){
      if($(this).is(':checked')){
         $(this).parents('tr').addClass('ui-state-highlight');
      } else {
         $(this).parents('tr').removeClass('ui-state-highlight');
      }
   });
   
   // hide details
   $('.action-details').hide();

   // vytvoření adresáře
   $('form#create-directory').submit(function(){
      var name = prompt(newDirNameMsg, '');
      if(!name || name == "") return false;
      $('form#create-directory input[name=create_dir_name]').val(name);// assign to input
      sendForm(this);
      return false;
   });
   // mazání označených položek
   $('form#delete-items').submit(function(){
      var $form = $(this);
      var $items = $('#datastorage-structure input.item-select:checked');
      if($items.length == 0) return false;
      if(!confirm(deleteItemsMsg)) return false;
      var $input = $('<input />').attr({
         name : formDeleteItems_items+'[]',
         type : 'hidden',
         value : ''
      }).addClass('delete_items_items_class');

      $(this).find('input.delete_items_items_class').remove();
      $items.each(function(){
         $form.append($input.clone().val($(this).val()));
      });
      return true;
   });

   // init eventů pro úložiště
   // filtrace - input #items-filter
   $('#items-filter').keyup(function(event) {
      //if esc is pressed or nothing is entered
      if (event.keyCode == 27 || $(this).val() == '') {
         $(this).val('');
         $('#datastorage-structure tbody tr').removeClass('hidden-item');
      }
      else {
         filter('#datastorage-structure tbody tr', $(this).val());
      }
   });

   // event pro označení
   $('input.item-select').live('change', function(){
      if($(this).is(':checked')){
         $(this).parents('tr').addClass('ui-state-highlight');
      } else {
         $(this).parents('tr').removeClass('ui-state-highlight');
      }
   });

   // označení všech
   $('#items-check-all').live('change', function(){
      var checkAll = false;
      if($(this).is(':checked')) {
         checkAll = true;
      }
      $('#datastorage-structure input.item-select').each(function(){
         if(checkAll){
            $(this).attr('checked', true);
            $(this).change();
         } else {
            $(this).attr('checked', false);
            $(this).change();
         }
      });
   });
   
   // rename dialog
   $( "#rename-form" ).dialog({
      autoOpen: false, modal: true,
      buttons: {
         Ok: function() {
            // validace prázdného názvu
            if(checkNotEmpty($('#rename_item_newname_1'))){
               $("#rename-form #rename_item_submit_1").click();
            }
         },
         'Zrušit': function() {
            $(this).dialog("close");
         }
      }
   });
   $('#item-rename').live('click',function(){
      $("#rename-form #rename_item_submit_1").hide();
      $("#rename-form #rename_item_oldname_1").val($(this).parent('td').find('#form-file-name').val());
      $("#rename-form #rename_item_newname_1").val($(this).parent('td').find('#form-file-name').val());
      $("#rename-form").dialog("open");
      return false;
   });
   
   // move dialog
   $( "#move-form" ).dialog({
      autoOpen: false, modal: true,
      buttons: {
         Ok: function() {
            var $form = $(this).find('form');
            var $items = $('#datastorage-structure input.item-select:checked');
            if($items.length == 0) return false;

            var $input = $('input[name="move_items_items[]"]').clone();
            $('input[name="move_items_items[]"]').remove();
            $items.each(function(i){
               $form.append($input.clone().attr('id', 'file_'+i).val($(this).val()));
            });
            $("#move-form #move_items_submit_1").click();
         },
         'Zrušit': function() {
            $(this).dialog("close");
         }
      }
   });
   $('input[name=open-move-items]').click(function(){
      if($('#datastorage-structure input.item-select:checked').length == 0){ return; }
      $("#move-form #move_items_submit_1").hide();
      $("#move-form").dialog("open");
   });
   
   // new dir dialog
   $( "#createdir-form" ).dialog({
      autoOpen: false, modal: true,
      buttons: {
         Ok: function() {
            // validace prázdného názvu
            if(checkNotEmpty($('#create_dir_name_1'))){
               $("#createdir-form #create_dir_submit_1").click();
            }
         },
         'Zrušit': function() {
            $(this).dialog("close");
         }
      }
   });
   $('#create-dir-button').click(function(){
      $("#createdir-form #create_dir_submit_1").hide();
      $("#createdir-form").dialog("open");
   });
}

// vytvořeni uploaderu
function createUploader(){
   var settings = {
				flash_url : "/jscripts/swfupload/swfupload.swf",
				flash9_url : "/jscripts/swfupload/swfupload_fp9.swf",
				upload_url: uploadUrl,
				post_params: {"upload_send" : "send", 'sessionid' : sessionId},
				file_size_limit : 0,
            file_types : "*",
            file_types_description : 'files',
				file_upload_limit : 100,
				file_queue_limit : 0,
            file_post_name : 'upload_file',
//				debug: true,
				// Button settings
				button_width: "60",
				button_height: "22",
				button_placeholder_id: 'swfUButtonPlaceholder',
//            button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND
			};
   
   $('.swfupload-control').swfupload(settings);

   // assign our event handlers
   $('.swfupload-control')
   .bind('fileQueued', function(event, file){
      $('#upload-queue').show();
      
      var listitem='<li id="'+file.id+'" >'+
      '<em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) '+
      '<span class="progressvalue" ></span> <span class="status" >Čekám</span> <a href="#cancel" class="cancel">Zrušit</a>'+
      '<p class="progressbar" ><span class="progress" ></span></p>'+'</li>';
      $('#upload-list').append(listitem);
      $('li#'+file.id+' .cancel').bind('click', function(){ //Remove from queue on cancel click
         var swfu = $.swfupload.getInstance('.swfupload-control');
         swfu.cancelUpload(file.id);
         $('li#'+file.id).slideUp('fast');
         return false;
      });
      // start the upload once a file is queued
      $(this).swfupload('startUpload');
   })
   .bind('uploadComplete', function(event, file){
         $(this).swfupload('startUpload');
   })
   .bind('fileQueueError', function(event, file, errorCode, message){
      alert('Velikost souboru '+file.name+' je větší než povolený limit');
   })
   .bind('uploadError', function(event, file, error, message){
      $('#upload-list li#'+file.id).find('span.status').text('Odeslání se nazdařilo');
      $('#upload-list li#'+file.id).find('span.cancel').remove();
   })
   .bind('uploadStart', function(event, file){ // kvůli flash9
      $('#upload-list li#'+file.id).find('span.status').text('Odesílám...');
      $('#upload-list li#'+file.id).find('span.progressvalue').text('0%');
//      $('#upload-list li#'+file.id).find('span.cancel').hide();
   })
   .bind('uploadProgress', function(event, file, bytesLoaded){//Show Progress
      var percentage=Math.round((bytesLoaded/file.size)*100);
      $('#upload-list li#'+file.id).find('.progress').css('width', percentage+'%');
      $('#upload-list li#'+file.id).find('.progressvalue').text(percentage+'% ');
   })
   .bind('uploadSuccess', function(event, file, serverData){
      $('#upload-list li#'+file.id).animate({opacity:0}, 300, function(){
            $(this).remove();if($('#upload-list li').length == 0){$('#upload-queue').hide();}
         });
      var stat = $.swfupload.getInstance('.swfupload-control').getStats();
      if(stat.files_queued == 0){
         
         $('#datastorage-structure').load(location.href+" #datastorage-structure>*","",function(){
//            initDataStore();
         });
      }
   });
}
// don't wait for the window to load
window.onload = createUploader;

// function for remove items from queue
function clearUploadQueue(){
   // remove items from queue
   while (document.getElementById('upload-list').hasChildNodes()) {
      document.getElementById('upload-list').removeChild(document.getElementById('upload-list').firstChild);
   }
   $('#upload-queue').hide();
//   document.getElementById('upload-queue').style.display = "none";
}
//filter results based on query
function filter(selector, query) {
   query =   $.trim(query); //trim white space
   query = query.replace(/ /gi, '|'); //add OR for regex query
   $(selector).each(function() {
      ($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).addClass('hidden-item') : $(this).removeClass('hidden-item');
   });
}
// check empty value in object
function checkNotEmpty($o) {
      if ( $o.val() == null || $o.val() == "" ) {
         $o.addClass("ui-state-error");
         $(".validateTips").text('Položky nebyla vyplněna').addClass("ui-state-highlight");
         setTimeout(function() {
            $(".validateTips").removeClass("ui-state-highlight", 1500);
         }, 500 );
         return false;
      } else {
         return true;
      }
   }