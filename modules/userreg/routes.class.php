<?php
class UserReg_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('editText', "edit-text", 'editText', 'edit-text/');
      $this->addRoute('editRegMail', "edit-mail-reg", 'editRegMail', 'edit-mail-reg/');
      $this->addRoute('editWelcome', "edit-welcome", 'editWelcome', 'edit-welcome/');
      $this->addRoute('varList', "varlist.json", 'varList', 'varlist.json');
      $this->addRoute('completeReg', "complete", 'completeReg', 'complete/');
      $this->addRoute('welcome', "welcome", 'welcome', 'welcome/');
      $this->addRoute('checkUserName', "checkuser.php", 'checkUserName', 'checkuser.php', 'XHR_Respond_VVEAPI');
   }
}
?>