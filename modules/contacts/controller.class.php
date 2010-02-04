<?php
class Contacts_Controller extends Controller {
  /**
   * Speciální imageinární sloupce
   * @var string
   */
   const CONTACT_EDIT_LINK = 'edit_link';

  /**
   * Názvy formůlářových prvků
   * @var string
   */
   const FORM_PREFIX = 'contact_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_INPUT_ID = 'id';
   const FORM_INPUT_NAME = 'name';
   const FORM_INPUT_FILE = 'file';
   const FORM_INPUT_TEXT = 'text';
//   const FORM_INPUT_CITY = 'city';
   const FORM_INPUT_ID_TYPE = 'id_type';
   const FORM_INPUT_POSITION_X = 'position_x';
   const FORM_INPUT_POSITION_Y = 'position_y';
   const FORM_INPUT_PRIORITY = 'priority';

   /**
    * Adresíř s malými obrázky
    */
   const IMAGES_SMALL_DIR = 'small';

   /**
    * Parametry s velikostí malého obrázku
    */
   const PARAM_IMAGE_WIDTH = 'width';
   const PARAM_IMAGE_HEIGHT = 'height';
   const PARAM_SMALL_IMAGE_WIDTH = 'smallwidth';
   const PARAM_SMALL_IMAGE_HEIGHT = 'smallheight';

  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      if($this->rights()->isWritable()){
         $this->checkDeleteContact();
      }
   }

   /**
   * Kontroler pro přidání kontaktu
   */
   public function addContactController(){
      $this->checkWritebleRights();

      $addContactForm = new Form(self::FORM_PREFIX);
      $addContactForm->crInputText(self::FORM_INPUT_NAME, true, true)
//      ->crInputText(self::FORM_INPUT_CITY)
      ->crInputText(self::FORM_INPUT_POSITION_X)
      ->crInputText(self::FORM_INPUT_POSITION_Y)
      ->crInputText(self::FORM_INPUT_PRIORITY)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crInputFile(self::FORM_INPUT_FILE, true)
      ->crSelect(self::FORM_INPUT_ID_TYPE, 1)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($addContactForm->checkForm()){
         $image = new File_Image($addContactForm->getValue(self::FORM_INPUT_FILE));
         if($image->isImage()){
               $contModel = new Contacts_Model_Detail($this->sys());
               //            Uložení obrázků
               // malý
               $image->saveImage($this->module()->getDir()->getDataDir()
                  .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR,
                  $this->module()->getParam(self::PARAM_SMALL_IMAGE_WIDTH, 200),
                  $this->module()->getParam(self::PARAM_SMALL_IMAGE_HEIGHT, 150));
               // velký
               $image->saveImage($this->module()->getDir()->getDataDir(),
                  $this->module()->getParam(self::PARAM_IMAGE_WIDTH, 800),
                  $this->module()->getParam(self::PARAM_IMAGE_HEIGHT, 600));
               if(!$contModel->saveNewContact($addContactForm->getValue(self::FORM_INPUT_NAME),
                  $addContactForm->getValue(self::FORM_INPUT_TEXT),
                  $addContactForm->getValue(self::FORM_INPUT_POSITION_X),
                  $addContactForm->getValue(self::FORM_INPUT_POSITION_Y),
                  $image->getName(),
                  $addContactForm->getValue(self::FORM_INPUT_ID_TYPE),
                  $addContactForm->getValue(self::FORM_INPUT_POSITION_Y))){
                  throw new ModuleException($this->_('Chyba při ukládání kontaktu'), 1);
               }
               $this->infoMsg()->addMessage($this->_('Kontakt byl uložen'));
               $this->getLink()->action()->reload();
         }
      }
      $this->view()->error_items = $addContactForm->getErrorItems();
   }

  /**
   * controller pro úpravu kontaktu
   */
   public function editContactController() {
      $this->checkWritebleRights();

      $editContactForm = new Form(self::FORM_PREFIX);
      $editContactForm->crInputText(self::FORM_INPUT_NAME, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crInputText(self::FORM_INPUT_POSITION_X)
      ->crInputText(self::FORM_INPUT_POSITION_Y)
      ->crInputText(self::FORM_INPUT_PRIORITY)
      ->crInputFile(self::FORM_INPUT_FILE)
      ->crSelect(self::FORM_INPUT_ID_TYPE, 1)
      ->crSubmit(self::FORM_BUTTON_SEND);

      if($editContactForm->checkForm()){
            $imageName = null;
         // byl odeslán obrázek
         if($editContactForm->getValue(self::FORM_INPUT_FILE)){
            $image = new File_Image($editContactForm->getValue(self::FORM_INPUT_FILE));
            // kontrola jestli je obrázek
            if($image->isImage()){
               // smažeme starý
               try {
                  $oldImage = new File($contM->getFile(), $this->module()->getDir()->getDataDir());
                  $oldImage->remove();
                  $oldImage = new File($contM->getFile(), $this->module()->getDir()->getDataDir()
                     .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR);
                  $oldImage->remove();
               } catch (Exception $e) {
                  new CoreException($this->_('Chyba při mazání obrázku.').$e->getMessage(),2);
               }

               // malý
               $image->saveImage($this->module()->getDir()->getDataDir()
                  .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR,
                  $this->module()->getParam(self::PARAM_SMALL_IMAGE_WIDTH, 200),
                  $this->module()->getParam(self::PARAM_SMALL_IMAGE_HEIGHT, 150));
               // velký
               $image->saveImage($this->module()->getDir()->getDataDir(),
                  $this->module()->getParam(self::PARAM_IMAGE_WIDTH,800),
                  $this->module()->getParam(self::PARAM_IMAGE_HEIGHT,600));
               $imageName = $image->getName();
            }
         }
         //            Uložení dat do db
         $contM = new Contacts_Model_Detail($this->sys());
         if(!$contM->saveEditContact($editContactForm->getValue(self::FORM_INPUT_NAME),
               $editContactForm->getValue(self::FORM_INPUT_TEXT),
               $editContactForm->getValue(self::FORM_INPUT_POSITION_X),
               $editContactForm->getValue(self::FORM_INPUT_POSITION_Y),
               $editContactForm->getValue(self::FORM_INPUT_PRIORITY),
               $imageName,
               $editContactForm->getValue(self::FORM_INPUT_ID_TYPE),
               $this->article())){
            throw new ModuleException($this->_('Chyba při ukládání kontaktu'),3);
         }

         $this->infoMsg()->addMessage($this->_('Kontakt byl uložen'));
         $this->getLink()->action()->article()->reload();
      }
//
//      //    Data do šablony
//      $this->container()->addData('CONTACT_DATA', $editContactForm->getValues());
//      $this->container()->addData('ERROR_ITEMS', $editContactForm->getErrorItems());
//      $this->container()->addData('AREAS', $cities);
//      //		Odkaz zpět
//      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

  /**
   * metoda pro mazání novinky
   */
   private function checkDeleteContact() {
      $delForm = new Form(self::FORM_PREFIX);
      $delForm->crSubmit(self::FORM_BUTTON_DELETE)
      ->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric');

      if($delForm->checkForm()){
         $contactM = new Contacts_Model_Detail($this->sys());
         $contactM->getContactDetailAllLangs($delForm->getValue(self::FORM_INPUT_ID));
         // smazání obrázků
         try {
            $oldImage = new File($contactM->getFile(), $this->module()->getDir()->getDataDir());
            $oldImage->remove();
            $oldImage = new File($contactM->getFile(), $this->module()->getDir()->getDataDir()
               .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR);
            $oldImage->remove();
            // vymaz z db
            $contactM->deleteContact($delForm->getValue(self::FORM_INPUT_ID));
            $this->infoMsg()->addMessage($this->_('Kontakt byl smazán'));
            $this->getLink()->reload();
         } catch (Exception $e) {
            new CoreException($this->_('Kontakt se nepodařilo smazat').$e->getMessage(), 4);
         }

      }
   }

   public function editotherrefController() {
      $form = new Form();
      $form->setPrefix(self::FORM_PREFIX.self::FORM_PREFIX_OTHER);

      $form->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
            ->crSubmit(self::FORM_BUTTON_SEND);

      $refM = new ReferenceModel();
      $form->setValue(self::FORM_INPUT_TEXT, $refM->getOtherRefAllLang());

 //        Pokud byl odeslán formulář
      if($form->checkForm()){
         if($refM->saveEditOtherReferences($form->getValue(self::FORM_INPUT_TEXT))){
            $this->infoMsg()->addMessage(_m('Ostatní reference byly uloženy'));
            $this->getLink()->action()->reload();
         } else {
            new CoreException(_m('Ostatní reference se nepodařilo uložit, chyba při ukládání.'), 1);
         }
      }
      //    Data do šablony
      $this->container()->addData('REFERENCE_OTHER_DATA', $form->getValues());
      $this->container()->addData('ERROR_ITEMS', $form->getErrorItems());
      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }
}
?>