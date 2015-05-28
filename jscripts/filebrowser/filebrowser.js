(function($){
    $.fn.disableSelection = function() {
        return this
                 .attr('unselectable', 'on')
                 .css('user-select', 'none')
                 .on('selectstart', false);
    };
})(jQuery);

Storage.prototype.setObj = function(key, obj) {
   return this.setItem(key, JSON.stringify(obj));
};
Storage.prototype.getObj = function(key) {
   return JSON.parse(this.getItem(key));
};

function openInNewTab(url) {
  var win = window.open(url, '_blank');
  win.focus();
}

var CubeBrowser = {
   params : {
      // základní adresy
      uploadLink: null,
      baseUrl: null,
      sessionId: null,
      listType: "images",
      imageBigSizeW: 1024,
      imageBigSizeH: 768,
      imageSmallSizeW: 200,
      imageSmallSizeH: 200,
      cookieName: 'Cube_Cms_brpath',
      baseListPath: '/home/',
      openDir: null,
      translations : {}
   },
   listType : null,
   currentPath : '/public/',
   currentAccess : {read : true, write : false},
   win : null,
   category : 0,

   init : function(params){
      $.extend(this.params, params);
      this.listType = tinyMCEPopup.getWindowArg("listType");
      this.win = tinyMCEPopup.getWindowArg("window");
      this.category = tinyMCEPopup.getWindowArg("cat");
      var path = tinyMCEPopup.getWindowArg("url");
      
      if(path !== ""){
         var regex = /data(\/.*\/)[a-z0-9._-]+/i;
         var matches = path.match(regex);
         if(matches){
            this.params.baseListPath = matches[1];
         }
      } else {
         var lastPath = localStorage.getItem('lastpath');
         if(lastPath !== null){
            this.params.baseListPath = lastPath;
         }
      }
      
      CubeBrowserPathWidget.init();
      CubeBrowserPreviewWidget.init();
      
      CubeBrowserListWidget.init(this.params.baseListPath);
      CubeBrowserTipsWidget.init();
      CubeBrowserToolboxWidget.init();
      CubeBrowserFileActionsWidget.init();
      CubeBrowserUploader.init(this.params);
      CubeBrowserLogsWidget.init();
      CubeBrowserProgressBarWidget.init();
      CubeBrowserFilterWidget.init();
      CubeBrowserClipBoardWidget.init();
      
      
      
   },
   submitFile : function (filepath) {
      // insert information now
      this.win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = filepath;
      // are we an image browser
      if (typeof(this.win.ImageDialog) !== "undefined") {
         // we are, so update image dimensions...
         if (this.win.ImageDialog.getImageData) {
            this.win.ImageDialog.getImageData();
         }
         // ... and preview if necessary
         if (this.win.ImageDialog.showPreviewImage) {
            this.win.ImageDialog.showPreviewImage(CubeBrowserListWidget.getSelectedItems().first().data('realpath'));
         }
      }
      // close popup window
      tinyMCEPopup.close();
   },
   getCurrentPath : function(){
      return this.currentPath;
   },
   setCurrentPath : function(path){
      this.currentPath = path;
      localStorage.setItem('lastpath', path);
   },
   getCurrentAccess : function(){
      return this.currentAccess;
   },
   setCurrentAccess : function(access){
      this.currentAccess = access;
   },
   
   request : function(action, postValues, sucessfunc, errfunc){
      $.ajax({
         type : 'POST', data : postValues, url : window.location.toString().replace('browser.php', action+'.php'),
         cache : false,
//         async : false,
         success: function(data){
            if($.isFunction(sucessfunc)) {
               sucessfunc.call(data);
            }
            if(data.infomsg.length > 0){
               $.each(data.infomsg, function(index, msg){
                  CubeBrowserLogsWidget.add(msg, 'info');
               });
            }
            if(data.errmsg.length > 0){
               $.each(data.errmsg, function(index, msg){
                  CubeBrowserLogsWidget.add(msg, 'err');
               });
            }
         },
         error: function(){
//            FileBrowser.showResult([], ['Chyba při komunikaci se serverem. Zkuste znovu.']);
         },
         complete : function(){}
      });
   },
   
   submitSelectedFile : function(){
      var $items = CubeBrowserListWidget.getSelectedItems();
      $items.each(function(){
         if($(this).data('itemclass') !== "dir" && $(this).data('itemclass') !== "dot") {
            CubeBrowser.submitFile($(this).data('realpath'));
            return;
         } 
      });
   },
   
   loadDirectories : function()
   {
      var $select = $('.directory-select');
      $.ajax({
         type : 'GET', url : window.location.toString().replace('browser.php', 'getdirs.php'),
         cache : false,
         async : false,
         success: function(data){
            $select.html("");
            var $grp2 = $('<optgroup></optgroup>').prop('label', 'Veřejné adresáře');
            $.each(data.dirsPublic, function(index, value){
               $grp2.append($('<option></option>').prop('value', value).text(value));
            });
            $select.append($grp2);
            
            var $grp1 = $('<optgroup></optgroup>').prop('label', 'Adresáře v domovské složce');
            $.each(data.dirsHome, function(index, value){
               $grp1.append($('<option></option>').prop('value', value).text(value));
            });
            $select.append($grp1);
         }
      });
   },
   
   openMoveDialog : function()
   {
      $('#dialog-move').fadeIn(300);
   },
   openRenameDialog : function(button)
   {
      if(typeof (button) === "undefined"){
         button = CubeBrowserListWidget.getSelectedItems().first();
      }
      
      var $item = $(button).closest('li');
      $('#dialog-rename').fadeIn(300).find('input[type="text"]').val($item.data('name'));
   },
   openCopyDialog : function()
   {
      $('#dialog-copy').fadeIn(300);
   },
   openNewDirDialog : function()
   {
      $('#dialog-new-dir').fadeIn(300);
   },
   openDeleteDialog : function(button)
   {
      var names = new Array();
      if(typeof (button) === 'undefined'){
         CubeBrowserListWidget.getSelectedItems().each(function(index){
            names.push($(this).data('name'));
         });
      } else {
         var $item = $(button).closest('li');
         names.push($item.data('name'));
      }
      $('#dialog-delete').find('.files-list').text(names.join(', '));
      $('#dialog-delete').fadeIn(300);
   },
   closeDialog : function(obj)
   {
      $(obj).closest('.dialog').fadeOut(300);
   },
   
   
   // METODY pro zpracování
   renameItem : function(newname){
      var that = this;
      var $item = CubeBrowserListWidget.getSelectedItems().first();
      this.request('rename', 
      { item : $item.data('realpath'), name : newname },
      function(){
         CubeBrowserListWidget.refreshPath();
      });
   },
   renameItems : function(newname){
      var $items = CubeBrowserListWidget.getSelectedItems().filter('.item-image');
      var queue = $items.length;
      var that = this;
      var $box = $('#image-flip');
      if(queue === 0){
         CubeBrowserLogsWidget.add('Nebyl vybrán žádný soubor', 'err');
         return;
      }
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('rename', {
            item : $(this).data('realpath'),
            name : newname
         }, function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
            }
         });
      });
   },
   deleteItem : function($item){
      
   },
   moveSelectedItems : function(target){
      var $items = CubeBrowserListWidget.getSelectedItems();
      var queue = $items.length;
     
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('move', 
            { item : $(this).data('realpath'), target : target },
         function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
            }
         });
      });
   },
   copySelectedItems : function(target){
      var $items = CubeBrowserListWidget.getSelectedItems();
      var queue = $items.length;
     
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('copy', 
            { item : $(this).data('realpath'), target : target },
         function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
            }
         });
      });
   },
   deleteSelectedItems: function(){
      var $items = CubeBrowserListWidget.getSelectedItems();
      var queue = $items.length;
      var that = this;

      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         that.request('delete', {
            item : $(this).data('realpath')
         }, function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
                CubeBrowserProgressBarWidget.setProgress(100);
                CubeBrowserListWidget.refreshPath();
            }
         });
      });
   },
   createDir: function(name){
      var that = this;
      this.request('createDir', 
      { item : CubeBrowser.getCurrentPath(), name : name },
      function(){
         CubeBrowserListWidget.refreshPath();
         $('#form-new-dir input[name="newname"]').val("");
         that.loadDirectories();
      });
   }
};

