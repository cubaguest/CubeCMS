<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShareDocs_Routes extends Routes {
	function initRoutes() {
      // výpis složky
      $this->addRoute('dirList', "dir-::iddir::/", 'dirList', 'dir-{iddir}/');
      
      // úprava přístupu ke složce
      $this->addRoute('editDirAccess', "dir-::iddir::/access/", 'editDirAccess', 'dir-{iddir}/access/');
      
      // úprava souboru
      $this->addRoute('editFile', "dir-::iddir::/file-::idfile::/edit/", 'editFile', 'dir-{iddir}/file-{idfile}/edit/');
      
      // generace odkazu
      $this->addRoute('generatePublicLink', "dir-::iddir::/file-::idfile::/generate-pb.php", 
         'generatePublicLink', 'dir-{iddir}/file-{idfile}/generate-pb.php',  "XHR_Respond_VVEAPI");
      
      // výpis souboru
      $this->addRoute('file', "dir-::iddir::/file-::idfile::/", 'file', 'dir-{iddir}/file-{idfile}/');
      
      
      // seznamy skupin a uživatelů
      $this->addRoute('usersList', "dir-::iddir::/usersacc.json", 'usersList','dir-{iddir}/usersacc.json');
      $this->addRoute('groupsList', "dir-::iddir::/groupsacc.json", 'groupsList','dir-{iddir}/groupsacc.json');
      // úprava přístupů
      $this->addRoute('editGroupAcc', "dir-::iddir::/groupacc.php", 'editGroupAcc', 'dir-{iddir}/groupacc.php',  "XHR_Respond_VVEAPI");
      $this->addRoute('editUserAcc', "dir-::iddir::/useracc.php", 'editUserAcc', 'dir-{iddir}/useracc.php',  "XHR_Respond_VVEAPI");
      
      $this->addRoute('setReadOnly', "dir-::iddir::/setreadonly.php", 'setReadOnly', 'dir-{iddir}/setreadonly.php',  "XHR_Respond_VVEAPI");
      
      // stažení souboru přes token
      $this->addRoute('downloadFile', "download.php", 'downloadFile', 'download.php');
       
	}
}

?>