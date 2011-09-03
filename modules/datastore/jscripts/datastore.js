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
      $items.each(function(){$form.append($input.clone().val($(this).val()));});
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
   $('input.item-select').change(function(){
      if($(this).is(':checked')){
         $(this).parents('tr').addClass('ui-state-highlight');
      } else {
         $(this).parents('tr').removeClass('ui-state-highlight');
      }
   });

   // označení všech
   $('#items-check-all').change(function(){
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
   
   // přejmenování
   $('form.formRenameItem').submit(function(){
      var newName = prompt(enterNameMsg, $('input[name='+formRenameItem_oldname+']', this).val());
      if(!newName || newName == "") return false;
      $('input[name='+formRenameItem_newname+']', this).val(newName);
      return true;
   });
   
   $('input[name=open-move-items]').click(function(){
      $('.action-details').hide();
      $('#move-items').show();
   });
   
   $('form#move-items').submit(function(){
      var $form = $(this);
      var $items = $('#datastorage-structure input.item-select:checked');
      if($items.length == 0) return false;
      var $input = $('<input />').attr({name : formMoveItems_items+'[]',type : 'hidden',value : ''})
      .addClass('move_items_items_class');

      $(this).find('input.move_items_items_class').remove();
      $items.each(function(){
         $form.append($input.clone().val($(this).val()));
      });
      return true;
   });
}

// vytvořeni uploaderu
function createUploader(){
   var uploader = new qq.FileUploader({
      element: document.getElementById('file-uploader'),
      action: uploadLink,
      debug: true,
      params : {sessionid : sessionId},
      listElement : document.getElementById('upload-list'),
      dropElement : document.getElementById('drop-file-container'),
      texts : uploadMsgs,
      onSubmit : function(id, fileName){
         $('.action-details').hide();
         $('#upload-queue').show();
      },
      onComplete : function(id, fileName, responseJSON){
         document.getElementById("upload-list").scrollTop = document.getElementById("upload-list").scrollHeight;
      },
      onQueueComplete : function(result){
         vveShowMessages(result);
         setTimeout("window.location.reload()", 500);
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
   document.getElementById('upload-queue').style.display = "none";
}
//filter results based on query
function filter(selector, query) {
   query =   $.trim(query); //trim white space
   query = query.replace(/ /gi, '|'); //add OR for regex query
   $(selector).each(function() {
      ($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).addClass('hidden-item') : $(this).removeClass('hidden-item');
   });
}

