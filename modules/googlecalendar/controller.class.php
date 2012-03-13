<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class GoogleCalendar_Controller extends Controller {
   const PARAM_CALENDAR_ID = "url";

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      // https://www.google.com/calendar/feeds/ID/public/full
      $this->view()->calendarUrl = 
         'https://www.google.com/calendar/feeds/'
         .urlencode($this->category()->getParam(self::PARAM_CALENDAR_ID, null))
         .'/public/full'
      ;
   }

   public function settings(&$settings, Form &$form) {
      $fGrpCalendar = $form->addGroup('calendar', $this->tr('Nastavení kalendáře'));
      
      $eCalID = new Form_Element_Text('idcal', $this->tr('ID kalendáře'));
      $eCalID->setSubLabel($this->tr('ID google kalendáře. Kalendář musí mít veřejný přístup!'));
      
      if(isset ($settings[self::PARAM_CALENDAR_ID])){
         $eCalID->setValues($settings[self::PARAM_CALENDAR_ID]);
      }
      $form->addElement($eCalID);
      
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_CALENDAR_ID] = $form->idcal->getValues();
      }
   }
}

?>