var CubeBrowserListWidget = {
   $browser : null,
   $area : null,
   path : null,
   lastSelecteRow : false,
   previewTimer : false,
   currentFolder : false,
   request : null,
   
   init : function(path)
   {
      this.$browser = $('#browser');
      this.$area = this.$browser.find('.list');
      
      // detekce cookie s cestou
      this.path = path;

      // načtení
      this.loadDefaultPath();
      
      var that = this;
      // inicializace eventů
      // označení
      $(this.$area).on('click','li,.name a',function(event){
         var $row = $(this);
         if($row.is('a')){
            $row = $(this).closest('li');
         }
         // neoznačovat tečky pro přechod
         if($row.data('type') === 'dot'){
            return false;
         }
         if(!event.ctrlKey && !event.shiftKey){
            that.unSelectAllItems();
         }
         if(event.shiftKey && that.lastSelecteRow){
            var $first = that.lastSelecteRow;
            var fIndex = $first.index() < $row.index() ? $first.index() : $row.index();
            var tIndex = $first.index() < $row.index() ? $row.index() : $first.index();
            
            that.getAllItems().slice(fIndex, tIndex).each(function(){
               that.selectItem($(this));
            });
         }
         if(event.ctrlKey && that.lastSelecteRow && that.isSelected($row)){
            that.unSelectItem($row);
         } else {
            that.selectItem($row);
         }
         
         return false;
      });
      // checkbox
      $(this.$area).on('click','li .selector input',function(event){
         event.stopPropagation();
         if($(this).is(":checked")){
            that.selectItem($(this).closest('li'));
         } else {
            that.unSelectItem($(this).closest('li'));
         }
         return;
      });
      $(this.$area).on('click','li .selector label',function(event){
         if($(this).prev('input').is(":checked")){
            that.selectItem($(this).closest('li'));
         } else {
            that.unSelectItem($(this).closest('li'));
         }
         event.stopPropagation();
      });
      // dvojklik
      $(this.$area).on('dblclick','li,.name a',function(){
         var $row = $(this);
         if($row.is('a')){
            $row = $(this).closest('li');
         }
         // přejdi do složky
         if($row.data('type') === 'dot' || $row.data('type') === 'dir'){
            that.loadPath($row.data('target'));
         } 
         // vyber soubor
         else {
            CubeBrowser.submitFile($row.data('realpath'));
         }
         return false;
      });
      
      // preview
      $(this.$area).on('mouseenter','.name a',function() {
         var $row = $(this).closest('li');
         if(that.previewTimer) {
            clearTimeout(that.previewTimer);
            that.previewTimer = null;
         }
         that.previewTimer = setTimeout(function() {
            that.showImagePreview($row);
         }, 500);
      });
      $(this.$area).on('mouseleave','.name a',function() {
         var $row = $(this).closest('li');
         clearTimeout(that.previewTimer);
         that.previewTimer = null;
         that.hideImagePreview($row);
      });
      
      // drop 
      // init drag & drop to area
//      var $dropArea = $('#drop-area');
//      $('#fb-wrap')
//      .on('dragenter, dragover', function(){
//         $dropArea.addClass('visible');
//      })
//      .on('dragleave', function(){
//         $dropArea.removeClass('visible');
//      });
      // drag drop
      
      // items
      $(this.$area)
      .on('dragenter', 'li', function(e) {
         e.stopPropagation();
//         e.preventDefault();
          $(this).addClass('draging');
         console.log('enter');
      })
      .on('dragover','li', function(e) {
         e.stopPropagation();
//         e.preventDefault();
         $(this).addClass('draging');
      })
      .on('dragleave','li', function(e) {
         e.stopPropagation();
         $(this).removeClass('draging');
         console.log('leave');
      });
      
      
      $(this.$area).parent()
      .on('dragenter', 'ul,#drop-area', function(e) {
         e.stopPropagation();
         e.preventDefault();
//          $(this).addClass('draging');
//         console.log('enter');
      })
      .on('dragover','ul,#drop-area', function(e) {
         e.stopPropagation();
         e.preventDefault();
//         if($(this).is('li')){
//            var data = $(this).data();
//            if(data.itemclass === 'dir' && data.access.write === false){
//               $(this).addClass('draging');
//            }
//         }
         $(this).addClass('draging');
//         console.log('over');
      })
      .on('dragleave','ul,#drop-area', function(e) {
         e.stopPropagation();
         $(this).removeClass('draging');
//         console.log('leave');
      })
      .on('drop', 'ul,#drop-area', function(e){
         e.stopPropagation();
         e.preventDefault();
         $(this).removeClass('draging');
         var files = e.originalEvent.dataTransfer.files;
         CubeBrowserUploader.handleFileUpload(files);
      })
      .on('drop', 'li', function(e){
         e.stopPropagation();
         e.preventDefault();
         $(this).removeClass('draging');
         var files = e.originalEvent.dataTransfer.files;
         var itemData = $(this).data();
         if(itemData.itemclass === 'dir' || itemData.itemclass === 'dot'){
            if(itemData.access.write === true){
               CubeBrowserUploader.handleFileUpload(files, itemData.target);
            } else {
               tinyMCEPopup.alert('Do tohoto adresáře není možné ukládat');
            }
         } else {
            CubeBrowserUploader.handleFileUpload(files);
         }
      });
      
      $(this.$area).on('click', 'a.button-open-external', function(e){
         e.stopPropagation();
         openInNewTab(this.href);
         return false;
      });
      
   },
   selectItem : function($item)
   {
      // select row
      $item.addClass('active').find('input[type="checkbox"]').prop('checked', true);
      this.lastSelecteRow = $item;
      // if image, update image preview
      CubeBrowserPreviewWidget.hidePreview();
      if($item.data('itemclass') === 'image'){
         CubeBrowserPreviewWidget.showPreview($item);
      }
      this.updateSubmitButton();
      console.log('item selected');
      // update file actions
      CubeBrowserFileActionsWidget.updateActions();
      CubeBrowserToolboxWidget.updateButtons();
   },
   unSelectItem : function($item)
   {
      $item.removeClass('active').find('input[type="checkbox"]').prop('checked', false);
      // update file actions
      CubeBrowserFileActionsWidget.updateActions();
      CubeBrowserToolboxWidget.updateButtons();
//      this.updateSubmitButton();
//      CubeBrowserPreviewWidget.hidePreview();
   },
   unSelectAllItems : function()
   {
      this.getSelectedItems().each(function(){
         CubeBrowserListWidget.unSelectItem($(this));
      });
   },
   isSelected : function($item)
   {
      return $item.hasClass('active');
   },
   /**
    * načte požadovanou cestu a překreslí widget
    * @param {type} path
    * @returns {undefined}
    */
   loadPath : function(path, force, restoreSelection)
   {
      if(typeof (force) === 'undefined'){ force = false; }
      if(typeof (restoreSelection) === 'undefined'){ restoreSelection = false; }
      
      if(path === this.path && this.$area.find('li').length > 0 && force === false){
         return;
      }

      var selectedItems = [];
      if(restoreSelection){
         this.getSelectedItems().each(function(){
            selectedItems.push($(this).data('realpath'));
         });
      }
     
      var that = this;
      this.clearList();
      CubeBrowserPathWidget.setPath(path);
      this.request = $.ajax({
         type : 'GET', data : { path : path, type: CubeBrowser.listType }, url : window.location.toString().replace('browser.php', 'getitems.php'),
         cache : false,
         async : false,
         beforeSend: function(xhr){    
            if(that.request !== null){
               that.request.abort();
            }
            that.request = xhr;
         },
         success: function(data){
            if(data.items){
               $.each(data.items, function(index, item){
                  that.createRow(item);
               });
               that.path = data.current;
               that.currentFolder = data;
               CubeBrowser.setCurrentPath(data.current);
               CubeBrowser.setCurrentAccess(data.access);
               
               if(selectedItems.length > 0){
                  $('li', that.$area).each(function(){
                     if($.inArray($(this).data('realpath'), selectedItems) !== -1){
                        that.selectItem($(this));
                     }
                  });
               }
            } else {
               alert(data.errmsg.join(', '));
            }
            CubeBrowserToolboxWidget.updateButtons();
            CubeBrowser.loadDirectories();
         },
         error: function(){
//            FileBrowser.showResult([], ['Chyba při komunikaci se serverem. Zkuste znovu.']);
         }
      });
      this.updateSubmitButton();
   },
   getFolderUp : function()
   {
      var path = null;
      if(this.currentFolder && this.currentFolder.current === "/"){
         path = this.currentFolder.current+"/";
      } else {
         path = this.$area.find('li').first().data('target');
      }
      this.loadPath(path);
   },
   /**
    * znovu načte požadovanou cestu a překreslí widget
    * @returns {undefined}
    */
   refreshPath : function()
   {
      this.loadPath(this.path, true, true);
   },
   /**
    * Načte výchozí cestu buď z cookie nebo z nasatvení
    * @returns {undefined}
    */
   loadDefaultPath : function()
   {
      this.loadPath(this.path);
   },
   
   // Private
   createRow : function (itemData)
   {
      var $newRow = $('#item-tpl li').first().clone();
      var randID = Math.round(Math.random()*10000000);
      
      $newRow.prop('id', 'item-'+randID);
      $newRow.find('.filename').text(itemData.name);
//      var sizeStr = itemData.type === "file" ? itemData.info.sizeFormated : "";
      var sizeStr = itemData.info.sizeFormated;
      if(itemData.info.dimension.w !== 0){
         sizeStr += ' ('+itemData.info.dimension.w+"x"+itemData.info.dimension.h+"px)";
      }
      $newRow.find('.size').text(sizeStr);
      if(itemData.access.write === false){
         $newRow.addClass('readonly');
      }
      
      $newRow.find('input[type="checkbox"]').prop('id', 'checkbox-'+randID);
      $newRow.find('input[type="checkbox"]').next('label').prop('for', 'checkbox-'+randID);
      
      $newRow.data(itemData);
      $newRow.disableSelection();
      // tlačítka akcí
      if(itemData.type === "dot" || itemData.access.write === false){
         $newRow.find('.actions').remove();
      }
      
      // přesun (dot)
      if(itemData.type === "dot"){
         $newRow.find('.selector input').remove();
         $newRow.find('.selector label').remove();
         $newRow.find('a.button-open-external').remove();
         $newRow.find('.preview').remove();
      } 
      // adresář
      else if(itemData.type === "dir"){
         if(itemData.itemclass === 'home'){
            $newRow.find('.name .icon').addClass('icon-home');
         } else if(itemData.itemclass === 'public'){
            $newRow.find('.name .icon').addClass('icon-globe');
         } else {
            $newRow.find('.name .icon').addClass('icon-folder-o');
         }
         $newRow.addClass('item-dir');
         $newRow.find('.preview').remove();
         $newRow.find('a.button-open-external').remove();
      } 
      // soubory
      else if(itemData.type === "file"){
         if(itemData.itemclass === "image"){
            $newRow.addClass('item-image');
            $newRow.find('.name .icon').addClass('icon-picture-o');
            $newRow.find('.preview img').prop('src', CubeBrowser.params.baseUrl+itemData.realpath+"?h="+encodeURIComponent( sizeStr));
         } else if(itemData.itemclass === "video"){
            $newRow.addClass('item-video');
            $newRow.find('.name .icon').addClass('icon-video-camera');
            $newRow.find('.preview').remove();
         } else if(itemData.itemclass === "flash"){
            $newRow.addClass('item-flash');
            $newRow.find('.name .icon').addClass('icon-falsh');
            $newRow.find('.preview').remove();
         } else{
            $newRow.addClass('item-text');
            $newRow.find('.name .icon').addClass('icon-file-text');
            $newRow.find('.preview').remove();
         }
         $newRow.find('a.button-open-external').prop('href', CubeBrowser.params.baseUrl+itemData.realpath);
      }
      this.$area.append($newRow);
   },
   clearList : function(){
      this.$area.find('li').remove();
      this.lastSelecteRow = false;
   },
   getAllItems : function()
   {
      return this.$area.find('li');
   },
   getSelectedItems : function()
   {
      return this.$area.find('li.active');
   },
   getItem : function()
   {
      
   },
   showImagePreview : function($item)
   {
      $item.find('.preview').show();
   },
   hideImagePreview : function($item)
   {
      $item.find('.preview').hide();
   },
  
   updateSubmitButton : function()
   {
      var $items = this.getSelectedItems();
      $('#button-insert').prop('disabled', true);
      if($items.length === 1){
         if($items.first().data('itemclass') !== "dir" && $items.first().data('itemclass') !== "dot") {
            $('#button-insert').prop('disabled', false);
         } 
      }
   }
};

