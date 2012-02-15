/**
 * editor_plugin_src.js
 *
 * Copyright 2012, Cube-Studio
 * Released under LGPL License.
 *
 * Author: Jakub Matas <j.matas@cube-studio.cz>
 *
 */

(function() {
   tinymce.PluginManager.requireLangPack('cubehelpers');
	tinymce.create('tinymce.plugins.CubeHelpers', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			ed.addCommand('mceAddParagraphToBegin', function() {
            var root=ed.dom.getRoot();  // This gets the root node of the editor window
            var firstnode=root.childNodes[0]; // And this gets the last node inside of it, so the last <p>...</p> tag
            
            var newP = ed.dom.create('p', null, "&nbsp;");
            
            root.insertBefore(newP, firstnode);
            ed.selection.select(newP);
            ed.selection.collapse(true);
				ed.nodeChanged();
			});

			ed.addCommand('mceAddParagraphToEnd', function() {
            var root=ed.dom.getRoot();  // This gets the root node of the editor window
            var lastnode=root.childNodes[root.childNodes.length-1]; // And this gets the last node inside of it, so the last <p>...</p> tag
            
            var newP = ed.dom.create('p', null, "&nbsp;");
            
            ed.dom.insertAfter(newP, lastnode);
            ed.selection.select(newP);
            ed.selection.collapse(true);
            ed.nodeChanged();
			});

			ed.addButton('insparbegin', {title : 'cubehelpers.ins_paragraph_begin', cmd : 'mceAddParagraphToBegin', image : url +'/img/p_ins_up.gif'});
			ed.addButton('insparend', {title : 'cubehelpers.ins_paragraph_end', cmd : 'mceAddParagraphToEnd', image : url +'/img/p_ins_down.gif'});
		},

		getInfo : function() {
			return {
				longname : 'Cube CMS Helpers',
				author : 'Cube Studio',
				authorurl : 'http://www.cube-studio.cz',
				infourl : 'http://csm.cube-studio.cz/dev/tinymce/plugins/cubehelpers/',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('cubehelpers', tinymce.plugins.CubeHelpers);
})();