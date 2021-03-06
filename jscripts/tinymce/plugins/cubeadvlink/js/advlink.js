/* Functions for the cubeadvlink plugin popup */

tinyMCEPopup.requireLangPack();

var templates = {
	"window.open" : "window.open('${url}','${target}','${options}')"
};

function preinit() {
	var url;

	if (url = tinyMCEPopup.getParam("external_link_list_url"))
		document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
}

function changeClass() {
	var f = document.forms[0];

	f.classes.value = getSelectValue(f, 'classlist');
}

function init() {
	tinyMCEPopup.resizeToInnerSize();

	var formObj = document.forms[0];
	var inst = tinyMCEPopup.editor;
	var elm = inst.selection.getNode();
	var action = "insert";
	var html;

	document.getElementById('hrefbrowsercontainer').innerHTML = getBrowserHTML('hrefbrowser','href','file','advlink'); //cubeadvlink_file_browser_callback
	document.getElementById('popupurlbrowsercontainer').innerHTML = getBrowserHTML('popupurlbrowser','popupurl','file','buildOnClick');
	document.getElementById('targetlistcontainer').innerHTML = getTargetListHTML('targetlist','target');

	// Link list
	html = getLinkListHTML('linklisthref','href');
	if (html == "")
		document.getElementById("linklisthrefrow").style.display = 'none';
	else
		document.getElementById("linklisthrefcontainer").innerHTML = html;

	// Anchor list
	html = getAnchorListHTML('anchorlist','href');
	if (html == "")
		document.getElementById("anchorlistrow").style.display = 'none';
	else
		document.getElementById("anchorlistcontainer").innerHTML = html;

	// Resize some elements
	if (isVisible('hrefbrowser'))
		document.getElementById('href').style.width = '260px';

	if (isVisible('popupurlbrowser'))
		document.getElementById('popupurl').style.width = '180px';

	elm = inst.dom.getParent(elm, "A");
   if (elm == null) {
		var prospect = inst.dom.create("p", null, inst.selection.getContent());
		if (prospect.childNodes.length === 1) {
			elm = prospect.firstChild;
		}
	}

	if (elm != null && elm.nodeName == "A")
		action = "update";

	formObj.insert.value = tinyMCEPopup.getLang(action, 'Insert', true);

	setPopupControlsDisabled(true);
   // init upload
   TinyMCEFileUploader.init();

	if (action == "update") {
		var href = inst.dom.getAttrib(elm, 'href');
		var onclick = inst.dom.getAttrib(elm, 'onclick');
		formObj.insert.value = tinyMCEPopup.getLang(action, 'Insert', true);

		// Setup form data
		setFormValue('href', href);
		setFormValue('title', inst.dom.getAttrib(elm, 'title'));
		setFormValue('id', inst.dom.getAttrib(elm, 'id'));
		setFormValue('style', inst.dom.getAttrib(elm, "style"));
		setFormValue('rel', inst.dom.getAttrib(elm, 'rel'));
		setFormValue('rev', inst.dom.getAttrib(elm, 'rev'));
		setFormValue('charset', inst.dom.getAttrib(elm, 'charset'));
		setFormValue('hreflang', inst.dom.getAttrib(elm, 'hreflang'));
		setFormValue('dir', inst.dom.getAttrib(elm, 'dir'));
		setFormValue('lang', inst.dom.getAttrib(elm, 'lang'));
		setFormValue('tabindex', inst.dom.getAttrib(elm, 'tabindex', typeof(elm.tabindex) != "undefined" ? elm.tabindex : ""));
		setFormValue('accesskey', inst.dom.getAttrib(elm, 'accesskey', typeof(elm.accesskey) != "undefined" ? elm.accesskey : ""));
		setFormValue('type', inst.dom.getAttrib(elm, 'type'));
		setFormValue('onfocus', inst.dom.getAttrib(elm, 'onfocus'));
		setFormValue('onblur', inst.dom.getAttrib(elm, 'onblur'));
		setFormValue('onclick', onclick);
		setFormValue('ondblclick', inst.dom.getAttrib(elm, 'ondblclick'));
		setFormValue('onmousedown', inst.dom.getAttrib(elm, 'onmousedown'));
		setFormValue('onmouseup', inst.dom.getAttrib(elm, 'onmouseup'));
		setFormValue('onmouseover', inst.dom.getAttrib(elm, 'onmouseover'));
		setFormValue('onmousemove', inst.dom.getAttrib(elm, 'onmousemove'));
		setFormValue('onmouseout', inst.dom.getAttrib(elm, 'onmouseout'));
		setFormValue('onkeypress', inst.dom.getAttrib(elm, 'onkeypress'));
		setFormValue('onkeydown', inst.dom.getAttrib(elm, 'onkeydown'));
		setFormValue('onkeyup', inst.dom.getAttrib(elm, 'onkeyup'));
		setFormValue('target', linkTarget);
		setFormValue('classes', inst.dom.getAttrib(elm, 'class'));

		// Parse onclick data
		if (onclick != null && onclick.indexOf('window.open') != -1)
			parseWindowOpen(onclick);
		else
			parseFunction(onclick);

		// Select by the values
		selectByValue(formObj, 'dir', inst.dom.getAttrib(elm, 'dir'));
		selectByValue(formObj, 'rel', inst.dom.getAttrib(elm, 'rel'));
		selectByValue(formObj, 'rev', inst.dom.getAttrib(elm, 'rev'));
		selectByValue(formObj, 'linklisthref', href);

		if (href.charAt(0) == '#')
			selectByValue(formObj, 'anchorlist', href);

		addClassesToList('classlist', 'advlink_styles');

		selectByValue(formObj, 'classlist', inst.dom.getAttrib(elm, 'class'), true);
		selectByValue(formObj, 'targetlist', linkTarget, true);
      
      TinyMCEFileUploader.disableUplaod();
	} else {
		addClassesToList('classlist', 'advlink_styles');
   }
}

