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
	tinymce.create('tinymce.plugins.CubeAdvancedLinkPlugin', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mceCubeAdvLink', function() {
				var se = ed.selection;

				// No selection and not in link
				if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
					return;

				ed.windowManager.open({
					file : url + '/link.htm',
					width : 500 + parseInt(ed.getLang('advlink.delta_width', 0)),
					height : 400 + parseInt(ed.getLang('advlink.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('link', {
				title : 'cubeadvlink.link_desc',
				cmd : 'mceCubeAdvLink'
			});

			ed.addShortcut('ctrl+k', 'cubeadvlink.advlink_desc', 'mceCubeAdvLink');

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('link', co && n.nodeName != 'A');
				cm.setActive('link', n.nodeName == 'A' && !n.name);
			});
		},

		getInfo : function() {
			return {
				longname : 'CubeCMS Advanced link',
				author : 'Moxiecode Systems AB + Cube Studio',
				authorurl : 'http://tinymce.moxiecode.com, http://www.cube-studio.cz',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advlink',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('cubeadvlink', tinymce.plugins.CubeAdvancedLinkPlugin);
})();