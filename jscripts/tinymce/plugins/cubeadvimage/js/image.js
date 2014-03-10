var ImageDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var f = document.forms[0], nl = f.elements, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode(), fl = tinyMCEPopup.getParam('external_image_list', 'tinyMCEImageList');

		tinyMCEPopup.resizeToInnerSize();
		this.fillClassList('class_list');
		this.fillFileList('src_list', fl);
		this.fillFileList('over_list', fl);
		this.fillFileList('out_list', fl);
		TinyMCE_EditableSelects.init();
		TinyMCEFileUploader.init();

		if (n.nodeName == 'IMG') {
			nl.src.value = dom.getAttrib(n, 'src');
			nl.width.value = dom.getAttrib(n, 'width');
			nl.height.value = dom.getAttrib(n, 'height');
			nl.alt.value = dom.getAttrib(n, 'alt');
			nl.title.value = dom.getAttrib(n, 'title');
			nl.vtspace.value = this.getAttrib(n, 'vtspace');
			nl.vbspace.value = this.getAttrib(n, 'vbspace');
			nl.hlspace.value = this.getAttrib(n, 'hlspace');
			nl.hrspace.value = this.getAttrib(n, 'hrspace');
			nl.border.value = this.getAttrib(n, 'border');
			selectByValue(f, 'align', this.getAttrib(n, 'align'));
			selectByValue(f, 'class_list', dom.getAttrib(n, 'class'), true, true);
			nl.style.value = dom.getAttrib(n, 'style');
			nl.id.value = dom.getAttrib(n, 'id');
			nl.dir.value = dom.getAttrib(n, 'dir');
			nl.lang.value = dom.getAttrib(n, 'lang');
			nl.usemap.value = dom.getAttrib(n, 'usemap');
			nl.longdesc.value = dom.getAttrib(n, 'longdesc');
			nl.insert.value = ed.getLang('update');

			if (/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/.test(dom.getAttrib(n, 'onmouseover')))
				nl.onmouseoversrc.value = dom.getAttrib(n, 'onmouseover').replace(/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/, '$1');

			if (/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/.test(dom.getAttrib(n, 'onmouseout')))
				nl.onmouseoutsrc.value = dom.getAttrib(n, 'onmouseout').replace(/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/, '$1');

			if (ed.settings.inline_styles) {
				// Move attribs to styles
				if (dom.getAttrib(n, 'align'))
					this.updateStyle('align');

				if (dom.getAttrib(n, 'hlspace'))
					this.updateStyle('hlspace');
				if (dom.getAttrib(n, 'hrspace'))
					this.updateStyle('hrspace');

				if (dom.getAttrib(n, 'border'))
					this.updateStyle('border');

				if (dom.getAttrib(n, 'vtspace'))
					this.updateStyle('vtspace');
				if (dom.getAttrib(n, 'vbspace'))
					this.updateStyle('vbspace');
			}
			TinyMCEFileUploader.disableUplaod();
		}

		// Setup browse button
		document.getElementById('srcbrowsercontainer').innerHTML = getBrowserHTML('srcbrowser','src','image','theme_advanced_image');
		if (isVisible('srcbrowser'))
			document.getElementById('src').style.width = '260px';

		// Setup browse button
		document.getElementById('onmouseoversrccontainer').innerHTML = getBrowserHTML('overbrowser','onmouseoversrc','image','theme_advanced_image');
		if (isVisible('overbrowser'))
			document.getElementById('onmouseoversrc').style.width = '260px';

		// Setup browse button
		document.getElementById('onmouseoutsrccontainer').innerHTML = getBrowserHTML('outbrowser','onmouseoutsrc','image','theme_advanced_image');
		if (isVisible('outbrowser'))
			document.getElementById('onmouseoutsrc').style.width = '260px';

		// If option enabled default contrain proportions to checked
		if (ed.getParam("advimage_constrain_proportions", true))
			f.constrain.checked = true;

		// Check swap image if valid data
		if (nl.onmouseoversrc.value || nl.onmouseoutsrc.value)
			this.setSwapImage(true);
		else
			this.setSwapImage(false);

		this.changeAppearance();
		this.showPreviewImage(nl.src.value, 1);
	},
   /* vkládá obrázek */
	insert : function(uploadComplete) {
		var ed = tinyMCEPopup.editor, t = this, f = document.forms[0];
      
//      if (f.uploadFile.value !== '') {
//         if( !confirm(tinyMCEPopup.getLang('cubeadvimage_dlg.file_is_not_uploaded'))) {
//            return;
//         }
//      }

		if (f.src.value === '') {
			if (ed.selection.getNode().nodeName == 'IMG') {
				ed.dom.remove(ed.selection.getNode());
				ed.execCommand('mceRepaint');
			}

			tinyMCEPopup.close();
			return;
		}

		if (tinyMCEPopup.getParam("accessibility_warnings", 1)) {
			if (!f.alt.value) {
				tinyMCEPopup.confirm(tinyMCEPopup.getLang('cubeadvimage_dlg.missing_alt'), function(s) {
					if (s)
						t.insertAndClose();
				});

				return;
			}
		}

		t.insertAndClose();
	},

	insertAndClose : function() {
		var ed = tinyMCEPopup.editor, f = document.forms[0], nl = f.elements, v, args = {}, el;

		tinyMCEPopup.restoreSelection();

		// Fixes crash in Safari
		if (tinymce.isWebKit)
			ed.getWin().focus();

		if (!ed.settings.inline_styles) {
			args = {
				vtspace : nl.vtspace.value,
				vbspace : nl.vbspace.value,
				hlspace : nl.hlspace.value,
				hrspace : nl.hrspace.value,
				border : nl.border.value,
				align : getSelectValue(f, 'align')
			};
		} else {
			// Remove deprecated values
			args = {
				vtspace : '',
				vbspace : '',
				hlspace : '',
				hrspace : '',
				border : '',
				align : ''
			};
		}

		tinymce.extend(args, {
			src : nl.src.value.replace(/ /g, '%20'),
			width : nl.width.value,
			height : nl.height.value,
			alt : nl.alt.value,
			title : nl.title.value,
			'class' : getSelectValue(f, 'class_list'),
			style : nl.style.value,
			id : nl.id.value,
			dir : nl.dir.value,
			lang : nl.lang.value,
			usemap : nl.usemap.value,
			longdesc : nl.longdesc.value
		});

		args.onmouseover = args.onmouseout = '';

		if (f.onmousemovecheck.checked) {
			if (nl.onmouseoversrc.value)
				args.onmouseover = "this.src='" + nl.onmouseoversrc.value + "';";

			if (nl.onmouseoutsrc.value)
				args.onmouseout = "this.src='" + nl.onmouseoutsrc.value + "';";
		}

		el = ed.selection.getNode();

		if (el && el.nodeName == 'IMG') {
			ed.dom.setAttribs(el, args);
		} else {
			tinymce.each(args, function(value, name) {
				if (value === "") {
					delete args[name];
				}
			});

         var cnt;
         // if preview create A and append IMG
         if(f.srcFull.value != null && f.cai_createThumbnail.checked == true){
            var img = tinyMCEPopup.editor.dom.createHTML('img', args);
            cnt = tinyMCEPopup.dom.createHTML('a', {
               'href' : f.srcFull.value,
               'class' : "pirobox preview",
               'rel' : "lightbox",
               'title' : (args.title ? args.title : (args.alt ? args.alt : ""))}, img);
            
            ed.execCommand('mceInsertContent', true, cnt, {skip_undo : 1});
         } else {
            ed.execCommand('mceInsertContent', true, tinyMCEPopup.editor.dom.createHTML('img', args), {skip_undo : 1});
            
         }

			ed.undoManager.add();
		}

		ed.execCommand('mceRepaint');
		ed.focus();
		tinyMCEPopup.close();
	},

   toggleResizeParams : function(){
      var form = document.forms[0];
      if(form.cai_resize.checked){
         tinyMCEPopup.dom.setStyle("resize_params", "display", "block");
      } else {
         tinyMCEPopup.dom.setStyle("resize_params", "display", "none");
      }
   },
   
   togglePreviewParams : function(){
      var form = document.forms[0];
      if(form.cai_createThumbnail.checked){
         tinyMCEPopup.dom.setStyle("thumb_params", "display", "block");
      } else {
         tinyMCEPopup.dom.setStyle("thumb_params", "display", "none");
      }
   },
   
	getAttrib : function(e, at) {
		var ed = tinyMCEPopup.editor, dom = ed.dom, v, v2;

		if (ed.settings.inline_styles) {
			switch (at) {
				case 'align':
					if (v = dom.getStyle(e, 'float'))
						return v;
					if (v = dom.getStyle(e, 'vertical-align'))
						return v;
					break;
				case 'hlspace':
               if (v = dom.getStyle(e, 'margin-left'))
      				return parseInt(v.replace(/[^0-9]/g, ''));
					break;
				case 'hrspace':
               if (v = dom.getStyle(e, 'margin-right'))
      				return parseInt(v.replace(/[^0-9]/g, ''));
					break;
				case 'vtspace':
               if (v = dom.getStyle(e, 'margin-top'))
      				return parseInt(v.replace(/[^0-9]/g, ''));
					break;
				case 'vbspace':
               if (v = dom.getStyle(e, 'margin-bottom'))
      				return parseInt(v.replace(/[^0-9]/g, ''));
					break;
				case 'border':
					v = 0;
					tinymce.each(['top', 'right', 'bottom', 'left'], function(sv) {
						sv = dom.getStyle(e, 'border-' + sv + '-width');
						// False or not the same as prev
						if (!sv || (sv != v && v !== 0)) {
							v = 0;
							return false;
						}
						if (sv)
							v = sv;
					});
					if (v)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;
			}
		}

		if (v = dom.getAttrib(e, at))
			return v;
		return '';
	},

	setSwapImage : function(st) {
		var f = document.forms[0];

		f.onmousemovecheck.checked = st;
		setBrowserDisabled('overbrowser', !st);
		setBrowserDisabled('outbrowser', !st);

		if (f.over_list)
			f.over_list.disabled = !st;

		if (f.out_list)
			f.out_list.disabled = !st;

		f.onmouseoversrc.disabled = !st;
		f.onmouseoutsrc.disabled  = !st;
	},

	fillClassList : function(id) {
		var dom = tinyMCEPopup.dom, lst = dom.get(id), v, cl;

		if (v = tinyMCEPopup.getParam('theme_advanced_styles')) {
			cl = [];

			tinymce.each(v.split(';'), function(v) {
				var p = v.split('=');

				cl.push({'title' : p[0], 'class' : p[1]});
			});
		} else
			cl = tinyMCEPopup.editor.dom.getClasses();

		if (cl.length > 0) {
			lst.options.length = 0;
			lst.options[lst.options.length] = new Option(tinyMCEPopup.getLang('not_set'), '');

			tinymce.each(cl, function(o) {
				lst.options[lst.options.length] = new Option(o.title || o['class'], o['class']);
			});
		} else
			dom.remove(dom.getParent(id, 'tr'));
	},

	fillFileList : function(id, l) {
		var dom = tinyMCEPopup.dom, lst = dom.get(id), v, cl;

		l = typeof(l) === 'function' ? l() : window[l];
		lst.options.length = 0;

		if (l && l.length > 0) {
			lst.options[lst.options.length] = new Option('', '');

			tinymce.each(l, function(o) {
				lst.options[lst.options.length] = new Option(o[0], o[1]);
			});
		} else
			dom.remove(dom.getParent(id, 'tr'));
	},

	resetImageData : function() {
		var f = document.forms[0];

		f.elements.width.value = f.elements.height.value = '';
	},

	updateImageData : function(img, st) {
		var f = document.forms[0];

		if (!st) {
			f.elements.width.value = img.width;
			f.elements.height.value = img.height;
		}

		this.preloadImg = img;
	},

	changeAppearance : function() {
		var ed = tinyMCEPopup.editor, f = document.forms[0], img = document.getElementById('alignSampleImg');

		if (img) {
			if (ed.getParam('inline_styles')) {
				ed.dom.setAttrib(img, 'style', f.style.value);
			} else {
				img.align = f.align.value;
				img.border = f.border.value;
				img.hlspace = f.hlspace.value;
				img.hrspace = f.hrspace.value;
				img.vtspace = f.vtspace.value;
				img.vbspace = f.vbspace.value;
			}
		}
	},

   changeHeight : function() {
      var f = document.forms[0], tp, t = this;

      if (!f.constrain.checked || !t.preloadImg) {
         return;
      }

      if (f.width.value == "" || f.height.value == "")
         return;

      tp = (parseInt(f.width.value) / parseInt(t.preloadImg.width)) * t.preloadImg.height;
      f.height.value = tp.toFixed(0);
   },

	changeWidth : function() {
		var f = document.forms[0], tp, t = this;

		if (!f.constrain.checked || !t.preloadImg) {
			return;
		}

		if (f.width.value == "" || f.height.value == "")
			return;

		tp = (parseInt(f.height.value) / parseInt(t.preloadImg.height)) * t.preloadImg.width;
		f.width.value = tp.toFixed(0);
	},

	updateStyle : function(ty) {
		var dom = tinyMCEPopup.dom, b, bStyle, bColor, v, isIE = tinymce.isIE, f = document.forms[0], img = dom.create('img', {style : dom.get('style').value});

		if (tinyMCEPopup.editor.settings.inline_styles) {
			// Handle align
			if (ty == 'align') {
				dom.setStyle(img, 'float', '');
				dom.setStyle(img, 'vertical-align', '');

				v = getSelectValue(f, 'align');
				if (v) {
					if (v == 'left' || v == 'right')
						dom.setStyle(img, 'float', v);
					else
						img.style.verticalAlign = v;
				}
			}

			// Handle border
			if (ty == 'border') {
				b = img.style.border ? img.style.border.split(' ') : [];
				bStyle = dom.getStyle(img, 'border-style');
				bColor = dom.getStyle(img, 'border-color');

				dom.setStyle(img, 'border', '');

				v = f.border.value;
				if (v || v == '0') {
					if (v == '0')
						img.style.border = isIE ? '0' : '0 none none';
					else {
					   var isOldIE = tinymce.isIE && (!document.documentMode || document.documentMode < 9);

                  if (b.length == 3 && b[isOldIE ? 2 : 1])
                     bStyle = b[isOldIE ? 2 : 1];
                  else if (!bStyle || bStyle == 'none')
                     bStyle = 'solid';
                  if (b.length == 3 && b[isIE ? 0 : 2])
                     bColor = b[isOldIE ? 0 : 2];
                  else if (!bColor || bColor == 'none')
                     bColor = 'black';
                  img.style.border = v + 'px ' + bStyle + ' ' + bColor;
					}
				}
			}

			// Handle hlspace left
			if (ty == 'hlspace') {
				dom.setStyle(img, 'marginLeft', '');

				v = f.hlspace.value;
				if (v) {
					img.style.marginLeft = v + 'px';
				}
			}
			// Handle hrspace right
			if (ty == 'hrspace') {
				dom.setStyle(img, 'marginRight', '');

				v = f.hrspace.value;
				if (v) {
					img.style.marginRight = v + 'px';
				}
			}

			// Handle vtspace
			if (ty == 'vtspace') {
				dom.setStyle(img, 'marginTop', '');

				v = f.vtspace.value;
				if (v) {
					img.style.marginTop = v + 'px';
				}
			}
			// Handle vbspace
			if (ty == 'vbspace') {
				dom.setStyle(img, 'marginBottom', '');

				v = f.vbspace.value;
				if (v) {
					img.style.marginBottom = v + 'px';
				}
			}

			// Merge
			dom.get('style').value = dom.serializeStyle(dom.parseStyle(img.style.cssText), 'img');
		}
	},

	changeMouseMove : function() {
	},

	showPreviewImage : function(u, st) {
		if (!u) {
			tinyMCEPopup.dom.setHTML('prev', '');
			return;
		}

		if (!st && tinyMCEPopup.getParam("advimage_update_dimensions_onchange", true))
			this.resetImageData();

		u = tinyMCEPopup.editor.documentBaseURI.toAbsolute(u);

		if (!st)
			tinyMCEPopup.dom.setHTML('prev', '<img id="previewImg" src="' + u + '" border="0" onload="ImageDialog.updateImageData(this);" onerror="ImageDialog.resetImageData();" />');
		else
			tinyMCEPopup.dom.setHTML('prev', '<img id="previewImg" src="' + u + '" border="0" onload="ImageDialog.updateImageData(this, 1);" />');
	}
};