function checkPrefix(n) {
	if (n.value && Validator.isEmail(n) && !/^\s*mailto:/i.test(n.value) && confirm(tinyMCEPopup.getLang('cubeadvlink_dlg.is_email')))
		n.value = 'mailto:' + n.value;

	if (/^\s*www\./i.test(n.value) && confirm(tinyMCEPopup.getLang('cubeadvlink_dlg.is_external')))
		n.value = 'http://' + n.value;
}

function setFormValue(name, value) {
	document.forms[0].elements[name].value = value;
}

function parseWindowOpen(onclick) {
	var formObj = document.forms[0];

	// Preprocess center code
	if (onclick.indexOf('return false;') != -1) {
		formObj.popupreturn.checked = true;
		onclick = onclick.replace('return false;', '');
	} else
		formObj.popupreturn.checked = false;

	var onClickData = parseLink(onclick);

	if (onClickData != null) {
		formObj.ispopup.checked = true;
		setPopupControlsDisabled(false);

		var onClickWindowOptions = parseOptions(onClickData['options']);
		var url = onClickData['url'];

		formObj.popupname.value = onClickData['target'];
		formObj.popupurl.value = url;
		formObj.popupwidth.value = getOption(onClickWindowOptions, 'width');
		formObj.popupheight.value = getOption(onClickWindowOptions, 'height');

		formObj.popupleft.value = getOption(onClickWindowOptions, 'left');
		formObj.popuptop.value = getOption(onClickWindowOptions, 'top');

		if (formObj.popupleft.value.indexOf('screen') != -1)
			formObj.popupleft.value = "c";

		if (formObj.popuptop.value.indexOf('screen') != -1)
			formObj.popuptop.value = "c";

		formObj.popuplocation.checked = getOption(onClickWindowOptions, 'location') == "yes";
		formObj.popupscrollbars.checked = getOption(onClickWindowOptions, 'scrollbars') == "yes";
		formObj.popupmenubar.checked = getOption(onClickWindowOptions, 'menubar') == "yes";
		formObj.popupresizable.checked = getOption(onClickWindowOptions, 'resizable') == "yes";
		formObj.popuptoolbar.checked = getOption(onClickWindowOptions, 'toolbar') == "yes";
		formObj.popupstatus.checked = getOption(onClickWindowOptions, 'status') == "yes";
		formObj.popupdependent.checked = getOption(onClickWindowOptions, 'dependent') == "yes";

		buildOnClick();
	}
}

function parseFunction(onclick) {
	var formObj = document.forms[0];
	var onClickData = parseLink(onclick);

	// TODO: Add stuff here
}

