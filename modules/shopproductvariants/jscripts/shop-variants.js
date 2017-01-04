/* obejkt pro práci s parametry */
ShopVariants = {
   /* base opts */
   opts : {
      urlGroups : null,
      urlEditGroup : null,
      urlVariants : null,
      urlEditVariant : null,
      $dialogParam : null,
      $dialogValue : null,
      $tableParams : null,
      $tableValues : null,
      $formEditParam : null,
      $formEditValue : null,
      strings : {
         dlgAddGroupTitle : 'Přidání skupiny',
         dlgEditGroupTitle : 'Úprava skupiny',
         dlgAddAttrTitle : 'Přidání parametru',
         dlgEditAttrTitle : 'Úprava parametru',
         infoSetName : 'Musí být zadán název',
         confirmDeleteVariant : 'Smazat tento atribut?',
         confirmDeleteGroup : 'Smazat tuto skupinu?',
         emptyValues : 'Žádná hodnota není vytvořena',
         saved : 'uloženo'
      }
   },
   $dialogGroup : null,
   $dialogVariant : null,
   $listGroups : null,
   $listVariants : null,
   $formEditGroup : null,
   $formEditVariant : null,
   $groupNameBox : null,
   $buttonAddVariant : null,
   $buttonReloadGroups : null,
   selectedGroupId : -1,

   /*
    * funkce
    */

   init : function(params, elemets){
      $.extend(this.opts, params);
      $.extend(this, elemets);

      this.initDialogs();
      this.loadGroups();
      this.initEvents();
      this.clearFormEditGroup();
      this.clearFormEditVariant();
   },
   /* BASE INIT */
   initDialogs : function(){
      var _this = this;
      _this.clearFormEditGroup();
      this.$dialogGroup = $('#dialog-variant-group-edit');
      this.$dialogVariant = $('#dialog-variant-edit');
   },
   /* INIT EVENTS */
   initEvents : function(){
      var _this = this;
      // potvrzení formuláře
      this.$formEditGroup.submit(function(){
         // check form
//         if($('input[name="group_edit_name"]').val() == ""){
//            alert(_this.opts.strings.infoSetName);
//            return false;
//         }
         $.ajax({
            type : 'POST', url : _this.opts.urlEditGroup,
            data : $(this).serialize(),
            success : function(data){
               if(data.errmsg.length == 0){
                  _this.loadGroups(function(){
                     // click on last row
                     if(_this.selectedGroupId == -1){
                        $('li:last a.name', _this.$listGroups).click();
                     } else {
                        $('li#attrgroup-'+_this.selectedGroupId+' a.name', _this.$listGroups).click();
                     }
                  });
                  _this.$dialogGroup.hide();
               }
            }
         });
         return false;
      });
      this.$formEditVariant.submit(function(){
         // check form
//         if($('input[name="variant_edit_name"]').val() == ""){
//            alert(_this.opts.strings.infoSetName);
//            return false;
//         }
         $.ajax({
            type : 'POST', url : _this.opts.urlEditVariant,
            data : $(this).serialize(),
            success : function(data){
               if(data.errmsg.length == 0){
                  _this.loadVariants( _this.$listVariants.data('loadedUrl') );
                  _this.$dialogVariant.hide();
               }
            }
         });

         return false;
      });
   },
   // výběr
   selectGroup : function(linkObj){
      $('li', this.$listGroups).removeClass('cubecms-state-highlight');
      var $row = $(linkObj).closest('li').addClass('cubecms-state-highlight');
      var data = this.getGroupData($row);
      this.selectedParamId = data.id;

      $('input[name="variant_edit_idGroup"]', this.$formEditVariant)
         .val(data.id);

      this.$groupNameBox.text($(linkObj).text());
      this.$buttonAddVariant.show();
      // load values
      this.loadVariants(linkObj.href);

   },

   // přidávání
   addGroup : function(){
      this.selectedGroupId = -1;
      this.clearFormEditGroup();
      this.$dialogGroup.show();
      this.$dialogGroup.find('.cubecms-modal-title').text(this.opts.strings.dlgAddGroupTitle);
   },
   addVariant : function(){
      this.clearFormEditVariant();
      this.$dialogVariant.show();
      this.$dialogVariant.find('.cubecms-modal-title').text(this.opts.strings.dlgAddAttrTitle);
   },
   // úpravy
   editGroup : function(id){
      var $row = $('#attrgroup-'+id);
      var data = this.getGroupData($row);

      // assign to form
      $('input[name="group_edit_id"]', this.$formEditGroup).val(id);
      $.each(data.name, function(lang, value){
         $('input[name="group_edit_name['+lang+']"]', this.$formEditGroup).val(value);
      });
      this.selectedGroupId = data.id;
      $('#attrgroup-'+data.id).find('a.cubecms-name').click();
      // show dialog
      this.$dialogGroup.show();
      this.$dialogGroup.find('.cubecms-modal-title').text(this.opts.strings.dlgEditGroupTitle);
   },
   editVariant : function(id){
      var $row = $('#attr-'+id);
      var data = this.getVariantData($row);
      // assign to form
      $('input[name="variant_edit_id"]', this.$formEditVariant).val(id);
      $.each(data.name, function(lang, value){
         $('input[name="variant_edit_name['+lang+']"]', this.$formEditVariant).val(value);
      });
      $('input[name="variant_edit_code"]', this.$formEditVariant).val(data.code);
      // show dialog
      this.$dialogVariant.show();
      this.$dialogVariant.find('.cubecms-modal-title').text(this.opts.strings.dlgEditAttrTitle);
   },
   // mazani
   deleteGroup : function(id){
      var _this = this;
      if(!confirm(this.opts.strings.confirmDeleteGroup)){
         return;
      }
      this.selectedGroupId = -1;
      $.ajax({
         type : 'POST', url : _this.opts.urlEditGroup,
         data : {action : 'delete', id : id},
         success : function(data){
            if(data.errmsg.length == 0){
               _this.loadGroups();
               // unset all values
               _this.clearVariants();
            }
         }
      });
   },
   deleteVariant : function(id){
      var _this = this;
      if(!confirm(this.opts.strings.confirmDeleteVariant)){
         return;
      }
      $.ajax({
         type : 'POST', url : _this.opts.urlEditVariant,
         data : {action : 'delete', id : id},
         success : function(data){
            if(data.errmsg.length == 0){
               _this.loadVariants();
            }
         }
      });
   },
   // vrací data
   getGroupData : function($row){
      return { id : $row.data('id'), name : $row.data('name') };
   },
   getVariantData : function($row){
      return { id : $row.data('id'), name : $row.data('name'), code : $row.data('code') };
   },
   // nahraje seznam parametrů
   loadGroups : function(callback){
      var _this = this;
      this.clearVariants();
      $(this.$listGroups).load(this.opts.urlGroups, function(){
         // init sorting
         $(_this.$listGroups).sortable({
            handle: ".sort-area",
            placeholder: "cubecms-list-row cubecms-state-highlight",
            forceHelperSize: true, 
            forcePlaceholderSize: true,
            update: function( event, ui ) {
               $.ajax({
                  type : 'POST', url : _this.opts.urlEditGroup,
                  data : {action : 'changepos', id : ui.item.data('id'), pos : ui.item.index()+1},
                  success : function(data){
                     if(data.errmsg.length != 0){
                        $(_this.$listGroups).sortable( "cancel" );
                        alert('Chyba při přesunu: '+data.errmsg.join(";"));
                     }
                  }
               });
            }
         });

         // callback after load
         if($.isFunction(callback)){
            callback.call(_this);
         }
      });
   },
   // nahraje seznam hodnot parametrů
   loadVariants : function(link){
      var _this = this;
      if(typeof(link) == "undefined"){
         link = this.$listVariants.data('loadedUrl');
      }
      if(link == null){
         return;
      }
      this.$listVariants
         .data('loadedUrl', link)
         .html('<li class="ui-widget-content">Loading...</li>')
         .load(link, function(){
            // init sorting
            $(_this.$listVariants).sortable({
               handle: ".sort-area",
               placeholder: "cubecms-list-row cubecms-state-highlight",
               forceHelperSize: true, 
               forcePlaceholderSize: true,
               update: function( event, ui ) {
                  $.ajax({
                     type : 'POST', url : _this.opts.urlEditVariant,
                     data : {action : 'changepos', id : ui.item.data('id'), pos : ui.item.index()+1},
                     success : function(data){
                        if(data.errmsg.length != 0){
                           $(_this.$tableValues).sortable( "cancel" );
                           alert('Chyba při přesunu: '+data.errmsg.join(";"));
                        }
                     }
                  });
               }
            });

         });
   },
   // podpůrné
   clearFormEditGroup : function(){
      $('input[name="group_edit_id"]', this.$formEditGroup).val("");
      $('input.group_edit_name_class', this.$formEditGroup).val("");
   },
   clearFormEditVariant : function(){
      $('input[name="variant_edit_id"]', this.$formEditVariant).val("");
      $('input[name="variant_edit_code"]', this.$formEditVariant).val("");
      $('input.variant_edit_name_class', this.$formEditVariant).val("");
   },
   clearVariants : function(){
      this.$listVariants
         .data('loadedUrl', null)
         .html('<li class="ui-widget-content">'+this.opts.strings.emptyValues+'</li>');
      this.$buttonAddVariant.hide();
   }
};