<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class GoogleCalendar_Controller extends Controller {
   const PARAM_URL = "url";

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->view()->calendarUrl = $this->category()->getParam(self::PARAM_URL, null);
   }

   public function settings(&$settings, Form &$form) {
      $fGrpCalendar = $form->addGroup('calendar', $this->tr('Nastavení kalendáře'));
      
      $eCalUrl = new Form_Element_Text('url', $this->tr('URL Adresa kalendáře v XML'));
      $eCalUrl->setSubLabel($this->tr('Formát: https://www.google.com/calendar/feeds/<em>userID</em>/public/full'));
      $eCalUrl->addValidation(new Form_Validator_Url());
      
      if(isset ($settings[self::PARAM_URL])){
         $eCalUrl->setValues($settings[self::PARAM_URL]);
      }
      $form->addElement($eCalUrl);
      
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_URL] = $form->url->getValues();
      }
   }
}

?>