<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */



class Orderform_Controller extends Controller {
   /**
    * Název session s prvky košíku
    */
   const SESSION_ITEMS_NAME = "orderform_items";

   /**
    * Název formulářových prvků
    */
   const FORM_ORDER_PERFIX = "orderform_";
   const FORM_CONFIRM_PERFIX = "confirm_";
   const FORM_BUTTON_SEND = "send";
   // pobočka
   const FORM_OFFICE = 'office';
   // údaje o obědnavateli
   const FORM_NAME = 'name';
   const FORM_SURNAME = 'surname';
   const FORM_COMPANY = 'company';
   const FORM_PHONE = 'phone';
   const FORM_EMAIL = 'email';

   /**
    * Kontroler pro zobrazení formuláře,
    * kterým dá zákazník kontakt na sebe a položí dotaz
    */
   public function mainController() {
   //		Kontrola práv
      $this->checkReadableRights();
      
      // uložené prvky
      $this->view()->items = $_SESSION['items'];
      $buyDetails = &$_SESSION['orderFormDetails'];

      // načtení služeb
      $servicesM = new Orderform_Model_Contactservices($this->sys());
      $services = $servicesM->getServices();
      $this->view()->services = $services;

      // formulář
      $form = new Form(self::FORM_ORDER_PERFIX);
      $form->crInputText(self::FORM_NAME, true)
      ->crInputText(self::FORM_SURNAME, true)
      ->crInputText(self::FORM_EMAIL, true, false, Form::VALIDATE_EMAIL)
      ->crInputText(self::FORM_COMPANY)
      ->crInputText(self::FORM_PHONE, true)
      ->crInputRadio(self::FORM_OFFICE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      // doplnění služeb
      foreach ($services as $service) {
        $form->crInputCheckbox((string)$service->inputname);
      }

      if($form->checkForm()){
         // doplnění dat z formuláře
         $buyDetails = $form->getValues();
         if(empty ($this->view()->items)){
            $this->errMsg()->addMessage($this->_("Košík je prázdný"));
         } else {
            $this->link()->action($this->action()->confirmOrder())->reload();
         }
      }

      // přenesení do šablony
      $this->view()->buyDetails = $buyDetails;

   }

   /**
   buyDetails * kontroler pro přidání prvku
    * @param Ajax $ajax -- objekt Ajaxu
    */
   public function basketAddItemAjaxController(Ajax $ajax) {
      if(!isset ($_SESSION['items'])){
         $_SESSION['items'] = array();
         $_SESSION['lastKey'] = 0;
      }
      $items = &$_SESSION['items'];
      $lastKey = &$_SESSION['lastKey'];

      $itemArray = array();
      foreach ($_POST as $key => $var) {
         $var = htmlspecialchars($var);
         // pokud se jedná o parametr je rozparsován
         $matches = array();
         if(preg_match("/^param_([a-z]+)_([[:digit:]]+)$/i", $key, $matches)){
            // ^param_([a-z]+)_([[:digit:]]+)$
            if(!isset($itemArray['params'][$matches[2]])){
               $itemArray['params'][$matches[2]] = array();
            }
            $itemArray['params'][$matches[2]][$matches[1]] = $var;
         } else {
            $itemArray[$key] = $var;
         }
      }

//      $ses = $session->getObj('items');
      $items[$lastKey] = $itemArray;

      $this->view()->itemId = $lastKey;
      $this->view()->item = $itemArray;
      $lastKey++;
   }

   /**
    * Ajax metoda pro odstranění položky z košíku
    */
   public function basketRemoveItemAjaxController(Ajax $ajax) {
      $items = &$_SESSION['items'];
      if(isset ($items[$_POST['idItem']])){
         unset ($items[$_POST['idItem']]);
      }
   }

   /**
    * Ajax metoda pro odstranění všech položek z košíku
    */
   public function basketRemoveAllAjaxController(Ajax $ajax) {
      if(isset ($_SESSION['items'])){
         unset ($_SESSION['items']);
         unset ($_SESSION['lastKey']);
      }
   }

   /**
    * Kontroler pro potvrzení formuláře
    */
   public function confirmOrderController() {
      $form = new Form(self::FORM_ORDER_PERFIX.self::FORM_CONFIRM_PERFIX);
      $form->crSubmit(self::FORM_BUTTON_SEND);

      // odeslání formuláře na email a uložení do db
      if($form->checkForm()){
         $this->infoMsg()->addMessage($this->_("Objednávka byla odeslána"));
         // odstranění dat
         unset ($_SESSION['items']);
         unset ($_SESSION['lastKey']);
         unset ($_SESSION['orderFormDetails']);
         $this->link()->action()->reload();
      }
      $this->view()->items = $_SESSION['items'];
      $this->view()->buyDetails = $_SESSION['orderFormDetails'];

   }
}

?>