/**
 * Widget s tooltipy
 * @returns {CubeBrowserTipsWidget}
 */
var CubeBrowserTipsWidget = {
   $area : null,
   init : function(path){
      this.$area = $('#tip-content');
      this.showRandomTip();
      var that = this;
      $('#button-show-tips').on('click', function(){
         if(that.$area.is(':visible')){
            that.$area.hide();
         } else {
            that.$area.slideDown();
         }
         return false;
      });
   },
   
   showRandomTip : function(){
      this.hideTips();
      var rand = Math.round(Math.random()*this.$area.find('p').length);
      this.$area.find('p').eq(rand-1).show();
   },
   
   hideTips : function(){
      this.$area.find('p').hide();
   }
};

/**
 * Widget se zprávami
 * @returns {CubeBrowserLogsWidget}
 */
var CubeBrowserLogsWidget = {
   $area : null,
   init : function(){
      this.$area = $('#logs .list');
   },
   add : function(string, type, $control){
      if(typeof (type) === 'undefined'){
         type = 'info';
      }
      
      var $newItem = $('<li></li>').addClass('msg').data('created', new Date().getTime()).html(string);
      switch (type) {
         case "info":
            $newItem.addClass('msg-info');
            break;
         case "warn":
            $newItem.addClass('msg-warning');
            break;
         case "err":
            $newItem.addClass('msg-error');
            break;
      }
      if(typeof ($control) !== 'undefined'){
         $newItem.append($control);
      }
      this.$area.append($newItem);
      this.$area.scrollTop(this.$area[0].scrollHeight - this.$area.height());
   },
   clear : function(){
      this.$area.html(null);
   }
};

