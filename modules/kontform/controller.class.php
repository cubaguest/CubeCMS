<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Kontform_Controller extends Controller {
   /**
    * Názvy formulářových prvků
    * @var string
    */
   const FORM_PREFIX = 'kontform_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_NAME = 'name';
   const FORM_SURNAME = 'surname';
   const FORM_EMAIL = 'email';
   const FORM_QUESTION = 'question';

   /**
    * Kontroler pro zobrazení formuláře,
    * kterým dá zákazník kontakt na sebe a položí dotaz
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      //		Model pro načtení textu
//      $model = new Kontform_Model_Detail();


      $form = new Form();
      $form->setPrefix(self::FORM_PREFIX);
      /*
       * Pužívej "NULL" => null
       * Typ kódování způsobí dekódování, zakódování přenesených znaků do HTML entit
       * Tady může být Form::CODE_HTMLENCODE nebo Form::CODE_NONE protože data  se
       * nebudou zobrazovat ve stránce. Kódování při vkládání do databáze nemusíš řešit,
       * protže je ve frameworku již zakomponováno viz. třída Mysqli_Db_Query metoda checkValueFormat()
       * nebo přímo třída MySQLiDb metoda escapeString()
       * 
       * Jinak je to dobrý, jenom asij délku řetězce nemusíš kontrolovat, protože pokud se uloží
       * do db a byl by delší byl by zkráccen, ale je to lepší - uživatelé sou kurvy
       * 
       * Pak tu příjde ještě ten EPlugin na odesílání emailu, ale ten ti integruji
       * po víkendu, ale mám ho hotový.
       */

       $form->crInputText(self::FORM_NAME, True, False, null, Form::CODE_HTMLENCODE, 20,1);
       //položka příjmení je nepovinná
       $form->crInputText(self::FORM_SURNAME, False, False, null, Form::CODE_HTMLENCODE, 20,1);
       //tady u toho inputu to chce ještě dodělat validaci na mejlovou adresu:
       // validaci nastavíš typem validace. je to konstanta objektu FORM
       $form->crInputText(self::FORM_EMAIL, True, False, Form::VALIDATE_EMAIL, Form::CODE_HTMLENCODE, 40,1);
       $form->crTextArea(self::FORM_QUESTION, True, False, Form::CODE_HTMLENCODE, 500, 1);
       $form->crSubmit(self::FORM_BUTTON_SEND);

       $kontform = new Kontform_Model_Detail($this->sys());


//        Pokud byl formulář odeslán
      if($form->checkForm()){
         
         if(!$kontform->saveKontform($form->getValue(self::FORM_NAME),
                                     $form->getValue(self::FORM_SURNAME),
                                     $form->getValue(self::FORM_EMAIL),
                                     $form->getValue(self::FORM_QUESTION))){
         throw new UnexpectedValueException(_m('Váš dotaz se nepodařilo odeslat'));}

            
         
         $this->infoMsg()->addMessage(_('Text byl uložen'));
         $this->getLink()->action()->reload();
      }
      //    Data do šablony
      //$this->container()->addData('TEXT_DATA', $form->getValues());
      //$this->container()->addData('ERROR_ITEMS', $form->getErrorItems());

      //		Odkaz zpět
      //$this->container()->addLink('BUTTON_BACK', $this->getLink()->action());



      }
   }

?>