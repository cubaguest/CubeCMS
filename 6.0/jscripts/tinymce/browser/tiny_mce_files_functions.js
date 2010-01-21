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
   },
   showResult : function(msg, type){
      if(typeof(type) == "undefined"){
         type = true
      }
      if(type == true){
         $('#operationResult').html('<span class="msg">'+msg+'<\/span>');
      } else {
         $('#operationResult').html('<span class="errmsg">'+msg+'<\/span>');
      }
   },
   showButtons : function(show){
      if(show == true){
         $('#buttonCreateDir').removeAttr('disabled').css({
            opacity : 1
         });
         $('#buttonDeleteDir').removeAttr('disabled').css({
            opacity : 1
         });
         $('#buttonRenameDir').removeAttr('disabled').css({
            opacity : 1
         });
      } else {
         $('#buttonCreateDir').attr('disabled', 'disabled').css({
            opacity : 0.5
         });
         $('#buttonDeleteDir').attr('disabled', 'disabled').css({
            opacity : 0.5
         });
         $('#buttonRenameDir').attr('disabled', 'disabled').css({
            opacity : 0.5
         });
      }
   }
}

var FileBrowserFilesFunctions = {
   currentFilePath : null,
   selectedFileName : null,
   selectedFiles : new Array(),
   selectedFilesPaths : new Array(),
   selectingMany : false,
   loadFiles : function (){
      $("#fileList").html('<img src="../../../images/progress.gif" alt="loading..." width="16"/>');
      $.ajax({
         type: "GET",
         url: FileBrowserFunctions.cmsURL+"getfiles.html?dir="+escape(FileBrowserDirsFunctions.currentPath)+"&type="+FileBrowserDialogue.listType+"&next=dalse",
         cache: false,
         success: function(data){
            $("#fileList").html(data);
            // smaz pole se soubor
            FileBrowserFilesFunctions.selectedFiles = new Array();
            FileBrowserFilesFunctions.renderSelectedFile();
            FileBrowserFilesFunctions.addEvents();
         }
      });
   },
   // eventy pro práci se soubory
   addEvents : function (){
      $(".dragableFile").draggable({
         //         opacity: 0.7,
         //         stack: { group: 'mainTable', min: 1000 },
         //					cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
//         revert: 'invalid', // when not dropped, the item will revert back to its initial position
         //					containment: $('#demo-frame').length ? '#demo-frame' : 'document', // stick to demo-frame if present
         //                  helper: 'clone',
         cursorAt: {
            cursor: 'move',
            top: 0,
            left: -25
         },
         helper: function(event){
            var file = null;
            if(FileBrowserFilesFunctions.selectedFiles.length > 0 && $(this).hasClass('fileSelected')){
               file = FileBrowserFilesFunctions.selectedFiles.join(', ');
            } else {
               file = $(this).find('span.name').text();
            }
            //            return $('<img src="../../../images/icons/go-up.png" width="16" height="16" title="Přesunout soubor do adresáře" alt="Přesunout soubor"/>');
            return $('<div class="moving">'+file+'</div>');
         }
      });
      $("#directoryList a").droppable({
         hoverClass: 'hover',
         tolerance: 'pointer',
         over: function(event, ui) {
//            $.tree.focused().open_branch($(this).parent('li'));
         },
         out: function(event, ui) {
//            $.tree.focused().close_branch($(this).parent('li'));
         },
         drop: function(ev, ui) {
            var uiobj = $(ui.draggable[0]);
            var files = null;
            // pokud není vybrán soubor
            if(!uiobj.hasClass('fileSelected')){
               files = uiobj.find('span.name').text();
            } else {
               files = FileBrowserFilesFunctions.selectedFiles.join(';');
            }
            var newDir = $(this).parent('li').attr('title');
            $.ajax({
               type: "POST",
               url: FileBrowserFunctions.cmsURL+"movefile.html",
               data: ({
                  dir : FileBrowserDirsFunctions.currentPath,
                  newdir : newDir,
                  file : files
               }),
               dataType : 'json',
               cache: false,
               success: function(dataObj){
                  FileBrowserFunctions.showResult(dataObj.message, dataObj.code);
                  if(dataObj.code == true){
                     FileBrowserFilesFunctions.loadFiles();
                  }
               },
               error : function(XMLHttpRequest, textStatus, errorThrown){
                  FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro přesun '+textStatus, false);
               }
            });
         }
      });
      // CTRL
      // ONE CLICK
      $("li.file").click(function (event) {
         if(event.metaKey == false){
            FileBrowserFilesFunctions.selectedFiles = new Array();
            $('li.file').removeClass('fileSelected');
            if(!$(this).hasClass('fileSelected')){
               $(this).addClass('fileSelected');
               FileBrowserFilesFunctions.addSelFile($(this).find('span.name').text(),
                  $(this).children('p.filePreview').find('img').attr('src'));
            }
         } else {
            if($(this).hasClass('fileSelected')){
               $(this).removeClass('fileSelected');
               FileBrowserFilesFunctions.removeSelFile($(this).find('span.name').text());
            } else {
               $(this).addClass('fileSelected');
               FileBrowserFilesFunctions.addSelFile($(this).find('span.name').text(),
                  $(this).children('p.filePreview').find('img').attr('src'));
            }
         }

      //         $('#actualFile').html($(this).children("p").children('span.name').text());
      //         FileBrowserFilesFunctions.currentFilePath = $(this).children('p.filePreview')
      //         .children('span').attr('file');

      });
      // HOVER
      $("li.file").mouseover(function () {
         $('li.file').removeClass('fileHover');
         $(this).addClass('fileHover');
      });
      // DBL click
      $("li.file").dblclick(function () {
         FileBrowserFilesFunctions.currentFilePath = $(this).children('p.filePreview')
         .find('img').attr('src');
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
               url: FileBrowserFunctions.cmsURL+"removefile.php",
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
   removeSelectedFiles :function(){
      if(FileBrowserFilesFunctions.selectedFiles.length == 0){
         return false;
      }
      tinyMCE.activeEditor.windowManager.confirm('Smazat soubory '+FileBrowserFilesFunctions.selectedFiles.join(', ') +' ze složky '+FileBrowserDirsFunctions.currentPath+' ?', function(s) {
         if (s){
            $.ajax({
               type: "POST",
               url: FileBrowserFunctions.cmsURL+"removefiles.php",
               data: ({
                  dir : FileBrowserDirsFunctions.currentPath,
                  files : FileBrowserFilesFunctions.selectedFiles.join(';')
               }),
               dataType : 'json',
               cache: false,
               success: function(dataObj){
                  FileBrowserFunctions.showResult(dataObj.message, true);
                  if(dataObj.code == true){
                     FileBrowserFilesFunctions.loadFiles();
                  }
               },
               error : function(){
                  FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro odstranění souborů', false);
               }
            });
         }
      });
      return false;
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
   },

   addSelFile : function(file,path){
      this.selectedFiles.push(file);
      this.selectedFilesPaths.push(path);
      this.renderSelectedFile();
   },
   removeSelFile : function(file){
      for(var i=0; i<this.selectedFiles.length; i++){
         if(this.selectedFiles[i] == file){
            this.selectedFiles.splice(i,1);
            this.selectedFilesPaths.splice(i,1);
         }
      }
      this.renderSelectedFile();
   },
   renderSelectedFile :function(){
      if (this.selectedFiles.length >= 1){
         $('#actualFile').html(FileBrowserFilesFunctions.selectedFiles[FileBrowserFilesFunctions.selectedFiles.length-1]);
         $('#actualFiles').html(FileBrowserFilesFunctions.selectedFiles.length);
         this.currentFilePath = FileBrowserFilesFunctions.selectedFilesPaths[FileBrowserFilesFunctions.selectedFilesPaths.length-1];
      } else {
         $('#actualFile').html('žádný');
         $('#actualFiles').html("0");
         this.currentFilePath = null;
      }
   }
   
}

var FileBrowserDirsFunctions = {
   parentNodePath : null,
   rollBackFunction : null,
   currentPath : null,
   createAction : false,
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
                  contextmenu : { }
               },
               selected : 'rootDir',
               types : {
                  "root" : {
                     draggable : false,
                     deletable : false,
                     icon : {
                        image : "drive.png"
                     }
                  },
                  "editable" : {
                  },
                  "readable" : {
                     deletable : false,
                     renameable : false,
                     draggable : false,
                     creatable : false,
                     valid_children : null,
                     icon : {
                        image : "noedit.png"
                     }
                  }
               },
               callback : {
                  onsearch : function (n,t) {
                     t.container.find('.search').removeClass('search');
                     n.addClass('search');
                  },
                  onselect : function (node, treeNode) {
                     FileBrowserDirsFunctions.setPath($(node).attr('title'));
                     // vypnutí nebo zapnutí buttonů
                     if($(node).attr('rel') == 'readable' || $(node).attr('rel') == 'root'){
                        FileBrowserFunctions.showButtons(false);
                     } else {
                        FileBrowserFunctions.showButtons(true);
                     }
                     if(FileBrowserDirsFunctions.createAction != true){
                        FileBrowserFilesFunctions.loadFiles();
                     }
                  },
                  onmove : function(node, ref_node, type, tree_obj, rollback){
                     var oldpath = null;
                     var newpath = null;
                     if(type == 'inside'){
                        oldpath = $(node).attr('title');
                        newpath = $(ref_node).attr('title');
                     } else {
                        oldpath = $(node).attr('title');
                        newpath = $(ref_node).parent('ul').parent('li').attr('title');
                     }
                     $.ajax({
                        type: "POST",
                        url: FileBrowserFunctions.cmsURL+"movedir.php",
                        data: ({
                           newpath : newpath,
                           oldpath : oldpath
                        }),
                        dataType : 'json',
                        cache: false,
                        success: function(dataObj){
                           FileBrowserFunctions.showResult(dataObj.message, dataObj.code);
                           if(dataObj.code == true){
                              // projití všech elementy uvnitř a přepsání cest
                              var regexp = new RegExp('^'+dataObj.dataold+'(\/|$)');
                              $('li[rel=editable]').each(function(){
                                 var elem = $(this).attr('title', $(this).attr('title').replace(regexp, dataObj.data+'$1'));
                              });

                           } else {
                              $.tree.rollback(rollback);
                           }
                        },
                        error : function(XMLHttpRequest, textStatus, errorThrown){
                           $.tree.rollback(rollback);
                           FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro přesun '+textStatus, false);
                        }
                     });
                  },
                  oncreate : function(node, ref_node, type, tree_obj, rb){
                     FileBrowserDirsFunctions.createAction = true;
                     FileBrowserDirsFunctions.rollBackFunction = rb;
                  },
                  beforerename : function(node){
                     // pokud neobsahuje cestu a není kořenový adrresář nelze jej upravit
                     if($(node).is('#rootDir')){
                        alert('kořenový prvek nelze přejmenovat');
                        return false;
                     }
                     return true;
                  },
                  onrename : function(node, treeNode, rollback){
                     var newName = treeNode.get_text(node);
                     var path = $(node).parent("ul").parent('li').attr('title');
                     var oldName = $(node).attr('title');
                     //                pokud se vytváří podpoložka
                     if(FileBrowserDirsFunctions.createAction == true){
                        $.ajax({
                           type: "POST",
                           url: FileBrowserFunctions.cmsURL+"createdir.php",
                           data: ({
                              sessionid : sessionId,
                              path : path,
                              dirname : newName
                           }),
                           dataType : 'json',
                           cache: false,
                           success: function(dataObj){
                              FileBrowserFunctions.showResult(dataObj.message, dataObj.code);
                              if(dataObj.code == true){
                                 // nastavení node
                                 $(node).attr('title', path+'/'+dataObj.data).attr('rel', 'editable');
                                 FileBrowserDirsFunctions.setNodeContent(node, dataObj.data);
                                 FileBrowserDirsFunctions.setPath(path+'/'+dataObj.data);
                                 FileBrowserFilesFunctions.loadFiles();
                              } else {
                                 //                                 $.tree.rollback(rollback);
                                 $.tree.rollback(FileBrowserDirsFunctions.rollBackFunction);
                              }
                           },
                           error : function(XMLHttpRequest, textStatus, errorThrown){
                              //                              $.tree.rollback(rollback);
                              $.tree.rollback(FileBrowserDirsFunctions.rollBackFunction);
                              FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro vytvoření '+textStatus, false);
                           }
                        });
                     }
                     // pokud se přejmenovává
                     else {
                        $.ajax({
                           type: "POST",
                           url: FileBrowserFunctions.cmsURL+"renamedir.php",
                           data: ({
                              path : path,
                              oldname : oldName,
                              newname : newName
                           }),
                           dataType : 'json',
                           cache: false,
                           success: function(dataObj){
                              FileBrowserFunctions.showResult(dataObj.message, dataObj.code);
                              if(dataObj.code == true){
                                 $(node).attr('title', path+'/'+dataObj.data).attr('rel', 'editable');
                                 FileBrowserDirsFunctions.setNodeContent(node, dataObj.data);
                                 FileBrowserDirsFunctions.setPath(path+'/'+dataObj.data);
                                 FileBrowserFilesFunctions.loadFiles();
                              } else {
                                 $.tree.rollback(rollback);
                              }
                           },
                           error : function(XMLHttpRequest, textStatus, errorThrown){
                              $.tree.rollback(rollback);
                              FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro přejmenování '+textStatus, false);
                           }
                        });
                     }
                     // reset create
                     FileBrowserDirsFunctions.createAction = false;
                     FileBrowserFunctions.showButtons(true);
                  },
                  beforedelete : function(node){
                     // TODO předělat na timce dialog (nevím jak vracet true s toho dialogu)
                     if(confirm("Opravdu smazat adresář \""+$(node).attr('title')+"\" ?")){
                        return true;
                     }
                     return false;
                  },
                  ondelete : function(node, treeObj, rollback){
                     var path = $(node).attr('title');
                     $.ajax({
                        type: "POST",
                        url: FileBrowserFunctions.cmsURL+"removedir.php",
                        data: ({
                           dir : path
                        }),
                        dataType : 'json',
                        cache: false,
                        success: function(dataObj){
                           FileBrowserFunctions.showResult(dataObj.message, dataObj.code);
                           if(dataObj.code != true){
                              $.tree.rollback(rollback);
                           }
                        },
                        error : function(XMLHttpRequest, textStatus, errorThrown){
                           $.tree.rollback(rollback);
                           FileBrowserFunctions.showResult('Chyba při přijímání požadavku pro smazání '+textStatus, false);
                        }
                     });
                     return true;
                  }
               }
            })
         })
   },
   getPath : function(node){
      return $(node).attr('title');
   },
   setPath : function(path){
      $("#actualDir").html(path);
      this.currentPath = path;
   },
   setNodeContent : function(node, name){
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