/**
 * Widget pro práci se schránkou
 * @returns {CubeBrowserClipBoardWidget}
 */
var CubeBrowserClipBoardWidget = {
   $area : null,
   $list : null,
   init : function()
   {
      this.$area = $('#clipboard');
      this.$list = $('.list', this.$area);
      
      // načtení dat z local storage
      var list = localStorage.getObj('clipboard');
      if(list === null){
         list = [];
      }
      localStorage.setObj('clipboard', list);
      this.createItems(list);
   },
   isEmpty : function()
   {
      var items = localStorage.getObj('clipboard');
      return (items !== null && items.length > 0) ? false : true;
   },
   clear : function()
   {
      // vyčistí local storage
      localStorage.setObj('clipboard', []); 
      // remove from list
      this.$list.html("");
   },
   addItem : function($item)
   {
   },
   createItems : function(objs)
   {
      var that = this;
      this.$list.html("");
      $.each(objs, function(index, val){
         var $newItem = $('<li></li>').addClass(val.type === 'cut' ? 'item-cut' : 'item-copy').html(val.path);
         that.$list.append($newItem);
      });
   },
   copySelected : function()
   {
      var $items = CubeBrowserListWidget.getSelectedItems();
      var list = [];
      $items.each(function(){
         list.push({
            name : $(this).data('name'),
            path : $(this).data('realpath'),
            type : "copy"
         });
      });
      localStorage.setObj('clipboard', list);
      this.createItems(list);
   },
   cutSelected : function()
   {
      var $items = CubeBrowserListWidget.getSelectedItems();
      var list = [];
      $items.each(function(){
         list.push({
            name : $(this).data('name'),
            path : $(this).data('realpath'),
            type : "cut"
         });
      });
      localStorage.setObj('clipboard', list);
      this.createItems(list);
   },
   paste : function()
   {
      var list = localStorage.getObj('clipboard');
      var itemProgress = Math.round(100/list.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      var queue = list.length;
      $.each(list, function(i, item){
         CubeBrowser.request(item.type === "cut" ? 'move' : 'copy', 
            { item : item.path, target : CubeBrowser.getCurrentPath() },
         function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               if(item.type === "cut"){
                  CubeBrowserClipBoardWidget.clear();
               }
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
            }
         });
      });
   }
};

