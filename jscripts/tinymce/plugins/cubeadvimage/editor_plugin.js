/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Copyright 2012, Cube Studio
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.CubeAdvancedImagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceCubeAdvImage', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class', '').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
					file : url + '/image.htm',
					width : 480 + parseInt(ed.getLang('cubeadvimage.delta_width', 0)),
					height : 430 + parseInt(ed.getLang('cubeadvimage.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('image', {
				title : 'cubeadvimage.image_desc',
				cmd : 'mceCubeAdvImage'
			});
		},

		getInfo : function() {
			return {
				longname : 'Cube CMS Advanced image',
				author : 'Moxiecode Systems AB + Cube Studio',
				authorurl : 'http://tinymce.moxiecode.com, http://www.cube-studio.cz',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advimage',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('cubeadvimage', tinymce.plugins.CubeAdvancedImagePlugin);
})();