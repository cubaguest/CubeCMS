<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class AdvEvents_View extends AdvEventsBase_View {
	public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
   }
   
	public function filterView()
   {
      $this->template()->addFile('tpl://filter.phtml');
      Template_Navigation::addItem($this->tr('Filtrace'), $this->link()->clear());
   }
   
	public function detailView()
   {
      $this->template()->addFile('tpl://detail.phtml');
      Template_Navigation::addItem($this->tr('Filtrace'), $this->link()->route('filter'));
      Template_Navigation::addItem($this->event->{AdvEventsBase_Model_Events::COLUMN_NAME}, $this->link());
   }
   
	public static function getSlider()
   {
      $tpl = new Template_Module(new Url_Link_Module(), new Category());
      
      $slides = AdvEventsBase_Model_Events::getHomePageEvents();
      
      if(empty($slides)){
         return;
      }
      $tpl->events = $slides;
      $tpl->addFile('tpl://slider.phtml');
      return $tpl;
   }
}
