CustomMenu = {
   editUrl : null,
   dialogTitle : { add : null, edit : null},

   // objects
   $formAddDialog : null,
   $tables : null,

   init : function(params){
      $.extend(this, params);

      this.$formAddDialog = $( "#form-edit-menu-item" );
      this.$tables = $('.menu-box tbody');

      this.initDialog();
      this.initSorting();
   },
   initDialog : function(){
      var cMenu = this;
      this.$formAddDialog.dialog({
         modal: true,
         resizable: false,
         autoOpen: false,
         width : 600, height : 450,
         title : cMenu.dialogTitle.add
      });
   },
   initSorting : function(){
      var cMenu = this;
      this.$tables.sortable({
         handle: ".move",
         axis: "y",
         update: function( event, ui ) {
                  $.ajax({
                     type : 'POST', url : cMenu.editUrl,
                     data : {action : 'changepos', id : ui.item.data('id'), pos : ui.item.index()+1},
                     success : function(data){
                        if(data.errmsg.length != 0){
                           $('tbody', cMenu.$tables).sortable( "cancel" );
                           alert('Chyba při přesunu: '+data.errmsg.join(";"));
                        }
                     }
                  });
         }
      });
   },

   addCustomMenuItem : function(){
      $('input.edit_menu_item_name_class', this.$formAddDialog).val("");
      $('input[name="edit_menu_item_id"]', this.$formAddDialog).val("");
      $('input[name="edit_menu_item_link"]', this.$formAddDialog).val("");
      $('select[name="edit_menu_item_cat"]', this.$formAddDialog).val(0);
      $('select[name="edit_menu_item_box"]', this.$formAddDialog).val("");
      $('input[name="edit_menu_item_active"]', this.$formAddDialog).attr('checked', true);
      $('input[name="edit_menu_item_newWin"]', this.$formAddDialog).attr('checked', false);
      this.$formAddDialog.dialog("option", 'title', this.dialogTitle.add).dialog('open');
   },
   editCustomMenuItem : function(id){
      var data = this.getRowData(id);
      // assign data to form
      $.each(data.name, function(index, value){
         $('input[name="edit_menu_item_name['+index+']"]', this.$formAddDialog).val(value);
      });
      $('input[name="edit_menu_item_id"]', this.$formAddDialog).val(data.id);
      $('input[name="edit_menu_item_link"]', this.$formAddDialog).val(data.link);
      $('select[name="edit_menu_item_cat"]', this.$formAddDialog).val(data.idcat);
      $('select[name="edit_menu_item_box"]', this.$formAddDialog).val(data.box);
      $('input[name="edit_menu_item_active"]', this.$formAddDialog).attr('checked', data.active);
      $('input[name="edit_menu_item_newWin"]', this.$formAddDialog).attr('checked', data.newwin);
      this.$formAddDialog.dialog("option", 'title', this.dialogTitle.edit).dialog('open');
   },
   deleteCustomMenuItem : function(id){
      if(!confirm('Opravdu smazat položku?')){
         return false;
      }
   },
   getRowData : function(id){
      var $row = $('#menu-item-'+id);
      return {
         id : $row.data('id'),
         name : $row.data('name'),
         box : $row.data('box'),
         active : $row.data('active'),
         link : $row.data('link'),
         idcat : $row.data('idcat'),
         newwin : $row.data('newwin')
      }
   }

}