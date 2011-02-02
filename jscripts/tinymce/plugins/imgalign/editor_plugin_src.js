/**
 * editor_plugin_src.js
 *
 * Copyright 2011, Cube-Studio
 * Released under LGPL License.
 *
 * Author: Jakub Matas <j.matas@cube-studio.cz>
 *
 */

(function() {
   tinymce.PluginManager.requireLangPack('imgalign');
	tinymce.create('tinymce.plugins.ImageAlign', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			ed.addCommand('mceImageAlignLeft', function() {
				var e = ed.selection.getNode();

				if (e) {
					if (ed.dom.hasClass(e,'image-right'))
						ed.dom.removeClass(e, "image-right");

					if (ed.dom.hasClass(e,'image-left'))
						ed.dom.removeClass(e, "image-left");
					else
						ed.dom.addClass(e, "image-left");
				}
				ed.nodeChanged();
			});

			ed.addCommand('mceImageAlignRight', function() {
            var e = ed.selection.getNode();

				if (e) {
               if (ed.dom.hasClass(e,'image-left'))
						ed.dom.removeClass(e, "image-left");

					if (ed.dom.hasClass(e,'image-right'))
						ed.dom.removeClass(e, "image-right");
					else
						ed.dom.addClass(e, "image-right");
				}
				ed.nodeChanged();
			});

			ed.addButton('imgal', {title : 'imgalign.imgal', cmd : 'mceImageAlignLeft', image : url +'/img/left.gif'});
			ed.addButton('imgar', {title : 'imgalign.imgal', cmd : 'mceImageAlignRight', image : url +'/img/right.gif'});

         ed.onNodeChange.add(function(ed, cm, n) {
            if (n == null) return;

            cm.setDisabled('imgal', n.nodeName != 'IMG');
            cm.setDisabled('imgar', n.nodeName != 'IMG');
            cm.setActive('imgal', ed.dom.hasClass(n, 'image-left'));
            cm.setActive('imgar', ed.dom.hasClass(n, 'image-right'));
			});
		},

		getInfo : function() {
			return {
				longname : 'Image Align',
				author : 'Cube Studio',
				authorurl : 'http://www.cube-studio.cz',
				infourl : 'http://csm.cube-studio.cz/dev/tinymce/plugins/imagealign/',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('imgalign', tinymce.plugins.ImageAlign);
})();