function getOption(opts, name) {
	return typeof(opts[name]) == "undefined" ? "" : opts[name];
}

function setPopupControlsDisabled(state) {
	var formObj = document.forms[0];

	formObj.popupname.disabled = state;
	formObj.popupurl.disabled = state;
	formObj.popupwidth.disabled = state;
	formObj.popupheight.disabled = state;
	formObj.popupleft.disabled = state;
	formObj.popuptop.disabled = state;
	formObj.popuplocation.disabled = state;
	formObj.popupscrollbars.disabled = state;
	formObj.popupmenubar.disabled = state;
	formObj.popupresizable.disabled = state;
	formObj.popuptoolbar.disabled = state;
	formObj.popupstatus.disabled = state;
	formObj.popupreturn.disabled = state;
	formObj.popupdependent.disabled = state;

	setBrowserDisabled('popupurlbrowser', state);
}

function parseLink(link) {
	link = link.replace(new RegExp('&#39;', 'g'), "'");

	var fnName = link.replace(new RegExp("\\s*([A-Za-z0-9\.]*)\\s*\\(.*", "gi"), "$1");

	// Is function name a template function
	var template = templates[fnName];
	if (template) {
		// Build regexp
		var variableNames = template.match(new RegExp("'?\\$\\{[A-Za-z0-9\.]*\\}'?", "gi"));
		var regExp = "\\s*[A-Za-z0-9\.]*\\s*\\(";
		var replaceStr = "";
		for (var i=0; i<variableNames.length; i++) {
			// Is string value
			if (variableNames[i].indexOf("'${") != -1)
				regExp += "'(.*)'";
			else // Number value
				regExp += "([0-9]*)";

			replaceStr += "$" + (i+1);

			// Cleanup variable name
			variableNames[i] = variableNames[i].replace(new RegExp("[^A-Za-z0-9]", "gi"), "");

			if (i != variableNames.length-1) {
				regExp += "\\s*,\\s*";
				replaceStr += "<delim>";
			} else
				regExp += ".*";
		}

		regExp += "\\);?";

		// Build variable array
		var variables = [];
		variables["_function"] = fnName;
		var variableValues = link.replace(new RegExp(regExp, "gi"), replaceStr).split('<delim>');
		for (var i=0; i<variableNames.length; i++)
			variables[variableNames[i]] = variableValues[i];

		return variables;
	}

	return null;
}

function parseOptions(opts) {
	if (opts == null || opts == "")
		return [];

	// Cleanup the options
	opts = opts.toLowerCase();
	opts = opts.replace(/;/g, ",");
	opts = opts.replace(/[^0-9a-z=,]/g, "");

	var optionChunks = opts.split(',');
	var options = [];

	for (var i=0; i<optionChunks.length; i++) {
		var parts = optionChunks[i].split('=');

		if (parts.length == 2)
			options[parts[0]] = parts[1];
	}

	return options;
}

function buildOnClick() {
	var formObj = document.forms[0];

	if (!formObj.ispopup.checked) {
		formObj.onclick.value = "";
		return;
	}

	var onclick = "window.open('";
	var url = formObj.popupurl.value;

	onclick += url + "','";
	onclick += formObj.popupname.value + "','";

	if (formObj.popuplocation.checked)
		onclick += "location=yes,";

	if (formObj.popupscrollbars.checked)
		onclick += "scrollbars=yes,";

	if (formObj.popupmenubar.checked)
		onclick += "menubar=yes,";

	if (formObj.popupresizable.checked)
		onclick += "resizable=yes,";

	if (formObj.popuptoolbar.checked)
		onclick += "toolbar=yes,";

	if (formObj.popupstatus.checked)
		onclick += "status=yes,";

	if (formObj.popupdependent.checked)
		onclick += "dependent=yes,";

	if (formObj.popupwidth.value != "")
		onclick += "width=" + formObj.popupwidth.value + ",";

	if (formObj.popupheight.value != "")
		onclick += "height=" + formObj.popupheight.value + ",";

	if (formObj.popupleft.value != "") {
		if (formObj.popupleft.value != "c")
			onclick += "left=" + formObj.popupleft.value + ",";
		else
			onclick += "left='+(screen.availWidth/2-" + (formObj.popupwidth.value/2) + ")+',";
	}

	if (formObj.popuptop.value != "") {
		if (formObj.popuptop.value != "c")
			onclick += "top=" + formObj.popuptop.value + ",";
		else
			onclick += "top='+(screen.availHeight/2-" + (formObj.popupheight.value/2) + ")+',";
	}

	if (onclick.charAt(onclick.length-1) == ',')
		onclick = onclick.substring(0, onclick.length-1);

	onclick += "');";

	if (formObj.popupreturn.checked)
		onclick += "return false;";

	// tinyMCE.debug(onclick);

	formObj.onclick.value = onclick;

	if (formObj.href.value == "")
		formObj.href.value = url;
}

