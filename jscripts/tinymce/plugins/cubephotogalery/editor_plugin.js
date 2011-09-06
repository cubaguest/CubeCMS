/**
 * editor_plugin_src.js
 *
 * Copyright 2011, Jakub Matas Cube Studio
 * Released under LGPL License.
 *
 */

(function() {
   tinymce.PluginManager.requireLangPack('cubephotogalery');
	tinymce.create('tinymce.plugins.CubePhotogaleryPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceCubePhotogalery', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;
            
				ed.windowManager.open({
					file : url + '/photogalery.htm',
					width : 480 + parseInt(ed.getLang('cubephotogalery.delta_width', 0)),
					height : 385 + parseInt(ed.getLang('cubephotogalery.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('cubephotogalery', {
				title : 'cubephotogalery.title',
				cmd : 'mceCubePhotogalery',
            image : url +'/img/gallery.png'
			});
		},

		getInfo : function() {
			return {
				longname : 'Cube CMS Photogalery',
				author : 'Cube Studio',
				authorurl : 'http://www.cube-studio.cz',
				infourl : 'http://www.cube-studio.cz',
				version : "1.1"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('cubephotogalery', tinymce.plugins.CubePhotogaleryPlugin);
})();