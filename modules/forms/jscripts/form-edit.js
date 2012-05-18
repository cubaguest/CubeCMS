
var FormEditor = {
   elementParams : {
      selectcountry : {label : 'Select country', options : []},
      defaultlabel : 'Name'
   },
   $form : null,
   $selectedItem : null,
   init : function(params){
      $.extend(this.elementParams, params);
      
      this.$form = $('#form');
      
      // sorting
      this.$form.sortable();
      
      // in it events over items
      $('.item', this.$form).live({
         editElement : function(e, target) {
            FormEditor.$selectedItem = $(this);
            var focusName = true;
            if(target.nodeName == "INPUT" || target.nodeName == "SELECT" || target.nodeName == "TEXTAREA"){
               focusName = false;
            }
            FormEditor.showFormItemSettings($(this), focusName);
         },
         click :function(e){
            $('.ui-state-highlight', this.form).removeClass("ui-state-highlight");
            $('.item-selected', this.form).removeClass("item-selected");

            $(this).addClass('item-selected').addClass('ui-state-highlight');
            $(this).trigger("editElement", e.srcElement || e.target);
            
         },
         mouseover: function() {
            $(this).addClass('ui-state-hover');
         },
         mouseout: function() {
            $(this).removeClass('ui-state-hover');
         },
         chengeName : function(e, value){
            var name = value;
            if( typeof(value) == "object" ){
               name = str2url($(value).val());
            }
            var $item = $(this);
            $item.data("name", name);
            var $elem = FormEditor.getFormItem($item);
            $elem.attr('name', name);
         },
         chengeLabel : function(e, value){
            var label = value;
            if( typeof(value) == "object" ){
               label = $(value).val();
            }
            
            var $item = $(this);
            var $elem = FormEditor.getFormItem($item);
            if(FormEditor.getFormItemType($item) == "submit"){
               $elem.val(label)
            } else if(FormEditor.getFormItemType($item) == "label"){
               $elem.text(label)
            } else {
               $item.find('label:first').text(label);
            }
            $item.data('label', label);
            $item.trigger("chengeName", label);
         },
         chengeValue : function(e, value){
            if( value instanceof jQuery ){
               value = $(value).val();
            }
            var $item = $(this);
            var $elem = FormEditor.getFormItem($item);
            var type = FormEditor.getFormItemType($item);
            if(type == "submit" || type == "label"){
            } else if(type == "checkbox"){
               $elem.prop('checked', value);
            } else if(type == "radio"){
               $item.find('input[value="'+value+'"]').prop('checked', true);
            } else if(type == "select"){
               $elem.val(value);
            } else {
               $elem.val(value);
            }
            $item.data('value', value);
         },
         chengeRequire : function(ev, value){
            var $item = $(this);
            var e = FormEditor.getFormItem($item);
            var req = false;
            if( value === true || (typeof(value) == "object" && $(value).is(":checked") == true ) ) {
               req = true;
            }
            if(req){
               $item.find('label').addClass("form-required");
               e.addClass('form-required');
            } else {
               $item.find('label').removeClass("form-required");
               e.removeClass('form-required');
            }
            $item.data('required', req);
         
         },
         chengeIsMultiple : function(ev, value){
            var e = FormEditor.getFormItem($(this));
            var type = FormEditor.getFormItemType($(this));
            
            var isMultiple = false;
            if( value === true || (typeof(value) == "object" && $(value).is(":checked") == true ) ) {
               isMultiple = true;
            }
            
            if( type == "select" || type == "selectcountry"){
               if(isMultiple){
                  e.prop("multiple", "multiple");
               } else {
                  e.removeProp("multiple")
               }
            }
            $(this).data('isMultiple', isMultiple);
         
         },
         chengeOptions : function(ev, options){
            var e = FormEditor.getFormItem($(this));
            var type = FormEditor.getFormItemType($(this));
            
            var data = new Array();
            if(typeof(options) === "string"){
               data = options.split(/[\n\r]{1,2}/m);
            } else {
               data = options;
            }
            
            if(type == "select" || type == "selectcountry"){
               $(e).html("");
               $.each(data, function(index, data){
                  if(/^\s*$/.test(data) == false){
                     $(e).append($('<option></option>').attr({value : data}).text(data));
                  }
               });
            } else if(type == "radio") {
               var name = FormEditor.getFormItemName($(this));
               var $container = $(this).find('p').html("");
               var idStr = name;
               
               $.each(data, function(index, data){
                  if(/^\s*$/.test(data) == false){
                     $container.append(
                        $('<input />').attr({
                           name : name,
                           type : "radio",
                           id : idStr + "id-" + (index+1),
                           value : data
                        }))
                      .append( $('<label></label>').attr({"for" : idStr + "id-" + (index+1)}).text(data) )
                      .append($('<br />'));
                  }
               });
               $container.find("input:first").attr("checked", "checked");
            }
         },
         chengeValidator : function(ev, validator){
            var $item = $(this);
            var validatorText = null;
            var $select = $('select[name="validator"]');
            if(typeof(validator) == "string"){
               validatorText = $select.find('option[value="'+validator+'"]').text();
            } else {
               validator = $select.val();
               validatorText = $select.find(':selected').text();
            }
            $item.data("validator", validator);
            if(validator != ""){
               $item.find('span.validator').text(" "+validatorText);
            }
         }
      });
      
      $('#button-serialize').click(function(){
         FormEditor.createData();
      });
      $('#form-data').submit(function(){
         FormEditor.createData();
      });
      
      if($('textarea[name="form_cr_data"]').val() != ""){
         this.createForm($('textarea[name="form_cr_data"]').val());
      }
   },
   
   createFormItem : function(type, eparams){
      if(type == ""){
         return;
      }
      var rid = parseInt(Math.random()*10000);
      var t = typeof(eparams);
      if(typeof(eparams) !== "undefined" && typeof(eparams.id) !== "undefined"){
         rid = eparams.id;
      }
      
      var params = {
         rid : rid,
         idStr : "elem-"+rid,
         required : false,
         isMultiple : false,
         validator : "",
         label : this.elementParams.defaultlabel,
         name : "name"+rid,
         value : null,
         type : type,
         id : null,
         options : new Array()
      };
      if(type == "selectcountry"){
         params.label = this.elementParams.selectcountry.label;
      }
      
      $.extend(params, eparams);
      
      var $item = $('<li></li>')
         .addClass("item")
         .addClass("ui-state-default")
         .addClass('item-'+params.type)
         .append($('#form-tpls .element-tools').clone(true))
         .data({
            type : params.type,
            id : params.id
         });
      
      if(params.type == "text"){
         $item.append(
            $('<label></label>').attr({"for" : params.idStr}).text(params.label)
         ).append(
            $('<input />').attr({
               type : "text",
               value : null,
               id : params.idStr
            })
         );
         
      } else if(params.type == "textarea"){
         $item.append(
            $('<label></label>').attr({"for" : params.idStr}).text(params.label)
         ).append(
            $('<textarea></textarea>').attr({
               id : params.idStr
            }).val(null)
         );
      } else if(params.type == "select"){
         $item.append(
            $('<label></label>').attr({"for" : params.idStr}).text(params.label)
         ).append(
            $('<select></select>').attr({
               id : params.idStr
            })
         );
      } else if(params.type == "selectcountry"){
         $item.append(
            $('<label></label>').attr({"for" : params.idStr}).text(params.label)
         ).append(
            $('<select></select>').attr({
               id : params.idStr
            })
         );
         params.options = this.elementParams.selectcountry.options;
      } else if(params.type == "checkbox"){
         var $elem = $('<input />').attr({
               type : "checkbox",
               id : params.idStr
            });
         
         $item
            .append($elem)
            .append( $('<label></label>').attr({"for" : params.idStr}).text(params.label) );
      } else if(type == "label"){
         $item.append( $('<span></span>').addClass('label').text(params.label) );
      } else if(type == "radio"){
         var $container = $('<p></p>')
            .append($('<input />').attr({
                  id : params.idStr,
                  type : "radio",
                  value : params.label
               })
            )
            .append($('<label></label>').attr({"for" : params.idStr}).text(params.label));
         
         $item.append( $('<label></label>').text(params.label) ).append($container);
      } else if(type == "submit"){
         $item.append(
            $('<label></label>').text(" ")
         ).append(
            $('<input />').attr({
               type : "submit",
               value : params.label,
               id : params.idStr
            })
         );
      }
      
      $item
         .append($('<span></span>').addClass('validator') )
         .append('<hr class="reseter" />');
      
      var $selected = this.$form.find(".item-selected");
      if($selected.length > 0){
         // append after selected
         $selected.after($item);
      } else {
         // append to end
         this.$form.append($item);
      }
      
      $item.trigger("chengeRequire", params.required);
      $item.trigger("chengeValidator", params.validator);
      $item.trigger("chengeLabel", params.label);
      $item.trigger("chengeName", params.name);
      if(params.options.length > 0){
         $item.trigger("chengeOptions", [params.options]);
      }
      $item.trigger("chengeIsMultiple", params.isMultiple);
      $item.trigger("chengeValue", [params.value]);
      
      $item.click();
   },
   
   changeFormName : function(name){
      $('#form-name').text(name);
   },
   changeFormMessage : function(msg){
      $('#form-message').text(msg);
   },
   
   showFormItemSettings : function($item, focus){
      $('#form-elem-setup').show();
      $('.elem-optional').hide();
      var $elem = this.getFormItem($item);
      var type = this.getFormItemType($item);
      var form = $('#form-elem-settings')[0];
      form.name.value = $item.data("label");
      
      // show allowed items
      if(type == "text" ){
         this.showFormItemOptions("require");
         this.showFormItemOptions("validators");
      } else if(type == 'submit'){
         form.name.value = $elem.val();
      } else if(type == 'checkbox'){
         this.showFormItemOptions("require");
      } else if(type == 'textarea'){
         this.showFormItemOptions("require");
         
      } else if(type == 'select'){
         this.showFormItemOptions("options");
         this.showFormItemOptions("is-multiple");
         // append options back to textarea
         var values = "";
         $('option',$elem).each(function(){
            values += $(this).attr('value')+"\n";
         });
         $('#elem-optional-options-wrap textarea').val(values);
      } else if(type == 'selectcountry'){
         this.showFormItemOptions("is-multiple");
      } else if(type == 'label'){
         form.name.value = $elem.text();
      } else if(type == 'radio'){
         this.showFormItemOptions("options");
         var opts = "";
         $item.find("input").each(function(){
            opts += $(this).next("label").text()+"\n";
            
         });
         $('#elem-optional-options-wrap textarea').val(opts);
      }
      if(typeof(focus) === "undefined" || focus == true ){
         $(form.name).select().focus();
      }
      
      // require 
      form.require.checked = $item.data('required');
      // multiple
      form.ismultiple.checked = $item.data('isMultiple');
      
      // select validators
      form.validator.value = "";
      if($elem.data("validator") != null){
         form.validator.value = $elem.data("validator");
      }
   },
   
   getFormItemType : function($formItem){
      return $formItem.data("type");
   },
   getFormItemName : function($formItem){
      return $formItem.data("name");
   },
   getFormItemLabel : function($formItem){
      return $formItem.data("label");
   },
   getFormItem : function($item){
      if($item.find('input').length > 0 ){
         return $item.find('input');
      } else if($item.find('textarea').length > 0 ){
         return $item.find('textarea');
      } else if($item.find('select').length > 0 ){
         return $item.find('select');
      } else if($item.find('span.label').length > 0 ){
         return $item.find('span.label');
      }
      return null;
   },
   
   showFormItemOptions : function(name){
      $('#elem-optional-'+name+'-wrap').show();
   },
   
   deleteFormItem : function(item){
      if(confirm("Opravdu smazat element?") ){
         var $i = $(item).parents('.item');
         if($i.data("id") != null) {
            var $input = $('input[name="form_cr_delete"]');
            var currentIds = new Array;
            var v = $input.val();
            if($input.val() != ""){
               currentIds = $input.val().split(";");
            }
            currentIds.push($i.data("id"));
            $input.val(currentIds.join(";"));
         }
         $i.remove();
      }
      return false;
   },
   
   _createElemName : function(name){
      return str2url(name);
   },
   
   createData : function(){
      var serializedData = new Array();
      
      this.$form.find("li").each(function(index, data){
         var $item = $(this);
         var itemDesc = {
            name : FormEditor.getFormItemName($item),
            label : FormEditor.getFormItemLabel($item),
            type : FormEditor.getFormItemType($item),
            required : $item.data('required'),
            isMultiple : $item.data('isMultiple'),
            value : null,
            id : $item.data('id'),
            options : new Array(),
            order : index+1,
            validator : null
         };
         
         if($item.data('validator') !== "undefined"){
            itemDesc.validator = $item.data('validator');
         }
         
         // specielni typy s parametry
         if(itemDesc.type == "select"){
            $item.find('option').each(function(){
               itemDesc.options.push($(this).text());
            });
            itemDesc.value = $item.find('select').val();
         } else if(itemDesc.type == "radio"){
            $item.find('input').each(function(){
               itemDesc.options.push( $(this).next("label").text() );
            });
            itemDesc.value = $item.find('input:checked').val();
         } else if(itemDesc.type == "checkbox"){
            itemDesc.value = $item.find('input').is(":checked");
         } else if(itemDesc.type == "textarea"){
            itemDesc.value = $item.find('textarea').val();
         } else if($item.find('input')){
            itemDesc.value = $item.find('input:first').val();
         }
         
         serializedData.push(itemDesc);
      });
      
      $('textarea[name="form_cr_data"]').val( JSON.stringify(serializedData, null, 2 ) ); // need compatibility
   },
   createForm : function(data){
      data = $.parseJSON(data);
      // base 
      if($('input[name="form_cr_name"]').val() != ""){
         $('#form-name').text($('input[name="form_cr_name"]').val());
      }
      if($('textarea[name="form_cr_messageSend"]').val() != ""){
         $('#form-message').text($('textarea[name="form_cr_messageSend"]').val());
      }
      if(data.length > 0){
         this.$form.html("");
      }
      
      // elements
      $.each(data, function(index, item){
         var $item = FormEditor.createFormItem(item.type, item);
      });
   }
 
}
