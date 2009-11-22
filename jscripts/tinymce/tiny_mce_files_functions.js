var FileBrowserDialogue = {
   listType : null,
   win : null,
   init : function () {
      // Here goes your code for setting your custom things onLoad.
      FileBrowserDialogue.listType = tinyMCEPopup.getWindowArg("listType");
      FileBrowserDialogue.win = tinyMCEPopup.getWindowArg("window");
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
tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

var FileBrowserFunctions = {
   cmsURL : null,
   init : function(){
      this.cmsURL = window.location.toString();
      this.cmsURL = this.cmsURL.match(/(.*)\//g);
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
   imagePopup : function(file, filepath){
      var generator=window.open('','Image '+file,'height=500,width=670,scrollbars=yes');

      generator.document.write('<html><head><title>Image</title>');
      generator.document.write('<meta http-equiv="cache-control" content="no-cache" />\n\
<meta http-equiv="pragma" content="no-cache" />\n\
<meta http-equiv="pragma" content="-1" />');
      generator.document.write('</head><body>');
      generator.document.write('<p style="font-size:8pt;margin:0">Zoom: CTRL + +/CTRL + -</p>');
      generator.document.write('<p style="font-size:8pt;margin:0"><a href="javascript:self.close()" title="Close"><img src="'+filepath+'" alt="'+file+'"/><br/>');
      generator.document.write('Close</a></p>');
      generator.document.write('</body></html>');
      generator.document.close();
      return false;
   },
   /* Odstranění souboru */
   removeFile : function(fileName){
      if(confirm('Smazat soubor '+fileName+' ze složky '+FileBrowserDirsFunctions.currentPath+' ?')){
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
      return false;
   },
   /* Odstranění zaškrtnutých souborů */
   removeCheckedFiles :function(){

   },
   /* Přejmenování souborů */
   renameFile : function(fileName){
      var newFileName = prompt("Přejmenovat soubor", fileName);
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
   scaleImage : function(fileName){
      var size = prompt("Zadejte rozměry x=1,y=1 (např.: \"x=200,y=100\" rozměry jsou v pixelech,\
pokud zadáte jen jeden rozměr, druhý bude dopočítán podle poměru stran). Pokud přidáte\n\
slovo \"crop\", obrázek bude ořezán na požadovanou velikost.", "x=100,y=100");
      //      var height = prompt("Zadejte novou výšku (pokud není zadána zmenšuje se poměrově)");
      if(size == ''){
         alert("Musíte zadat rozměr");
         return false;
      }

      $.ajax({
         type: "POST",
         url: FileBrowserFunctions.cmsURL+"resizeimage.html",
         data: ({
            path : FileBrowserDirsFunctions.currentPath,
            file : fileName,
            size : size
         }),
         cache: false,
         dataType : 'json',
         success: function(dataObj){
            //            FileBrowserFunctions.showResult(dataObj);
            FileBrowserFunctions.showResult(dataObj.message);
            if(dataObj.code == true){
               FileBrowserFilesFunctions.loadFiles();
            }
         },
         error : function(XMLHttpRequest, textStatus, errorThrown){
            FileBrowserFunctions.showResult('Chyba při přijímání požadavku na změnu velikosti obrázku '+textStatus);
         }
      });

      return true;
   },
   rotateImage : function(fileName){
      var angle = prompt("Zadejte úhel otočení (pouze desetiná čísla). Pokud je \n\
uveden parametr bg=X, je nastavena i barva pozadí, kde za X se doplné kód barvy.");
      if(angle == ''){
         alert("Musíte zadat úhel");
         return false;
      }

      $.ajax({
         type: "POST",
         url: FileBrowserFunctions.cmsURL+"rotateimage.html",
         data: ({
            path : FileBrowserDirsFunctions.currentPath,
            file : fileName,
            angle : angle
         }),
         cache: false,
         dataType : 'json',
         success: function(dataObj){
            FileBrowserFunctions.showResult(dataObj.message);
            //            FileBrowserFunctions.showResult(dataObj);
            if(dataObj.code == true){
               FileBrowserFilesFunctions.loadFiles();
            }
         },
         error : function(){
            FileBrowserFunctions.showResult('Chyba při přijímání požadavku na změnu rotace obrázku');
         }
      });

      return true;
   },
   flipImage : function(fileName){
      var axis = prompt("Zadejte osu podle které se má obrázek otočit \"x\" nebo \"y\".", 'x');
      if(axis == false){
         return false;
      }
      if(axis == ''){
         alert("Musíte zadat osu");
         return false;
      }
      $.ajax({
         type: "POST",
         url: FileBrowserFunctions.cmsURL+"flipimage.html",
         data: ({
            path : FileBrowserDirsFunctions.currentPath,
            file : fileName,
            axis : axis
         }),
         cache: false,
         dataType : 'json',
         success: function(dataObj){
            FileBrowserFunctions.showResult(dataObj.message);
            //            FileBrowserFunctions.showResult(dataObj);
            if(dataObj.code == true){
               FileBrowserFilesFunctions.loadFiles();
            }
         },
         error : function(XMLHttpRequest, textStatus, errorThrown){
            FileBrowserFunctions.showResult('Chyba při přijímání požadavku na změnu zrcadlení obrázku '+textStatus);
         }
      });
      return true;
   }
}

var FileBrowserDirsFunctions = {
   tmpString : null,
   createRollBack : null,
   currentPath : null,
   nodeOperation : null,
   // načtení adresáře
   tree : $(function () {
      $("#directoryList").tree({
         data : {
            async : true,
            opts : {
               url : FileBrowserFunctions.cmsURL+"getdirs.html"
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
//               renameable : false
//               valid_children : [ "folder" ]
//               icon : {
//                  image : "../../../../images/toolbox/drive.png"
//               }
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
   }),
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
