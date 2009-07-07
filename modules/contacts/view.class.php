<?php
class Contacts_View extends View {
   const GOOGLE_MAP_KEY = "ABQIAAAAsHf9LHrD4hQz3AMUm7rvMBTr_fp-O8KjtAz6tL21PRFG2njwgBTMUDVDPLZ_5kz0x8fMwm8eL4If1A";

   public function mainView() {
      $contactsM = new Contacts_Model_List($this->sys());
      $contacts = $contactsM->getContactsList();
      if($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_contact', $this->_("Přidat"),
             $this->link()->action($this->sys()->action()->addContact()),
             $this->_("Přidat kontakt"), "text_add.png");
         $this->template()->toolbox = $toolbox;

         // doplnění toolboxů do kontaktů
         foreach ($contacts as &$contact) {
            $toolbox = new Template_Toolbox();
            $toolbox->addTool('edit_contact', $this->_("Upravit"),
                $this->link()->action($this->sys()->action()->editContact())
                ->article($contact[Contacts_Model_Detail::COLUMN_CONTACT_NAME],
                $contact[Contacts_Model_Detail::COLUMN_CONTACT_ID]),
                $this->_("Upravit kontakt"), "text_edit.png")
                ->addTool(Contacts_Controller::FORM_PREFIX.Contacts_Controller::FORM_BUTTON_DELETE,
                   $this->_m("Smazat"), $this->link(),
                $this->_m("Smazat kontakt"), "remove.png", Contacts_Controller::FORM_PREFIX
                .Contacts_Controller::FORM_INPUT_ID,
                $contact[Contacts_Model_Detail::COLUMN_CONTACT_ID],
                $this->_m("Opravdu smazat kontakt")."?");
            $contact['toolbox'] = $toolbox;
         }

      }
      $this->template()->contacts = $contacts;

      $this->template()->addTplFile("map.phtml");
      $this->template()->addTplFile("list.phtml");

      $this->template()->addCssFile("style.css");

      $googleMapsFile = new JsPlugin_JsFile("http://maps.google.com/maps");
      $googleMapsFile->setParam("file", "api");
      $googleMapsFile->setParam("v", "2");
      $googleMapsFile->setParam("key", self::GOOGLE_MAP_KEY);
      $this->template()->addJsFile($googleMapsFile);


      $this->template()->datadir = $this->sys()->module()->getDir()->getDataDir(true);

      $this->template()->addJsPlugin(new JsPlugin_LightBox());
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addContactView() {
      $this->template()->addTplFile('editContact.phtml');
      $this->template()->addCssFile("style.css");

      $contM = new Contacts_Model_Types($this->sys());
      // načtení měst
      $this->template()->contactTypes = $contM->getContactTypes();

      //      tinyMCE
      $tinyMce = new JsPlugin_TinyMce();
      $tinyMce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editContactView() {
      $this->template()->addTplFile('editContact.phtml');
      $this->template()->addCssFile("style.css");

      $contM = new Contacts_Model_Types($this->sys());
      // načtení měst
      $this->template()->contactTypes = $contM->getContactTypes();

      $contModel = new Contacts_Model_Detail($this->sys());
      $this->template()->contact = $contModel->getContactDetailAllLangs($this->sys()->article());

      //      tinyMCE
      $tinyMce = new JsPlugin_TinyMce();
      $tinyMce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);


      //Taby - uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}
?>