
function vveTinyMCEFileBrowser (field_name, url, type, win) {
   /* If you work with sessions in PHP and your client doesn't accept cookies you might need to carry
       the session name and session ID in the request string (can look like this: "?PHPSESSID=88p0n70s9dsknra96qhuk6etm5").
       These lines of code extract the necessary parameters and add them back to the filebrowser URL again. */

   var cmsURL = location.toString();    // script URL - use an absolute path!
   tinyMCE.activeEditor.windowManager.open({
      file : "./jscripts/tinymce/browser/filebrowser.phtml?cat="+tinyMCE.activeEditor.getParam('category_id'),
      title : 'File Browser',
      width : 750,  // Your dimensions may differ - toy around with them!
      height : 400,
      resizable : "yes",
      inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
      close_previous : "no"
   }, {
      window : win,
      input : field_name,
      listType : type,
      cat : tinyMCE.activeEditor.getParam('category_id')
   });
   return false;
}
