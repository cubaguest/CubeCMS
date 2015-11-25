<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class AdvEventsUserAdd_View extends AdvEventsBase_View {
	public function mainView()
   {
      parent::mainView();
      Template::setFullWidth(true);
      
      if(Auth::isLogin()){
         $this->setTinyMCE($this->formEdit->text, 'advanced');
         $this->template()->addFile('tpl://adveventsadmevents:edit.phtml');
         $this->template()->showClose = false;
         Template_Navigation::addItem($this->tr('Přidání nové události'), $this->link(), true);
         Template::addPageTitle($this->category()->getName());
         Template::addPageTitle($this->tr('Přidání nové události'));
      } else {
         $this->template()->addFile('tpl://main.phtml');
         $this->setTinyMCE($this->formEdit->text, 'simple2');
      }
      
      
   }
}
