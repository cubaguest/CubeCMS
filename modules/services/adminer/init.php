<?php
/* INIT function */


function adminer_object() {
   // required to run any plugin
   include_once "./plugin.php";

   $plugins = array(
         // specify enabled plugins here
         new AdminerFrames,
         new AdminerFileUpload('data/'),
   );

   class AdminerSoftware extends AdminerPlugin {
   
      function name() {
         // custom name in title and heading
         return 'DB Adminer';
      }
   
//       function credentials() {
         // server, username and password for connecting to database
//          return array($_GET['server'], $_GET['database'], '');
//       }
   
//       function database() {
         // database name, will be escaped by Adminer
//          return ;
//       }
   
//       function login($login, $password) {
//          // validate user submitted credentials
//          return ($login == 'admin' && $password == '');
//       }
   }
   
   return new AdminerSoftware($plugins);
}
/* Adminer */
// include original Adminer or Adminer Editor
include "./adminer.php";
?>