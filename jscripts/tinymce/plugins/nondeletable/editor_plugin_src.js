/**
 * $Id: editor_plugin_src.js 743 2008-03-23 17:47:33Z spocke $
 *
 * @author Michael Piera
 * @copyright Copyright © 2004-2008, Michael Piera, All rights reserved.
 */

(function() {
        //var Event = tinymce.dom.Event;
        tinymce.create('tinymce.plugins.NonDeleteablePlugin', {
                init : function(ed, url) {
                        var t = this, nonDeleteableClass;
                        t.editor = ed;
                        nonDeleteableClass = ed.getParam("nondeleteable_class", "mceNonDeleteable");
                        ed.onNodeChange.addToTop(function(ed, cm, n) {
                                var sc, ec;
                                // check if start or end is inside a nonDeleteableClass element
                                sc = ed.dom.getParent(ed.selection.getStart(), function(n) {
                                        return ed.dom.hasClass(n, nonDeleteableClass);
                                });
                                ec = ed.dom.getParent(ed.selection.getEnd(), function(n) {
                                        return ed.dom.hasClass(n, nonDeleteableClass);
                                });
                                if (sc || ec) {
                                        var ssc = ed.dom.hasClass(n,'mceNonEditable');
                                        if (ssc) {
                                                var el = ed.dom.create('p',{},'<br />');
                                                ed.dom.insertAfter(el,n);
                                                return;
                                        }
                                }
                        });
                },
                // plugin infos
                getInfo : function() {
                        return {
                                longname : 'Non deleteable elements',
                                author : 'Michael Piera',
                                authorurl : 'http://www.univ-paris8.fr',
                                infourl : 'http://www.univ-paris8.fr',
                                version : tinymce.majorVersion + "." + tinymce.minorVersion
                        };
                }
        });

        // Register plugin
        tinymce.PluginManager.add('nondeleteable', tinymce.plugins.NonDeleteablePlugin);
})();