/**
 * Toolbar widget
 * @returns {CubeBrowserToolboxWidget}
 */
var CubeBrowserToolboxWidget =  {
   $area : null,
   init : function(){
      this.$area = $('#toolbox');
      this.updateButtons();
      
   },
   updateButtons : function(){
      var $selectedItems = CubeBrowserListWidget.getSelectedItems();
      if(CubeBrowserListWidget.currentFolder){
         // omezení zápisu
         if(CubeBrowserListWidget.currentFolder.access.write){
            $('#toolbox-button-clipboard-paste, #button-create-folder, #toolbox-button-upload',this.$area).prop('disabled', false);
         } else {
            $('#toolbox-button-clipboard-paste, #button-create-folder, #toolbox-button-upload',this.$area).prop('disabled', true);
         }
         // přejít nahoru
         if(CubeBrowserListWidget.currentFolder.current === "/") {
            $('#toolbox-button-folder-up',this.$area).prop('disabled', true);
         } else {
            $('#toolbox-button-folder-up',this.$area).prop('disabled', false);
         }
      }
      
      // první tlačítka které závisí na vybrané položce
      var needItemsIDS = new Array(
         '#toolbox-button-clipboard-cut',
         '#toolbox-button-rename',
         '#toolbox-button-move',
         '#toolbox-button-delete'
         );
      if($selectedItems.length > 0){
         
         $(needItemsIDS.join(','),this.$area).prop('disabled', false);
         
         $selectedItems.each(function(){
            if($(this).data('access').write === false){
               $(needItemsIDS.join(','),this.$area).prop('disabled', true);
            }
         });
         
         $('#toolbox-button-clipboard-copy',this.$area).prop('disabled', false);
         $('#toolbox-button-copy',this.$area).prop('disabled', false);
      } else {
         $(needItemsIDS.join(','),this.$area).prop('disabled', true);
         $('#toolbox-button-clipboard-copy',this.$area).prop('disabled', true);
         $('#toolbox-button-copy',this.$area).prop('disabled', true);
      }
      
      if(CubeBrowserClipBoardWidget.isEmpty() || CubeBrowser.currentAccess.write === false){
         $('#toolbox-button-clipboard-paste',this.$area).prop('disabled', true);
      } else {
         $('#toolbox-button-clipboard-paste',this.$area).prop('disabled', false);
      }
   }
};

