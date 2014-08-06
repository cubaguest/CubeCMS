<?php
class News_Controller extends Articles_Controller {
   public function showController($urlkey) {
      $this->setOption('deleteMsg', $this->_('Novinka byla smazána'));
      $this->setOption('publicMsg', $this->_('Novinka byla zveřejněna'));
      if(parent::showController($urlkey) === false) return false;
      if($this->view()->formDelete instanceof Form){
         $this->view()->formDelete->delete->setLabel($this->_('Smazat novinku'));
      }
   }
}