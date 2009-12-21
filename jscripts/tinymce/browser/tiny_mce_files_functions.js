var FileBrowserDialogue = {
   listType : null,
   win : null,
   category : 0,
   init : function () {
      // Here goes your code for setting your custom things onLoad.
      FileBrowserDialogue.listType = tinyMCEPopup.getWindowArg("listType");
      FileBrowserDialogue.win = tinyMCEPopup.getWindowArg("window");
      FileBrowserDialogue.category = tinyMCEPopup.getWindowArg("cat");
      FileBrowserFunctions.init();
      FileBrowserDirsFunctions.init();
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
}

var FileBrowserFunctions = {
   cmsURL : null,
   cmsPluginUrl : null,
   init : function(){
      this.cmsURL = window.location.toString();
      this.cmsURL = this.cmsURL.replace(/jscripts(.*)$/g, '');
      this.cmsPluginUrl = this.cmsURL+"jscripts/tinymce/";
      this.cmsURL = this.cmsURL+"jsplugin/tinymce/cat-"+FileBrowserDialogue.category+"/";
      //nbastavení formuláře pro upload
      //      $("#uploadForm").attr('action', this.cmsPluginUrl+"uploadFile.html");
   },
   showResult : function(msg){
      $('#operationResult').html('<span class="msg">'+msg+'<\/span>');
   }
}

var FileBrowserFilesFunctions = {
   currentFilePath : null,
   loadFiles : function (){
      $("#fileList").html('<img src="../../../images/progress.gif" alt="loading..." width="16"/>');
      $.ajax({
         type: "GET",
         url: FileBrowserFunctions.cmsURL+"getfiles.html?dir="+escape(FileBrowserDirsFunctions.currentPath)+"&type="+FileBrowserDialogue.listType+"&next=dalse",
         cache: false,
         success: function(data){
            $("#fileList").html(data);
            FileBrowserFilesFunctions.addEvents();
            //            $('#fileList .icon img').each(function(){
            //               $(this).attr('src', $(this).attr('src')+'?'+(new Date()).getTime());
            //            });
         }
      });
   },
   // eventy pro práci se soubory
   addEvents : function (){
      $(".draggable").draggable({
         //					cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
         revert: 'invalid', // when not dropped, the item will revert back to its initial position
         //					containment: $('#demo-frame').length ? '#demo-frame' : 'document', // stick to demo-frame if present
         //         helper: 'clone',
         helper: function(event){
            return $('<img src="../../../images/icons/go-up.png" width="16" height="16" title="Přesunout soubor do adresáře" alt="Přesunout soubor"/>');
         },

         cursor: 'move',
         start: function(event, ui) {
         }
      });
      $("#directoryList a").droppable({
         hoverClass: 'hover',
         over: function(event, ui) {},
         out: function(event, ui) {},
         drop: function(ev, ui) {
            // bacha provede stejně jako počet zanoření do kterého se vkládá
            var newDir = FileBrowserDirsFunctions.getPath($(this).parent('li'));
            var file = $(ui.draggable).children("input").val();
            $.ajax({
               type: "POST",
               url: FileBrowserFunctions.cmsURL+"movefile.html",
               data: ({
                  dir : FileBrowserDirsFunctions.currentPath,
                  newdir : newDir,
                  file : file
               }),
               dataType : 'json',
               cache: false,
               success: function(dataObj){
                  FileBrowserFunctions.showResult(dataObj.message);
                  if(dataObj.code == true){
                     FileBrowserFilesFunctions.loadFiles();
                  }
               },
               error : function(XMLHttpRequest, textStatus, errorThrown){
                  FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro přesun '+textStatus);
               }
            });
         }
      });

      $("tr.file").click(function () {
         $('tr.file').removeClass('clicked');
         $(this).addClass('clicked');
         $('#actualFile').html($(this).children("td").children('span.name').text());
         //         FileBrowserFilesFunctions.currentFilePath = $(this).children('td.icon')
         //         .children('a').children('img').attr('src').replace(/\?.*/, '', 'gi');
         FileBrowserFilesFunctions.currentFilePath = $(this).children('td.icon')
         .children('span').attr('file');
      });
      $("tr.file").dblclick(function () {
         //         FileBrowserFilesFunctions.currentFilePath = $(this).children('td.icon')
         //         .children('a').children('img').attr('src').replace(/\?.*/, '', 'gi');
         FileBrowserFilesFunctions.currentFilePath = $(this).children('td.icon')
         .children('span').attr('file');
         FileBrowserDialogue.submitFile();
      });
   },
   startUpload : function(){
      $('#newfileDir').attr('value', FileBrowserDirsFunctions.currentPath);
      $('#newfileType').attr('value', FileBrowserDialogue.listType);
      $('#upload_process').show();
      return true;
   },
   stopUpload : function(success){
      FileBrowserFunctions.showResult(success);
      $('#upload_process').hide();
      //         FileBrowserFilesFunctions.loadFiles(FileBrowserDirsFunctions.currentPath);
      FileBrowserFilesFunctions.loadFiles();
      return true;
   },
   imageEdit : function(file, path){
      tinyMCE.activeEditor.windowManager.open({
         file : FileBrowserFunctions.cmsPluginUrl+"browser/imagepreview.phtml" ,
         title : 'Image preview',
         width : 600,  // Your dimensions may differ - toy around with them!
         height : 500,
         resizable : "yes",
         inline : "no",  // This parameter only has an effect if you use the inlinepopups plugin!
         close_previous : "no"
      }, {
         file : file,
         path : path,
         cmsUrl : FileBrowserFunctions.cmsURL
      });
      return false;
   },
   /* Odstranění souboru */
   removeFile : function(fileName){
      tinyMCE.activeEditor.windowManager.confirm('Smazat soubor '+fileName+' ze složky '+FileBrowserDirsFunctions.currentPath+' ?', function(s) {
         if (s){
            $.ajax({
               type: "POST",
               url: FileBrowserFunctions.cmsURL+"removefile.html",
               data: ({
                  dir : FileBrowserDirsFunctions.currentPath,
                  file : fileName
               }),
               dataType : 'json',
               cache: false,
               success: function(dataObj){
                  FileBrowserFunctions.showResult(dataObj.message);
                  if(dataObj.code == true){
                     FileBrowserFilesFunctions.loadFiles();
                  }
               },
               error : function(){
                  FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro odstranění souboru');
               }
            });
         }
      });
      return false;
   },
   /* Odstranění zaškrtnutých souborů */
   removeCheckedFiles :function(){

   },
   /* Přejmenování souborů */
   renameFile : function(fileName){
      var newFileName = prompt("Přejmenovat soubor", fileName);
      if(newFileName == false) return false;
      if(newFileName == null || newFileName == ''){
         alert('Musíte zadat název');
         return false;
      } else {
         // dodělat kontrolu
         $.ajax({
            type: "POST",
            url: FileBrowserFunctions.cmsURL+"renamefile.html",
            data: ({
               path : FileBrowserDirsFunctions.currentPath,
               oldname : fileName,
               newname : newFileName
            }),
            cache: false,
            dataType : 'json',
            success: function(dataObj){
               FileBrowserFunctions.showResult(dataObj.message);
               if(dataObj.code == true){
                  FileBrowserFilesFunctions.loadFiles();
               }
            },
            error : function(){
               FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro přejmenování');
            }
         });
         return true;
      }
   }
   
}

var FileBrowserDirsFunctions = {
   tmpString : null,
   createRollBack : null,
   currentPath : null,
   nodeOperation : null,
   // načtení adresáře
   init : function(){
      var timestamp =  new Date().getTime();
      tree : $(function () {
         $("#directoryList").tree({
            data : {
               async : false,
               opts : {
                  url : FileBrowserFunctions.cmsURL+"getdirs.html?timestamp="+timestamp
               }
            },
            ui : { /* theme_name : "classic"*/ },
            plugins : {
               //contextmenu : { }
            },
            selected : 'rootDir',
            types : {
               "root" : {
                  draggable : false,
                  deletable : false,
                  creatable	: true
               },
               "child-edit" : {
                  draggable : true,
                  deletable : true,
                  renameable : true
               }
            },
            callback : {
               onsearch : function (n,t) {
                  t.container.find('.search').removeClass('search');
                  n.addClass('search');
               },
               onselect : function (node, treeNode) {
                  //               FileBrowserDirsFunctions.getDir(node);
                  //               $('#operationResult').html(FileBrowserDirsFunctions.getDir(node));
                  //               alert(FileBrowserDirsFunctions.path);
                  if(this.nodeOperation != 'create'){
                     FileBrowserDirsFunctions.setCurrentNode(node);
                     FileBrowserFilesFunctions.loadFiles();
                  }
               },
               onmove : function(node, ref_node, type, tree_obj, rollback){
                  $.ajax({
                     type: "POST",
                     url: FileBrowserFunctions.cmsURL+"movedir.js",
                     data: ({
                        newpath : FileBrowserDirsFunctions.getPath(node),
                        oldpath : FileBrowserDirsFunctions.tmpString
                     }),
                     dataType : 'json',
                     cache: false,
                     success: function(dataObj){
                        //                     alert(dataObj);
                        FileBrowserFunctions.showResult(dataObj.message);
                        if(dataObj.code == true){
                        } else {
                           $.tree.rollback(rollback);
                        }
                        // nastavení na aktuální prvek
                        FileBrowserDirsFunctions.setCurrentNode(node);
                     }
                  });
               },
               oncreate : function(node, ref_node, type, tree_obj, rb){
                  if(type == 'inside'){
                     FileBrowserDirsFunctions.tmpString = FileBrowserDirsFunctions.getPath(ref_node);
                  } else {
                     FileBrowserDirsFunctions.tmpString = FileBrowserDirsFunctions.currentPath;
                  }
                  this.nodeOperation = 'create';
                  FileBrowserDirsFunctions.createRollBack = rb;
               },
               beforerename : function(node,lang,treeNode){
                  if(this.nodeOperation != 'create'){
                     // pokud se jenom přejmenovává
                     FileBrowserDirsFunctions.tmpString = treeNode.get_text(node);
                  }
                  return true;
               },
               onrename : function(node, treeNode, rollback){
                  var newName = treeNode.get_text(node);
                  // pokud se vytváří podpoložka
                  if(this.nodeOperation == 'create'){
                     //                  alert(FileBrowserDirsFunctions.tmpString);
                     $.ajax({
                        type: "POST",
                        url: FileBrowserFunctions.cmsURL+"createdir.js",
                        data: ({
                           //                        path : FileBrowserDirsFunctions.currentPath,
                           path : FileBrowserDirsFunctions.tmpString,
                           dirname : newName
                        }),
                        dataType : 'json',
                        cache: false,
                        success: function(dataObj){
                           FileBrowserFunctions.showResult(dataObj.message);
                           if(dataObj.code == true){
                              FileBrowserDirsFunctions.createNodeContent(node, dataObj.data);
                              FileBrowserDirsFunctions.setCurrentNode(node);
                              FileBrowserFilesFunctions.loadFiles();
                           } else {
                              $.tree.rollback(rollback);
                              $.tree.rollback(FileBrowserDirsFunctions.createRollBack);
                           }
                           // nastavení na aktuální prvek
                           FileBrowserDirsFunctions.setCurrentNode(node);
                           this.nodeOperation = null;
                        }
                     });
                  }
                  // pokud se přejmenovává
                  else {
                     $.ajax({
                        type: "POST",
                        url: FileBrowserFunctions.cmsURL+"renamedir.js",
                        data: ({
                           path : FileBrowserDirsFunctions.getPath(node),
                           oldname : FileBrowserDirsFunctions.tmpString,
                           newname : newName
                        }),
                        dataType : 'json',
                        cache: false,
                        success: function(dataObj){
                           FileBrowserFunctions.showResult(dataObj.message);
                           if(dataObj.code == true){
                              FileBrowserDirsFunctions.createNodeContent(node, dataObj.data);
                              FileBrowserDirsFunctions.setCurrentNode(node);
                              FileBrowserFilesFunctions.loadFiles();
                           } else {
                              $.tree.rollback(rollback);
                           }
                        }
                     });
                  }

                  // doplnění parametrů a přejmenování na validní název
                  this.nodeOperation = null;
               },
               beforedelete : function(node){
                  FileBrowserDirsFunctions.tmpString = FileBrowserDirsFunctions.getPath(node);
                  if(confirm("Opravdu smazat adresář \""+FileBrowserDirsFunctions.tmpString+"\" ?")){
                     return true;
                  }
                  return false;
               },
               ondelete : function(node, treeObj, rollback){
                  $.ajax({
                     type: "POST",
                     url: FileBrowserFunctions.cmsURL+"removedir.js",
                     data: ({
                        dir : FileBrowserDirsFunctions.tmpString
                     }),
                     dataType : 'json',
                     cache: false,
                     success: function(dataObj){
                        FileBrowserFunctions.showResult(dataObj.message);
                        if(dataObj.code != true){
                           $.tree.rollback(rollback);
                        }
                     },
                     error : function(XMLHttpRequest, textStatus, errorThrown){
                        FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro smazání '+textStatus);
                     }
                  });
                  return true;
               }
            }
         })
      })
   },
   setCurrentNode : function(node){
      this.currentPath = this.getPath(node);
      $("#actualDir").html(this.currentPath);
   },
   getPath : function(node){
      var path = '';
      do {
         path = "/"+this.getNodeName(node)+path;
         node = $(node).parent("ul").parent('li');
      } while (node.length != 0);
      // zrušíme první lomítko
      path = path.substr(1, path.length-1);
      return path;
   },
   getNodeName : function(node){
      return $(node).children('a').html().replace("<ins>&nbsp;</ins>", "", "g")
   },
   createNodeContent : function(node, name){
      $(node).children('a').html('<ins>&nbsp</ins>'+name);
   },
   createDir : function(){
      var t = $.tree.focused();
      if(t.selected){
         t.create();
      } else {
         alert("Musíte označit adresář");
      }
   },
   removeDir : function(){
      var t = $.tree.focused();
      if(t.selected){
         t.remove();
      } else {
         alert("Musíte označit adresář");
      }
   },
   renameDir : function(){
      var t = $.tree.focused();
      if(t.selected){
         t.rename();
      } else {
         alert("Musíte označit adresář");
      }
   }
}