var CubeBrowserUploader = {
   iframeinicialized : false,
   uploadUrl : null,
   init : function(params){
      var that = this;
      this.uploadUrl = params.uploadLink;
      // uploader
      $('#toolbox-button-upload').on('click', function(){
         $(this).closest('form').find('input[type="file"]').click();
      });
      
      if(window.FileReader) {
         // html5 upload
//         $('#upload_iframe_1').val(null);
         $('#upload_file_1').on('change', function(){
            that.handleFileUpload($(this)[0].files, CubeBrowser.getCurrentPath());
         });
      } else {
         // standart upload
         $('#upload_file_1').on('change', function(){
            $('input[name="upload_path"]').val(CubeBrowser.getCurrentPath());
            $(this).closest('form').submit();
            CubeBrowserProgressBarWidget.setProgress(10);
         });
         // upload kompletní
         $('#upload_target').load(function(){
            if(that.iframeinicialized){
               var error = $('#upload_target').get(0).contentWindow.error;
               var info = $('#upload_target').get(0).contentWindow.info;
               $.each(error, function(index, cnt){
                  CubeBrowserLogsWidget.add(cnt, 'err');
               });
               $.each(info, function(index, cnt){
                  CubeBrowserLogsWidget.add(cnt, 'info');
               });
               CubeBrowserListWidget.refreshPath();
               CubeBrowserProgressBarWidget.setProgress(100);
            } else {
               that.iframeinicialized = true;
            }
         });
      }
   },
   
   // uploading přes html5
   handleFileUpload: function(files, path) {
      if(typeof (path) === 'undefined'){
         path = CubeBrowser.currentPath;
      }
//      FileBrowser.uploadFilesPosParams.path = FileBrowser.currentDir;
      // create form
      var fd = new FormData();
      fd.append('upload_path', path);
      fd.append('upload_iframe', 0);
      fd.append('_upload__check', 1);
      $.each(files, function(index, file){
         fd.append('upload_file[]', file);
      });
      
      this.sendFilesToServer(fd);
   },
   /*
    * Odešle soubor na server
    * @param {type} formData
    * @param {type} statusBar
    * @returns {undefined}
    */
   sendFilesToServer: function(formData)
   {
      var that = this;
      CubeBrowserProgressBarWidget.hideProgress();
      CubeBrowserProgressBarWidget.setText('Nahrávám');
      var jqXHR = $.ajax({
         xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
               xhrobj.upload.addEventListener('progress', function(event) {
                  var percent = 0;
                  var position = event.loaded || event.position;
                  var total = event.total;
                  if (event.lengthComputable) {
                     percent = Math.ceil(position / total * 100);
                  }
                  //Set progress
                  CubeBrowserProgressBarWidget.setProgress(percent);
               }, false);
            }
            return xhrobj;
         },
         url: that.uploadUrl,
         type: "POST",
         contentType: false,
         processData: false,
         cache: false,
         data: formData,
         success: function(data) {
            CubeBrowserProgressBarWidget.setProgress(100);
            $.each(data.errmsg, function(index, cnt){
               CubeBrowserLogsWidget.add(cnt, 'err');
            });
            $.each(data.infomsg, function(index, cnt){
               CubeBrowserLogsWidget.add(cnt, 'info');
            });
            CubeBrowserListWidget.refreshPath(true);
         }
      });
      CubeBrowserProgressBarWidget.setAbort(function (){
         jqXHR.abort();
         CubeBrowserListWidget.refreshPath(true);
         CubeBrowserLogsWidget.add('nahrávání zrušeno', 'warn');
      });
   }
};

/**
 * Cureent path widget
 * @returns {CubeBrowserPathWidget}
 */
var CubeBrowserPathWidget = {
   $area : null,
   init : function(){
      this.$area = $('#current-path');
      $('#form-change-path', this.$area).submit(function(){
         CubeBrowserListWidget.loadPath($(this).find('input').val());
         return false;
      });
   },
  
   setPath : function(path){
      this.$area.find('#input-current-path').val(path);
   }
};