var TinyMCEFileUploader = {
   uploadUrl : "/component/tinymce_uploader/0/imageUpload.php",
   dirListUrl : "/component/tinymce_uploader/0/dirsList.json",
   init : function(){
     this.uploadUrl = "/component/tinymce_uploader/"+tinyMCE.activeEditor.getParam('cid')+"/imageUpload.php";
     this.dirListUrl = "/component/tinymce_uploader/"+tinyMCE.activeEditor.getParam('cid')+"/dirsList.json";
	  var aDirs = tinyMCE.activeEditor.getParam('alloweddirs');
	  if(aDirs && aDirs.length > 0){
		  var dirsStr = '?';
		  for (var i = 0; i < aDirs.length; i++){
			  dirsStr+= 'allowDirs[]='+escape(aDirs[i]);
			  if(i+1 < aDirs.length){
				  dirsStr+="&";
			  }
		  }
		  this.uploadUrl+=dirsStr;
		  this.dirListUrl+=dirsStr;
	  }

      this.loadUploadDirs();
      document.getElementById("uploading_progress").style.visibility = "hidden";
   },
   
   disableUplaod : function(){
      // hide uplaod if edit image_desc
		document.getElementById("upload_tab").style.display = "none";
   },
   
   upload : function(){
      var form = document.forms[0];
      // check if file is empty
      
      // show progress
      document.getElementById("uploading_progress").style.visibility = "visible";
      
      // Set properties of form...
      form.setAttribute("target", "upload_iframe");
      form.setAttribute("action", TinyMCEFileUploader.uploadUrl+"?t="+(new Date().getTime()));
      form.setAttribute("method", "post");
      form.setAttribute("enctype", "multipart/form-data");
      form.setAttribute("encoding", "multipart/form-data");
 
      // Submit the form...
      form.submit();
      return;
   },
   
   uploadDone : function(data){
      var form = document.forms[0];
      
      tinyMCEPopup.dom.setStyle("uploading_progress", "visibility", "hidden");
   
      var statusBar = tinyMCEPopup.dom.get("upload_status");
      tinyMCEPopup.dom.removeClass(statusBar, "err");
      // show status
      if(data.infoMsg.length > 0 ){
         tinyMCEPopup.dom.setHTML(statusBar, data.infoMsg[0]);
      }
      if(data.errMsg.length > 0 ){
         tinyMCEPopup.dom.setHTML(statusBar, data.errMsg[0]);
         tinyMCEPopup.dom.addClass(statusBar, "err");
         return;
      }
      
      // add file url
      form.src.value = data.imageUrl;
      ImageDialog.showPreviewImage(data.imageUrl);
      form.srcFull.value = data.imageFullUrl;
      // change tab
      mcTabs.displayTab('general_tab','general_panel');
   },
   
   loadUploadDirs : function(){
      tinyMCE.util.XHR.send({
         url : TinyMCEFileUploader.dirListUrl,
         success : function(result) {
            var data = tinyMCE.util.JSON.parse(result);
            if(data.errmsg.length > 0 ){
               alert("Error: Cannot load directories");
            }
            var forceDir = tinyMCE.activeEditor.getParam('forcedir');
            // allowed
            if(data.dirsAllowed && data.dirsAllowed.length > 0 ){
               for (var i = 0; i < data.dirsAllowed.length; i++) {
            	  var attribs = {value : data.dirsAllowed[i]};
            	  if(forceDir == data.dirsAllowed[i]){
            		  attribs.selected = "selected";
            	  }
            	  tinyMCEPopup.dom.add('dirs-list-allowed', 'option', attribs, data.dirsAllowed[i]);
               }
            } else if(forceDir != null) {
               var attribs = {value : forceDir, selected : "selected" };
               tinyMCEPopup.dom.add('dirs-list-allowed', 'option', attribs, forceDir);
            } else {
            	tinyMCEPopup.dom.remove('dirs-list-allowed');
            }
            
            // public
            if(data.dirsPublic && data.dirsPublic.length > 0 ){
            	for (var i = 0; i < data.dirsPublic.length; i++) {
            	   var attribs = {value : data.dirsPublic[i]};
            	   if(forceDir == data.dirsPublic[i]){
            	      attribs.selected = "selected";
            	   }
            	   tinyMCEPopup.dom.add('dirs-list-public', 'option', attribs, data.dirsPublic[i]);
            	}
            } else {
            	tinyMCEPopup.dom.remove('dirs-list-public');
            }
            
            // home
            if(data.dirsHome.length > 0 ){
               for (var i = 0; i < data.dirsHome.length; i++) {
            	  var attribs = {value : data.dirsHome[i]};
            	  if(forceDir == data.dirsHome[i]){
            	     attribs.selected = "selected";
            	  }
                  tinyMCEPopup.dom.add('dirs-list-home','option', attribs, data.dirsHome[i]);
               }
            }
         }
      });
   }
};

ImageDialog.preInit();
tinyMCEPopup.onInit.add(ImageDialog.init, ImageDialog);
