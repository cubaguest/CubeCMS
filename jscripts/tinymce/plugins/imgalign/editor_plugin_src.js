/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.ImageAlign', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			ed.addCommand('mceImageAlignLeft', function() {
//				var e = ed.dom.getParent(ed.selection.getNode(), ed.dom.isBlock);
//
//				if (e) {
//					if (ed.dom.getAttrib(e, "dir") != "ltr")
//						ed.dom.setAttrib(e, "dir", "ltr");
//					else
//						ed.dom.setAttrib(e, "dir", "");
//				}
//
//				ed.nodeChanged();
			});
//
			ed.addCommand('mceImageAlignRight', function() {
//				var e = ed.dom.getParent(ed.selection.getNode(), ed.dom.isBlock);
//
//				if (e) {
//					if (ed.dom.getAttrib(e, "dir") != "rtl")
//						ed.dom.setAttrib(e, "dir", "rtl");
//					else
//						ed.dom.setAttrib(e, "dir", "");
//				}
//
//				ed.nodeChanged();
			});
//
			ed.addButton('imgal', {title : 'imgalign.imgal', cmd : 'mceImageAlignLeft', image : './img/left.gif'});
			ed.addButton('imgar', {title : 'imgalign.imgal', cmd : 'mceImageAlignRight', image : './img/right.gif'});
//
//			ed.onNodeChange.add(t._nodeChange, t);
		},

		getInfo : function() {
			return {
				longname : 'Image Align',
				author : 'Cube Studio',
				authorurl : 'http://www.cube-studio.cz',
				infourl : 'http://cms.moxiecode.com/dev/tinymce/plugins/imagealign/',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},

		// Private methods

		_nodeChange : function(ed, cm, n) {
			var dom = ed.dom, dir;

			n = dom.getParent(n, dom.isBlock);
			if (!n) {
				cm.setDisabled('ltr', 1);
				cm.setDisabled('rtl', 1);
				return;
			}

			dir = dom.getAttrib(n, 'dir');
			cm.setActive('ltr', dir == "ltr");
			cm.setDisabled('ltr', 0);
			cm.setActive('rtl', dir == "rtl");
			cm.setDisabled('rtl', 0);
		}
	});

	// Register plugin
	tinymce.PluginManager.add('imgalign', tinymce.plugins.ImageAlign);
})();