var CubeBrowserFilterWidget = {
   $input : null,
   $list : null,
   init : function(){
      this.$input = $('#browser-filter-input');
      this.$input.on("keyup", function(){
         var search = $(this).val();
         if(search !== ""){
            CubeBrowserFilterWidget.filterList(search);
         } else {
            CubeBrowserFilterWidget.showAll();
         }
      }).val("");
   },
   filterList : function(string){
      $('#browser .list>li').each(function(){
         var text = $(".filename",this).text().toLowerCase()+" "+$(".size",this).text().toLowerCase();
         if(text.indexOf(string) >= 0){
            $(this).show();
         } else {
            $(this).hide();
         }
      });
   },
   showAll : function(){
      $('#browser .list>li').show();
   }
};

/**
 * Objekt pro práci se soubory
 * @returns {CubeBrowserFileActionsWidget}
 */
var CubeBrowserFileActionsWidget = {
   $area : null,
   init : function(){
      this.$area = $('#file-operations');
      this.$selector = $('#file-operations-selector', this.$area);
      
      this.disableSelector();
      
      var that = this;
      this.$selector.change(function(){
         $('div.file-operations-box', that.$area).hide();
         var target = $(this).find(":selected").data('target');
         $(target).slideDown();
         if(target === "#image-resize"){
            that.initImageResize();
         }
      });
      // fitlry a argumenty
      $('#image-filter-select', this.$area).change(function(){
         if($(this).find(":selected").data('adv') === 1){
            $('#fitler-settings', that.$area).show();
            $('#fitler-settings span', that.$area).text($(this).find(":selected").data('advtitle'));
            $('#fitler-settings input[name="filter_arg"]', that.$area).val($(this).find(":selected").data('advdefault'));
         } else {
            $('#fitler-settings', that.$area).hide();
         }
      }).change();
      
   },
   disableSelector : function(){
      this.$selector.val($("option:first", this.$selector).val());
      this.$selector.find('option').not(':first').prop('disabled', true);
   },
   updateActions : function(){
      var that = this;
      var $items = CubeBrowserListWidget.getSelectedItems();
      if($items.length === 0){
         this.disableSelector();
         $('div.file-operations-box', this.$area).hide();
      } else {
         var imageOperations = false;
         var fileOperations = false;
         
         $items.each(function(){
            if($(this).data('type') === 'file' && $(this).data('access').write === true){
               fileOperations = true;
            }
            if($(this).data('itemclass') === "image"){
               imageOperations = true;
            }
         });
         this.$selector.find('option.image-operation').prop('disabled', !imageOperations);
         this.$selector.find('option.file-operation').prop('disabled', !fileOperations);
         
         // hide boxes 
         if(!imageOperations){
            $('div.image-operation').hide();
            // select first
            this.$selector.val($("option:first", this.$selector).val());
         } else {
            this.$selector.change();
         } 
         if(!fileOperations){
            $('div.file-operation').hide();
            // select forst
            this.$selector.val($("option:first", this.$selector).val());
         } else {
            this.$selector.change();
         }
         
      }
   },
   
   initImageResize : function(){
      var $box = $('#image-resize');
      // přepočet rozměrů
      $('input[name="image_size_x"]', $box).val(CubeBrowserPreviewWidget.realWidth);
      $('input[name="image_size_y"]', $box).val(CubeBrowserPreviewWidget.realHeight);
      $('input[name="image_size_x"]', $box).off('change').on('change', function(){
         if($('input[name="mantain_ratio"]', $box).is(':checked')){
            var ratio = CubeBrowserPreviewWidget.realWidth/CubeBrowserPreviewWidget.realHeight;
            $('input[name="image_size_y"]', $box).val(Math.round($(this).val()/ratio));
         }
      });
      $('input[name="image_size_y"]', $box).off('change').on('change', function(){
         if($('input[name="mantain_ratio"]', $box).is(':checked')){
            var ratio = CubeBrowserPreviewWidget.realHeight/CubeBrowserPreviewWidget.realWidth;
            $('input[name="image_size_x"]', $box).val(Math.round($(this).val()/ratio));
         }
      });
   },
   resizeImages : function(){
      var $items = CubeBrowserListWidget.getSelectedItems().filter('.item-image');
      var queue = $items.length;
      var that = this;
      var $box = $('#image-resize');
      if(queue === 0){
         CubeBrowserLogsWidget.add('Nebyl vybrán žádný obrázek', 'err');
         return;
      }
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('imageResize', {
            item : $(this).data('realpath'),
            width : $('input[name="image_size_x"]', $box).val(),
            height : $('input[name="image_size_y"]', $box).val(),
            ratio : $('input[name="mantain_ratio"]', $box).is(":checked") ? 1 : 0,
            crop : $('input[name="image_crop"]', $box).is(":checked") ? 1 : 0,
            createNew : $('input[name="create_new"]', $box).is(":checked") ? 1 : 0
         }, function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
               $('div.file-operations-box', that.$area).hide();
            }
         });
      });
   },
   rotateImages : function(){
      var $items = CubeBrowserListWidget.getSelectedItems().filter('.item-image');
      var queue = $items.length;
      var that = this;
      var $box = $('#image-rotate');
      if(queue === 0){
         CubeBrowserLogsWidget.add('Nebyl vybrán žádný obrázek', 'err');
         return;
      }
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('imageRotate', {
            item : $(this).data('realpath'),
            degree : $('select[name="degree"]', $box).val(),
            createNew : $('input[name="create_new"]', $box).is(":checked") ? 1 : 0
         }, function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
               $('div.file-operations-box', that.$area).hide();
            }
         });
      });
   },
   filterImages : function(){
      var $items = CubeBrowserListWidget.getSelectedItems().filter('.item-image');
      var queue = $items.length;
      var that = this;
      var $box = $('#image-filter');
      if(queue === 0){
         CubeBrowserLogsWidget.add('Nebyl vybrán žádný obrázek', 'err');
         return;
      }
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('imageFilter', {
            item : $(this).data('realpath'),
            filter : $('select[name="filter"]', $box).val(),
            arg : $('input[name="filter_arg"]', $box).val(),
            createNew : $('input[name="create_new"]', $box).is(":checked") ? 1 : 0
         }, function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
               $('div.file-operations-box', that.$area).hide();
            }
         });
      });
   },
   flipImages : function(){
      var $items = CubeBrowserListWidget.getSelectedItems().filter('.item-image');
      var queue = $items.length;
      var that = this;
      var $box = $('#image-flip');
      if(queue === 0){
         CubeBrowserLogsWidget.add('Nebyl vybrán žádný obrázek', 'err');
         return;
      }
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('imageFlip', {
            item : $(this).data('realpath'),
            flip : $('select[name="flip"]', $box).val(),
            createNew : $('input[name="create_new"]', $box).is(":checked") ? 1 : 0
         }, function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
               $('div.file-operations-box', that.$area).hide();
            }
         });
      });
   },
   watermarkImages : function(){
      var $items = CubeBrowserListWidget.getSelectedItems().filter('.item-image');
      var queue = $items.length;
      var that = this;
      var $box = $('#image-watermark');
      if(queue === 0){
         CubeBrowserLogsWidget.add('Nebyl vybrán žádný obrázek', 'err');
         return;
      }
      
      if (/^[0-9a-f]{6}$/i.test($('input[name="color"]', $box).val()) === false) {
         //Match
         alert('Nesprávně zadaná barva textu');
         return false;
      }
      if ($('input[name="colorBg"]', $box).val() !== "" 
         && /^[0-9a-f]{6}$/i.test($('input[name="colorBg"]', $box).val()) === false) {
         //Match
         alert('Nesprávně zadaná barva pozadí');
         return false;
      }
      
      var itemProgress = Math.round(100/$items.length);
      CubeBrowserProgressBarWidget.setProgress(0);
      $items.each(function(){
         CubeBrowser.request('imageWatermark', {
            item : $(this).data('realpath'),
            text : $('input[name="text"]', $box).val(),
            color : $('input[name="color"]', $box).val(),
            colorBg : $('input[name="colorBg"]', $box).val(),
            posX : $('select[name="posX"]', $box).val(),
            posY : $('select[name="posY"]', $box).val(),
            createNew : $('input[name="create_new"]', $box).is(":checked") ? 1 : 0
         }, function(){
            CubeBrowserProgressBarWidget.addProgress(itemProgress);
            queue--;
            if(queue === 0){
               CubeBrowserProgressBarWidget.setProgress(100);
               CubeBrowserListWidget.refreshPath();
               $('div.file-operations-box', that.$area).hide();
            }
         });
      });
   }
};