function setAttrib(elm, attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib.toLowerCase()];
	var dom = tinyMCEPopup.editor.dom;

	if (typeof(value) == "undefined" || value == null) {
		value = "";

		if (valueElm)
			value = valueElm.value;
	}

	// Clean up the style
	if (attrib == 'style')
		value = dom.serializeStyle(dom.parseStyle(value), 'a');

	dom.setAttrib(elm, attrib, value);
}

function getAnchorListHTML(id, target) {
	var ed = tinyMCEPopup.editor, nodes = ed.dom.select('a'), name, i, len, html = "";

	for (i=0, len=nodes.length; i<len; i++) {
      if ((name = ed.dom.getAttrib(nodes[i], "name")) != "")
         html += '<option value="#' + name + '">' + name + '</option>';

      if ((name = nodes[i].id) != "" && !nodes[i].href)
         html += '<option value="#' + name + '">' + name + '</option>';
   }

	if (html == "")
		return "";

	html = '<select id="' + id + '" name="' + id + '" class="mceAnchorList"'
		+ ' onchange="this.form.' + target + '.value=this.options[this.selectedIndex].value"'
		+ '>'
		+ '<option value="">---</option>'
		+ html
		+ '</select>';

	return html;
}

function insertAction() {
	var inst = tinyMCEPopup.editor;
	var elm, elementArray, i;

	elm = inst.selection.getNode();
	checkPrefix(document.forms[0].href);

	elm = inst.dom.getParent(elm, "A");

	// Remove element if there is no href
	if (!document.forms[0].href.value) {
		i = inst.selection.getBookmark();
		inst.dom.remove(elm, 1);
		inst.selection.moveToBookmark(i);
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
		return;
	}

	// Create new anchor elements
	if (elm == null) {
		inst.getDoc().execCommand("unlink", false, null);
		tinyMCEPopup.execCommand("mceInsertLink", false, "#mce_temp_url#", {skip_undo : 1});

		elementArray = tinymce.grep(inst.dom.select("a"), function(n) {return inst.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
		for (i=0; i<elementArray.length; i++)
			setAllAttribs(elm = elementArray[i]);
	} else
		setAllAttribs(elm);

	// Don't move caret if selection was image
	if (elm.childNodes.length != 1 || elm.firstChild.nodeName != 'IMG') {
		inst.focus();
		inst.selection.select(elm);
		inst.selection.collapse(0);
		tinyMCEPopup.storeSelection();
	}

	tinyMCEPopup.execCommand("mceEndUndoLevel");
	tinyMCEPopup.close();
}

function setAllAttribs(elm) {
	var formObj = document.forms[0];
	var href = formObj.href.value.replace(/ /g, '%20');
	var target = getSelectValue(formObj, 'targetlist');

	setAttrib(elm, 'href', href);
	setAttrib(elm, 'title');
	setAttrib(elm, 'target', target == '_self' ? '' : target);
	setAttrib(elm, 'id');
	setAttrib(elm, 'style');
	setAttrib(elm, 'class', getSelectValue(formObj, 'classlist'));
	setAttrib(elm, 'rel');
	setAttrib(elm, 'rev');
	setAttrib(elm, 'charset');
	setAttrib(elm, 'hreflang');
	setAttrib(elm, 'dir');
	setAttrib(elm, 'lang');
	setAttrib(elm, 'tabindex');
	setAttrib(elm, 'accesskey');
	setAttrib(elm, 'type');
	setAttrib(elm, 'onfocus');
	setAttrib(elm, 'onblur');
	setAttrib(elm, 'onclick');
	setAttrib(elm, 'ondblclick');
	setAttrib(elm, 'onmousedown');
	setAttrib(elm, 'onmouseup');
	setAttrib(elm, 'onmouseover');
	setAttrib(elm, 'onmousemove');
	setAttrib(elm, 'onmouseout');
	setAttrib(elm, 'onkeypress');
	setAttrib(elm, 'onkeydown');
	setAttrib(elm, 'onkeyup');

	// Refresh in old MSIE
	if (tinyMCE.isMSIE5)
		elm.outerHTML = elm.outerHTML;
}

function getSelectValue(form_obj, field_name) {
	var elm = form_obj.elements[field_name];

	if (!elm || elm.options == null || elm.selectedIndex == -1)
		return "";

	return elm.options[elm.selectedIndex].value;
}

function getLinkListHTML(elm_id, target_form_element, onchange_func) {
	if (typeof(tinyMCELinkList) == "undefined" || tinyMCELinkList.length == 0)
		return "";

	var html = "";

	html += '<select id="' + elm_id + '" name="' + elm_id + '"';
	html += ' class="mceLinkList" onchange="this.form.' + target_form_element + '.value=';
	html += 'this.options[this.selectedIndex].value;';

	if (typeof(onchange_func) != "undefined")
		html += onchange_func + '(\'' + target_form_element + '\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value);';

	html += '"><option value="">---</option>';

	for (var i=0; i<tinyMCELinkList.length; i++)
		html += '<option value="' + tinyMCELinkList[i][1] + '">' + tinyMCELinkList[i][0] + '</option>';

	html += '</select>';

	return html;

	// tinyMCE.debug('-- image list start --', html, '-- image list end --');
}

function getTargetListHTML(elm_id, target_form_element) {
	var targets = tinyMCEPopup.getParam('theme_advanced_link_targets', '').split(';');
	var html = '';

	html += '<select id="' + elm_id + '" name="' + elm_id + '" onchange="this.form.' + target_form_element + '.value=';
	html += 'this.options[this.selectedIndex].value;">';
	html += '<option value="_self">' + tinyMCEPopup.getLang('cubeadvlink_dlg.target_same') + '</option>';
	html += '<option value="_blank">' + tinyMCEPopup.getLang('cubeadvlink_dlg.target_blank') + ' (_blank)</option>';
	html += '<option value="_parent">' + tinyMCEPopup.getLang('cubeadvlink_dlg.target_parent') + ' (_parent)</option>';
	html += '<option value="_top">' + tinyMCEPopup.getLang('cubeadvlink_dlg.target_top') + ' (_top)</option>';

	for (var i=0; i<targets.length; i++) {
		var key, value;

		if (targets[i] == "")
			continue;

		key = targets[i].split('=')[0];
		value = targets[i].split('=')[1];

		html += '<option value="' + key + '">' + value + ' (' + key + ')</option>';
	}

	html += '</select>';

	return html;
}

/* CUBE ADVLINK */
var TinyMCEFileUploader = {
   uploadUrl : "/component/tinymce_uploader/0/fileUpload.php",
   dirListUrl : "/component/tinymce_uploader/0/dirsList.json",

   init : function(){
      this.uploadUrl = "/component/tinymce_uploader/"+tinyMCE.activeEditor.getParam('cid')+"/fileUpload.php";
      this.dirListUrl = "/component/tinymce_uploader/"+tinyMCE.activeEditor.getParam('cid')+"/dirsList.json";
      var aDirs = tinyMCE.activeEditor.getParam('alloweddirs');
      if(aDirs && aDirs.length > 0){
         var dirsStr = '?';
         for (var i = 0; i < aDirs.length; i++){
            dirsStr+= 'allowDirs[]='+escape(aDirs[i]);
            if(i+1 < aDirs.length){ dirsStr+="&"; }
         }
         this.uploadUrl+=dirsStr;
         this.dirListUrl+=dirsStr;
      }

      document.getElementById("uploading_progress").style.visibility = "hidden";
      this.loadUploadDirs();
   },
   
   disableUplaod : function(){
      document.getElementById("upload_tab").style.display = "none";
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
   },

   /* uploader */
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
      form.href.value = data.url;
      // change tab
      mcTabs.displayTab('general_tab','general_panel');
   }
}
// While loading
preinit();
tinyMCEPopup.onInit.add(init);
