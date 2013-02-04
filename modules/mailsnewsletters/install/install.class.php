<?php
class MailsNewsletters_Install extends Install_Module {
   public $version = array('major' => 1, 'minor' => 1);
   protected $depModules = array('mails', 'mailsaddressbook');
   //protected $depModules = array('newsletter');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