/**
 * Widget s náhledem
 * @returns {CubeBrowserPreviewWidget}
 */
var CubeBrowserPreviewWidget = {
   $area : null,
   realWidth : 0,
   realHeight : 0,
   init : function(){
      this.$area = $('#preview');
   },
   showPreview : function($item){
      var src = CubeBrowser.params.baseUrl + $item.data("realpath") + "?time="+ new Date().getTime();
      this.$area.find('img').prop('src', src).show();
      this.$area.find('span.icon').hide();
      var that = this;
      $("<img/>") // Make in memory copy of image to avoid css issues
         .attr("src", src)
         .load(function() {
            that.realWidth = this.width;
            that.realHeight = this.height;
      });
   },
   hidePreview : function(){
      this.$area.find('img').prop('src', null).hide();
      this.$area.find('span.icon').show();
   }
};


var rowCount = 0;
var CubeBrowserProgressBarWidget = {
   $area : null,
   $meter : null,
   text : null,
   progress : 0,
   init : function(){
      this.$area = $('#progress-bar');
      this.$meter = this.$area.find('span.progress');
      this.$percents = this.$area.find('span.percent');
      
      this.hideProgress();
   },
   addProgress : function(progress) {       
      this.setProgress(this.progress+progress);
   },
   setProgress : function(progress) {       
      this.$area.show();
      this.progress = progress >= 100 ? 100 : progress;
      this.$meter.css('width', this.progress+"%");
      this.$percents.text( (this.text !== null ? this.text+" " : null)+this.progress+"%");
      
      if(this.progress === 100){
         // nastavit zmizeni
         setTimeout(function(){
            CubeBrowserProgressBarWidget.hideProgress();
         }, 2000);
      }
   },
   setAbort : function(func){
      var that = this;
      this.$area.find('.button-cancel')
         .unbind('click')
         .bind('click', function(){
            func.call();
            that.hideProgress();
         })
         .show();
   },
   hideProgress : function(){
      this.$area.hide();
      this.$meter.css('width', "0%");
      this.$percents.text("0%");
      this.$area.find('.button-cancel').hide();
      this.text = null;
   },
   setText : function(text){
      this.text = text;
   }
};
