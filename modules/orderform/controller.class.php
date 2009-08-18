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
   // údaje o objednavateli
   const FORM_NAME = 'name';
   const FORM_SURNAME = 'surname';
   const FORM_COMPANY = 'company';
   const FORM_PHONE = 'phone';
   const FORM_EMAIL = 'email';

   /**
    * Metoda pro vytvoření emailu
    * @param array $obj
    */
   private function createMail($obj) {
      define ("TLOUSTKA","2");
      $mejl = new XmlWriter();
      $mejl->openMemory();
      $mejl->setIndent(4);
      $mejl->writeDTD('xhtml', "-//W3C//DTD XHTML 1.0 Transitional//EN", "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd");
      $mejl->startElement("html");
      $mejl->writeAttribute("xmlns","http://www.w3.org/1999/xhtml");
      $mejl->writeAttribute("lang", "cs");
      $mejl->writeAttribute("xml:lang","cs");
      //hlavička
      $mejl->startElement("head");
      //      $mejl->startElement("meta");
      //<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

      //      $mejl->writeAttribute("http-equiv", "Content-Type");
      //      $mejl->writeAttribute("content", "text/html; charset=UTF-8");
      //      $mejl->endElement();


      $mejl->writeElement("title", "Objednávka");

      $mejl->endElement(); //konec hlavičky

      //tělo mejlu
      $mejl->startElement("body");
      //nadpis mejlu
      $mejl->startElement("h1");
      $mejl->writeAttribute("style", "font-size:14pt");
      $mejl->writeRaw("Objednávka plastových oken a dveří ze stránek ");
      $mejl->startElement("a");
      $mejl->writeAttribute("href", "http://www.moravaokno.cz");
      $mejl->writeRaw("Moravaokno.cz");
      $mejl->endElement(); //a
      $mejl->endElement(); //h1
      // konec nadpisu

      $mejl->startElement("h2");
      $mejl->writeAttribute("style", "font-size:12pt");
      $mejl->writeRaw("Osobní údaje:");
      $mejl->endElement(); //h2
      
      $mejl->startElement("table");
      $mejl->writeAttribute("border", TLOUSTKA);

      $mejl->startElement("tr");
      $mejl->writeElement("td", "Objednavatel:");
      $mejl->writeElement("td", "Místo odběru:");
      $mejl->endElement(); //tr
      $mejl->startElement("tr");
      $mejl->startElement("td");

      // osobní údaje objednávajícího
      $mejl->writeRaw($obj['orderFormDetails']['name']);
      $mejl->writeRaw($obj['orderFormDetails']['surname']);
      $mejl->writeElement("br");
      if ($obj['orderFormDetails']['company'] != '') {
         $mejl->writeRaw($obj['orderFormDetails']['company']);
         $mejl->writeElement("br");
      }
      $mejl->writeRaw('Tel: '.$obj['orderFormDetails']['phone']);
      $mejl->writeElement("br");

      $mejl->writeRaw('Email: ');
      $mejl->startElement("a");
      $mejl->writeAttribute("title", $obj['orderFormDetails']['email']);
      $mejl->writeAttribute("href", 'mailto:'.$obj['orderFormDetails']['email']);
      $mejl->writeRaw($obj['orderFormDetails']['email']);
      $mejl->endElement(); //a
      $mejl->writeElement("br");

      $mejl->endElement(); //td

      $mejl->startElement("td");
      // údaje o pobočce
      $IdPobocky = $obj['orderFormDetails']['office'];
      //vytvoření modelu pro načtení pobočky z db
      $cities = new Contacts_Model_Detail($this->sys());
      $pobocka = $cities->getContactDetail($IdPobocky);
      $pobockaInfo = $cities->getContactDetail2($IdPobocky);
      //print_r($pobockaInfo);

      $mejl->writeRaw($pobocka["name"]);
      $mejl->writeElement("br");
      //výpis adresy
      foreach ($pobockaInfo as $det) {
         if ($det['type'] == 'street') {$mejl->writeRaw("Adresa: ".$det['value']);
            $mejl->writeElement("br");}
      }
      //výpis města
      foreach ($pobockaInfo as $det) {
         if ($det['type'] == 'city') {$mejl->writeRaw($det['value']);
            $mejl->writeElement("br");}
      }
      //výpis telefonů:
      foreach ($pobockaInfo as $det) {
         if ($det['type'] == 'phone') {$mejl->writeRaw("Tel: ".$det['value']);
            $mejl->writeElement("br");}
      }
      //fax:
      foreach ($pobockaInfo as $det) {
         if ($det['type'] == 'fax') {$mejl->writeRaw("Fax: ".$det['value']);
            $mejl->writeElement("br");}
      }
      //mobily:
      foreach ($pobockaInfo as $det) {
         if ($det['type'] == 'cell_phone') {$mejl->writeRaw("Mobil: ".$det['value']);
            $mejl->writeElement("br");}
      }
      //email:
      foreach ($pobockaInfo as $det) {
         if ($det['type'] == 'email_order') {$mejl->writeRaw("Email: <a title=\"".$det['value']."\" href=\" mailto:".$det['value']."\">".$det['value']."</a>");
            $mejl->writeElement("br");}
      }

      $mejl->endElement(); //td
      $mejl->endElement(); //tr
      $mejl->endElement(); //table
      $mejl->writeElement("hr");

      $mejl->writeElement("br");

      $mejl->startElement("h2");//<h2>
         $mejl->writeAttribute("style", "font-size:12pt");
         $mejl->writeRaw("Objednané položky:");
      $mejl->endElement(); //</h2>

      $mejl->writeElement("hr");

      //tabulky s objednanými prvky
      //      var_dump($obj['items']);
      foreach ($obj['items'] as $item) {
         $mejl->startElement("table");
         $mejl->startElement("tr");
         $mejl->startElement("td");
         $mejl->writeAttribute("colspan", 2);
         $mejl->startElement("img");
         $mejl->writeAttribute("src", str_ireplace('./', Links::getMainWebDir(), $item['image']));
         //                  $mejl->writeAttribute("src", basename($item['image']));
         $mejl->writeAttribute("alt", $item['label']);
         $mejl->endElement();
         $mejl->endElement();
         $mejl->endElement();
         $mejl->startElement("tr");
         $mejl->startElement("td");
         $mejl->writeAttribute("width", 150);
         $mejl->writeElement("b", "Typ položky: ");
         $mejl->endElement();//td
         $mejl->writeElement("td", $item['label']);
         $mejl->endElement(); //tr
         $mejl->startElement("tr");
         $mejl->writeElement("td", "Počet: ");
         $mejl->writeElement("td", $item['count']." kusů");
         $mejl->endElement(); //tr
         //profily
         if ($item["profileWindowType"]!="null") {
            $mejl->startElement("tr");
            $mejl->writeElement("td","Okenní profil: ");
            $mejl->writeElement("td",$item["profileWindowName"]);
            $mejl->endElement(); //tr
         }
         if ($item["profileDoorType"]!="null") {
            $mejl->startElement("tr");
            $mejl->writeElement("td","Dveřní profil: ");
            $mejl->writeElement("td",$item["profileDoorName"]);
            $mejl->endElement(); //tr
         }
         $i = 0; //počítadlo
         foreach ($item['params'] as $par) {
            $i++;
            $mejl->startElement("tr");
            $mejl->startElement("td");
            $mejl->writeAttribute("colspan", "2");
            $mejl->writeElement("b", "Část ".$i.": ");
            $mejl->endElement();//td
            $mejl->endElement();//tr
            $mejl->startElement("tr");
            $mejl->writeElement("td", "Rozměry: ");
            $mejl->writeElement("td", $par['width'].'x'.$par['height']." mm");
            $mejl->endElement(); //tr
            $mejl->startElement("tr"); //doplnky
            $mejl->writeElement("td", "Doplňky: ");
            $mejl->startElement("td");
            if ($par['louver']) {
               $mejl->writeRaw("Žaluzie");
               $mejl->writeElement("br");
            }
            if ($par['grid']) {
               $mejl->writeRaw("Okenní sítě");
               $mejl->writeElement("br");
            }
            if ($par['doorgrid']) {
               $mejl->writeRaw("Síťové dveře");
               $mejl->writeElement("br");
            }
            if ($par['rolltop']) {
               $mejl->writeRaw("Rolety");
               $mejl->writeElement("br");
            }
            $mejl->endElement();//td
            $mejl->endElement();
            ////
            $mejl->startElement("tr");//mrizka
            $mejl->writeElement("td", "Mřížka: ");
            $mejl->writeElement("td",$par['gridtypename']);
            $mejl->endElement(); //tr
         }
         $mejl->endElement(); //table
         $mejl->writeElement('hr');
      }

      //objednané služby
      $mejl->startElement("h2");
         $mejl->writeAttribute("style", "font-size:12pt");
         $mejl->writeRaw("Objednané služby:");
      $mejl->endElement(); //h2
      
      $ConServ = new Orderform_Model_Contactservices($this->sys());
      $serv = $ConServ->getServices();
      $mejl->startElement("table");
      $mejl->writeAttribute("border", TLOUSTKA);
      foreach ($serv as $service) {
         $mejl->startElement("tr");
         $mejl->writeElement("td", $service->name);
         if ($obj['orderFormDetails'][(string)$service->inputname] == true)
            $mejl->writeElement("td", "ANO");
         else $mejl->writeElement("td", "NE");
         $mejl->endElement(); //tr
      }
      $mejl->endElement(); //table
      $mejl->endElement(); // body
      $mejl->endElement(); // html

      //      $mejl->endDocument();
      return $mejl->outputMemory();

   }

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

      if($form->sendForm()) {
         $buyDetails = $form->getValues();
      }

      if($form->checkForm()) {
      // doplnění dat z formuláře

         if(empty ($this->view()->items)) {
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
      if(!isset ($_SESSION['items'])) {
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
         if(preg_match("/^param_([a-z]+)_([[:digit:]]+)$/i", $key, $matches)) {
         // ^param_([a-z]+)_([[:digit:]]+)$
            if(!isset($itemArray['params'][$matches[2]])) {
               $itemArray['params'][$matches[2]] = array();
            }
            if($var == "true") {
               $var = true;
            } else if($var == "false") {
                  $var = false;
               }
            $itemArray['params'][$matches[2]][$matches[1]] = $var;
         } else {
            $itemArray[$key] = $var;
         }
      }
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
      if(isset ($items[$_POST['idItem']])) {
         unset ($items[$_POST['idItem']]);
      }
   }

   /**
    * Ajax metoda pro odstranění všech položek z košíku
    */
   public function basketRemoveAllAjaxController(Ajax $ajax) {
      if(isset ($_SESSION['items'])) {
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

      //uložení osobních údajů do db
      $orderform = new Orderform_Model_Detail($this->sys());
      if($form->checkForm()) {
         if(!$orderform->saveOrderform($_SESSION['orderFormDetails'][self::FORM_NAME],
         $_SESSION['orderFormDetails'][self::FORM_SURNAME],
         $_SESSION['orderFormDetails'][self::FORM_COMPANY],
         $_SESSION['orderFormDetails'][self::FORM_PHONE],
         $_SESSION['orderFormDetails'][self::FORM_EMAIL])) {
            throw new UnexpectedValueException($this->_m('Váš dotaz se nepodařilo odeslat'));
         }
         // konec ukládání osobních údajů do db

         // vytvoření a odeslání emailu
         $mailObj = new Email(true);
         $mailObj->setContent(htmlspecialchars_decode(urldecode($this->createMail($_SESSION))));
         $mailObj->setSubject($this->_("Objednávka oken"));
         //načtení emailů
         $entryM = new Contacts_Model_EntryList($this->sys());
         foreach ($entryM->getOrderMails($_SESSION['orderFormDetails']['office']) as $mail) {
            $mailObj->addAddress($mail[Contacts_Model_EntryList::COLUMN_VALUE]);
         }
         // přidání obrázků
         //         foreach ($_SESSION['items'] as $item) {
         //            $mailObj->addAttachment(new File($item['image']));
         //         }

         $mailObj->addAddress($_SESSION['orderFormDetails'][self::FORM_EMAIL]);
         $mailObj->sendMail();
         ////////////////////////////////////////////////////////
         //Jen pro testovací účely
         //var_dump($_SESSION['items']);
         //         var_dump($_SESSION);
         //         print ($this->createMail($_SESSION));
         //         exit ();
         //Jen pro testovací účely
         /////////////////////////////////////////////////////////

         // odstranění dat
         unset ($_SESSION['items']);
         unset ($_SESSION['lastKey']);
         unset ($_SESSION['orderFormDetails']);
         $this->infoMsg()->addMessage($this->_("Objednávka byla odeslána"));
         $this->link()->action()->reload();
      }
      $this->view()->items = $_SESSION['items'];
      $this->view()->buyDetails = $_SESSION['orderFormDetails'];
   //      var_dump($_SESSION);
   }
}

?>