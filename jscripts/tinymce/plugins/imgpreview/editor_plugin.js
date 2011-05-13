//(function(){tinymce.PluginManager.requireLangPack('imgpreview');tinymce.create('tinymce.plugins.ImagePrewiev',{init:function(ed,url){var t=this;t.editor=ed;ed.addCommand('mceImagePreview',function(){var e=ed.selection.getNode();var pnode=e.parentNode;if(pnode!=null&&pnode.nodeName=='A'&&(ed.dom.hasClass(pnode,'pirobox')||ed.dom.hasClass(pnode,'pirobox_gall'))){var eClone=e.cloneNode(true);ed.dom.remove(e.parentNode);ed.selection.setNode(eClone)}else{var title=ed.dom.getAttrib(e,'alt',null);if(title==null)title=e.src.replace(/^.*\//,'');var gallLink=ed.dom.create('a',{'class':'pirobox','href':e.src.replace('/small/','/medium/'),'title':title});gallLink.appendChild(e.cloneNode(true));ed.dom.remove(e);ed.selection.setNode(gallLink)}ed.nodeChanged()});ed.addButton('imgpreview',{title:'imgpreview.preview',cmd:'mceImagePreview',image:url+'/img/preview_gallery.gif'});ed.onNodeChange.add(function(ed,cm,n){if(n==null)return;cm.setDisabled('imgpreview',n.nodeName!='IMG');cm.setActive('imgpreview',t._havePreview(ed,n))})},getInfo:function(){return{longname:'Image Preview - PiroBox',author:'Cube Studio',authorurl:'http://www.cube-studio.cz',infourl:'http://www.cube-studio.cz/dev/tinymce/plugins/imagepreview/',version:tinymce.majorVersion+"."+tinymce.minorVersion}},_havePreview:function(ed,node){if(node.parentNode!=null&&node.parentNode.nodeName=='A'&&(ed.dom.hasClass(node.parentNode,'pirobox')||ed.dom.hasClass(node.parentNode,'pirobox_gall')))return true;return false}});tinymce.PluginManager.add('imgpreview',tinymce.plugins.ImagePrewiev)})();

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
   tinymce.PluginManager.requireLangPack('imgpreview');
   tinymce.create('tinymce.plugins.ImagePrewiev', {
      init : function(ed, url) {
         var t = this;
         t.editor = ed;

         // Příkaz na změnu galerie
         ed.addCommand('mceImagePreview', function() {
            var se = ed.selection;

            // No selection and not in link or image
            if (se.isCollapsed() && (!ed.dom.getParent(se.getNode(), 'A') || !ed.dom.getParent(se.getNode(), 'IMG')))
               return;

            var e = ed.selection.getNode();
//             var pnode = e.parentNode;
            var pnode = ed.dom.getParent(e, "A");
            if(pnode != null && pnode.nodeName == 'A' && (ed.dom.hasClass(pnode, 'pirobox') || ed.dom.hasClass(pnode, 'pirobox_gall')) ){
                  ed.getDoc().execCommand("unlink", false, null);
                  ed.getDoc().execCommand("RemoveFormat", false, null);
            } else {
               // pokud není vytvořen preview
               var title = ed.dom.getAttrib(e, 'alt', e.src.replace(/^.*\//, ''));
               tinyMCE.execCommand("mceInsertLink", false, {'class' : 'pirobox', 'href' : e.src.replace('/small/', '/medium/'), 'title' : title});
            }

            ed.nodeChanged();
         });

         ed.addButton('imgpreview', {title : 'imgpreview.preview', cmd : 'mceImagePreview', image : url +'/img/preview_gallery.gif'});

         ed.onNodeChange.add(function(ed, cm, n) {
            if (n == null) return;

            cm.setDisabled('imgpreview', n.nodeName != 'IMG');
            cm.setActive('imgpreview', t._havePreview(ed, n));
         });
      },

      getInfo : function() {
         return {
            longname : 'Image Preview - PiroBox',
            author : 'Cube Studio',
            authorurl : 'http://www.cube-studio.cz',
            infourl : 'http://www.cube-studio.cz/dev/tinymce/plugins/imagepreview/',
            version : tinymce.majorVersion + "." + tinymce.minorVersion
         };
      },

      _havePreview : function(ed, node){
         if(node.parentNode != null && node.parentNode.nodeName == 'A' && (ed.dom.hasClass(node.parentNode, 'pirobox') || ed.dom.hasClass(node.parentNode, 'pirobox_gall')) )
            return true;

         return false;
      }
   });

   // Register plugin
   tinymce.PluginManager.add('imgpreview', tinymce.plugins.ImagePrewiev);
})();