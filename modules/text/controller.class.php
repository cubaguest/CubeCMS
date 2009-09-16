<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Text_Controller extends Controller {
   /**
    * Názvy formulářových prvků
    * @var string
    */
   const FORM_PREFIX = 'text_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_TEXT = 'text';

   /**
    * Názvy parametrů modulu
    */
   const PARAM_FILES = 'files';
   const PARAM_THEME = 'theme';

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      // ================ SOF ODSTRANIT
//      $link = new Url_Link();
//      $link = $this->link();
//      var_dump($link);
//      $start = xdebug_time_index();

//      var_dump($link);
//      for ($number_variable = 0 ; $number_variable < 10 ; $number_variable++) {
//         print ($link->clear()->route('edit2', array('article' => 'dlouhý článek s mezerama a češtinou!!?', 'page' => 2))."<br />");
//      }
//      print ($link->lang()."<br />");
////      print ($link->clear(true)."<br />");
//      print (Locale::getLang()."<br />");
//      print ("PAGE ".$this->getRequest('page', 1)."<br />");
//      print ("PAGE ".$this->getRequest('article')."<br />");
//         print ((xdebug_time_index() - $start)."<br>");

   // ================ EOF ODSTRANIT
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
//      $this->checkWritebleRights();
//
//      if($this->getModule()->getParam(self::PARAM_FILES, true)){
//         // Uživatelské soubory
//         $files = new Eplugin_UserFiles($this->sys());
//         $this->view()->EPLfiles = $files;
//      }
//
//      $form = new Form();
//      $form->setPrefix(self::FORM_PREFIX);
//
//      $form->crTextArea(self::FORM_TEXT, true, true, Form::CODE_HTMLDECODE)
//      ->crSubmit(self::FORM_BUTTON_SEND);
//
//      //        Pokud byl odeslán formulář
//      if($form->checkForm()){
//         $text = new Text_Model_Detail($this->sys());
//         if(!$text->saveEditText($form->getValue(self::FORM_TEXT))){
//            throw new UnexpectedValueException($this->_('Text se nepodařilo uložit'));
//         }
//
//         $this->infoMsg()->addMessage($this->_('Text byl uložen'));
//         $this->link()->action()->reload();
//      }
   }

   public function listController() {
   }
}

?>